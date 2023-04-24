<?php

namespace mirazmac\plugins\Rewriter;

/**
* Article Rewriter
*/
class Rewriter
{
    protected static $enabledFeeds;

    protected static $dicts;

    protected static $thesaurus;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    public function onEnable()
    {
    }

    public function onDisable()
    {
    }

    public function addButtonsToFeed(array $item)
    {
        $data = $this->getEnabledFeeds();

        $text = '<a href="' . url_for('rewriter.feed', ['id' => $item['feed_id']]) . '" class="d-inline-block mr-3" style="text-decoration:underline">Rewriting: ';

        if (isset($data[$item['feed_id']])) {
            $text .= '<span class="badge badge-success">ON</span>';
        } else {
            $text .= '<span class="badge badge-danger">OFF</span>';
        }

        $text .= '</a>';

        echo $text;
    }

    public function handleOptions()
    {
    }

    public function getEnabledFeeds()
    {
        if (static::$enabledFeeds) {
            return static::$enabledFeeds;
        }

        $data = json_decode(get_option('rewriter_enabled_feed', '{}'), true);

        if (!$data) {
            $data = [];
        }

        static::$enabledFeeds = $data;

        return $data;
    }

    public function getDictionaries()
    {
        if (static::$dicts) {
            return static::$dicts;
        }

        $files = glob(REWRITER_PLUGIN_PATH . '/dict/*.php');
        $dicts = [];

        foreach ($files as $file) {
            $dicts[] = basename(basename($file, '.php'));
        }

        static::$dicts = $dicts;

        return $dicts;
    }

    public function loadDictonary($name)
    {
        if (isset(static::$thesaurus[$name])) {
            return static::$thesaurus[$name];
        }

        $file = REWRITER_PLUGIN_PATH . "/dict/{$name}.php";

        $dict = [];

        if (is_file($file)) {
            $dict = require $file;
        }

        static::$thesaurus[$name] = $dict;

        return $dict;
    }

    public function rewriteArticle(array $post, array $feed)
    {
        $enabledFeeds = $this->getEnabledFeeds();

        // Not enabled for this feed
        if (!isset($enabledFeeds[$feed['feed_id']])) {
            return $post;
        }

        $dictonary = $enabledFeeds[$feed['feed_id']];

        $thesaurus = $this->loadDictonary($dictonary);
        
        $post['post_content'] = str_replace(array_keys($thesaurus), array_values($thesaurus), $post['post_content']);

        return $post;
    }
}
