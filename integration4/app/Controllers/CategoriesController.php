<?php

namespace App\Controllers;

use App\Models\Association;
use App\Models\Category;
use App\Models\DonationItem;
use App\Models\Product;

/**
 * Back office: categories of the shop (type 'product', member 2)
 * AND categories of the associations (type 'association', member 3) on the same page.
 */
class CategoriesController extends Controller
{
    protected string $page   = 'categories';
    protected string $title  = "Categories | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin(); // every action of this controller is for admins
    }

    /** List with tabs All / Products / Associations; ?edit=ID opens the form with the category values */
    public function index(): void
    {
        $type = $_GET['type'] ?? '';
        if (!isset(Category::TYPES[$type])) {
            $type = '';
        }

        $categories = array_reverse($type === '' ? Category::all() : Category::ofType($type)); // newest first
        foreach ($categories as &$c) {
            $c['type']       = Category::typeOf($c);
            $c['item_count'] = Category::itemCount($c);
        }
        unset($c);

        $this->render('categories', [
            'categories' => $categories,
            'type'       => $type,
            'counts'     => ['' => count(Category::all()), 'product' => count(Category::ofType('product')), 'association' => count(Category::ofType('association'))],
            'edit'       => isset($_GET['edit']) ? Category::find((int) $_GET['edit']) : null,
            'flash'      => takeFlash(),
        ]);
    }

    /** CREATE (no category_id) or UPDATE (category_id) */
    public function create(): void
    {
        $back = ['type' => $_POST['type'] ?? ''];
        if ($this->isPost()) {
            $id   = (int) ($_POST['category_id'] ?? 0);
            $old  = $id ? Category::find($id) : null;
            $data = [
                'type'        => $_POST['type'] ?? '',
                'name'        => trim($_POST['name'] ?? ''),
                'emoji'       => trim($_POST['emoji'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
            ];

            $error = null;
            if (!isset(Category::TYPES[$data['type']])) {
                $error = 'Please choose the type: product or association.';
            } elseif (mb_strlen($data['name']) < 2) {
                $error = 'The name must have at least 2 characters.';
            } elseif (mb_strlen($data['emoji']) > 4) {
                $error = 'The emoji must be 1 emoji (or empty).';
            } elseif ($old && Category::typeOf($old) !== $data['type'] && Category::itemCount($old) > 0) {
                $error = 'This category is used: its type cannot be changed.';
            } else {
                foreach (Category::ofType($data['type']) as $other) { // unique name inside one type
                    if ((int) $other['id'] !== $id && mb_strtolower($other['name']) === mb_strtolower($data['name'])) {
                        $error = 'This category already exists.';
                    }
                }
            }
            if ($error) {
                flash($error, true);
                $this->redirect('categories', $id ? ['edit' => $id] : $back);
            }

            if ($old) {
                Category::update($id, $data);
                flash('Category updated');
            } else {
                Category::insert($data);
                flash('Category added');
            }
        }
        $this->redirect('categories', $back);
    }

    /**
     * DELETE. Product category: its products are deleted too (unless one of them is part of a donation).
     * Association category: refused while associations use it (move them to another category first).
     */
    public function delete(): void
    {
        $c = Category::find((int) ($_POST['category_id'] ?? 0));
        if ($this->isPost() && $c) {
            $id = (int) $c['id'];
            if (Category::typeOf($c) === 'association') {
                if (Association::forCategory($id)) {
                    flash('Associations use this category: give them another category first.', true);
                } else {
                    Category::delete($id);
                    flash('Category deleted');
                }
            } else {
                $locked = array_filter(Product::forCategory($id), fn($p) => DonationItem::usesProduct((int) $p['id']));
                if ($locked) {
                    flash('A product of this category is part of a donation: the category cannot be deleted.', true);
                } else {
                    Category::deleteWithProducts($id);
                    flash('Category and its products deleted');
                }
            }
        }
        $this->redirect('categories', ['type' => $_POST['type_filter'] ?? '']);
    }
}
