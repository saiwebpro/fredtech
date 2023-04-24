<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Nav\Pagination;
use spark\helpers\UrlSlug;
use spark\models\CategoryModel;

/**
* DashboardCategoriesController
*
* @package spark
*/
class DashboardCategoriesController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * @hook Fires before DashboardCategoriesController is initialized
         */
        do_action('dashboard.categories_controller_init_before');

        // this is it
        if (!current_user_can('manage_categories')) {
            sp_not_permitted();
        }


        breadcrumb_add('dashboard.categories', __('Categories'), url_for('dashboard.categories'));
        view_set('categories__active', 'active');

        /**
         * @hook Fires after DashboardCategoriesController is initialized
         */
        do_action('dashboard.categories_controller_init_after');
    }

    /**
     * List entries
     *
     * @return
     */
    public function index()
    {
        $app = app();

        // Model instance
        $categoryModel = new CategoryModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) config('dashboard.items_per_page');

        // Total item count
        $totalCount = $categoryModel->countRows();

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$categoryModel->isSortAllowed($sort)) {
            $sort = 'category-order';
        }

        $sortRules = $categoryModel->getAllowedSorting();

        // Filters
        $filters = [
            'sort' => e_attr($sort)
        ];

        $queryStr = request_build_query(['page', 'sort']);
        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@&sort={$sort}{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // List entries
        $entries = $categoryModel->readMany(
            ['*'],
            $offset,
            $itemsPerPage,
            $filters
        );

        // Template data
        $data = [
            'list_entries'    => $entries,
            'total_items'     => $totalCount,
            'offset'          => $offset === 0 ? 1 : $offset,
            'current_page'    => $currentPage,
            'items_per_page'  => $itemsPerPage,
            'current_items'   => $itemsPerPage * $currentPage,
            'sort_type'       => $sort,
            'pagination_html' => $paginationHtml,
            'sorting_rules'   => $sortRules,
            'query_str'       => $queryStr
        ];
        return view('admin::categories/index.php', $data);
    }

    /**
     * Create new entry
     *
     * @return
     */
    public function create()
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);
        sp_enqueue_script('dropzone-js', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.categories.create', __('Create Category'));

        $categoryModel = new CategoryModel;
        $lastID = $categoryModel
        ->select(['category_order'])
        ->orderBy('category_order', 'DESC')->limit(1, 0)->execute()->fetch();

        $order = 100;

        if (!empty($lastID['category_order'])) {
            $order = (int)$lastID['category_order'] + 1;
        }

        $data = [
            'order' => $order,
        ];
        return view('admin::categories/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('categories-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.categories');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'category_name'         => trim($req->post('category_name')),
            'category_slug'         => trim($req->post('category_slug')),
            'category_icon'         => trim($req->post('category_icon')),
            'category_order'        => (int) $req->post('category_order'),
            'category_feat_at_home' => sp_int_bool($req->post('category_feat_at_home')),
        ];


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'category_name', 200)
          ->rule('lengthMax', 'category_slug', 200)
          ->rule('required', [
                "category_name",
                //"category_slug"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('categories-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $urlSlug = new UrlSlug;
        if (empty($data['category_slug'])) {
            $data['category_slug'] = $data['category_name'];
        }

        $data['category_slug'] = $urlSlug->generate($data['category_slug']);

        $categoryModel = new CategoryModel;
        $data['category_name'] = sp_strip_tags($data['category_name']);
        $data['category_icon'] = sp_strip_tags($data['category_icon']);
        $data['category_slug'] = ensure_unique_value($categoryModel, 'category_slug', $data['category_slug']);
        $categoryModel->create($data);

        flash('categories-success', __('Category was created successfully'));
        return redirect_to('dashboard.categories');
    }

    /**
     * Update entry page
     *
     * @param mixed $id
     * @return
     */
    public function update($id)
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);
        
        sp_enqueue_script('dropzone-js', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.categories.update', __('Update Category'));

        $categoryModel = new CategoryModel;

        $category = $categoryModel->read($id);

        if (!$category) {
            flash('categories-danger', __('No such category found.'));
            return redirect_to('dashboard.categories');
        }

        $data = [
            'category' => $category,
        ];

        return view('admin::categories/update.php', $data);
    }

    /**
     * Update entry action
     *
     * @param mixed $id
     * @return
     */
    public function updatePOST($id)
    {
        if (is_demo()) {
            flash('categories-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.categories');
        }

        $categoryModel = new CategoryModel;

        $category = $categoryModel->read($id);

        if (!$category) {
            flash('categories-danger', __('No such category found.'));
            return redirect_to('dashboard.categories');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'category_name'         => trim($req->post('category_name')),
            'category_slug'         => trim($req->post('category_slug')),
            'category_icon'         => trim($req->post('category_icon')),
            'category_order'        => (int) $req->post('category_order'),
            'category_feat_at_home' => sp_int_bool($req->post('category_feat_at_home')),
        ];


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'category_name', 200)
          ->rule('lengthMax', 'category_slug', 200)
          ->rule('required', [
                "category_name",
                "category_slug"
            ]);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('categories-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }


        $urlSlug = new UrlSlug;
        $data['category_slug'] = $urlSlug->generate($data['category_slug']);

        $data['category_name'] = sp_strip_tags($data['category_name']);
        $data['category_icon'] = sp_strip_tags($data['category_icon']);
        $data['category_slug'] = ensure_unique_value($categoryModel, 'category_slug', $data['category_slug'], $category['category_slug']);
        $categoryModel->update($id, $data);

        flash('categories-success', __('Category was updated successfully'));
        return redirect_to_current_route();
    }

    /**
     * Delete entry page
     *
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {
        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.categories.update', __('Delete Category'));

        $categoryModel = new CategoryModel;

        $category = $categoryModel->read($id);

        if (!$category) {
            flash('categories-danger', __('No such category found.'));
            return redirect_to('dashboard.categories');
        }

        $data = [
            'category' => $category,
        ];
        return view('admin::categories/delete.php', $data);
    }

    /**
     * Delete entry action
     *
     * @param mixed $id
     * @return
     */
    public function deletePOST($id)
    {
        if (is_demo()) {
            if (is_ajax()) {
                return;
            }
            flash('categories-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.categories');
        }

        $categoryModel = new CategoryModel;

        $category = $categoryModel->read($id);

        if (!$category) {
            flash('categories-danger', __('No such category found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.categories');
        }

        $categoryModel->delete($id);

        flash('categories-success', __('Category was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.categories');
    }
}
