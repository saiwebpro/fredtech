<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('categories'); ?>
    <form method="post" action="?" class="card">
        <?=$t['csrf_html']?>
        <div class="card-header">
            <h3 class="card-title"><?= __("Confirm Deletion"); ?></h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <span class="form-text">
                    <?= __("Are you sure you want to delete this category?"); ?>
                    </span>
                </div>
                <div class="form-group">
                    <a href="<?= e_attr(url_for('dashboard.categories')); ?>" class="btn btn-outline-primary mr-1">
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
      'title' => __('Delete Category'),
      'body_class' => 'categories categories-create',
      'page_heading' => __('Delete Category'),
      'page_subheading' => __('Remove existing category.'),
    ]
);
