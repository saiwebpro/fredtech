<?php

namespace mirazmac\plugins\Rewriter;

use spark\controllers\Dashboard\DashboardController;
use spark\models\FeedModel;

/**
*
*/
class RewriterController extends DashboardController
{

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        /**
         * @hook Fires before RewriterController is initialized
         */
        do_action('plugins.rewriter_controller_init_before');


        // this is it
        if (!current_user_can('manage_feeds')) {
            sp_not_permitted();
        }


        breadcrumb_add('dashboard.feeds', __('Feeds'), url_for('dashboard.feeds'));
        breadcrumb_add('rewriter.feed', __('Article Rewriter'), url_for('rewriter.feed'));
        view_set('feeds__active', 'active');

        /**
         * @hook Fires after RewriterController is initialized
         */
        do_action('plugins.rewriter_controller_init_after');
    }

    public function manageFeed($id)
    {
        $feedModel = new FeedModel;

        $feed = $feedModel->read($id, ['feed_id', 'feed_name']);


        if (!$feed) {
            flash('feeds-danger', __('No such feed found.'));
            return redirect_to('dashboard.feeds');
        }


        $rewriter = new Rewriter;

        $dicts = $rewriter->getDictionaries();

        $enabledFeeds = $rewriter->getEnabledFeeds();

        $data = [
            'feed' => $feed,
            'enable_rewriting' => isset($enabledFeeds[$feed['feed_id']]) ? 1 : 0,
            'dicts' => $dicts,
        ];

        return view('article-rewriter::manage_feed.php', $data);
    }

    public function manageFeedPOST($id)
    {
        if (is_demo()) {
            flash('feeds-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to('dashboard.feeds');
        }

        $feedModel = new FeedModel;

        $feed = $feedModel->read($id, ['feed_id', 'feed_name']);


        if (!$feed) {
            flash('feeds-danger', __('No such feed found.'));
            return redirect_to('dashboard.feeds');
        }



        $app = app();
        $req = $app->request;

        $data = [
            'enable_rewriting' => (int) $req->post('enable_rewriting', 1),
            'dictionary' => $req->post('dictionary'),
        ];

        $rewriter = new Rewriter;

        $dicts = $rewriter->getDictionaries();

        if (!in_array($data['dictionary'], $dicts, true)) {
            flash('rewriter-danger', 'No such dictionary exists');
            return redirect_to_current_route();
        }

        $enabledFeeds = $rewriter->getEnabledFeeds();

        if ($data['enable_rewriting']) {
            $enabledFeeds[$feed['feed_id']] = $data['dictionary'];
        } else {
            unset($enabledFeeds[$feed['feed_id']]);
        }


        set_option('rewriter_enabled_feed', json_encode($enabledFeeds));

        flash('rewriter-success', 'Changes saved successfully.');
        return redirect_to_current_route();
    }
}
