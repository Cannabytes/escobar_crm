<?php

namespace App\DTO\Admin;

readonly class UpdateUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public int $roleId,
        public ?string $phone,
        public ?string $operator,
        public ?string $phoneComment,
        public ?string $password,
    ) {
    }
}


