<?php

use spark\drivers\Nav\Pagination;
use spark\models\CategoryModel;
use spark\models\ContentModel;
use spark\models\PostModel;

breadcrumb_add('home', __('home', _T), base_uri());

$app = app();
$pool = app()->cache;

$contentModel = new ContentModel;
$pages = $contentModel->select(['content_slug', 'content_title'])
                      ->where('content_type', '=', ContentModel::TYPE_PAGE)
                      ->execute()
                      ->fetchAll();

view_set('site_pages', $pages);

// Fetch the categories for sidebars
$categoryModel = new CategoryModel;
$postModel = new PostModel;

$categories = $categoryModel->readMany(
    ['category_id', 'category_name', 'category_slug', 'category_icon'],
    0,
    50,
    ['sort' => 'category-order']
);

foreach ($categories as $key => $cat) {
    $filters = [];
    $filters['where'][] = ['post_category_id', '=', $cat['category_id']];
    $count = $postModel->countRows(null, $filters);
    $categories[$key]['count'] = $count;
}

view_set('categories', $categories);

// Fetch the latest trends
$feed = new \SimplePie;
$geo = get_option('trends_region', 'US');
$feed->set_feed_url("https://trends.google.com/trends/trendingsearches/daily/rss?geo={$geo}");
$feed->enable_cache(true);
$feed->set_cache_location(SIMPLEPIE_CACHE_PATH);
$feed->init();

$trends = [];
foreach ($feed->get_items(0, 10) as $item) {
    $trends[] = $item->get_title();
}

view_set('trends', $trends);

view_set('__loop', 0);

$popularPostsCount = (int) get_option('popular_posts_count', 5);
$popularPostsFilter = get_option('popular_posts_interval', 'all-time');


$interval = $filter = '';
switch ($popularPostsFilter) {
    case 'daily':
        $interval = '1 DAY';
        break;
    case 'weekly':
        $interval = '1 WEEK';
        break;
    case 'monthly':
        $interval = '1 MONTH';
        break;
    case 'yearly':
        $interval = '1 YEAR';
        break;
    case 'all-time':
        break;
    default:
        break;
}

if ($interval) {
    $filter = "WHERE post_pubdate > UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL {$interval}))";
}

$sql = "SELECT post_id , post_type , post_title , post_featured_image , post_source , post_pubdate , post_hits , created_at FROM {$postModel->getTable()} {$filter} ORDER BY post_hits DESC LIMIT 0 , {$popularPostsCount}";

$sql = apply_filters("popular_posts_sql", $sql);

$popular = $postModel->db()->query($sql)->fetchAll();


view_set('popular_posts', $popular);

breadcrumb_add('home', __('home', _T), base_uri());

view_set('active_locale', get_theme_active_locale_item());


Pagination::setTranslations([
    'first' => __('first', _T),
    'last'  => __('last', _T),
    'next'  => __('next', _T),
    'prev'  => __('prev', _T),
    'numeric'  => [
      '1' => __('num_1', _T),
      '2' => __('num_2', _T),
      '3' => __('num_3', _T),
      '4' => __('num_4', _T),
      '5' => __('num_5', _T),
      '6' => __('num_6', _T),
      '7' => __('num_7', _T),
      '8' => __('num_8', _T),
      '9' => __('num_9', _T),
      '0' => __('num_0', _T),
    ]
]);
