<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;

/** Front office: product listing + "add to bag". Customers and visitors only (the admin has no shop window). */
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
            $categories[(int) $c['id']] = $c['name'];
        }

        $this->render('shop', [
            'products'   => Product::search($categoryId, $search),
            'categories' => $categories,
            'categoryId' => $categoryId,
            'search'     => $search,
            'flash'      => takeFlash(),
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
