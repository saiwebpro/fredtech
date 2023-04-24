<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('pages'); ?>
    <form method="post" action="?" class="card">
        <?=$t['csrf_html']?>
        <div class="card-header">
            <h3 class="card-title"><?= __("Confirm Deletion"); ?></h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <span class="form-text">
                    <?= sprintf(__("Are you sure you want to delete this page: <em>%s</em>?"), $t['page.content_title']); ?>
                    </span>
                </div>
                <div class="form-group">
                    <a href="<?= e_attr(url_for('dashboard.pages')); ?>" class="btn btn-outline-primary mr-1">
                        <?=__('Nope')?></a>
                    <button type="submit" class="btn btn-danger"><?=__('Confirm')?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Delete Page'),
      'body_class' => 'pages pages-create',
      'page_heading' => __('Delete Page'),
      'page_subheading' => __('Remove existing page.'),
    ]
);
