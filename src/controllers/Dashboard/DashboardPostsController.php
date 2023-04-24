<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Filter\Xss;
use spark\drivers\Nav\Pagination;
use spark\models\CategoryModel;
use spark\models\FeedModel;
use spark\models\PostModel;

/**
* DashboardPostsController
*
* @package spark
*/
class DashboardPostsController extends DashboardController
{
    protected $allowedTags = ['iframe', 'embed'];

    public function __construct()
    {
        parent::__construct();

        /**
         * @hook Fires before DashboardPostsController is initialized
         */
        do_action('dashboard.posts_controller_init_before');


        // this is it
        if (!current_user_can('manage_posts')) {
            sp_not_permitted();
        }


        breadcrumb_add('dashboard.posts', __('Posts'), url_for('dashboard.posts'));
        view_set('posts__active', 'active');

        /**
         * @hook Fires after DashboardPostsController is initialized
         */
        do_action('dashboard.posts_controller_init_after');
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
        $postModel = new PostModel;
        $categoryModel = new CategoryModel;
        $feedModel = new FeedModel;

        $feedsTable = $feedModel->getTable();
        $postsTable = $postModel->getTable();
        $categoriesTable = $categoryModel->getTable();


        // Sort value
        $sort = $app->request->get('sort', null);

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        $categoryID = (int) $app->request->get('category_id', 0);

        // Items per page
        $itemsPerPage = (int) config('dashboard.items_per_page');
        
        // Ensure the target sort type is allowed
        if (!$postModel->isSortAllowed($sort)) {
            $sort = 'newest';
        }


        // Filters
        $filters = [
            'sort' => e_attr($sort)
        ];

        // Only if it exists
        if ($categoryID) {
            $categoryQuery = $categoryModel->read($categoryID, ['category_name']);
            if ($categoryQuery) {
                $filters['where'][] = ["{$postsTable}.post_category_id", '=', $categoryID];
            } else {
                $categoryID = false;
            }
        }

        // Total item count
        $totalCount = $postModel->countRows(null, $filters);



        $sortRules = $postModel->getAllowedSorting();

        $queryStr = request_build_query(['page', 'sort']);
        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@&sort={$sort}{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // Fields to query
        $fields[] = "{$postsTable}.*";
        $fields[] = "{$categoriesTable}.category_name, {$categoriesTable}.category_id";
        $fields[] = "{$feedsTable}.feed_name, {$feedsTable}.feed_id";

        // Query to fetch the users and their respective role names
        $sql = $postModel->select($fields)
        ->leftJoin(
            $categoriesTable,
            "{$postsTable}.post_category_id",
            '=',
            "{$categoriesTable}.category_id"
        )
        ->leftJoin(
            $feedsTable,
            "{$postsTable}.post_feed_id",
            '=',
            "{$feedsTable}.feed_id"
        );

        // Limit
        $sql = $sql->limit($itemsPerPage, $offset);
        // Apply Filters
        $sql = $postModel->applyModelFilters($sql, $filters);

        $stmt = $sql->execute();

        // List entries
        $entries = $stmt->fetchAll();

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


        if (isset($categoryQuery['category_name'])) {
            $data['page_subheading'] = sprintf(__('Showing posts under category: <em>%s</em>'), e($categoryQuery['category_name'])) . '<a href="?"  class="close">
    <span aria-hidden="true">×</span></a>';
        }

        return view('admin::posts/index.php', $data);
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

        sp_enqueue_script('trumbowyg-editor', 2);
        sp_enqueue_script('trumbowyg-editor-upload-plugin', 2);
        sp_enqueue_style('trumbowyg-editor-style');


        // Set breadcrumb trails
        breadcrumb_add('dashboard.posts.create', __('Create Post'));


        $categoryModel = new CategoryModel;
        $categories = $categoryModel->readMany(['category_id', 'category_name'], 0, 200);

        $data = [
            'categories' => $categories,
        ];
        return view('admin::posts/create.php', $data);
    }

    /**
     * Handles delete old posts action
     *
     * @return
     */
    public function deleteOldPosts()
    {
        if (is_demo()) {
            flash('posts-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.posts');
        }

        if (!current_user_can('manage_posts')) {
            return sp_not_permitted();
        }

        $app = app();


        $days = (int) $app->request->post('num_of_days', 0);

        if ($days < 1) {
            return redirect_to('dashboard');
        }


        $timestamp = strtotime("-{$days} days");

        $postModel = new PostModel;

        $state = (int) $postModel->purgeOldPosts($timestamp, [PostModel::TYPE_IMPORTED]);

        flash('posts-success', sprintf(__('%d Posts were found and was deleted successfully.'), $state));

        return redirect_to('dashboard.posts');
    }

    /**
     * Handles post actions
     *
     * @return
     */
    public function postActions()
    {
        if (is_demo()) {
            flash('posts-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.posts');
        }

        $app = app();

        $action = $app->request->post('action');

        $ids = (array) $app->request->post('item_multi');

        $postModel = new PostModel;

        $type = PostModel::TYPE_IMPORTED;

        if ($action === 'flush') {
            $postModel->db()->query("DELETE FROM {$postModel->getTable()} WHERE post_type = '{$type}'");
            flash('posts-success', __('All posts were cleared successfully.'));
            return redirect_to('dashboard.posts');
        }

        if (empty($action) || empty($ids)) {
            return redirect_to('dashboard.posts');
        }

        switch ($action) {
            case 'delete':
                foreach ($ids as $key => $id) {
                    $postModel->delete($id);
                }
                flash('posts-success', __('Selected items were deleted successfully.'));
                break;
        }

        return redirect_to('dashboard.posts');
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('posts-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.posts');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'post_title'          => trim($req->post('post_title')),
            'post_content'        => trim($req->post('post_content')),
            'post_excerpt'        => trim($req->post('post_excerpt')),
            'post_featured_image' => trim($req->post('post_featured_image')),
            'post_category_id'    => (int) ($req->post('post_category_id')),
        ];


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'post_title', 800)
          ->rule('required', [
                "post_title",
                "post_content",
                "post_featured_image",
            ])->rule(
                function ($field, $value, $params, $fields) {
                    $categoryModel = new CategoryModel;
                    return $categoryModel->exists($value);
                },
                "post_category_id"
            );

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('posts-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }


        if (empty($data['post_excerpt'])) {
            $data['post_excerpt'] = limit_string($data['post_content'], 200);
        }

        $xss = new Xss;
        $xss->addAllowedTags($this->allowedTags);

        $postModel = new PostModel;
        $data['post_title'] = sp_strip_tags($data['post_title']);
        $data['post_content'] = $xss->filter($data['post_content']);
        $data['post_excerpt'] = sp_strip_tags($data['post_excerpt']);
        $data['post_featured_image'] = sp_strip_tags($data['post_featured_image']);
        $data['post_feed_id'] = 0;
        $data['post_pubdate'] = time();
        // to prevent null type error
        $data['post_source'] = 'n/a';
        $data['post_author'] = get_option('site_name');
        $data['post_type'] = PostModel::TYPE_ORIGINAL;
        $postModel->create($data);

        flash('posts-success', __('Post was created successfully'));
        return redirect_to('dashboard.posts');
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
        
        
        sp_enqueue_script('trumbowyg-editor', 2);
        sp_enqueue_script('trumbowyg-editor-upload-plugin', 2);
        sp_enqueue_style('trumbowyg-editor-style');

        // Set breadcrumb trails
        breadcrumb_add('dashboard.posts.update', __('Update Post'));

        $postModel = new PostModel;

        $post = $postModel->read($id);

        if (!$post) {
            flash('posts-danger', __('No such post found.'));
            return redirect_to('dashboard.posts');
        }


        $categoryModel = new CategoryModel;
        $categories = $categoryModel->readMany(['category_id', 'category_name'], 0, 200);

        $postTypes = [
            PostModel::TYPE_ORIGINAL => __('Original'),
            PostModel::TYPE_IMPORTED => __('Imported from RSS'),
        ];

        $data = [
            'post' => $post,
            'post_types' => $postTypes,
            'categories' => $categories,
        ];

        return view('admin::posts/update.php', $data);
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
            flash('posts-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.posts');
        }

        $postModel = new PostModel;

        $post = $postModel->read($id);

        if (!$post) {
            flash('posts-danger', __('No such post found.'));
            return redirect_to('dashboard.posts');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'post_title'          => trim($req->post('post_title')),
            'post_content'        => trim($req->post('post_content')),
            'post_excerpt'        => trim($req->post('post_excerpt')),
            'post_featured_image' => trim($req->post('post_featured_image')),
            'post_source'         => trim($req->post('post_source')),
            'post_type'           => trim($req->post('post_type')),
            'post_category_id'    => (int) $req->post('post_category_id'),
        ];

        // Handle post type
        if ($data['post_type'] !== PostModel::TYPE_IMPORTED) {
            $data['post_type'] = PostModel::TYPE_ORIGINAL;
        }


        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'post_title', 800)
          ->rule('required', [
                "post_title",
                "post_content",
                "post_featured_image",
            ])
          ->rule(function ($field, $value, $params, $fields) {
                    $categoryModel = new CategoryModel;
                    return $categoryModel->exists($value);
          },
              "post_category_id")->message(__('No such category exists'));

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('posts-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        if (empty($data['post_excerpt'])) {
            $data['post_excerpt'] = limit_string($data['post_content'], 200);
        }


        $xss = new Xss;
        $xss->addAllowedTags($this->allowedTags);

        $data['post_title'] = sp_strip_tags($data['post_title']);
        $data['post_content'] = $xss->filter($data['post_content']);
        $data['post_excerpt'] = sp_strip_tags($data['post_excerpt'], true);
        $data['post_featured_image'] = sp_strip_tags($data['post_featured_image']);
        $data['post_source'] = sp_strip_tags($data['post_source']);
        $postModel->update($id, $data);

        flash('posts-success', __('Post was updated successfully'));
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
        breadcrumb_add('dashboard.posts.update', __('Delete Post'));

        $postModel = new PostModel;

        $post = $postModel->read($id);

        if (!$post) {
            flash('posts-danger', __('No such post found.'));
            return redirect_to('dashboard.posts');
        }

        $data = [
            'post' => $post,
        ];
        return view('admin::posts/delete.php', $data);
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
            flash('posts-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.posts');
        }

        $postModel = new PostModel;

        $post = $postModel->read($id);

        if (!$post) {
            flash('posts-danger', __('No such post found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.posts');
        }

        $postModel->delete($id);

        flash('posts-success', __('Post was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.posts');
    }
}
