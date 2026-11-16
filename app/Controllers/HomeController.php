<?php
/**
 * app/Controllers/HomeController.php
 * Storefront Homepage Controller.
 * Phase 06: Project Structure Refactoring
 */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Repositories\ProductRepository;

class HomeController extends Controller
{
    private ProductRepository $productRepo;

    public function __construct(?ProductRepository $productRepo = null)
    {
        $this->productRepo = $productRepo ?? new ProductRepository();
    }

    public function index(Request $request): Response
    {
        $featuredProducts = $this->productRepo->findAll(['limit' => 6]);

        return $this->render('home/index', [
            'pageTitle'        => 'PharmaLink | Clinical-Grade Online Pharmacy Portal',
            'featuredProducts' => $featuredProducts
        ]);
    }
}
