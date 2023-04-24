<?php block('form_content'); ?>
<div id="form-wrap" <?= $t['form_countdown'] ? 'style="display:none"' : ''; ?>>
<p class="text-muted">
    <?= __('Enter your email address and instruction to reset the password will be emailed to you.'); ?>
</p>
<div class="form-group">
    <label class="form-label" for="email"><?= __('E-Mail'); ?></label>
    <input
    type="email" class="form-control" id="email" name="email"
    value="<?= is_logged() ? e_attr(current_user_field('email')) : ''; ?>"
    placeholder="<?= e_attr(__('tony@stark-industries.com')); ?>"
    <?= is_logged() ? 'readonly' : ''; ?>>
</div>
<?= sp_google_recaptcha('<div class="text-center">', '</div>'); ?>
<div class="form-footer">
    <button type="submit" class="btn btn-primary btn-pill"><?= __("Reset Password"); ?></button>
</div>
</div>

<div class="form-group" id="form-counter-wrap" style="display:none">
    <p class="counter-desc text-left text-muted mb-3">
        <?= __('Please wait the time given below before requesting any more activation password reset emails.') ?>
    </p>
    <div id="form-counter" class="countdown d-flex justify-content-left"></div>
</div>
<?php endblock(); ?>

<?php block('form-after'); ?>
    <?= __("Remember password?"); ?>
    <?php if (is_logged()) :?>
        <a href="<?= e_attr(url_for('dashboard')); ?>"><?= __("Go back to Dashboard"); ?></a>
    <?php else : ?>
        <a href="<?= e_attr(url_for('dashboard.account.signin')); ?>"><?= __("Go back to sign in page"); ?></a>
    <?php endif; ?>
<?php endblock(); ?>
<?php block('body_end'); ?>
<?php insert('admin::account/partials/countdown.php'); ?>
<?php endblock(); ?>
<?php
// Extends the base skeleton
extend(
    'admin::layouts/nonlogged_skeleton.php',
    [
        'title' => __('Forgot password'),
        'meta.noindex' => false,
        'body_class' => 'account forgot-pass',
        'form_heading' => __('Forgot password'),
    ]
);
