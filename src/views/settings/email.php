<?php breadcrumb_add('dashboard.settings.site', __('E-Mail Settings')); ?>

<?php block('form-content'); ?>
<div class="form-group">
  <label class="form-label" for="smtp_enabled"><?= __('Use SMTP'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="smtp_enabled" value="0">
    <input type="checkbox" name="smtp_enabled" value="1" class="custom-switch-input" data-toggle-prefix=".stmp-enabled-" <?php checked(1, (int) sp_post('smtp_enabled', get_option('smtp_enabled'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Enable SMTP'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('Toggle SMTP for sending E-mails'); ?></span>
</div>

<section class="stmp-enabled-1">
  <div class="form-group">
    <label for="smtp_host" class="form-label"><?= __('SMTP Host'); ?></label>
    <input type="text" class="form-control" name="smtp_host" id="smtp_host" value="<?= sp_post('smtp_host', get_option('smtp_host')); ?>">
    <span class="form-text text-muted"><?= __('SMTP host address'); ?></span>
  </div>

  <div class="form-group">
    <label for="smtp_port" class="form-label"><?= __('SMTP Port'); ?></label>
    <input type="number" class="form-control" name="smtp_port" min="0" id="smtp_port" value="<?= sp_post('smtp_port', get_option('smtp_port')); ?>">
    <span class="form-text text-muted"><?= __('SMTP host port'); ?></span>
  </div>


  <div class="form-group">
    <label class="form-label" for="smtp_auth_enabled"><?= __('SMTP Authentication'); ?></label>
    <label class="custom-switch mt-3">
      <input type="hidden" name="smtp_auth_enabled" value="0">
      <input type="checkbox" name="smtp_auth_enabled" value="1" class="custom-switch-input" data-toggle-prefix=".stmp-auth-enabled-" <?php checked(1, (int) sp_post('smtp_auth_enabled', get_option('smtp_auth_enabled'))); ?>>
      <span class="custom-switch-indicator"></span>
      <span class="custom-switch-description"> <?= __('Enable SMTP authentication'); ?></span>
    </label>
    <span class="form-text text-muted"><?= __('Toggle SMTP authentication'); ?></span>
  </div>



<section class="stmp-auth-enabled-1">
  <div class="form-group">
    <label for="smtp_username" class="form-label"><?= __('SMTP Username'); ?></label>
    <input type="text" class="form-control" name="smtp_username" id="smtp_username" value="<?= sp_post('smtp_username', get_option('smtp_username')); ?>">
    <span class="form-text text-muted"><?= __('Username for SMTP authentication'); ?></span>
  </div>

  <div class="form-group">
    <label for="smtp_password" class="form-label"><?= __('SMTP Password'); ?></label>
    <input type="text" class="form-control" name="smtp_password" id="smtp_password" value="<?= sp_post('smtp_password', get_option('smtp_password')); ?>">
    <span class="form-text text-muted"><?= __('Password for SMTP authentication'); ?></span>
  </div>
</section>

</section>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('E-Mail Settings'),
    'body_class' => 'settings email-settings',
    'page_heading' => __('E-Mail Settings'),
    'page_subheading' => __('Modify E-Mail related settings'),
    ]
);
