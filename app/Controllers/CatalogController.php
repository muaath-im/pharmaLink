<?php
/**
 * app/Controllers/CatalogController.php
 * Medicine Catalog & Drug Details Controller.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ProductRepository;
use App\Repositories\InventoryRepository;

class CatalogController extends Controller
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

    public function index(Request $request): Response
    {
        $categoryId = $request->query('category') ? (int)$request->query('category') : null;
        $search     = $request->query('q') ? trim((string)$request->query('q')) : null;

        if ($search !== null && $search !== '') {
            $products = $this->productRepo->search($search);
        } else {
            $products = $this->productRepo->findAll(['category_id' => $categoryId]);
        }

        return $this->render('catalog/index', [
            'pageTitle' => 'Medication Catalog | PharmaLink',
            'products'  => $products,
            'search'    => $search
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $product = $this->productRepo->findBySlug($slug);

        if (!$product) {
            return Response::make('<h1>404 Not Found</h1><p>Medication not found in catalog.</p>', 404);
        }

        $batches = $this->inventoryRepo->getBatchesForProduct($product->id);

        return $this->render('catalog/show', [
            'pageTitle' => "{$product->name} | Clinical Product Details",
            'product'   => $product,
            'batches'   => $batches
        ]);
    }
}
