<?php
/**
 * app/Repositories/InventoryRepository.php
 * Data Access Layer for Lot Batches and FEFO Tracking.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\InventoryBatch;
use PDO;

class InventoryRepository
{
    /**
     * Retrieve batches for a product ordered by FEFO (First-Expired, First-Out).
     *
     * @return InventoryBatch[]
     */
    public function getBatchesForProduct(int $productId): array
    {
        $sql = "
            SELECT b.*, p.name as product_name
            FROM inventory_batches b
            JOIN products p ON b.product_id = p.id
            WHERE b.product_id = :product_id AND (b.quantity - b.allocated_quantity) > 0
            ORDER BY b.expiry_date ASC
        ";

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['product_id' => $productId]);

        $batches = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $batches[] = InventoryBatch::fromArray($row);
        }

        return $batches;
    }

    /**
     * Retrieve all batches expiring within a specified number of days.
     *
     * @return InventoryBatch[]
     */
    public function getNearExpiryBatches(int $days = 90): array
    {
        $sql = "
            SELECT b.*, p.name as product_name
            FROM inventory_batches b
            JOIN products p ON b.product_id = p.id
            WHERE b.expiry_date <= DATE_ADD(CURDATE(), INTERVAL :days DAY)
              AND (b.quantity - b.allocated_quantity) > 0
            ORDER BY b.expiry_date ASC
        ";

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['days' => $days]);

        $batches = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $batches[] = InventoryBatch::fromArray($row);
        }

        return $batches;
    }
}
