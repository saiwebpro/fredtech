<?php block('form_content'); ?>

<div class="form-group">
    <?= sprintf(__("Please check your mail inbox <strong>%s</strong> for an email containing instructions on how to activate your account. If you can't find any, you can request a new one from below."), $t['user.email']); ?>
</div>
<div id="form-wrap" <?= $t['form_countdown'] ? 'style="display:none"' : ''; ?>>
<div class="form-footer">
    <button type="submit" class="btn btn-primary btn-pill"><?= __("Request"); ?></button>
</div>
</div>

<div class="form-group" id="form-counter-wrap" style="display:none">
    <p class="counter-desc text-muted mb-3">
        <?= __('Please wait the time given below before requesting any more activation E-mails.') ?>
    </p>
    <div id="form-counter" class="countdown d-flex justify-content-left"></div>
</div>
<?php endblock(); ?>

<?php block('form-after'); ?>
<?php if (!config('auth.force_email_verification', false)) : ?>
    <a class="btn btn-pill btn-outline-primary" href="<?= e_attr(get_redirect_to_uri()); ?>"><?= __('Back'); ?></a>
<?php endif; ?>
<form method="post" action="<?= e_attr(url_for('dashboard.account.logout')); ?>" class="d-inline">
    <?= $t['csrf_html']; ?>
    <button class="btn btn-pill btn-danger" type="submit"><?= __('Log Out'); ?></button>
</form>
<?php endblock(); ?>

<?php block('body_end'); ?>
<?php insert('admin::account/partials/countdown.php'); ?>
<?php endblock(); ?>
<?php
// Extends the base skeleton
extend(
    'admin::layouts/nonlogged_skeleton.php',
    [
        'title' => __('Account activation'),
        'body_class' => 'account account-activation',
        'form_heading' => __('Account activation'),
    ]
);
