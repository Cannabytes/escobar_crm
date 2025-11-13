<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\ChatRoom;

$room = ChatRoom::find(11);
if ($room) {
    $users = User::all();
    foreach ($users as $user) {
        $room->participants()->syncWithoutDetaching([
            $user->id => ['joined_at' => now()]
        ]);
    }
    echo 'All users added to room 11' . PHP_EOL;
} else {
    echo 'Room 11 not found' . PHP_EOL;
}
