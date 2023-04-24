<?php block('content'); ?>
<div class="container page-flex">
    <div class="page-flex-content">
    <div class="px-1 row">
        <div class="col-md-8">
        <div class="mb-2">
            <img src="<?= e_attr(sp_logo_uri()); ?>" class="site-logo">
        </div>
        <h5><?= __('404-heading', _T); ?></h5>
        <p class="my-2">
            <?= __('404-text', _T); ?><br>
            <span class="text-muted"><?= __('thats-all-we-know', _T); ?></span>
        </p>
        <p>
            <a href="<?= e_attr(base_uri()); ?>"><?= __('return-to-home', _T); ?></a>
        </p>
    </div>
    <div class="col col-auto d-none d-md-flex align-items-center">
        <?= svg_icon('sad', 'text-secondary', ['style' => "height:5rem;width:5rem"]); ?>
    </div>
    </div>
    </div>

</div>
<?php endblock(); ?>

<?php
extend(
    'layouts/basic.php',
    [
        'body_class' => '404',
        'hide_header' => true,
        'hide_footer' => true,
    ]
);

