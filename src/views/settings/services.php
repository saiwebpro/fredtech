<?php breadcrumb_add('dashboard.settings.services', __('Services Settings')); ?>

<?php block('form-content'); ?>

<div class="form-group">
  <label class="form-label" for="captcha_enabled"><?= __('Google Recaptcha'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="captcha_enabled" value="0">
    <input type="checkbox" id="captcha_enabled" name="captcha_enabled" value="1" class="custom-switch-input" data-toggle-prefix=".google-captcha-settings-" <?php checked(1, (int) sp_post('captcha_enabled', get_option('captcha_enabled'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Enable Google Recaptcha'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('Toggle site-wide Google Recaptcha verification'); ?></span>
</div>

<section class="google-captcha-settings-1">
  <div class="form-group">
    <label for="google_recaptcha_secret_key" class="form-label"><?= __('Google Recaptcha Secret Key'); ?></label>
    <input type="text" class="form-control" name="google_recaptcha_secret_key" id="google_recaptcha_secret_key" value="<?= sp_post('google_recaptcha_secret_key', get_option('google_recaptcha_secret_key')); ?>">
    <span class="form-text text-muted"><?= __('Required for Google Recaptchas to work'); ?></span>
  </div>
  <div class="form-group">
    <label for="google_recaptcha_site_key" class="form-label"><?= __('Google Recaptcha Site Key'); ?></label>
    <input type="text" class="form-control" name="google_recaptcha_site_key" id="google_recaptcha_site_key" value="<?= sp_post('google_recaptcha_site_key', get_option('google_recaptcha_site_key')); ?>">
    <span class="form-text text-muted"><?= __('Required for Google Recaptchas to work'); ?></span>
  </div>

</section>

<div class="form-group">
  <label class="form-label" for="disqus_enabled"><?= __('Disqus Comments'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="disqus_enabled" value="0">
    <input type="checkbox" id="disqus_enabled" name="disqus_enabled" value="1" class="custom-switch-input" data-toggle-prefix=".disqus-settings-" <?php checked(1, (int) sp_post('disqus_enabled', get_option('disqus_enabled'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Enable Disqus Comments'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('Toggle site-wide Disqus Comments'); ?></span>
</div>

<section class="disqus-settings-1">
  <div class="form-group">
    <label for="disqus_url" class="form-label"><?= __('Disqus URL'); ?></label>
    <input type="text" class="form-control" name="disqus_url" id="disqus_url" value="<?= sp_post('disqus_url', get_option('disqus_url')); ?>" placeholder="https://sitename.disqus.com" pattern="^https?://\w+\.disqus\.com/?$" data-parsley-pattern-message="<?= e_attr(__('The Disqus URL is not valid.')); ?>">
    <span class="form-text text-muted"><?= __('Required for Disqus comments to work'); ?></span>
  </div>
</section>

<div class="form-group">
  <label class="form-label" for="fb_comments_enabled"><?= __('Facebook Comments'); ?></label>
  <label class="custom-switch mt-3">
    <input type="hidden" name="fb_comments_enabled" value="0">
    <input type="checkbox" id="fb_comments_enabled" name="fb_comments_enabled" value="1" class="custom-switch-input"  <?php checked(1, (int) sp_post('fb_comments_enabled', get_option('fb_comments_enabled'))); ?>>
    <span class="custom-switch-indicator"></span>
    <span class="custom-switch-description"> <?= __('Enable Facebook Comments'); ?></span>
  </label>
  <span class="form-text text-muted"><?= __('Toggle site-wide Facebook Comments'); ?></span>
</div>

<div class="form-group">
  <label class="form-label" for="facebook_app_id"><?= __('Facebook App Id'); ?></label>
  <input type="number" class="form-control" name="facebook_app_id" id="facebook_app_id" value="<?= sp_post('facebook_app_id', get_option('facebook_app_id')); ?>" maxlength="200" min="0">
  <p class="form-text text-muted">
    <?= __('Facebook APP Id for this app, consider filling it as some part of the site may need it, for example facebook comments.'); ?>
  </p>
</div>
<div class="form-group">
  <label class="form-label" for="facebook_app_secret"><?= __('Facebook App Secret'); ?></label>
  <input type="text" class="form-control" name="facebook_app_secret" id="facebook_app_secret" value="<?= sp_post('facebook_app_secret', get_option('facebook_app_secret')); ?>">
  <p class="form-text text-muted">
    <?= __('Facebook app secret for this app'); ?>
  </p>
</div>

<?php endblock(); ?>
<?php block('body_end'); ?>
<script type="text/javascript">
  $(document).ready(function() {
    $(document).on('change', '#disqus_enabled', function(e) {
      var selector_input = $('#disqus_url');
      if (this.checked) {
        selector_input.attr('required', '');
      } else {
        selector_input.removeAttr('required');
      }
    });

    $(document).on('change', '#fb_comments_enabled', function(e) {
      var selector_input = $('#facebook_app_id');
      if (this.checked) {
        selector_input.attr('required', '');
      } else {
        selector_input.removeAttr('required');
      }
    });
});
</script>
<?php endblock(); ?>
<?php

// Extends the base skeleton
extend(
    'admin::layouts/settings_skeleton.php',
    [
    'title' => __('Services Settings'),
    'body_class' => 'settings services-settings',
    'page_heading' => __('Services Settings'),
    'page_subheading' => __("Manage third party services"),
    ]
);
