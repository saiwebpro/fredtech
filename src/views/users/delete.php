<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('users'); ?>
    <form method="post" action="?" class="card">
        <?=$t['csrf_html']?>
        <div class="card-header">
            <h3 class="card-title"><?= __("Confirm Deletion"); ?></h3>
        </div>
        <div class="card-body">

            <div class="form-group d-flex">
                    <span class="avatar avatar-lg" style="background-image: url(<?= e_attr(sp_user_avatar_uri($t['user.avatar'], $t['user.email'], 48)); ?>)"></span>
                    <span class="ml-2">
                      <span class="text-default"><?= e($t['user.full_name']); ?></span>
                  <small class="text-muted d-block mt-1">#<?= e($t['user.user_id']); ?>, <?= __('Registered:') . ' ' . date('M d, Y', $t['user.created_at']); ?></small>
                    </span>
                </div>
            <div class="form-group">
                <span class="form-text">
                    <?= __("Are you sure you want to delete this user?"); ?>
                    </span>
                </div>
                <div class="form-group">
                    <a href="<?= e_attr(url_for('dashboard.users')); ?>" class="btn btn-outline-primary mr-1">
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
      'title' => __('Delete User'),
      'body_class' => 'users users-create',
      'page_heading' => __('Delete User'),
      'page_subheading' => __('Remove existing user.'),
    ]
);
