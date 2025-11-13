<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use App\Models\User;

class ChatRoom extends Model
{
    use HasFactory;

    public const TYPE_PUBLIC = 'public';
    public const TYPE_PRIVATE = 'private';

    protected $fillable = [
        'name',
        'slug',
        'type',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function booted(): void
    {
        static::creating(function (ChatRoom $room) {
            if ($room->type === null) {
                $room->type = self::TYPE_PUBLIC;
            }

            if ($room->type === self::TYPE_PUBLIC && empty($room->slug) && $room->name) {
                $room->slug = self::generateUniqueSlug($room->name);
            }
        });

        static::updating(function (ChatRoom $room) {
            if ($room->type === self::TYPE_PUBLIC && $room->isDirty('name')) {
                $room->slug = self::generateUniqueSlug($room->name, $room->getKey());
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_room_user')
            ->withTimestamps()
            ->withPivot(['joined_at', 'last_read_at']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
            $q->where('type', self::TYPE_PUBLIC)
                ->orWhereHas('participants', fn ($builder) => $builder->where('user_id', $user->id));
        });
    }

    public function ensureParticipant(User $user): void
    {
        if ($this->type === self::TYPE_PUBLIC) {
            return;
        }

        $this->participants()->syncWithoutDetaching([
            $user->id => ['joined_at' => now()],
        ]);
    }

    public function displayNameFor(User $user): string
    {
        if ($this->type === self::TYPE_PRIVATE) {
            $otherParticipant = $this->participants->firstWhere('id', '!=', $user->id);

            if ($otherParticipant) {
                return $otherParticipant->name;
            }

            return __('Приватный чат');
        }

        return $this->name ?? __('Публичный чат');
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (self::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}

