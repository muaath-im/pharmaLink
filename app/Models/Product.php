<?php
/**
 * app/Models/Product.php
 * Clinical Drug & Medicine Entity Model.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Models;

class Product
{
    public function __construct(
        public readonly int $id,
        public readonly int $categoryId,
        public readonly string $sku,
        public readonly string $name,
        public readonly ?string $genericName,
        public readonly string $slug,
        public readonly string $description,
        public readonly string $dosageForm,
        public readonly ?string $strength,
        public readonly string $packSize,
        public readonly float $price,
        public readonly float $costPrice,
        public readonly bool $requiresPrescription,
        public readonly int $lowStockThreshold,
        public readonly string $image,
        public readonly bool $isActive,
        public readonly ?string $categoryName = null,
        public readonly int $stockQuantity = 0
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            categoryId: (int)$data['category_id'],
            sku: (string)$data['sku'],
            name: (string)$data['name'],
            genericName: isset($data['generic_name']) ? (string)$data['generic_name'] : null,
            slug: (string)$data['slug'],
            description: (string)($data['description'] ?? ''),
            dosageForm: (string)($data['dosage_form'] ?? 'tablet'),
            strength: isset($data['strength']) ? (string)$data['strength'] : null,
            packSize: (string)($data['pack_size'] ?? '1 Box'),
            price: (float)($data['price'] ?? 0.00),
            costPrice: (float)($data['cost_price'] ?? 0.00),
            requiresPrescription: !empty($data['requires_prescription']),
            lowStockThreshold: (int)($data['low_stock_threshold'] ?? 10),
            image: (string)($data['image'] ?? 'panadol.jpg'),
            isActive: !empty($data['is_active']),
            categoryName: isset($data['category_name']) ? (string)$data['category_name'] : null,
            stockQuantity: (int)($data['stock_quantity'] ?? 0)
        );
    }

    public function isLowStock(): bool
    {
        return $this->stockQuantity <= $this->lowStockThreshold;
    }
}
