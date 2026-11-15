<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

class CategoriesController extends Controller
{
    protected string $page   = 'categories';
    protected string $title  = "Categories | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    public function index(): void
    {
        $categories = array_reverse(Category::all()); // newest first
        foreach ($categories as &$c) {
            $c['product_count'] = count(Product::forCategory($c['id']));
        }
        unset($c);

        $this->render('categories', ['categories' => $categories]);
    }

    public function create(): void
    {
        if ($this->isPost()) {
            $name = trim($_POST['name'] ?? '');
            if ($name !== '') {
                Category::insert([
                    'name'        => $name,
                    'description' => trim($_POST['description'] ?? ''),
                ]);
            }
        }
        $this->redirect('categories');
    }

    public function delete(): void
    {
        if ($this->isPost()) {
            Category::deleteWithProducts((int) ($_POST['category_id'] ?? 0));
        }
        $this->redirect('categories');
    }
}
