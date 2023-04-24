<?php

use spark\helpers\UrlSlug;
use spark\models\PostModel;

function category_icon_url($url)
{
    if (empty($url)) {
        return site_uri('assets/img/circle.png');
    }
    
    return ensure_abs_url($url);
}

function feat_img_url($url)
{
    if (empty($url)) {
        $url = get_option('default_thumb_url', 'site/assets/img/broken.gif');
    }
    
    return ensure_abs_url($url);
}

function feed_logo_url($url)
{
    if (empty($url)) {
        return sp_logo_uri();
    }
    
    return ensure_abs_url($url);
}

/**
 * Generates URL to a post
 *
 * @param  array $post
 * @return string
 */
function post_url(array $post)
{
    $redirection = (int) get_option('feed_redirection', 1);
    if ($post['post_type'] == PostModel::TYPE_IMPORTED && $redirection && !empty($post['post_source'])) {
        return url_for('site.redirect', ['id' => $post['post_id']]);
    }

    static $urlSlug = null;

    if (!$urlSlug) {
        $urlSlug = new UrlSlug(['limit' => '100']);
    }

    $slug = $urlSlug->generate($post['post_title']);

    return url_for('site.read', ['id' => $post['post_id'], 'slug' => $slug]);
}

/**
 * Generates link attributes for a post
 *
 * @param  array $post
 * @return string
 */
function post_attrs(array $post)
{
    $redirection = (int) get_option('feed_redirection', 1);

    if ($post['post_type'] == PostModel::TYPE_IMPORTED && $redirection) {
        return 'rel="nofollow noreferrer noopener" target="_blank"';
    }

    return 'rel="bookmark"';
}

/**
 * Detects and returns YouTube video ID from an URL
 *
 * @param  string $url
 * @return mixed
 */
function detect_youtube_ID($url)
{
    $regex = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
    $video_id = false;
    if (preg_match($regex, $url, $match)) {
        $video_id = $match[1];
    }
    return $video_id;
}
