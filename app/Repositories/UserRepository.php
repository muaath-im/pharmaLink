<?php
/**
 * app/Repositories/UserRepository.php
 * Data Access Layer for Accounts, Credentials, and Roles.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\User;
use PDO;

class UserRepository
{
    public function findById(int $id): ?User
    {
        $sql = "
            SELECT u.*, r.name as role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.id = :id
        ";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? User::fromArray($row) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $sql = "
            SELECT u.*, r.name as role_name 
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE u.email = :email
        ";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['email' => strtolower(trim($email))]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? User::fromArray($row) : null;
    }

    /**
     * Retrieve list of assigned permission slugs for a role.
     *
     * @return string[]
     */
    public function getRolePermissions(int $roleId): array
    {
        $sql = "
            SELECT p.slug 
            FROM permissions p
            JOIN role_permissions rp ON p.id = rp.permission_id
            WHERE rp.role_id = :role_id
        ";
        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['role_id' => $roleId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
