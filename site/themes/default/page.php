<?php block('content'); ?>
<div class="container pt-2">
    <h1 class="site-heading">
        <span><?= e($t['page.content_title']); ?></span>
    </h1>
    <?= sp_alert_flashes('pages', true, false); ?>
    <div class="page-content">
        <?= $t['page.content_body']; ?>
    </div>

    <?= breadcrumb_render(); ?>
</div>

    <div class="d-md-none">
        <?php insert('partials/site_sidebar_left.php'); ?>
    </div>
<?php endblock(); ?>

<?php
extend(
    'layouts/basic.php',
    [
        'body_class' => "page {$t['page.content_slug']}",
    ]
);


