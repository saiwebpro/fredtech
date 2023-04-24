<?php block('content'); ?>
<div class="container">
    <div class="home-search-box-wrapper py-5">
        <div class="container search-box-container">
            <div class="home-alt-logo mb-3">
            <a href="<?= e_attr(base_uri()); ?>">
                <img src="<?= e_attr(sp_logo_uri()); ?>" class="site-logo home-alt-logo-img">
            </a>
        </div>

        <form method="get" action="<?= e_attr(url_for('site.search')); ?>" id="searchForm" class="home-alt-search-box">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="<?= __('search-box-placeholder', _T); ?>" name="q" id="q" autocomplete="off" value="<?= e_attr($t['search_value']); ?>">
                <input type="hidden" name="engine" id="engineInput" value="<?= e_attr($t['default_engine']); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><?= svg_icon('search', 'svg-md'); ?></button>
                </div>
            </div>
        </form>

            <ul class="nav nav-tabs mt-2" id="engines-tab">
            <?php foreach ($t['engines'] as $engine) : ?>
                <li class="nav-item">
                        <a class="nav-link <?= ($engine['engine_id'] == $t['default_engine']) ? 'active-engine' : ''; ?> engine-switcher" href="javascript:void(0);" data-id="<?= e_attr($engine['engine_id']); ?>">
                            <?= e($engine['engine_name']); ?>
                            </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
</div>
<?php endblock(); ?>
<?php block('before_body_closure'); ?>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        var engineInput = $('#engineInput');

        $('.engine-switcher').on('click', function () {
            var activeEngine = $('.active-engine');
            activeEngine.removeClass('active-engine');
            var link = $(this);
            var id = link.attr('data-id');
            link.addClass('active-engine');
            engineInput.val(id);
        });
    });
</script>
<?php endblock(); ?>

<?php
extend(
    'layouts/basic.php',
    [
        'body_class' => 'home home-alt',
        'hide_header' => true,
    ]
);

