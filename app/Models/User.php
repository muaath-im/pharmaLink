<?php
/**
 * app/Models/User.php
 * Enterprise User & Role Account Entity Model.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly int $roleId,
        public readonly ?string $firstName,
        public readonly ?string $lastName,
        public readonly string $username,
        public readonly string $email,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly int $age,
        public readonly string $status,
        public readonly bool $isAdmin,
        public readonly ?string $roleName = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            roleId: (int)($data['role_id'] ?? 7),
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            username: (string)$data['username'],
            email: (string)$data['email'],
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            age: (int)($data['age'] ?? 18),
            status: (string)($data['status'] ?? 'active'),
            isAdmin: !empty($data['is_admin']),
            roleName: $data['role_name'] ?? null
        );
    }

    public function getFullName(): string
    {
        if ($this->firstName || $this->lastName) {
            return trim(($this->firstName ?? '') . ' ' . ($this->lastName ?? ''));
        }
        return $this->username;
    }
}
