<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreChatMessageRequest;
use App\Http\Requests\Admin\StoreChatRoomRequest;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $rooms = ChatRoom::query()
            ->with([
                'participants:id,name,avatar',
                'lastMessage.user:id,name,avatar',
            ])
            ->withCount('messages')
            ->forUser($user)
            ->orderByDesc('updated_at')
            ->get();

        $transformedRooms = $rooms->map(fn (ChatRoom $room) => $this->transformRoom($room, $user));
        $privateUsers = $this->buildPrivateUsers($user, $rooms)->values();

        return view('admin.chat.index', [
            'initialRooms' => $transformedRooms,
            'initialPrivateUsers' => $privateUsers,
        ]);
    }

    public function rooms(Request $request): JsonResponse
    {
        $user = $request->user();

        $rooms = ChatRoom::query()
            ->with([
                'participants:id,name,avatar',
                'lastMessage.user:id,name,avatar',
            ])
            ->withCount('messages')
            ->forUser($user)
            ->orderByDesc('updated_at')
            ->get();

        $transformedRooms = $rooms->map(fn (ChatRoom $room) => $this->transformRoom($room, $user));
        $privateUsers = $this->buildPrivateUsers($user, $rooms);

        return response()->json([
            'data' => $transformedRooms,
            'private_users' => $privateUsers,
        ]);
    }

    public function storeRoom(StoreChatRoomRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        if ($data['type'] === ChatRoom::TYPE_PRIVATE) {
            /** @var User $otherUser */
            $otherUser = User::query()->findOrFail($data['participant_id']);

            $existingRoom = ChatRoom::query()
                ->where('type', ChatRoom::TYPE_PRIVATE)
                ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
                ->whereHas('participants', fn ($q) => $q->where('user_id', $otherUser->id))
                ->first();

            if ($existingRoom) {
                $existingRoom->load([
                    'participants:id,name,avatar',
                    'lastMessage.user:id,name,avatar',
                ])->loadCount('messages');

                return response()->json([
                    'data' => $this->transformRoom($existingRoom, $user),
                    'meta' => [
                        'existing' => true,
                    ],
                ]);
            }

            $room = DB::transaction(function () use ($user, $otherUser) {
                /** @var ChatRoom $room */
                $room = ChatRoom::query()->create([
                    'type' => ChatRoom::TYPE_PRIVATE,
                    'created_by' => $user->id,
                ]);

                $room->participants()->attach([
                    $user->id => ['joined_at' => now()],
                    $otherUser->id => ['joined_at' => now()],
                ]);

                return $room;
            });
        } else {
            $room = DB::transaction(function () use ($user, $data) {
                /** @var ChatRoom $room */
                $room = ChatRoom::query()->create([
                    'name' => $data['name'],
                    'type' => ChatRoom::TYPE_PUBLIC,
                    'created_by' => $user->id,
                ]);

                $room->participants()->syncWithoutDetaching([
                    $user->id => ['joined_at' => now()],
                ]);

                return $room;
            });
        }

        $room->load([
            'participants:id,name,avatar',
            'lastMessage.user:id,name,avatar',
        ])->loadCount('messages');

        return response()->json([
            'data' => $this->transformRoom($room, $user),
            'meta' => [
                'existing' => false,
            ],
        ], Response::HTTP_CREATED);
    }

    public function messages(Request $request, ChatRoom $room): JsonResponse
    {
        $user = $request->user();

        $this->ensureRoomAccess($room, $user);

        $perPage = (int) $request->integer('per_page', 50);
        $perPage = $perPage > 100 ? 100 : ($perPage < 10 ? 10 : $perPage);

        $messages = $room->messages()
            ->with('user:id,name,avatar')
            ->orderBy('created_at')
            ->paginate($perPage);

        return response()->json([
            'data' => $messages->getCollection()->map(fn (ChatMessage $message) => $this->transformMessage($message, $user)),
            'meta' => [
                'current_page' => $messages->currentPage(),
                'last_page' => $messages->lastPage(),
                'per_page' => $messages->perPage(),
                'total' => $messages->total(),
            ],
        ]);
    }

    public function storeMessage(StoreChatMessageRequest $request, ChatRoom $room): JsonResponse
    {
        $user = $request->user();
        $this->ensureRoomAccess($room, $user);

        if ($room->type === ChatRoom::TYPE_PRIVATE) {
            $room->ensureParticipant($user);
        } else {
            $room->participants()->syncWithoutDetaching([
                $user->id => ['joined_at' => now()],
            ]);
        }

        $message = $room->messages()->create([
            'user_id' => $user->id,
            'body' => trim($request->input('message')),
            'is_system' => false,
        ]);

        $message->load('user:id,name,avatar');

        return response()->json([
            'data' => $this->transformMessage($message, $user),
        ], Response::HTTP_CREATED);
    }

    public function users(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => $this->buildPrivateUsers($user),
        ]);
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $search = (string) $request->input('q', '');
        $user = $request->user();

        $users = User::query()
            ->where('id', '!=', $user->id)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email', 'avatar']);

        return response()->json([
            'data' => $users->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
            ]),
        ]);
    }

    protected function ensureRoomAccess(ChatRoom $room, User $user): void
    {
        if ($room->type === ChatRoom::TYPE_PUBLIC) {
            return;
        }

        $isParticipant = $room->participants()
            ->where('chat_room_id', $room->id)
            ->where('user_id', $user->id)
            ->exists();

        abort_unless($isParticipant, Response::HTTP_FORBIDDEN, __('У вас нет доступа к этому чату.'));
    }

    protected function transformRoom(ChatRoom $room, User $user): array
    {
        $participants = $room->participants->map(fn (User $participant) => [
            'id' => $participant->id,
            'name' => $participant->name,
            'avatar_url' => $participant->avatar_url,
            'is_self' => $participant->id === $user->id,
        ]);

        return [
            'id' => $room->id,
            'name' => $room->displayNameFor($user),
            'raw_name' => $room->name,
            'slug' => $room->slug,
            'type' => $room->type,
            'messages_count' => $room->messages_count,
            'updated_at' => optional($room->updated_at)->toIso8601String(),
            'updated_at_human' => optional($room->updated_at)->diffForHumans(),
            'participants' => $participants,
            'last_message' => $room->lastMessage
                ? $this->transformMessage($room->lastMessage, $user)
                : null,
        ];
    }

    protected function transformMessage(ChatMessage $message, User $currentUser): array
    {
        return [
            'id' => $message->id,
            'body' => $message->body,
            'is_system' => (bool) $message->is_system,
            'is_mine' => $message->user_id === $currentUser->id,
            'user' => $message->user ? [
                'id' => $message->user->id,
                'name' => $message->user->name,
                'avatar_url' => $message->user->avatar_url,
            ] : null,
            'created_at' => optional($message->created_at)->toIso8601String(),
            'created_at_human' => optional($message->created_at)->diffForHumans(),
        ];
    }

    /**
     * Построить список всех пользователей с привязкой к приватным чатам.
     *
     * @param  User  $user
     * @param  Collection<int, ChatRoom>|null  $rooms
     * @return Collection<int, array<string, mixed>>
     */
    protected function buildPrivateUsers(User $user, ?Collection $rooms = null): Collection
    {
        if ($rooms === null) {
            $rooms = ChatRoom::query()
                ->where('type', ChatRoom::TYPE_PRIVATE)
                ->whereHas('participants', fn ($q) => $q->where('user_id', $user->id))
                ->with([
                    'participants:id,name,avatar',
                    'lastMessage.user:id,name,avatar',
                ])
                ->withCount('messages')
                ->get();
        } else {
            $rooms = $rooms->filter(fn (ChatRoom $room) => $room->type === ChatRoom::TYPE_PRIVATE);
        }

        $roomMap = [];

        foreach ($rooms as $room) {
            $otherParticipant = $room->participants->firstWhere('id', '!=', $user->id);

            if ($otherParticipant) {
                $roomMap[$otherParticipant->id] = $room;
            }
        }

        $users = User::query()
            ->where('id', '!=', $user->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'avatar']);

        return $users->map(function (User $participant) use ($user, $roomMap) {
            /** @var ChatRoom|null $room */
            $room = $roomMap[$participant->id] ?? null;

            return [
                'id' => $participant->id,
                'name' => $participant->name,
                'email' => $participant->email,
                'avatar_url' => $participant->avatar_url,
                'room' => $room ? $this->transformRoom($room, $user) : null,
            ];
        });
    }
}

