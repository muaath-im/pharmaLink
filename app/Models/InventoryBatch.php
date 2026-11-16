<?php
/**
 * app/Models/InventoryBatch.php
 * Pharmaceutical Lot Batch & FEFO Expiry Entity Model.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Models;

class InventoryBatch
{
    public function __construct(
        public readonly int $id,
        public readonly int $productId,
        public readonly string $batchNumber,
        public readonly int $quantity,
        public readonly int $allocatedQuantity,
        public readonly string $manufacturingDate,
        public readonly string $expiryDate,
        public readonly ?string $supplierName = null,
        public readonly ?string $productName = null
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            productId: (int)$data['product_id'],
            batchNumber: (string)$data['batch_number'],
            quantity: (int)($data['quantity'] ?? 0),
            allocatedQuantity: (int)($data['allocated_quantity'] ?? 0),
            manufacturingDate: (string)$data['manufacturing_date'],
            expiryDate: (string)$data['expiry_date'],
            supplierName: $data['supplier_name'] ?? null,
            productName: $data['product_name'] ?? null
        );
    }

    public function getAvailableQuantity(): int
    {
        return max(0, $this->quantity - $this->allocatedQuantity);
    }

    public function isNearExpiry(int $daysThreshold = 90): bool
    {
        $expiryTimestamp = strtotime($this->expiryDate);
        $thresholdTimestamp = strtotime("+{$daysThreshold} days");
        return $expiryTimestamp <= $thresholdTimestamp;
    }
}
