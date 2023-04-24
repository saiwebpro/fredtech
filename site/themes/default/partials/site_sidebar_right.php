<?php
/**
* @hook Fires at the right sidebar of the feeds page
*/
do_action('feed_right_sidebar_before');
?>

<!-- Ad Unit 1 -->
<div class="ad-block"><?= get_option('ad_unit_1'); ?></div>

<!-- Trending Searches Begin -->
<div class="sidebar-block">
  <h4 class="sidebar-heading"><span><?= __('trending', _T); ?></span> <?= svg_icon('trending-up', 'svg-md text-primary'); ?></h4>
  <div class="sidebar-body p-2">
    <div class="row no-gutters">
      <?php foreach ($t['trends'] as $key => $query) : ?>
        <div class="col-6">
          <div class="text-truncate trend-item px-1"><?= $key+1; ?>.
            <a data-toggle="tooltip" title="<?= e_attr($query); ?>" href="<?= e_attr(url_for('site.search')); ?>?q=<?= e_attr($query); ?>"><?= e($query); ?></a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<!-- Trending Searches End -->

<div class="sidebar-block bg-transparent shadow-none">
  <div class="sidebar-body px-2 px-sm-0">
    <form method="get" action="<?php echo e_attr(url_for('site.archive')); ?>">
      <div class="input-group">
        <input type="search" name="s" minlength="3" class="form-control border-right-0 shadow-none" placeholder="<?php echo e_attr(__('search-articles', _T)); ?>" value="<?php echo e_attr($t['site_search_query']); ?>" required>

        <div class="input-group-append">
          <button type="submit" class="input-group-text bg-white"><?= svg_icon('search'); ?></button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Popular Posts Begin -->
<?php if (has_items($t['popular_posts'])) : ?>
  <div class="sidebar-block">
    <h4 class="sidebar-heading"><span><?= __('popular', _T); ?></span> <?= svg_icon('flame', 'text-primary svg-md'); ?></h4>
    <div class="sidebar-body">
      <?php foreach ($t['popular_posts'] as $t['loop']) : ?>
            <?php insert('partials/sidebar_loop.php'); ?>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>
<!-- Popular Posts End -->


<!-- Ad Unit 4 -->
<div class="ad-block"><?= get_option('ad_unit_4'); ?></div>

<div class="sidebar-block footer-block">
  <div class="sidebar-body py-3 px-3">
    <div class="mb-2">
      <?php
        $locales = get_theme_locales();
        $last = count($locales) - 1;

        if ($last < 0) {
            $last = 0;
        }
      
        foreach ($locales as $key => $locale) : ?>
      <a href="<?php echo e_attr(url_for('site.change_locale', ['locale' => $key])); ?>" class="<?php echo $locale['active'] ? 'text-body' : ''; ?>">
            <?php if ($locale['icon']) : ?>
          <img class="locale-icon mr-1" src="<?php echo e_attr($locale['icon']); ?>" alt="<?php echo e_attr($locale['name']); ?>">
            <?php endif; ?>
            <?php echo e($locale['name']); ?> <?php if ($key != $last) :
                ?> &middot; <?php
            endif; ?>
      </a>
        <?php endforeach; ?>
  </div>

  <div class="my-1">
    <?php if (has_items($t['site_pages'])) : ?>
      <?php foreach ($t['site_pages'] as $page) : ?>
        <a class="footer-link" href="<?= e_attr(url_for('site.page', ['identifier' => $page['content_slug']])); ?>"><?= e($page['content_title'], _T); ?></a> &middot; 
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <p class="m-0">&copy; <?= e(get_option('site_name')) . ' ' . date('Y'); ?>. <?= __('copyright', _T); ?></p>
</div>
</div>
<?php
/**
* @hook Fires after the right sidebar of the feeds page
*/
do_action('feed_right_sidebar_after');
?>
