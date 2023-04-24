<?php block('content'); ?>
<?php
$bgs = ['primary', 'secondary', 'dark', 'info', 'warning', 'danger'];
$key = array_rand($bgs);
?>
<?php if ($t['page_heading']) : ?>
<div class="hero-header bg-<?= $bgs[$key]; ?> mb-2">
    <div class="container">
        <h3 class="m-0 p-0">
            <?php echo $t['page_heading']; ?>
  </h3>
</div>
</div>
<?php endif; ?>

<div class="container px-0 px-md-3 mt-3">
  <div class="row no-gutters">
    <div class="col-12 col-md-0 col-lg-2 p-0 px-lg-3"><div class="sidebar sidebar-left">
        <?php insert('partials/site_sidebar_left.php'); ?></div>
    </div>
    <div class="col-md-8 col-lg-7 px-0 px-md-3">
        <?php if (!$t['page_heading']) : ?>   
            <?php insert('partials/slider.php'); ?>
            <h3 class="site-heading my-3"><span><?= __('latest', _T); ?></span></h3>
        <?php endif; ?>

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
                        'empty_message' => __('nothing-found-archive', _T)
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
    'layouts/basic.php'
);
?>
