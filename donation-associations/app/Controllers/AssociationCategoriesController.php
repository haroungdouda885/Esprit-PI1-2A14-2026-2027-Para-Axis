<?php

namespace App\Controllers;

use App\Models\Association;
use App\Models\Category;

/**
 * Back office (my module): CRUD of the association categories (Health, Childhood…).
 * They are rows of the shared Category entity with type = 'association'.
 * In the integrated project this is done on the shared "Categories" page (tab "Associations").
 */
class AssociationCategoriesController extends Controller
{
    protected string $page   = 'association-categories';
    protected string $title  = "Association categories | Par'Axis Back office";
    protected string $layout = 'layout';

    public function __construct()
    {
        $this->requireAdmin();
    }

    /** READ: list; ?edit=ID opens the form with the category values */
    public function index(): void
    {
        $categories = array_reverse(Category::ofType('association'));
        foreach ($categories as &$c) {
            $c['item_count'] = count(Association::forCategory((int) $c['id']));
        }
        unset($c);

        $edit = isset($_GET['edit']) ? Category::findOfType((int) $_GET['edit'], 'association') : null;
        $this->render('association-categories', ['categories' => $categories, 'edit' => $edit, 'flash' => takeFlash()]);
    }

    /** CREATE (no category_id) or UPDATE (category_id) */
    public function save(): void
    {
        if ($this->isPost()) {
            $id   = (int) ($_POST['category_id'] ?? 0);
            $data = [
                'type'        => 'association',
                'name'        => trim($_POST['name'] ?? ''),
                'emoji'       => trim($_POST['emoji'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
            ];

            $error = null;
            if (mb_strlen($data['name']) < 2) {
                $error = 'The name must have at least 2 characters.';
            } elseif (mb_strlen($data['emoji']) > 4) {
                $error = 'The emoji must be 1 emoji (or empty).';
            } else {
                foreach (Category::ofType('association') as $other) { // unique name
                    if ((int) $other['id'] !== $id && mb_strtolower($other['name']) === mb_strtolower($data['name'])) {
                        $error = 'This category already exists.';
                    }
                }
            }
            if ($error) {
                flash($error, true);
                $this->redirect('association-categories', $id ? ['edit' => $id] : []);
            }

            if ($id && Category::findOfType($id, 'association')) {
                Category::update($id, $data);
                flash('Category updated');
            } else {
                Category::insert($data);
                flash('Category added');
            }
        }
        $this->redirect('association-categories');
    }

    /** DELETE: refused while associations use the category */
    public function delete(): void
    {
        $c = Category::findOfType((int) ($_POST['category_id'] ?? 0), 'association');
        if ($this->isPost() && $c) {
            if (Association::forCategory((int) $c['id'])) {
                flash('Associations use this category: give them another category first.', true);
            } else {
                Category::delete((int) $c['id']);
                flash('Category deleted');
            }
        }
        $this->redirect('association-categories');
    }
}
