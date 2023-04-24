<?php block('form_content'); ?>

<?php if (config('site.registration_enabled')) :?>
<div class="form-group">
    <label class="form-label" for="email"><?= __('E-Mail'); ?></label>
    <input
    type="email" class="form-control" id="email" name="email"
    value="<?= sp_post('email'); ?>"
    placeholder="<?= e_attr(__('tony@stark-industries.com')); ?>"
    data-parsley-remote="<?= e_attr(url_for('dashboard.ajax.email_check')); ?>"
    data-parsley-remote-reverse="true"
    data-parsley-remote-message="<?= e_attr(__('E-Mail already exists in database.')); ?>"
    required>
</div>

<div class="form-group">
  <label class="form-label" for="password"><?= __('Password'); ?></label>
    <input
    type="password" name="password" id="password" class="form-control"
    placeholder="<?= __("It'll be our secret"); ?>"
    minlength="<?= e_attr(config('internal.password_minlength')); ?>" required>
</div>

<div class="form-group">
  <label class="form-label" for="full_name"><?= __('Full Name'); ?></label>
    <input
    type="text" name="full_name" id="full_name" class="form-control"
    placeholder="<?= __("Tony Stark"); ?>"
    value="<?= sp_post('full_name'); ?>"
    minlength="3" maxlength="200" required>
</div>

<div class="form-group">
  <label class="form-label" for="gender"><?= __('Gender'); ?></label>

  <div class="custom-controls-stacked">
    <?php foreach (sp_genders() as $_gender_id => $_gender_label) :?>
      <label class="custom-control custom-radio custom-control-inline">
        <input type="radio" class="custom-control-input" name="gender" value="<?= e_attr($_gender_id); ?>" <?= (int) sp_post('gender') == $_gender_id ? 'checked' : ''; ?>>
        <span class="custom-control-label"><?= e($_gender_label); ?></span>
    </label>
    <?php endforeach; ?>
</div>
</div>

    <?= sp_google_recaptcha('<div class="text-center">', '</div>'); ?>

<div class="form-footer">
    <button type="submit" class="btn btn-primary btn-pill"><?= __("Create new account"); ?></button>
</div>
<?php else : ?>
    <?= sp_bootstrap_alert(
        __('Sorry, registration is disabled by the site administrator'),
        'danger',
        sp_svg_icon_for_alert('warning'),
        false
    ); ?>
<?php endif; ?>
<?php endblock(); ?>

<?php block('form-after'); ?>
    <?= __("Already have account?"); ?>
    <a href="<?= e_attr(url_for('dashboard.account.signin')); ?>"><?= __("Sign In"); ?></a>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
  });
</script>
<?php endblock(); ?>
<?php
// Extends the base skeleton
extend(
    'admin::layouts/nonlogged_skeleton.php',
    [
        'title' => __('Create new account'),
        'meta.noindex' => false,
        'body_class' => 'account account-register',
        'form_heading' => __('Create new account'),
    ]
);
