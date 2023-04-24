<?php
breadcrumb_add('dashboard.settings.debug', __('Site'));
sp_enqueue_script('dropzone-js', 2, ['dashboard-core-js']);
?>

<?php block('form-content'); ?>
<h4 class="py-0 px-3 font-weight-normal border-bottom border-dark h5">
  <span class="d-inline bg-white px-2 py-3 text-primary"><?= __('Feeds & Home'); ?></span>
</h4>
<div class="form-group">
  <label class="form-label" for="use_search_as_default"><?= __('Use search page as homepage'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="use_search_as_default" value="0">
    <input type="checkbox" name="use_search_as_default" value="1" class="custom-switch-input" <?= checked(1, get_option('use_search_as_default')); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Use search page as homepage.'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('If enabled, the homepage will show a search page rather than the content. You can still acccess the contents at: ' . e_attr(url_for('site.archive'))); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="trends_region"><?= __('Trending searches region'); ?></label>
  <select name="trends_region" id="trends_region" class="form-control">
        <?php foreach ($t['regions'] as $key => $name) : ?>
        <option value="<?= e_attr($key); ?>" <?= selected(sp_post('trends_region', get_option('trends_region')), $key); ?> ><?= e($name); ?></option>
        <?php endforeach; ?>
  </select>
  <span class="form-text text-muted"><?= __('Choose the region of the trending searches. You may need to clear the cache from <code>Settings > Debugging</code> to see the changes.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="default_thumb_url"><?= __('Default Featured Image URL'); ?></label>
  <input type="text" class="form-control" name="default_thumb_url" id="default_thumb_url" value="<?= sp_post('default_thumb_url', get_option('default_thumb_url', '/site/assets/img/broken.gif')); ?>" maxlength="200" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?= __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="thumb-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?= __('Drop image here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>
</div>
<div class="form-group">
  <label class="form-label" for="latest_posts_count"><?= __('Latest posts count'); ?></label>
  <input type="number" class="form-control" name="latest_posts_count" id="latest_posts_count" value="<?= sp_post('latest_posts_count', get_option('latest_posts_count')); ?>" maxlength="10" min="1" required>
  <span class="form-text text-muted"><?= __('Number of latest posts to show on homepage'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="max_slider_items"><?= __('Slider posts count'); ?></label>
  <input type="number" class="form-control" name="max_slider_items" id="max_slider_items" value="<?= sp_post('max_slider_items', get_option('max_slider_items')); ?>" maxlength="10" min="1" required>
  <span class="form-text text-muted"><?= __('Number of latest posts to show on slider'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="popular_posts_count"><?= __('Popular posts count'); ?></label>
  <input type="number" class="form-control" name="popular_posts_count" id="popular_posts_count" value="<?= sp_post('popular_posts_count', get_option('popular_posts_count')); ?>" maxlength="10" min="1" required>
  <span class="form-text text-muted"><?= __('Number of popular posts to show on the sidebar'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="popular_posts_interval"><?= __('Popular Posts Sorting'); ?></label>
  <select name="popular_posts_interval" id="popular_posts_interval" class="form-control">
        <?php foreach ($t['popular_posts_filters'] as $key) : ?>
        <option value="<?= e_attr($key); ?>" <?= selected(sp_post('popular_posts_interval', get_option('popular_posts_interval', 'all-time')), $key); ?> ><?= e(ucfirst(str_replace('-', ' ', $key))); ?></option>
        <?php endforeach; ?>
  </select>
  <span class="form-text text-muted"><?= __('Filter for popular posts.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="related_posts_count"><?= __('Related posts count'); ?></label>
  <input type="number" class="form-control" name="related_posts_count" id="related_posts_count" value="<?= sp_post('related_posts_count', get_option('related_posts_count', 3)); ?>" maxlength="10" min="0" required>
  <span class="form-text text-muted"><?= __('Number of related posts to show on the article page, use 0 to disable.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="category_posts_count"><?= __('Number of posts per category page'); ?></label>
  <input type="number" class="form-control" name="category_posts_count" id="category_posts_count" value="<?= sp_post('category_posts_count', get_option('category_posts_count')); ?>" maxlength="10" min="1" required>
  <span class="form-text text-muted"><?= __('Number of posts to show per page on the category view'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="iframe_allowed_domains"><?= __('Allowed domains in iFrames'); ?></label>
  <textarea class="form-control" rows="6" name="iframe_allowed_domains" id="iframe_allowed_domains"><?= sp_post('iframe_allowed_domains', get_option('iframe_allowed_domains', "youtube.com\nyoutu.be\nplayer.twitch.tv\nplayer.vimeo.com\ndailymotion.com")); ?></textarea>
  <span class="form-text text-muted"><?= __('Provide which domains are allowed when importing iframes from feed content. One at a line. Do not include <strong>www.</strong>. Set this blank to ignore importing iFrames'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="feed_redirection"><?= __('Enable Feed Redirection'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="feed_redirection" value="0">
    <input type="checkbox" name="feed_redirection" value="1" class="custom-switch-input" <?= checked(1, get_option('feed_redirection')); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Enable Feed Redirection'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('Choose if clicking on the feed items will redirect to their original source page or not.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="auto_delete_posts_after"><?= __('Automatically delete old posts after'); ?></label>
  <div class="input-group">
    <input type="number" class="form-control" name="auto_delete_posts_after" id="auto_delete_posts_after" value="<?= sp_post('auto_delete_posts_after', get_option('auto_delete_posts_after')); ?>" maxlength="10" min="0" required>  <div class="input-group-append">
      <span class="input-group-text"><?= __('days'); ?></span>
    </div></div>


    <span class="form-text text-muted"><?= __('Set after how many days the imported posts will be deleted automatically. Set this to <strong>0</strong> to disable the feature. Cron must be set-up in order this to work'); ?></span>
  </div>
<h4 class="py-0 px-3 font-weight-normal border-bottom border-dark h5">
  <span class="d-inline bg-white px-2 py-3 text-primary"><?= __('Search'); ?></span>
</h4>
  <div class="form-group">
    <label class="form-label" for="search_items_count"><?= __('Search results per page'); ?></label>
    <input type="number" class="form-control" name="search_items_count" id="search_items_count" value="<?= sp_post('search_items_count', get_option('search_items_count')); ?>" max="40" min="1" required>
    <span class="form-text text-muted"><?= __('Number of web searches to show per-page. Google CSE limits it to 10-20.'); ?></span>
  </div>
  
<div class="form-group">
  <label class="form-label" for="enable_search_ads"><?= __('Show Search Ads'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="enable_search_ads" value="0">
    <input type="checkbox" name="enable_search_ads" value="1" class="custom-switch-input" <?= checked(1, get_option('enable_search_ads')); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Enable Search Ads'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('If your CSE has AdSense enabled, enabling this option will display the ads on search results.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="safesearch_status"><?= __('Default SafeSearch Status'); ?></label>
  <div class="form-group">
      <select class="form-control" id="safesearch_status" name="safesearch_status">
          <?php foreach (['off', 'moderate', 'active'] as $value) : ?>
            <option value="<?= $value; ?>" <?= selected($value, get_option('safesearch_status', 'off')); ?>><?= ucfirst($value); ?></option>
          <?php endforeach; ?>
      </select>
  </div>
  <span class="form-text text-muted"><?= __('Default safesearch status, user can override via the preferences.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="search_links_newwindow"><?= __('Open results in new window/tab'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="search_links_newwindow" value="0">
    <input type="checkbox" name="search_links_newwindow" value="1" class="custom-switch-input" <?= checked(1, get_option('search_links_newwindow', 1)); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Open results in new window/tab'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('Choose if results link would open in new window/tab, user can override this in preferences'); ?></span>
</div>

<?php endblock(); ?>
<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
    $("#thumb-uploader").dropzone({
      url: "<?= url_for('dashboard.gallery.create_post'); ?>",
      maxFileSize: <?= format_bytes(get_max_upload_size()); ?>,
      acceptedFiles: 'image/*',
      params: {
        csrf_token: "<?= $t['csrf_token']; ?>",
      },
      success: function (dropzone, response) {
        if (response.content_url) {
          $('#default_thumb_url').val(response.content_relative_url).focus();
        }
      },
    });
  });
</script>
<?php endblock(); ?>
<?php
// Extends the base skeleton
    extend(
        'admin::layouts/settings_skeleton.php',
        [
        'title' => __('Site Settings'),
        'body_class' => 'settings site-settings',
        'page_heading' => __('Site Settings'),
        'page_subheading' => __("Manage over-all site"),
        ]
    );
