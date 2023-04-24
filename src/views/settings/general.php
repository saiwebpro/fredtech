<?php
sp_enqueue_script('dropzone-js', 2, ['dashboard-core-js']);
breadcrumb_add('dashboard.settings.general', __('General Settings'));
?>
<?php block('form-content'); ?>
<div class="form-group">
  <label class="form-label" for="site_name"><?= __('Site Name'); ?></label>
  <input type="text" class="form-control" name="site_name" id="site_name" value="<?= sp_post('site_name', get_option('site_name')); ?>" maxlength="200" required>
</div>
<div class="form-group">
  <label class="form-label" for="site_tagline"><?= __('Site Tagline'); ?></label>
  <input type="text" class="form-control" name="site_tagline" id="site_tagline" value="<?= sp_post('site_tagline', get_option('site_tagline')); ?>" maxlength="200" required>
</div>
<div class="form-group">
  <label class="form-label" for="site_description"><?= __('Site Description'); ?></label>
  <textarea class="form-control" name="site_description" maxlength="6000" id="site_description"><?= sp_post('site_description', get_option('site_description'));?></textarea>
</div>
<div class="form-group">
  <label class="form-label" for="site_email"><?= __('Site E-mail'); ?></label>
  <input type="email" class="form-control" name="site_email" id="site_email" value="<?= sp_post('site_email', get_option('site_email')); ?>" maxlength="600" required>
</div>
<div class="form-group">
  <label class="form-label" for="site_logo"><?= __('Site Logo URL'); ?></label>
  <input type="text" class="form-control" name="site_logo" id="site_logo" value="<?= sp_post('site_logo', get_option('site_logo')); ?>" maxlength="200" required>
    <?php if (current_user_can('manage_gallery')) : ?>
  <span class="form-text text-muted"><?= __('You may provide a URL or upload via the uploader given below.'); ?></span>
  <div id="logo-uploader" class="dz my-5">
    <div class="dz-message dz-small"><strong>
        <?= __('Drop logo here or click to upload.'); ?></strong>
      </div>
    </div>
    <?php endif; ?>
</div>
<div class="form-group">
  <label class="form-label" for="enable_registration"><?= __('User Registration'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="enable_registration" value="0">
    <input type="checkbox" id="enable_registration" name="enable_registration" value="1" class="custom-switch-input"  <?php checked(1, (int) sp_post('enable_registration', get_option('enable_registration'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Enable User Registration'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('Toggle user registration for the site.'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="timezone"><?= __('Site Timezone'); ?></label>
  <select id="timezone" name="timezone" class="form-control" required>
    <?php foreach (timezone_list() as $key => $timezone) :?>
      <option value="<?=$key?>" <?php selected($key, sp_post('timezone', get_option('timezone'))); ?>><?=$timezone?></option>
    <?php endforeach; ?>
  </select>
</div>
<!--<div class="form-group">
  <label class="form-label" for="site_locale"><?= __('Site Locale'); ?></label>
  <select id="site_locale" name="site_locale" class="form-control" required>
    <?php foreach (sp_dashboard_locales() as $key => $locale) :?>
      <option value="<?=$key?>" <?php selected($key, sp_post('site_locale', get_option('site_locale'))); ?>><?=$locale['name']; ?></option>
    <?php endforeach; ?>
  </select>
  <span class="form-text text-muted">
    <?= __('Mostly affects dashboard, plugins and themes can have their own options'); ?>
  </span>
</div>-->

<div class="form-group">
  <label class="form-label" for="header_scripts"><?= __('Header Scripts'); ?></label>
  <textarea class="form-control" rows="4" name="header_scripts" id="header_scripts"><?= sp_post('header_scripts', get_option('header_scripts')); ?></textarea>
  <span class="form-text text-muted"><?= __('Code to be excecuted inside &lt;head&gt;...&lt;/head&gt;'); ?></span>
</div>
<div class="form-group">
  <label class="form-label" for="footer_scripts"><?= __('Footer Scripts'); ?></label>
  <textarea class="form-control" rows="4" name="footer_scripts" id="footer_scripts"><?= sp_post('footer_scripts', get_option('footer_scripts')); ?></textarea>
  <span class="form-text text-muted"><?= __('Code to be excecuted before the ...&lt;/body&gt; tag'); ?></span>
</div>
<?php endblock(); ?>
<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
    $("#logo-uploader").dropzone({
      url: "<?= url_for('dashboard.gallery.create_post'); ?>",
      maxFileSize: <?= format_bytes(get_max_upload_size()); ?>,
      acceptedFiles: 'image/*',
      params: {
        csrf_token: "<?= $t['csrf_token']; ?>",
      },
      success: function (dropzone, response) {
        if (response.content_url) {
          $('#site_logo').val(response.content_relative_url).focus();
        }
      },
    });


    /**
     * Mark the Google re-captcha fields as required if the Google captcha status changes
     */
    $('#captcha_enabled').on('change', function (e) {
      var captcha_enabled = $(this).prop('checked');
      // by default those fields are not required
      var required = false;

      if (captcha_enabled) {
        required = true;
      }

      // Update requirement
      $('#google_recaptcha_secret_key').prop('required', required);
      $('#google_recaptcha_site_key').prop('required', required);
    });
  });
</script>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
        'title' => __('General Settings'),
        'body_class' => 'settings general-settings',
        'page_heading' => __('General Settings'),
        'page_subheading' => __('Configure the basics.'),
    ]
);
