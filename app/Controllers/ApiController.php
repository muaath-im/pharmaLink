<?php
/**
 * app/Controllers/ApiController.php
 * RESTful JSON API Controller for PharmaLink Services.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ProductRepository;
use App\Repositories\InventoryRepository;

class ApiController extends Controller
{
    private ProductRepository $productRepo;
    private InventoryRepository $inventoryRepo;

    public function __construct(
        ?ProductRepository $productRepo = null,
        ?InventoryRepository $inventoryRepo = null
    ) {
        $this->productRepo   = $productRepo ?? new ProductRepository();
        $this->inventoryRepo = $inventoryRepo ?? new InventoryRepository();
    }

    public function health(Request $request): Response
    {
        return $this->json([
            'status'    => 'healthy',
            'timestamp' => time(),
            'version'   => '2.0.0',
            'php'       => PHP_VERSION
        ], 200, 'PharmaLink API Operational');
    }

    public function products(Request $request): Response
    {
        $search = $request->query('q');
        if ($search) {
            $products = $this->productRepo->search((string)$search);
        } else {
            $products = $this->productRepo->findAll();
        }

        $data = array_map(function ($p) {
            return [
                'id'                    => $p->id,
                'sku'                   => $p->sku,
                'name'                  => $p->name,
                'generic_name'          => $p->genericName,
                'slug'                  => $p->slug,
                'dosage_form'           => $p->dosageForm,
                'strength'              => $p->strength,
                'pack_size'             => $p->packSize,
                'price'                 => $p->price,
                'requires_prescription' => $p->requiresPrescription,
                'stock_quantity'        => $p->stockQuantity,
                'category'              => $p->categoryName,
                'image'                 => $p->image
            ];
        }, $products);

        return $this->json($data, 200, 'Products retrieved successfully');
    }

    public function productDetail(Request $request, string $idOrSlug): Response
    {
        if (is_numeric($idOrSlug)) {
            $product = $this->productRepo->findById((int)$idOrSlug);
        } else {
            $product = $this->productRepo->findBySlug($idOrSlug);
        }

        if (!$product) {
            return $this->error('Product not found', 404, 'PRODUCT_NOT_FOUND');
        }

        $batches = $this->inventoryRepo->getBatchesForProduct($product->id);
        $batchData = array_map(function ($b) {
            return [
                'batch_number' => $b->batchNumber,
                'expiry_date'  => $b->expiryDate,
                'available'    => $b->getAvailableQuantity()
            ];
        }, $batches);

        return $this->json([
            'product' => [
                'id'                    => $product->id,
                'sku'                   => $product->sku,
                'name'                  => $product->name,
                'generic_name'          => $product->genericName,
                'slug'                  => $product->slug,
                'description'           => $product->description,
                'dosage_form'           => $product->dosageForm,
                'strength'              => $product->strength,
                'pack_size'             => $product->packSize,
                'price'                 => $product->price,
                'requires_prescription' => $product->requiresPrescription,
                'stock_quantity'        => $product->stockQuantity,
                'category'              => $product->categoryName,
                'image'                 => $product->image,
                'batches'               => $batchData
            ]
        ], 200, 'Product details retrieved');
    }

    public function nearExpiryBatches(Request $request): Response
    {
        $days = (int)($request->query('days', 90));
        $batches = $this->inventoryRepo->getNearExpiryBatches($days);

        $data = array_map(function ($b) {
            return [
                'id'                 => $b->id,
                'product_name'       => $b->productName,
                'batch_number'       => $b->batchNumber,
                'quantity_available' => $b->getAvailableQuantity(),
                'expiry_date'        => $b->expiryDate,
                'supplier'           => $b->supplierName
            ];
        }, $batches);

        return $this->json($data, 200, "Batches expiring within {$days} days");
    }
}
