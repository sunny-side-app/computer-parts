<?php

namespace Database\DataAccess\Interfaces;

require_once __DIR__ . '/../../../Models/User.php';
use Models\User;

interface UserDAO
{
    public function create(User $user, string $password): bool;
    public function getById(int $id): ?User;
    public function getByEmail(string $email): ?User;
    public function getHashedPasswordById(int $id): ?string;
}