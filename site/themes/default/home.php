<?php block('content'); ?>
<div class="container px-md-0 mt-3">
  <div class="row no-gutters">
    <div class="col-12 col-md-0 col-lg-2 p-0 px-lg-3">
        <div class="sidebar sidebar-left">
        <?php insert('partials/site_sidebar_left.php'); ?>
    </div>
</div>
    <div class="col-md-8 col-lg-7 px-0 px-md-3">
        <?php insert('partials/slider.php'); ?>
        <h3 class="site-heading my-3"><span><?= __('Latest'); ?></span></h3>

        <?php if (has_items($t['latest_posts'])) : ?>
            <div class="row">
            <?php foreach ($t['latest_posts'] as $t['loop']) : ?>
                <div class="col-12 mb-3">
                    <?php insert('partials/loop.php'); ?>
                </div>
            <?php endforeach; ?>
        </div>

        
            <?= $t['pagination_html']; ?>
        <?php else : ?>
                <?php
                insert(
                    'partials/empty.php',
                    [
                        'empty_message' => __('no-posts-found-home', _T)
                    ]
                );
                ?>
        <?php endif; ?>
    </div>
    <div class="col-12 col-md-4 col-lg-3 p-0 px-md-2">
        <div class="sidebar sidebar-right">
        <?php insert('partials/site_sidebar_right.php'); ?>
    </div>
    </div>
    </div>
  </div>
<?php endblock(); ?>

<?php
extend(
    'layouts/basic.php',
    [
    'body_class' => 'home',
    ]
);

