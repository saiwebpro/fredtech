<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('roles'); ?>
    <form method="post" action="?" class="card">
        <?=$t['csrf_html']?>
        <div class="card-header">
            <h3 class="card-title"><?= __("Confirm Deletion"); ?></h3>
        </div>
        <div class="card-body">
            <div class="form-group">
                <span class="form-text">
                    <?= sprintf(__("Are you sure you want to delete the role <em><b>%s</b></em>?"), e($t['role.role_name'])); ?>
                    </span>
                </div>
                <div class="form-group">
                    <a href="<?= e_attr(url_for('dashboard.roles')); ?>" class="btn btn-outline-primary mr-1">
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
      'title' => __('Delete Role'),
      'body_class' => 'roles role-delete',
      'page_heading' => __('Delete Role'),
      'page_subheading' => __('Trash role.'),
    ]
);
