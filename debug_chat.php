<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\Admin\ChatController;

// Проверяем сообщения в комнате 11
echo "=== СООБЩЕНИЯ В КОМНАТЕ 11 ===\n";

$user = User::first(); // Берем первого пользователя
$controller = new ChatController();

$messages = DB::table('chat_messages')
    ->where('chat_room_id', 11)
    ->join('users', 'chat_messages.user_id', '=', 'users.id')
    ->select('chat_messages.*', 'users.name as user_name')
    ->orderBy('chat_messages.created_at', 'desc')
    ->take(20)
    ->get();

foreach ($messages as $message) {
    echo "Сообщение ID {$message->id}:\n";
    echo "  Текст: {$message->body}\n";
    echo "  Пользователь: {$message->user_name} (ID: {$message->user_id})\n";
    echo "  is_mine: " . (($message->user_id === $user->id) ? 'true' : 'false') . "\n";
    echo "  current_user: {$user->name} (ID: {$user->id})\n";
    echo "---\n";
}
