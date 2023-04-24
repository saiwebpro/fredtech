<?php block('form_content'); ?>
<div id="form-wrap" <?= $t['form_countdown'] ? 'style="display:none"' : ''; ?>>
<div class="form-group">
    <label class="form-label" for="email"><?= __('E-Mail'); ?></label>
    <input
    type="email" class="form-control" id="email" name="email"
    value="<?= sp_post('email'); ?>"
    placeholder="<?= e_attr(__('tony@stark-industries.com')); ?>"
    required>
</div>

<div class="form-group">
  <label class="form-label" for="password"><?= __('Password'); ?>
    <a href="<?= e_attr(url_for('dashboard.account.forgotpass')); ?>" class="float-right small"><?= __("Forgot Password"); ?></a></label>
    <input
    type="password" name="password" id="password" class="form-control"
    placeholder="<?= __("Remember our secret?"); ?>"
    minlength="<?= e_attr(config('internal.password_minlength')); ?>" required>
</div>
<div class="form-group">
    <label class="custom-control custom-checkbox">
        <input type="checkbox" class="custom-control-input" name="remember_me" value="1"
        <?php checked((int) sp_post('remember_me'), 1); ?>
        >
        <span class="custom-control-label"><?= __("Remember me"); ?></span>
    </label>
</div>

<div class="form-footer d-flex">
    <button type="submit" class="btn btn-primary btn-pill"><?= __("Sign In"); ?></button>
</div>
</div>

<div class="form-group" id="form-counter-wrap" style="display:none">
    <p class="counter-desc text-center text-muted mb-3">
        <?= __('You have reached maximum amount of failed login attempts. Please wait the given time below before trying to login again.') ?>
    </p>
    <div id="form-counter" class="countdown d-flex justify-content-center"></div>
</div>
<?php endblock(); ?>

<?php block('form-after'); ?>
<?php if (config('site.registration_enabled')) :?>
    <?= __("Don't have account yet?"); ?>
    <a href="<?= e_attr(url_for('dashboard.account.register') . '?' . request_build_query([], null)); ?>"><?= __("Register"); ?></a>
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
        'title' => __('Sign In'),
        'meta.noindex' => false,
        'body_class' => 'account account-signin',
        'form_heading' => __('Log in to dashboard'),
    ]
);
