<?php

namespace App\Controllers;

use App\Models\DonationItem;
use App\Models\Quiz;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Product;

class ProductsController extends Controller
{
    protected string $page   = 'products';
    protected string $title  = "Products | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $products = array_reverse(Product::all()); // newest first
        foreach ($products as &$p) {
            $p['category_name'] = Category::find((int) $p['category_id'])['name'] ?? '—'; // join category_id -> category
        }
        unset($p);

        $this->render('products', ['products' => $products, 'categories' => Category::ofType('product'), 'flash' => takeFlash()]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $categoryId = (int) ($_POST['category_id'] ?? 0);
            $name       = trim($_POST['name'] ?? '');
            $price      = (float) ($_POST['price'] ?? -1);
            $stock      = (int) ($_POST['stock'] ?? -1);

            if (Category::findOfType($categoryId, 'product') && $name !== '' && $price >= 0 && $stock >= 0) {
                Product::insert([
                    'category_id' => $categoryId,
                    'name'        => $name,
                    'brand'       => trim($_POST['brand'] ?? '') ?: "Par'Axis",
                    'price'       => round($price, 3),
                    'stock'       => $stock,
                    'emoji'       => trim($_POST['emoji'] ?? '') ?: '🧴',
                    'color'       => '#e8f1e9',
                    'description' => trim($_POST['description'] ?? ''),
                ]);
            }
        }
        $this->redirect('products');
    }

    /** Changes the stock of one product */
    public function stock(): void
    {
        if ($this->isPost()) {
            $stock = max(0, (int) ($_POST['stock'] ?? 0));
            Product::update((int) ($_POST['product_id'] ?? 0), ['stock' => $stock]);
        }
        $this->redirect('products');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            $id = (int) ($_POST['product_id'] ?? 0);
            if (DonationItem::usesProduct($id)) {
                // DonationItem "concerne" exactly 1 Product: the donation history must keep its products
                flash('This product is part of a donation: it cannot be deleted (set its stock to 0 instead).', true);
            } else {
                Ticket::detachProduct($id); // tickets and quizzes stay, without product (ON DELETE SET NULL)
                Quiz::detachProduct($id);
                Product::delete($id);
                flash('Product deleted');
            }
        }
        $this->redirect('products');
    }
}
