<?php block('form_content'); ?>
<?php if (!$t['invalid']) :?>
    <p class="text-muted">
        <?= __('Please create a new password for your account using the form below.'); ?>
    </p>
    <div class="form-group">
      <label class="form-label" for="password"><?= __('New Password'); ?></label>
      <input
      type="password" name="password" id="password" class="form-control"
      placeholder="<?= __("Please do remember this time"); ?>"
      minlength="<?= e_attr(config('internal.password_minlength')); ?>" required>
  </div>
    <div class="form-group">
      <label class="form-label" for="confirm_password"><?= __('Confirm New Password'); ?></label>
      <input
      type="password" name="confirm_password" id="confirm_password" class="form-control"
      placeholder="<?= __("Just to be sure"); ?>"
      data-parsley-equalto="#password"
      data-parsley-equalto-message="<?= e_attr(__("Passwords doesn't match")); ?>"
      minlength="<?= e_attr(config('internal.password_minlength')); ?>" required>
  </div>

<div class="form-footer">
    <button type="submit" class="btn btn-primary btn-pill"><?= __("Reset Password"); ?></button>
</div>
<?php else : ?>
        <div class="alert alert-danger">
            <?= __('Sorry, the link is invalid or expired.'); ?>
            <hr>
            <a href="<?= e_attr(url_for('dashboard.account.forgotpass')); ?>" class="btn btn-white"><?= __("Request a new one"); ?></a>
        </div>
<?php endif; ?>
<?php endblock(); ?>

<?php block('form-after'); ?>
<div class="">
    <?= __("Change of mind?"); ?>
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
        'title' => __('Reset password'),
        'body_class' => 'account account-reset-password',
        'form_heading' => __('Reset password'),
    ]
);
