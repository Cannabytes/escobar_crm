<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\ChatRoom;
use App\Models\ChatMessage;

$users = User::all();
echo "Found " . $users->count() . " users\n";

if ($users->count() >= 2) {
    // Создаем приватную комнату между первыми двумя пользователями
    $room = ChatRoom::firstOrCreate([
        'name' => 'Test Private Room',
        'type' => ChatRoom::TYPE_PRIVATE,
        'created_by' => $users->first()->id
    ]);

    // Добавляем участников
    $room->participants()->syncWithoutDetaching([
        $users[0]->id => ['joined_at' => now()],
        $users[1]->id => ['joined_at' => now()],
    ]);

    // Создаем сообщения от разных пользователей
    ChatMessage::create([
        'chat_room_id' => $room->id,
        'user_id' => $users[0]->id,
        'body' => 'Привет! Это сообщение от ' . $users[0]->name,
        'is_system' => false
    ]);

    ChatMessage::create([
        'chat_room_id' => $room->id,
        'user_id' => $users[1]->id,
        'body' => 'Привет! А это ответ от ' . $users[1]->name,
        'is_system' => false
    ]);

    ChatMessage::create([
        'chat_room_id' => $room->id,
        'user_id' => $users[0]->id,
        'body' => 'Отлично, сообщения отображаются правильно!',
        'is_system' => false
    ]);

    echo "Created test messages in room ID: " . $room->id . "\n";
} else {
    echo "Need at least 2 users to create test messages\n";
}
