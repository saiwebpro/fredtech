<?php breadcrumb_add('dashboard.settings.ads', __('Ads Settings')); ?>

<?php block('form-content'); ?>

<div class="form-group">
  <label class="form-label" for="ad_unit_1"><?= __('Feed Right Sidebar Top Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_1" id="ad_unit_1"><?= sp_post('ad_unit_1', get_option('ad_unit_1')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays at the very top of the right sidebar, in the feed pages.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="ad_unit_4"><?= __('Feed Right Sidebar Bottom Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_4" id="ad_unit_4"><?= sp_post('ad_unit_4', get_option('ad_unit_4')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays at the very bottom of the right sidebar, in the feed pages.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="ad_unit_2"><?= __('Feed Left Sidebar Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_2" id="ad_unit_2"><?= sp_post('ad_unit_2', get_option('ad_unit_2')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays at the very top of the left sidebar, in the feed pages.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="ad_unit_3"><?= __('News Loop Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_3" id="ad_unit_3"><?= sp_post('ad_unit_3', get_option('ad_unit_3')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays after every four news items in a loop, at each feed items page.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="ad_unit_5"><?= __('Search Results Sidebar Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_5" id="ad_unit_5"><?= sp_post('ad_unit_5', get_option('ad_unit_5')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays at the search results sidebar'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="ad_unit_6"><?= __('Search Results Before Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_6" id="ad_unit_6"><?= sp_post('ad_unit_6', get_option('ad_unit_6')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays before beginning the search results.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="ad_unit_7"><?= __('Search Results After Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_7" id="ad_unit_7"><?= sp_post('ad_unit_7', get_option('ad_unit_7')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays after the search results.'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="ad_unit_8"><?= __('Article Adcode'); ?></label>
  <textarea class="form-control" rows="4" name="ad_unit_8" id="ad_unit_8"><?= sp_post('ad_unit_8', get_option('ad_unit_8')); ?></textarea>
  <span class="form-text text-muted"><?= __('Displays at the article page.'); ?></span>
</div>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Advertisement Settings'),
    'body_class' => 'settings ads-settings',
    'page_heading' => __('Advertisement Settings'),
    'page_subheading' => __("Good old ad slots"),
    ]
);
