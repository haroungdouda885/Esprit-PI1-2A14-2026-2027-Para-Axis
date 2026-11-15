<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;

/**
 * Front office of the shop. Customers and visitors only (the admin has no shop window).
 * - Catalog (member 2)            : index (products + category filter + search), show (one product)
 * - Orders and payments (member 3): add (add to bag), demo (reload demo bag)
 */
class ShopController extends Controller
{
    protected string $page   = 'shop';
    protected string $title  = "Shop | Par'Axis";
    protected string $layout = 'front-layout';

    public function __construct()
    {
        $this->blockAdmin();
        Cart::initDemoOnce();
    }

    public function index(): void
    {
        $categoryId = (int) ($_GET['category'] ?? 0) ?: null;
        $search     = trim($_GET['q'] ?? '');

        $categories = [];
        foreach (Category::all() as $c) {
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
            'flash'    => takeFlash(),
        ]);
    }

    public function add(): void
    {
        $product = Product::find((int) ($_POST['product_id'] ?? 0));

        if ($this->isPost() && $product) {
            $inBag = Cart::quantity((int) $product['id']);
            if ($inBag >= $product['stock']) {
                flash('Only ' . $product['stock'] . ' left in stock', true);
            } else {
                Cart::set((int) $product['id'], $inBag + 1);
                flash($product['name'] . ' added to your bag');
            }
        }

        // back to the product page when the button was clicked there, otherwise back to the shop
        if (($_POST['from'] ?? '') === 'product' && $product) {
            $this->redirect('product', ['id' => (int) $product['id']]);
        }
        $this->redirect('shop', array_filter(['category' => $_POST['category'] ?? '', 'q' => $_POST['q'] ?? '']));
    }

    /** "Reload demo bag" button */
    public function demo(): void
    {
        if ($this->isPost()) {
            Cart::loadDemo();
            flash('Demo bag loaded');
        }
        $this->redirect('bag');
    }
}
