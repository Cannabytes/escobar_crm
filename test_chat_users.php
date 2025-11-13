<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\ChatController;

echo "=== ТЕСТИРОВАНИЕ API ПОЛЬЗОВАТЕЛЕЙ ===\n";

// Создаем фейковый запрос
$request = new Request();
$request->merge(['q' => '']); // Пустой поиск

// Получаем первого пользователя (предполагаем, что он авторизован)
$user = DB::table('users')->first();
if (!$user) {
    echo "Нет пользователей в базе данных\n";
    exit;
}

// Имитируем авторизацию пользователя
auth()->loginUsingId($user->id);

// Создаем контроллер и вызываем метод users
$controller = new ChatController();
try {
    $response = $controller->users($request);
    $data = json_decode($response->getContent(), true);

    echo "Всего пользователей: " . count($data) . "\n\n";

    foreach ($data as $contact) {
        echo "Пользователь: {$contact['name']}\n";
        echo "  Роль: {$contact['role']}\n";
        echo "  Есть комната: " . ($contact['has_room'] ? 'Да' : 'Нет') . "\n";
        echo "  Непрочитанных: {$contact['unread_count']}\n";
        echo "  Последнее сообщение: {$contact['last_message_text']}\n";
        echo "---\n";
    }
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
}
