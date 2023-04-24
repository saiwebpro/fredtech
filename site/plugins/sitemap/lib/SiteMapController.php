<?php

namespace mirazmac\plugins\SiteMap;

use spark\controllers\Controller;
use spark\helpers\UrlSlug;
use spark\models\CategoryModel;
use spark\models\PostModel;

/**
* SiteMapController
*/
class SiteMapController extends Controller
{
    protected $ignoreImported = false;

    protected $showFullText = true;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->showFullText = (int) get_option('rss_show_fulltext', 1);
    }

    /**
     * Index page for sitemap
     *
     */
    public function sitemapIndex()
    {
        $app = app();
        $data = [];
        $postModel = new PostModel;

        $filters = [];

        if ($this->ignoreImported) {
            $filters['where'][] = ['post_type', '=', PostModel::TYPE_ORIGINAL];
        }
        
        $total = $postModel->countRows(null, $filters);
        // Even though Google's limit is 50,000 per page,
        // but its better to split up because of the crawling rate
        $sitemapItemsPerPage = (int) get_option('sitemap_links_per_page', 1000);

        $numberOfSiteMaps = ceil($total / $sitemapItemsPerPage);

        // So only one it is
        if ($numberOfSiteMaps < 1) {
            $numberOfSiteMaps = 1;
        }

        $data['total_sitemaps'] = $numberOfSiteMaps;

        $app->response->headers->set('content-type', 'application/xml');
        return view('sitemap::sitemap-index.php', $data);
    }

    /**
     * Sitemap
     *
     * @return
     */
    public function sitemap($page = 1)
    {
        $app = app();
        $data = [];

        $app->response->headers->set('content-type', 'application/xml');

        $postModel    = new PostModel;

        $itemsPerPage = (int) get_option('sitemap_links_per_page', 1000);
        $offset = ($page - 1) * $itemsPerPage;


        $filters = ['sort' => 'oldest'];

        if ($this->ignoreImported) {
            $filters['where'][] = ['post_type', '=', PostModel::TYPE_ORIGINAL];
        }

        $items = $postModel->readMany(
            ['post_id', 'post_title', 'updated_at'],
            $offset,
            $itemsPerPage,
            $filters
        );

        $data['slug'] = new UrlSlug;

        $data['entries'] = $items;

        return view('sitemap::sitemap-single.php', $data);
    }


    /**
     * Rss
     *
     * @return
     */
    public function rssIndex()
    {
        $app = app();
        $data = [];

        $page = (int) $app->request->get('page', 1);
        $catID = (int) $app->request->get('category', 0);

        $category = null;

        if ($catID) {
            $category = (new CategoryModel)->read($catID, ['category_name', 'category_slug']);
        }

        $app->response->headers->set('content-type', 'application/xml');

        $postModel    = new PostModel;

        $itemsPerPage = (int) get_option('rss_items_per_page', 50);
        $offset = ($page - 1) * $itemsPerPage;
        
        $fields = ['post_id', 'post_title', 'post_excerpt', 'created_at', 'updated_at', 'post_featured_image'];

        if ($this->showFullText) {
            $fields[] = 'post_content';
        }

        $filters = ['sort' => 'newest'];

        if ($category) {
            $filters['where'][] = ['post_category_id', '=', $catID];
        }

        $items = $postModel->readMany(
            $fields,
            $offset,
            $itemsPerPage,
            $filters
        );

        $data['last_build_date'] = time();

        if (isset($items[0]['updated_at'])) {
            $data['last_build_date'] = $items[0]['updated_at'];
        }

        $data['rss_title'] = get_option('site_name');
        $data['rss_desc'] = get_option('site_description');

        if ($category) {
            $data['rss_title'] = $category['category_name'] . ' - ' . get_option('site_name');
            $data['rss_desc'] = "Read latest posts from the category: {$category['category_name']}";
        }

        $data['slug'] = new UrlSlug;

        $data['entries'] = $items;
        $data['show_fulltext'] = $this->showFullText;

        return view('sitemap::rss.php', $data);
    }
}
