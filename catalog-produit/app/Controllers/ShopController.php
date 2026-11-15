<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

/** Front office of the Catalog module: product list (filter + search) and one product. */
class ShopController extends Controller
{
    protected string $page   = 'shop';
    protected string $title  = "Shop | Par'Axis";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $categoryId = (int) ($_GET['category'] ?? 0);
        $q          = trim($_GET['q'] ?? '');

        $categories = Category::all();
        $names      = array_column($categories, 'name', 'id'); // join category_id -> category name

        $this->render('shop', [
            'categories' => $categories,
            'names'      => $names,
            'products'   => Product::search($categoryId, $q),
            'categoryId' => $categoryId,
            'q'          => $q,
        ]);
    }

    public function show(): void
    {
        $product = Product::find((int) ($_GET['id'] ?? 0));
        if (!$product) {
            $this->redirect('shop');
        }

        // the product page belongs to the "Shop" menu ($page stays 'shop')
        $this->title = $product['name'] . " | Par'Axis";

        $this->render('product', [
            'product'  => $product,
            'category' => Category::find((int) $product['category_id']),
        ]);
    }
}
