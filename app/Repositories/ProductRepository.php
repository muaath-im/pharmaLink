<?php
/**
 * app/Repositories/ProductRepository.php
 * Data Access Layer for Products & Clinical Inventory.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Product;
use PDO;

class ProductRepository
{
    /**
     * Retrieve all active products with category names and aggregated stock.
     *
     * @return Product[]
     */
    public function findAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                p.*,
                c.name as category_name,
                COALESCE(SUM(b.quantity - b.allocated_quantity), 0) as stock_quantity
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN inventory_batches b ON p.id = b.product_id
            WHERE p.is_active = 1
        ";

        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params['category_id'] = (int)$filters['category_id'];
        }

        if (!empty($filters['rx_only'])) {
            $sql .= " AND p.requires_prescription = 1";
        }

        $sql .= " GROUP BY p.id ORDER BY p.name ASC";

        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute($params);

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = Product::fromArray($row);
        }

        return $results;
    }

    /**
     * Find single product by ID.
     */
    public function findById(int $id): ?Product
    {
        $sql = "
            SELECT 
                p.*,
                c.name as category_name,
                COALESCE(SUM(b.quantity - b.allocated_quantity), 0) as stock_quantity
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN inventory_batches b ON p.id = b.product_id
            WHERE p.id = :id
            GROUP BY p.id
        ";

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Product::fromArray($row) : null;
    }

    /**
     * Find single product by unique URL slug.
     */
    public function findBySlug(string $slug): ?Product
    {
        $sql = "
            SELECT 
                p.*,
                c.name as category_name,
                COALESCE(SUM(b.quantity - b.allocated_quantity), 0) as stock_quantity
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN inventory_batches b ON p.id = b.product_id
            WHERE p.slug = :slug
            GROUP BY p.id
        ";

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? Product::fromArray($row) : null;
    }

    /**
     * Search products by keyword across name, generic name, and description.
     *
     * @return Product[]
     */
    public function search(string $keyword): array
    {
        $sql = "
            SELECT 
                p.*,
                c.name as category_name,
                COALESCE(SUM(b.quantity - b.allocated_quantity), 0) as stock_quantity
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN inventory_batches b ON p.id = b.product_id
            WHERE p.is_active = 1
              AND (p.name LIKE :kw OR p.generic_name LIKE :kw OR p.description LIKE :kw)
            GROUP BY p.id
            ORDER BY p.name ASC
            LIMIT 20
        ";

        $stmt = Database::getConnection()->prepare($sql);
        $stmt->execute(['kw' => '%' . $keyword . '%']);

        $results = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $results[] = Product::fromArray($row);
        }

        return $results;
    }
}
