<?php block('html_head'); ?>
<style type="text/css" id="default-theme-styles">
.layout-img {
    max-width: 150px;
    height: auto;
}
</style>
<?php endblock(); ?>
<?php block('form-content'); ?>
<?php
$single_layout = get_option('single_page_layout', 'single');
?>

<div class="form-group">
    <label for="site_language" class="form-label"><?php echo __('site-language-label', _T); ?></label>
    <select name="site_language" id="site_language" class="form-control">
        <?php foreach (get_theme_locales() as $key => $locale) : ?>
            <option value="<?php echo e_attr($key); ?>" <?php echo selected($key, sp_post('site_language', get_option('site_language', 'en_US'))); ?>><?php echo e($locale['name']); ?></option>
        <?php endforeach; ?>
    </select>
    <span class="form-text text-muted"><?php echo __('site-language-desc', _T); ?></span>
</div>
<div class="form-group">
    <label for="default_theme_custom_css" class="form-label"><?php echo __('custom-css-label', _T); ?></label>
    <textarea name="default_theme_custom_css" id="default_theme_custom_css" class="form-control"><?= get_option('default_theme_custom_css', ''); ?></textarea>
    <span class="form-text text-muted"><?php echo __('custom-css-desc', _T); ?></span>
</div>
<div class="form-group">
    <div class="form-label"><?php echo __('single-layout-label', _T); ?></div>
    <div class="custom-controls-stacked row row gutters-sm">
        <div class="col-6 col-sm-2 text-center">
          <label class="custom-control custom-radio">
            <input type="radio" class="custom-control-input" name="single_page_layout" value="single" <?= checked('single', $single_layout); ?>>
            <div class="custom-control-label"><img src="<?= e_attr(current_theme_uri('assets/img/layout-single.png')); ?>" class="layout-img">
                <div class="mt-1">
                    <?= __('layout-single', _T); ?>
                </div>
            </div>
        </label>
    </div>
    <div class="col-6 col-sm-2 text-center">
      <label class="custom-control custom-radio">
        <input type="radio" class="custom-control-input" name="single_page_layout" value="single-with-sidebar" <?= checked('single-with-sidebar', $single_layout); ?>>
        <div class="custom-control-label"><img src="<?= e_attr(current_theme_uri('assets/img/layout-with-sidebar.png')); ?>" class="layout-img">
            <div class="mt-1">
                <?= __('layout-with-sidebar', _T); ?>
            </div>
        </div>
    </label>
</div>
</div>
<span class="form-text text-muted"><?php echo __('single-layout-desc', _T); ?></span>
</div>
<div class="form-group">
    <label class="form-label" for="news_loop_interval"><?= __('news-loop-ad-interval', _T); ?></label>
    <input type="number" name="news_loop_interval" min="0" max="100" id="news_loop_interval" class="form-control" value="<?= sp_post('news_loop_interval', get_option('news_loop_interval', 4)); ?>">
    <span class="form-text"><?= __('news-loop-ad-interval-desc', _T); ?></span>
</div>
<?php endblock(); ?>
<?php
// Extends the plugins options skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
        'title'           => __('theme-options', _T),
        'body_class'      => 'theme-options default',
        'page_heading'    => __('theme-options', _T),
        'page_subheading' => __('theme-options-subheading', _T),
    ]
);
