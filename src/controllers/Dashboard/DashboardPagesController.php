<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Filter\Xss;
use spark\drivers\Nav\Pagination;
use spark\helpers\UrlSlug;
use spark\models\ContentModel;

/**
* DashboardPagesController
*
* @package spark
*/
class DashboardPagesController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * @event Fires before DashboardPagesController is initialized
         */
        do_action('dashboard.pages_controller_init_before');

        if (!current_user_can('manage_pages')) {
            sp_not_permitted();
        }

        breadcrumb_add('dashboard.pages', __('Pages'), url_for('dashboard.pages'));

        view_set('pages__active', 'active');

        /**
         * @event Fires after DashboardPagesController is initialized
         */
        do_action('dashboard.pages_controller_init_after');
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
        $contentModel = new ContentModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) $app->config('dashboard.items_per_page');

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$contentModel->isSortAllowed($sort)) {
            $sort = 'newest';
        }

        $sortRules = $contentModel->getAllowedSorting();

        // Filters
        $filters = [
            'sort' => e_attr($sort)
        ];

        $filters['where'][] = ['content_type', '=', 'page'];


        // Total item count
        $totalCount = $contentModel->countRows(null, $filters);


        $queryStr = request_build_query(['page', 'sort']);
        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@&sort={$sort}{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // List entries
        $entries = $contentModel->readMany(
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
        return view('admin::pages/index.php', $data);
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
        sp_enqueue_script('trumbowyg-editor', 2);
        sp_enqueue_script('trumbowyg-editor-upload-plugin', 2);
        sp_enqueue_style('trumbowyg-editor-style');

        // Set breadcrumb trails
        breadcrumb_add('dashboard.pages.create', __('Create Page'));

        $data = [];
        return view('admin::pages/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('pages-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.pages');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'content_title' => trim($req->post('content_title')),
            'content_body' => $req->post('content_body'),
            'content_slug' => trim($req->post('content_slug')),
        ];

        $v = new Validator($data);

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v->labels([
            'content_title' => __('Page Title'),
            'content_body' => __('Page Content'),
            'content_slug' => __('Page Slug'),
        ])
        ->rule('required', ['content_title', 'content_body']);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('pages-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $slug = new UrlSlug;

        if (empty($data['content_slug'])) {
            $data['content_slug'] = $slug->generate($data['content_title']);
        } else {
            $data['content_slug'] = $slug->generate($data['content_slug']);
        }

        $contentModel = new ContentModel;

        $uniqueSlug = ensure_unique_value($contentModel, 'content_slug', $data['content_slug']);


        $xss = new Xss;
        $data['content_body'] = $xss->filter($data['content_body']);
        $data['content_slug'] = $uniqueSlug;
        $data['content_type'] = 'page';
        $data['content_author'] = current_user_ID();
        $data['content_path'] = '';

        $contentModel->create($data);

        flash('pages-success', 'Page was created successfully');
        return redirect_to('dashboard.pages');
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
        sp_enqueue_script('trumbowyg-editor', 2);
        sp_enqueue_script('trumbowyg-editor-upload-plugin', 2);
        sp_enqueue_style('trumbowyg-editor-style');

        // Set breadcrumb trails
        breadcrumb_add('dashboard.pages.update', __('Update Page'));

        $contentModel = new ContentModel;

        $filters = [];
        $filters['where'][] = ['content_type', '=', 'page'];
        $page = $contentModel->read($id, ['content_id', 'content_title', 'content_body', 'content_slug'], $filters);

        if (!$page) {
            flash('pages-danger', __('No such page found.'));
            return redirect_to('dashboard.pages');
        }

        $customTemplate = has_custom_template($page['content_slug']);

        $data = [
            'page' => $page,
            'custom_template' => $customTemplate
        ];
        return view('admin::pages/update.php', $data);
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
            flash('pages-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.pages');
        }

        $contentModel = new ContentModel;

        $filters = [];
        $filters['where'][] = ['content_type', '=', 'page'];
        $page = $contentModel->read($id, ['content_id', 'content_slug'], $filters);

        if (!$page) {
            flash('pages-danger', __('No such page found.'));
            return redirect_to('dashboard.pages');
        }


        $app = app();
        $req = $app->request;
        $data = [
            'content_title' => trim($req->post('content_title')),
            'content_body' => $req->post('content_body'),
            'content_slug' => trim($req->post('content_slug')),
        ];

        $v = new Validator($data);

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v->labels([
            'content_title' => __('Page Title'),
            'content_body' => __('Page Content'),
            'content_slug' => __('Page Slug'),
        ])
        ->rule('required', ['content_title', 'content_body']);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('pages-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $slug = new UrlSlug;

        if (empty($data['content_slug'])) {
            $data['content_slug'] = $slug->generate($data['content_title']);
        } else {
            $data['content_slug'] = $slug->generate($data['content_slug']);
        }


        $xss = new Xss;
        $data['content_body'] = $xss->filter($data['content_body']);


        $contentModel = new ContentModel;

        $uniqueSlug = ensure_unique_value($contentModel, 'content_slug', $data['content_slug'], $page['content_slug']);

        $data['content_slug'] = $uniqueSlug;

        $contentModel->update($id, $data);

        flash('pages-success', 'Page was updated successfully');
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
        breadcrumb_add('dashboard.pages.delete', __('Delete Page'));

        $contentModel = new ContentModel;

        $filters = [];
        $filters['where'][] = ['content_type', '=', 'page'];
        $page = $contentModel->read($id, ['content_title'], $filters);

        if (!$page) {
            flash('pages-danger', __('No such page found.'));
            return redirect_to('dashboard.pages');
        }

        $data = [
            'page' => $page,
        ];
        return view('admin::pages/delete.php', $data);
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
            flash('pages-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.pages');
        }

        $contentModel = new ContentModel;

        $filters = [];
        $filters['where'][] = ['content_type', '=', 'page'];
        $page = $contentModel->read($id, ['content_id'], $filters);

        if (!$page) {
            flash('pages-danger', __('No such page found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.pages');
        }

        $contentModel->delete($id);


        flash('pages-success', __('Page was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.pages');
    }
}
