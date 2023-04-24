<?php

namespace spark\controllers\Dashboard;

use Valitron\Validator;
use spark\controllers\Controller;
use spark\drivers\Http\RssImporter;
use spark\drivers\Nav\Pagination;
use spark\models\CategoryModel;
use spark\models\FeedModel;
use spark\models\PostModel;

/**
* DashboardFeedsController
*
* @package spark
*/
class DashboardFeedsController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * @hook Fires before DashboardFeedsController is initialized
         */
        do_action('dashboard.feeds_controller_init_before');

        // this is it
        if (!current_user_can('manage_feeds')) {
            sp_not_permitted();
        }


        breadcrumb_add('dashboard.feeds', __('Feeds'), url_for('dashboard.feeds'));
        view_set('feeds__active', 'active');

        view_set('feed_keyword_mode_list', [
            FeedModel::KEYWORD_FILTER_DISABLED => 'Disabled',
            FeedModel::KEYWORD_FILTER_FOLLOW => 'Fetch post if contains these keywords',
            FeedModel::KEYWORD_FILTER_IGNORE => 'Ignore post if contains these keywords',
        ]);

        /**
         * @hook Fires after DashboardFeedsController is initialized
         */
        do_action('dashboard.feeds_controller_init_after');
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
        $feedModel = new FeedModel;

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        // Items per page
        $itemsPerPage = (int) config('dashboard.items_per_page');

        // Total item count
        $totalCount = $feedModel->countRows();

        // Sort value
        $sort = $app->request->get('sort', null);

        // Ensure the target sort type is allowed
        if (!$feedModel->isSortAllowed($sort)) {
            $sort = 'newest';
        }

        $sortRules = $feedModel->getAllowedSorting();

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

        $categoryModel = new CategoryModel;

        $feedsTable = $feedModel->getTable();
        $categoriesTable = $categoryModel->getTable();

        // Fields to query
        $fields[] = "{$feedsTable}.*";
        $fields[] = "{$categoriesTable}.category_name";

        // Query to fetch the users and their respective role names
        $sql = $feedModel->select($fields)
        ->leftJoin(
            $categoriesTable,
            "{$feedsTable}.feed_category_id",
            '=',
            "{$categoriesTable}.category_id"
        );

        // Limit
        $sql = $sql->limit($itemsPerPage, $offset);
        // Apply Filters
        $sql = $feedModel->applyModelFilters($sql, $filters);

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
        return view('admin::feeds/index.php', $data);
    }

    public function feedActions()
    {
        if (is_demo()) {
            flash('feeds-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.feeds');
        }

        $app = app();

        $action = (string) $app->request->post('action');

        $feedModel = new FeedModel;

        $db = $feedModel->db();
        $table = $feedModel->getTable();

        if ($action === 'reset') {
            $db->update(['feed_last_refreshed' => 0])
               ->table($table)
               ->execute();
            flash('feeds-success', __('Feeds last update time reset successfully.'));
        }

        return redirect_to('dashboard.feeds');
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
        sp_enqueue_script('jquery-form-toggle', 2);
        sp_enqueue_script('dropzone-js', 2);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.feeds.create', __('Create Feed'));

        $categoryModel = new CategoryModel;
        $categories = $categoryModel->readMany(['category_id', 'category_name'], 0, 200);

        $data = [
            'categories' => $categories,
        ];
        return view('admin::feeds/create.php', $data);
    }

    /**
     * Create new entry action
     *
     * @return
     */
    public function createPOST()
    {
        if (is_demo()) {
            flash('feeds-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.feeds');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'feed_name'                    => trim($req->post('feed_name')),
            'feed_url'                     => trim($req->post('feed_url')),
            'feed_logo_url'                => trim($req->post('feed_logo_url')),
            'feed_fulltext_selector'       => trim($req->post('feed_fulltext_selector')),
            'feed_category_id'             => (int) $req->post('feed_category_id'),
            'feed_priority'                => (int) $req->post('feed_priority'),
            'feed_max_items'               => (int) $req->post('feed_max_items'),

            // @since 1.0.5
            'feed_required_content_length' => (int) $req->post('feed_required_content_length'),
            'feed_content_maxlength'       => (int) $req->post('feed_content_maxlength'),
            'feed_keyword_mode'            => (int) $req->post('feed_keyword_mode'),
            'feed_required_keywords'       => sp_strip_tags($req->post('feed_required_keywords')),

            'feed_ignore_without_image'    => (int) $req->post('feed_ignore_without_image', 1),
            'feed_fetch_fulltext'          => sp_int_bool($req->post('feed_fetch_fulltext')),
            'feed_auto_update'             => sp_int_bool($req->post('feed_auto_update')),
        ];

        $required = [
                "feed_name",
                "feed_url",
                "feed_priority",
                "feed_max_items",
                "feed_category_id",
                "feed_fetch_fulltext"
            ];

        if ($data['feed_keyword_mode']) {
            $required[] = 'feed_required_keywords';
        }

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'feed_name', 200)
          ->rule('max', 'feed_priority', 10)
          ->rule('lengthMax', 'feed_fulltext_selector', 100)
          ->rule('required', $required)
          ->rule('url', ['feed_url']);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('feeds-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $categoryModel = new CategoryModel;
        if (!$categoryModel->exists($data['feed_category_id'])) {
            flash('feeds-danger', __('No such category exists.'));
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $forceFeed = (int) $req->post('force_feed', 0);

       
        $data['feed_url'] = static::fixFeedBurnerURL($data['feed_url']);


        $feed = new \SimplePie;
        // Supress certificate errors
        $feed->set_curl_options([
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);
        $feed->set_feed_url($data['feed_url']);
        $feed->set_useragent('Mozilla/5.0 (Windows NT 6.3; WOW64) ' .
            'AppleWebKit/537.36 (KHTML, like Gecko) ' .
            'Chrome/60.0.2214.115 Safari/537.36');
        $feed->enable_cache(false);

        if ($forceFeed) {
            $feed->force_feed(true);
        }

        $feed->init();

        if ($feed->error()) {
            flash('feeds-danger', $feed->error());
            sp_store_post($data);
            return redirect_to_current_route();
        }

        if (empty($data['feed_logo_url']) && $feed->get_image_url()) {
            $data['feed_logo_url'] = $feed->get_image_url();
        }

        $feedModel = new FeedModel;
        $data['feed_name'] = sp_strip_tags($data['feed_name']);
        $data['feed_url'] = sp_strip_tags($data['feed_url']);
        $data['feed_logo_url'] = sp_strip_tags($data['feed_logo_url']);
        $data['feed_fulltext_selector'] = sp_strip_tags($data['feed_fulltext_selector']);
        $feedModel->create($data);

        flash('feeds-success', __('Feed was created successfully'));
        return redirect_to('dashboard.feeds');
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
        sp_enqueue_script('jquery-form-toggle', 2);
        sp_enqueue_script('dropzone-js', 2);

        // Set breadcrumb trails
        breadcrumb_add('dashboard.feeds.update', __('Update Feed'));

        $feedModel = new FeedModel;

        $feed = $feedModel->read($id);

        if (!$feed) {
            flash('feeds-danger', __('No such feed found.'));
            return redirect_to('dashboard.feeds');
        }


        $categoryModel = new CategoryModel;
        $categories = $categoryModel->readMany(['category_id', 'category_name'], 0, 200);

        $data = [
            'feed'       => $feed,
            'categories' => $categories,
        ];

        return view('admin::feeds/update.php', $data);
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
            flash('feeds-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.feeds');
        }

        $feedModel = new FeedModel;

        $feed = $feedModel->read($id);

        if (!$feed) {
            flash('feeds-danger', __('No such feed found.'));
            return redirect_to('dashboard.feeds');
        }

        $app = app();
        $req = $app->request;
        $data = [
            'feed_name'                 => trim($req->post('feed_name')),
            'feed_url'                  => trim($req->post('feed_url')),
            'feed_logo_url'             => trim($req->post('feed_logo_url')),
            'feed_fulltext_selector'    => trim($req->post('feed_fulltext_selector')),
            'feed_category_id'          => (int) $req->post('feed_category_id'),
            'feed_priority'             => (int) $req->post('feed_priority'),
            'feed_max_items'            => (int) $req->post('feed_max_items'),

            // @since 1.0.5
            'feed_required_content_length' => (int) $req->post('feed_required_content_length'),
            'feed_content_maxlength'       => (int) $req->post('feed_content_maxlength'),
            'feed_keyword_mode'            => (int) $req->post('feed_keyword_mode'),
            'feed_required_keywords'       => sp_strip_tags($req->post('feed_required_keywords')),

            'feed_ignore_without_image' => (int) $req->post('feed_ignore_without_image', 1),
            'feed_fetch_fulltext'       => sp_int_bool($req->post('feed_fetch_fulltext')),
            'feed_auto_update'          => sp_int_bool($req->post('feed_auto_update')),
        ];

        $required = [
                "feed_name",
                "feed_url",
                "feed_priority",
                "feed_max_items",
                "feed_category_id",
                "feed_fetch_fulltext"
            ];


        if ($data['feed_keyword_mode']) {
            $required[] = 'feed_required_keywords';
        }

        // Basic validation is basic * ding *
        // Go checkout CinemaSins on YouTube
        $v = (new Validator($data))
          ->rule('lengthMax', 'feed_name', 200)
          ->rule('max', 'feed_priority', 10)
          ->rule('lengthMax', 'feed_fulltext_selector', 100)
          ->rule('required', $required)
          ->rule('url', ['feed_url']);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('feeds-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $categoryModel = new CategoryModel;
        if (!$categoryModel->exists($data['feed_category_id'])) {
            flash('feeds-danger', __('No such category exists.'));
            sp_store_post($data);
            return redirect_to_current_route();
        }


        $data['feed_url'] = static::fixFeedBurnerURL($data['feed_url']);
        $data['feed_name'] = sp_strip_tags($data['feed_name']);
        $data['feed_url'] = sp_strip_tags($data['feed_url']);
        $data['feed_logo_url'] = sp_strip_tags($data['feed_logo_url']);
        $data['feed_fulltext_selector'] = sp_strip_tags($data['feed_fulltext_selector']);
        $feedModel->update($id, $data);

        flash('feeds-success', __('Feed was updated successfully'));
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
        breadcrumb_add('dashboard.feeds.update', __('Delete Feed'));

        $feedModel = new FeedModel;

        $feed = $feedModel->read($id);

        if (!$feed) {
            flash('feeds-danger', __('No such feed found.'));
            return redirect_to('dashboard.feeds');
        }

        $data = [
            'feed' => $feed,
        ];
        return view('admin::feeds/delete.php', $data);
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
            flash('feeds-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.feeds');
        }

        $feedModel = new FeedModel;

        $feed = $feedModel->read($id);

        if (!$feed) {
            flash('feeds-danger', __('No such feed found.'));

            if (is_ajax()) {
                return;
            }

            return redirect_to('dashboard.feeds');
        }

        $feedModel->delete($id);

        flash('feeds-success', __('Feed was deleted successfully'));

        if (is_ajax()) {
            return;
        }

        return redirect_to('dashboard.feeds');
    }

    public function refreshFeed($id)
    {
        if (is_demo()) {
            flash('feeds-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.feeds');
        }

        $feedModel = new FeedModel;

        $feed = $feedModel->read($id);

        if (!$feed) {
            flash('feeds-danger', __('No such feed found.'));
            return redirect_to('dashboard.feeds');
        }

        $i = static::importFeedPosts($feed);

        flash('feeds-info', sprintf(__('Feed refreshed. %s new items were added.'), $i));
        return redirect_to('dashboard.feeds');
    }

    public static function importFeedPosts(array $feed, $fromCron = false)
    {
        // We may need a lot of time to run
        @ignore_user_abort(1);
        @set_time_limit(0);
        $rssImporter = new RssImporter;
        $posts = $rssImporter->fetchPosts($feed);
        $postModel = new PostModel;
        $feedModel = new FeedModel;
        
        $i = 0;
        $lastRefreshTime = 0;
        foreach ($posts as $post) {
            $post = apply_filters('import_post_insert_before', $post, $feed, $fromCron);

            // Try to ignore posts with the same source
            $filters = [];
            $filters['where'][] = ['post_source', '=', $post['post_source']];
            $postExists = (int) $postModel->countRows(null, $filters);

            if ($postExists) {
                continue;
            }


            // Try to ignore posts based on the exact same title
            $filters = [];
            $filters['where'][] = ['post_title', '=', $post['post_title']];
            $postExists = (int) $postModel->countRows(null, $filters);

            if ($postExists) {
                continue;
            }

            $post['post_category_id'] = $feed['feed_category_id'];
            $post['post_feed_id'] = $feed['feed_id'];

            try {
                $postID = $postModel->create($post);
            } catch (\Exception $e) {
                logger()->critical($e);
                continue;
            }

            if ($postID) {
                $post = apply_filters('import_post_insert_after', $post, $feed, $fromCron, $postID);
                $i++;
                if ($post['post_pubdate'] > $lastRefreshTime) {
                    $lastRefreshTime = $post['post_pubdate'];
                }
            }
        }

        if ($i) {
            if (!$lastRefreshTime) {
                $lastRefreshTime = time();
            }

            $feedModel->update($feed['feed_id'], ['feed_last_refreshed' => $lastRefreshTime]);
        }


        return $i;
    }

    public static function fixFeedBurnerURL($str)
    {
         $url = parse_url($str);

        // Detected a FeedBurner URL
        if (strpos($url['host'], 'feedburner.com') !== false) {
            // No query is present, add the format=xml
            if (empty($url['query'])) {
                $url['query'] = 'format=xml';
            } else {
                parse_str($url['query'], $query);
                // Add the format parameter
                $query['format'] =  'xml';
                // Build again
                $url['query'] = http_build_query($query);
            }

            $str = build_url($url);
        }

        return $str;
    }
}
