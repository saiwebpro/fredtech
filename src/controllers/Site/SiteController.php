<?php

namespace spark\controllers\Site;

use MirazMac\YouFetch\YouFetch;
use Valitron\Validator;
use spark\controllers\Controller;
use spark\controllers\Dashboard\DashboardFeedsController;
use spark\drivers\Auth\Auth;
use spark\drivers\Http\Http;
use spark\drivers\Http\InstantAnswer;
use spark\drivers\Http\RssImporter;
use spark\drivers\I18n\Locale;
use spark\drivers\Mail\Mailer;
use spark\drivers\Nav\Pagination;
use spark\models\CategoryModel;
use spark\models\ContentModel;
use spark\models\EngineModel;
use spark\models\FeedModel;
use spark\models\PostModel;

/**
* SiteController
*
* @package spark
*/
class SiteController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Good ol' homepage
     *
     * @return
     */
    public function home()
    {
        $engineModel = new EngineModel;
        $engines = $engineModel->readMany(['engine_id', 'engine_name']);

        if ((int) get_option('use_search_as_default', 0)) {
            $data = [
                'default_engine'         => (int) get_option('default_engine'),
                'engines'                => $engines,
            ];
            
            return view('search-index.php', $data);
        }

        $app = app();
        $postModel = new PostModel;
        $feedModel = new FeedModel;

        $data = [];


        $feedsTable = $feedModel->getTable();
        $postsTable = $postModel->getTable();

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        $filters['sort'] = 'post-publish-date';

        $itemsPerPage = (int) get_option('latest_posts_count', 10);

        // Total item count
        $totalCount = $postModel->countRows(null, $filters);


        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // Fields to query
        $fields[] = "{$postsTable}.post_id, {$postsTable}.post_title, {$postsTable}.post_excerpt, {$postsTable}.post_featured_image, {$postsTable}.post_type, {$postsTable}.post_source, {$postsTable}.post_pubdate, {$postsTable}.created_at, {$postsTable}.post_hits";
        $fields[] = "{$feedsTable}.feed_name, {$feedsTable}.feed_id, {$feedsTable}.feed_logo_url";

        // Query to fetch the users and their respective role names
        $sql = $postModel->select($fields)
        ->leftJoin(
            $feedsTable,
            "{$postsTable}.post_feed_id",
            '=',
            "{$feedsTable}.feed_id"
        )->limit($itemsPerPage, $offset);

        // Apply Filters
        $sql = $postModel->applyModelFilters($sql, $filters);
        $stmt = $sql->execute();
        // List entries
        $latestPosts = $stmt->fetchAll();

        $maxSliderItems = get_option('max_slider_items', 10);
        $sliderPosts = array_slice($latestPosts, 0, $maxSliderItems);



        $data['is_home'] = true;
        $data['default_engine']         = (int) get_option('default_engine');
        $data['engines']                = $engines;
        $data['home_active']            = 'active';
        $data['title_append_site_name'] = false;
        $data['latest_posts']           = $latestPosts;
        $data['slider_posts']           = $sliderPosts;
        $data['pagination_html']        = $paginationHtml;

        return view('home.php', $data);
    }

    /**
     * Archive page
     *
     * @return
     */
    public function archive()
    {
        $app = app();
        $postModel = new PostModel;
        $feedModel = new FeedModel;
        $engineModel = new EngineModel;

        $data = [
            'title' => __('archive', _T),
            'meta.description' => __('archive-description', _T),
            'body_class' => 'archive',
            'searching_site' => false,
            'by_feed' => false,
        ];

        $query = sp_strip_tags(trim($app->request->get('s')), true);
        $feedID  = (int) $app->request->get('feed', 0);


        $feedsTable = $feedModel->getTable();
        $postsTable = $postModel->getTable();

        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        $filters['sort'] = 'post-publish-date';

        if (mb_strlen($query) > 3) {
            $filters['where'][] = ["{$postsTable}.post_title", 'LIKE', "%$query%"];
            $filters['where'][] = ["{$postsTable}.post_content", 'LIKE', "%$query%", 'OR'];

            $data['site_search_query'] = $query;
            $data['searching_site'] = true;

            $data['page_heading'] = sprintf(__('search-results-for', _T), $query);
            $data['title'] = sprintf(__('search-results-for-title', _T), $query);
            $data['meta.description'] = sprintf(__('search-results-for-desc', _T), $query);
            $data['body_class'] .= ' search-results';
        }

        if ($feedID) {
            $feed = $feedModel->read($feedID, ['feed_id', 'feed_name']);
            if ($feed) {
                $filters['where'][] = ["{$postsTable}.post_feed_id", '=', $feed['feed_id']];
                $data['by_feed'] = true;
                $data['feed'] = $feed;
                $data['title'] = $feed['feed_name'];
                $data['page_heading'] = $feed['feed_name'];
            }
        }

        $itemsPerPage = (int) get_option('latest_posts_count', 10);

        // Total item count
        $totalCount = $postModel->countRows(null, $filters);

        $pageQuery = [];

        if ($data['searching_site']) {
            $pageQuery['s'] = $query;
        }

        if ($data['by_feed']) {
            $pageQuery['feed'] = $feedID;
        }

        $queryStr = http_build_query($pageQuery);

        if (!empty($queryStr)) {
            $queryStr = "&{$queryStr}";
        }

        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@{$queryStr}");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();

        // Fields to query
        $fields[] = "{$postsTable}.post_id, {$postsTable}.post_title, {$postsTable}.post_excerpt, {$postsTable}.post_featured_image, {$postsTable}.post_type, {$postsTable}.post_source, {$postsTable}.post_pubdate, {$postsTable}.created_at, {$postsTable}.post_hits";
        $fields[] = "{$feedsTable}.feed_name, {$feedsTable}.feed_id, {$feedsTable}.feed_logo_url";

        // Query to fetch the users and their respective role names
        $sql = $postModel->select($fields)
        ->leftJoin(
            $feedsTable,
            "{$postsTable}.post_feed_id",
            '=',
            "{$feedsTable}.feed_id"
        )->limit($itemsPerPage, $offset);

        // Apply Filters
        $sql = $postModel->applyModelFilters($sql, $filters);
        $stmt = $sql->execute();
        // List entries
        $latestPosts = $stmt->fetchAll();


        $maxSliderItems = get_option('max_slider_items', 10);
        $sliderPosts = array_slice($latestPosts, 0, $maxSliderItems);


        $engines = $engineModel->readMany(['engine_id', 'engine_name']);

        $data['default_engine']         = (int) get_option('default_engine');
        $data['engines']                = $engines;
        $data['archive_active']            = 'active';
        $data['slider_posts']           = $sliderPosts;
        $data['latest_posts']           = $latestPosts;
        $data['pagination_html']        = $paginationHtml;

        return view('archive.php', $data);
    }


    /**
     * Category page
     *
     * @return
     */
    public function category($slug)
    {
        $app = app();
        $categoryModel = new CategoryModel;
        $postModel = new PostModel;
        $feedModel = new FeedModel;

        $feedsTable = $feedModel->getTable();
        $postsTable = $postModel->getTable();


        $category = $categoryModel->select(['*'])
                                  ->where('category_slug', '=', $slug)
                                  ->execute()
                                  ->fetch();

        if (!$category) {
            return $app->notFound();
        }


        // Current page number
        $currentPage = (int) $app->request->get('page', 1);

        $filters['sort'] = 'post-publish-date';
        $filters['where'][] = ["{$postsTable}.post_category_id", '=', $category['category_id']];

        // Total item count
        $totalCount = $postModel->countRows(null, $filters);

        $itemsPerPage = (int) get_option('category_posts_count', 20);

        // Pagination instance
        $pagination = new Pagination($totalCount, $currentPage, $itemsPerPage);
        $pagination->setUrl("?page=@id@");

        // Generated HTML
        $paginationHtml = $pagination->renderHtml();

        // Offset value based on current page
        $offset = $pagination->offset();


        // Fields to query
        $fields[] = "{$postsTable}.post_id, {$postsTable}.post_title, {$postsTable}.post_excerpt, {$postsTable}.post_featured_image, {$postsTable}.post_type, {$postsTable}.post_source, {$postsTable}.post_pubdate, {$postsTable}.created_at, {$postsTable}.post_hits";
        $fields[] = "{$feedsTable}.feed_name, {$feedsTable}.feed_id, {$feedsTable}.feed_logo_url";

        // Query to fetch the users and their respective role names
        $sql = $postModel->select($fields)
        ->leftJoin(
            $feedsTable,
            "{$postsTable}.post_feed_id",
            '=',
            "{$feedsTable}.feed_id"
        )->limit($itemsPerPage, $offset);

        // Apply Filters
        $sql = $postModel->applyModelFilters($sql, $filters);
        $stmt = $sql->execute();
        // List entries
        $latestPosts = $stmt->fetchAll();


        $title = __(
            "category-label-{$category['category_slug']}",
            _T,
            ['defaultValue' => $category['category_name']]
        );


        $data = [
                'title'                             => $title,
                'meta.description'                       => sprintf(
                    __('category-meta-desc', _T),
                    $category['category_name']
                ),
                "{$category['category_id']}_active" => 'active',
                'category'                          => $category,
                'latest_posts'                      => $latestPosts,
                'pagination_html'                   => $paginationHtml,
        ];

        return view('category.php', $data);
    }

    /**
     * Allows to read an article
     *
     * @param  string $slug
     * @param  integer $id
     * @return
     */
    public function readArticle($slug, $id)
    {
        $app = app();

        $categoryModel = new CategoryModel;
        $postModel = new PostModel;
        $feedModel = new FeedModel;

        $feedsTable = $feedModel->getTable();
        $postsTable = $postModel->getTable();
        $categoriesTable = $categoryModel->getTable();

        // Fields to query
        $fields[] = "{$postsTable}.post_id, {$postsTable}.post_title, {$postsTable}.post_content, {$postsTable}.post_excerpt, {$postsTable}.post_featured_image, {$postsTable}.post_author, {$postsTable}.post_type, {$postsTable}.post_source, {$postsTable}.post_pubdate, {$postsTable}.created_at, {$postsTable}.post_category_id, {$postsTable}.post_hits";
        $fields[] = "{$feedsTable}.feed_name, {$feedsTable}.feed_id, {$feedsTable}.feed_logo_url";
        $fields[] = "{$categoriesTable}.category_name, {$categoriesTable}.category_id, {$categoriesTable}.category_slug";

        // Query to fetch the users and their respective role names
        $post = $postModel->select($fields)
        ->leftJoin(
            $feedsTable,
            "{$postsTable}.post_feed_id",
            '=',
            "{$feedsTable}.feed_id"
        )
        ->leftJoin(
            $categoriesTable,
            "{$postsTable}.post_category_id",
            '=',
            "{$categoriesTable}.category_id"
        )
        ->where("{$postsTable}.post_id", '=', $id)
        ->execute()
        ->fetch();


        if (!$post) {
            return $app->notFound();
        }

        // Update the post hit count
        $postModel->db()->query("UPDATE {$postsTable}
         SET post_hits = post_hits + 1
         WHERE post_id = {$id}");


        // Since we update the view count after fetching the post,
        // let's update that manually so it affects the view instantly as well
        $post['post_hits']++;


        // Add the breadcrumb
        breadcrumb_add(
            'category',
            __(
                "category-label-{$post['category_slug']}",
                _T,
                ['defaultValue' => $post['category_name']]
            ),
            url_for('site.category', ['slug' => $post['category_slug']])
        );
        
        breadcrumb_add('post', $post['post_title']);


        $relatedPostsCount = (int) get_option('related_posts_count', 3);
        $relatedPosts = [];

        if ($relatedPostsCount) {
            $filters = [];
            $filters['sort'] = 'post-publish-date';

            $fields = [];
            // Fields to query
            $fields[] = "{$postsTable}.post_id, {$postsTable}.post_title, {$postsTable}.post_excerpt, {$postsTable}.post_featured_image, {$postsTable}.post_type, {$postsTable}.post_hits, {$postsTable}.post_source, {$postsTable}.post_pubdate, {$postsTable}.created_at";
            $fields[] = "{$feedsTable}.feed_name, {$feedsTable}.feed_id, {$feedsTable}.feed_logo_url";

            $sql = $postModel->select($fields)
            ->where('post_category_id', '=', $post['post_category_id'])
            ->where('post_id', '!=', $post['post_id'])
            ->leftJoin(
                $feedsTable,
                "{$postsTable}.post_feed_id",
                '=',
                "{$feedsTable}.feed_id"
            )->limit($relatedPostsCount, 0);

            // Apply Filters
            $sql = $postModel->applyModelFilters($sql, $filters);
            $stmt = $sql->execute();

            // List entries
            $relatedPosts = $stmt->fetchAll();
        }


        $data = [
            'title'            => $post['post_title'],
            'meta.description' => $post['post_excerpt'],
            'meta.image'       => feat_img_url($post['post_featured_image']),
            'post'             => $post,
            "{$post['post_category_id']}_active" => 'active',
            'related_posts'    => $relatedPosts,
        ];
        return view('single.php', $data);
    }

    /**
     * Endpoint for thumbnails
     *
     * @return
     */
    public function thumbnail()
    {
        load_functions(['thumb.php']);
    }

    /**
     * 404 Not Found
     *
     * @return
     */
    public function notFound()
    {
        $app = app();

        $app->response->setStatus(404);

        $data = [
            'title'      => __('404 Page Not Found', _T),
        ];
        return view('404.php', $data);
    }

    /**
     * Access a page
     *
     * @param mixed  $slugOrID
     * @return
     */
    public function page($slugOrID)
    {
        $app = app();
        $contentModel = new ContentModel;

        $filters = [];
        $filters['where'][] = ['content_slug', '=', $slugOrID, 'OR'];
        $filters['where'][] = ['content_type', '!=', ContentModel::TYPE_PAGE];

        $page = $contentModel->db()->select()
        ->from($contentModel->getTable())
        ->where('content_slug', '=', $slugOrID)
        ->orWhere('content_id', '=', $slugOrID)
        ->where('content_type', '=', 'page')
        ->limit(1, 0)
        ->execute()
        ->fetch();

        if (!$page) {
            return $app->notFound();
        }

        $customTemplate = has_custom_template($page['content_slug']);

        $template = 'page.php';

        if ($customTemplate) {
            $template = $customTemplate;
        }


        $method = strtolower($app->request->getMethod());

        // Add the breadcrumb
        breadcrumb_add('page', $page['content_title']);


        /**
        * @event Runs when a page is visited via any http method.
        *
        * @var array $page The page information
        * @var mixed $customTemplate Path to the page's custom template, or boolean
        */
        do_action("page.render_before", $page, $customTemplate);

        /**
        * @event Runs when a page is visited via any http method. Dynamic portion of the hook name `$page['content_slug']`
        *       refers to the page's slug.
        *
        * @var array $page The page information
        * @var mixed $customTemplate Path to the page's custom template, or boolean
        */
        do_action("page.{$page['content_slug']}", $page, $customTemplate);

        // Prepare description
        $description = limit_string(sp_strip_tags($page['content_body'], true), 300, '');

        $data = [
            'title' => $page['content_title'],
            'meta.description' => $description,
            'page' => $page,
        ];

        return $app->render($template, $data);
    }


    /**
     * Handles contact form
     *
     * @return
     */
    public function handleContactForm()
    {
        $app = app();
        $req = $app->request;

        $data = [
            'name'    => $req->post('name'),
            'email'   => $req->post('email'),
            'subject' => sp_strip_tags($req->post('subject'), true),
            'message' => sp_strip_tags($req->post('message')),
        ];

        $v = new Validator($data);


        $v->labels([
            'email'   => __('E-Mail', _T),
            'subject'   => __('Subject', _T),
            'name'    => __('Name', _T),
            'message' => __('Message', _T),
        ])->rule('required', ['email', 'name', 'message'])
          ->rule('email', 'email')
          ->rule('lengthBetween', 'subject', 10, 200)
          ->rule('lengthBetween', 'message', 200, 5000);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('pages-danger', $errors);
            sp_store_post($data);
            return follow_referer_uri();
        }

        // Verify the captcha
        if (!sp_verify_recaptcha()) {
            flash('pages-danger', __('Invalid captcha. Please fill the captcha properly', _T));
            sp_store_post($data);
            return follow_referer_uri();
        }

        $data['user_ip']    = $req->getIp();
        $data['user_agent'] = $req->getUserAgent();

        if (empty($data['subject'])) {
            $data['subject'] = __('Contact form E-Mail from ') . get_option('site_name');
        }

        $body = $app->view->fetch('admin::emails/contact.php', $data);

        $mailer = (new Mailer)->getPhpMailer(
            $data['email'],
            get_option('site_email'),
            $data['subject'],
            $body
        );

        if ($mailer->send()) {
            flash(
                'pages-success',
                __('We have successfully received your message. We will get back to you as soon as possible.', _T)
            );
        } else {
            sp_store_post($data);
            flash('pages-danger', __('Unknown error at mailer. Please check your server configuration.', _T));
        }

        return follow_referer_uri();
    }

    /**
     * Changes the site locale
     *
     * @param  string $locale
     * @return
     */
    public function changeLocale($locale)
    {
        set_cookie(Locale::COOKIE_NAME, $locale, '+2 Year');
        return follow_referer_uri();
    }

    /**
     * Runs cron based tasks
     *
     * @return
     */
    public function runTasks()
    {
        $app = app();
        $req = $app->request;
        $token = $req->get('token');

        // Must match the token
        if ($token !== get_option('spark_cron_job_token')) {
            return sp_not_permitted();
        }

        @ignore_user_abort(1);
        @set_time_limit(0);

        $feedModel = new FeedModel;
        $postModel = new PostModel;
        $rssImporter = new RssImporter;

        $feeds = $feedModel->select()->where('feed_auto_update', '=', 1)
         ->orderBy('feed_priority', 'ASC')
         ->execute()
         ->fetchAll();

        foreach ($feeds as $feed) {
            // Unified feed refresh at one method for easier debugging
            DashboardFeedsController::importFeedPosts($feed, true);
        }

        // Delete old posts
        $days = (int) get_option('auto_delete_posts_after');

        if ($days > 0) {
            $olderThan = strtotime("-{$days} days");
            $postModel->purgeOldPosts($olderThan, [PostModel::TYPE_IMPORTED]);
        }

        /**
         * @hook Runs when the cron tasks URL is visited
         */
        do_action('cron_tasks');

        // Ran the tasks
        logger()->info('Cron ran at: ' . date('d-m-Y h:i:s A'));
    }

    /**
     * Handles log out process
     *
     * @return
     */
    public function logOut()
    {
        $auth = new Auth;
        $auth->logOut();

        return follow_redirect_to_uri(url_for('dashboard.account.signin'));
    }

    /**
     * Search page
     *
     * @return
     */
    public function search()
    {
        $app = app();
        $q = trim($app->request->get('q'));

        $engineModel = new EngineModel;

        $engines = $engineModel->readMany(['engine_id', 'engine_name'], 0, 100, ['sort' => 'oldest']);

        if (empty($q)) {
            if (has_template('search-index.php')) {
                $data = [
                    'default_engine'         => (int) get_option('default_engine'),
                    'engines'                => $engines,
                ];
                return view('search-index.php', $data);
            }
            
            return redirect_to('site.home');
        }

        $engineID = (int) $app->request->get('engine');

        // If there's no engine fall back to default
        if (!$engineID) {
            $engineID = get_option('default_engine');
        }

        $engine = $engineModel->read($engineID, ['engine_is_image', 'engine_cse_id', 'engine_id', 'engine_show_thumb']);

        // No such engine
        if (!$engine) {
            return $app->notFound();
        }

        /**
         * @hook Fires after the search has a valid engine and query
         *
         * @param string $q The query
         * @param array $engine The engine
         */
        do_action('search.discovered', $q, $engine);

        $cseID = js_string($engine['engine_cse_id']);

        $locale = get_theme_active_locale();

        $script = <<<SCRIPT

        (function() {
            var cx = '{$cseID}';
            var gcse = document.createElement('script');
            gcse.type = 'text/javascript';
            // Disable RocketLoader
            gcse.dataset.cfasync = false;
            gcse.async = false;
            gcse.src = 'https://cse.google.com/cse.js?hl={$locale}&cx=' + cx;
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(gcse, s);
        })();
SCRIPT;


        $isDefaultEngine = (int) get_option('default_engine') === (int) $engineID;

        $instantAnswer = [];

        if ($isDefaultEngine) {
            $instantAnswer = (new InstantAnswer)->getAnswer($q);
        }

        $safeSearch = get_option('safesearch_status', 'off');
        $newWindow = (int) get_option('search_links_newwindow', 1);

        $target = '_self';
        
        if ($newWindow) {
            $target = '_blank';
        }



        $isImage = (int) $engine['engine_is_image'];

        // Attributes
        $attrs = [
            'enableHistory' => true,
            'safeSearch' => $safeSearch,
            'linkTarget' => $target,
            'noResultsString' => sprintf(__('no-search-results', _T), $q),
        ];

        $attrText = '';

        // Attribues specific to image search
        if ($isImage) {
            $attrs['disableWebSearch'] = true;
            $attrs['enableImageSearch'] = true;
            $attrs['defaultToImageSearch'] = true;
            // Enabled only for mobile devices
            $attrs['mobileLayout'] = 'enabled';
            $attrs['imageSearchResultSetSize'] = (int) get_option('search_items_count', 10);
        } else {
            // Forced for better styling
            $attrs['mobileLayout'] = 'forced';
            $attrs['webSearchResultSetSize'] = (int) get_option('search_items_count', 10);
        }

        foreach ($attrs as $key => $value) {
            $attrText .= " data-{$key}=\"{$value}\"";
        }

        $cseElement = '<div class="gcse-searchresults-only" ' . $attrText . '></div>';


        $data = [
            'title'          => $q,
            'body_class'     => 'search',
            'cse_script'     => $script,
            'answer'         => $instantAnswer,
            'q'              => $q,
            'items_per_page' => (int) get_option('search_items_count', 10),
            'engines'        => $engines,
            'engine'         => $engine,
            'is_image' => $isImage,
            'cse_element' => $cseElement,
        ];
        return view('search.php', $data);
    }

    /**
     * Suggests queries as the user types from Google auto-complete
     *
     * @return
     */
    public function suggestQueries()
    {
        $app = app();
        $q = $app->request->get('q');
        if (empty($q)) {
            return json([]);
        }

        $url = "https://suggestqueries.google.com/complete/search?client=firefox&q={$q}";
        $http = Http::getSession();
        try {
            $response = $http->get($url);
        } catch (\Exception $e) {
            // ssh!
            return json([$q]);
        }

        if (!$response->success) {
            return json([$q]);
        }

        $json = json_decode($response->body, true);

        if (empty($json[1])) {
            return json([$q]);
        }

        $data = [];
        unset($json[1][0]);

        foreach ($json[1] as $value) {
            $data[] = $value;
        }

        return json($data);
    }

    /**
     * Handles feed item redirection
     *
     * @param  integer $id
     * @return
     */
    public function feedRedirect($id)
    {
        $app = app();
        $postModel = new PostModel;
        $table = $postModel->getTable();
        $id = (int) $id;

        $post = $postModel->read($id, ['post_type', 'post_source']);

        if (empty($post['post_source'])) {
            return redirect_to('site.home');
        }

        // Update the post hit count
        $postModel->db()->query("UPDATE {$table}
         SET post_hits = post_hits + 1
         WHERE post_id = {$id}");

        // redirect to the source
        return redirect($post['post_source']);
    }
}
