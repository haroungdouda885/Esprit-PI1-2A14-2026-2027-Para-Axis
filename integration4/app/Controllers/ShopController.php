<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;

/**
 * Front office of the shop. Customers and visitors only (the admin has no shop window).
 * - Catalog (member 2)            : index (products + category filter + search), show (one product)
 * - Orders and payments (member 3): add (add to bag, without reloading the page when called with fetch)
 */
class ShopController extends Controller
{
    protected string $page   = 'shop';
    protected string $title  = "Shop | Par'Axis";
    protected string $layout = 'front-layout';

    public function index(): void
    {
        $categoryId = (int) ($_GET['category'] ?? 0) ?: null;
        $search     = trim($_GET['q'] ?? '');

        $categories = [];
        foreach (Category::ofType('product') as $c) { // association categories are not shown in the shop
            $categories[(int) $c['id']] = $c['name']; // join category_id -> category name
        }

        $this->render('shop', [
            'products'   => Product::search($categoryId, $search),
            'categories' => $categories,
            'categoryId' => $categoryId,
            'search'     => $search,
            'flash'      => takeFlash(),
        ]);
    }

    /** Product page (Catalog) with the "Add to bag" button (Orders) */
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
            'inBag'    => Cart::quantity((int) $product['id']),
            'quizzes'  => \App\Models\Quiz::forProduct((int) $product['id']), // Quiz "porte sur" Product
            'flash'    => takeFlash(),
        ]);
    }

    public function add(): void
    {
        $product = Product::find((int) ($_POST['product_id'] ?? 0));
        $message = 'Product not found';
        $error   = true;

        if ($this->isPost() && $product) {
            $inBag = Cart::quantity((int) $product['id']);
            if ($inBag >= $product['stock']) {
                $message = 'Only ' . $product['stock'] . ' left in stock';
            } else {
                Cart::set((int) $product['id'], $inBag + 1);
                $message = $product['name'] . ' added to your bag';
                $error   = false;
            }
        }

        // Called with fetch() by main.js: answer in JSON, the page is NOT reloaded
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'fetch') {
            header('Content-Type: application/json');
            echo json_encode([
                'ok'      => !$error,
                'message' => $message,
                'count'   => Cart::count(),
                'inBag'   => $product ? Cart::quantity((int) $product['id']) : 0,
            ]);
            exit;
        }

        // Without JavaScript: classic redirect with a one-time message
        flash($message, $error);

        // back to the product page when the button was clicked there, otherwise back to the shop
        if (($_POST['from'] ?? '') === 'product' && $product) {
            $this->redirect('product', ['id' => (int) $product['id']]);
        }
        $this->redirect('shop', array_filter(['category' => $_POST['category'] ?? '', 'q' => $_POST['q'] ?? '']));
    }
}
