<?php block('form_content'); ?>
<?= sp_bootstrap_alert($t['message'], $t['type'], null, false); ?>
<?php endblock(); ?>

<?php block('form-after'); ?>
<div class="">
    <?= __("Finished?"); ?>
    <?php if (is_logged()) :?>
        <a href="<?= e_attr(url_for('dashboard')); ?>"><?= __("Go back to Dashboard"); ?></a>
    <?php else : ?>
        <a href="<?= e_attr(url_for('site.home')); ?>"><?= __("Go back to homepage"); ?></a>
    <?php endif; ?>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<?php
// Extends the base skeleton
extend(
    'admin::layouts/nonlogged_skeleton.php',
    [
        'title' => __('Email verification'),
        'body_class' => 'account account-verify-action',
        'form_heading' => __('Email verification'),
    ]
);
