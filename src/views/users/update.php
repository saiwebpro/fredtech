<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('users'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?=$t['csrf_html']?>
      <div class="card-body form-row">
        <div class="form-group col-md-6">
          <label class="form-label" for="email"><?= __('E-Mail'); ?></label>
          <input type="email" name="email" id="email" value="<?= sp_post('email', $t['user.email']); ?>"
          class="form-control" maxlength="200"
          data-parsley-remote="<?= e_attr(url_for('dashboard.ajax.email_check')); ?>?email={value}&except=<?=e_attr($t['user.email']);?>"
          data-parsley-remote-reverse="true"
          placeholder="<?= e_attr(__('tony@stark-industries.com')); ?>"
          data-parsley-remote-message="<?= e_attr(__('E-Mail already exists in database.')); ?>"
          required>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label" for="password"><?= __('New Password'); ?></label>
          <input type="password" name="password" id="password" class="form-control" value="<?= sp_post('password'); ?>"
          placeholder="<?= e_attr(sprintf("%d characters or more", config('internal.password_minlength'))); ?>"
          minlength="<?= e_attr(config('internal.password_minlength')); ?>">
          <span class="form-text text-muted small"><?= __("Leave blank if you don't want to change."); ?></span>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label" for="username"><?= __('Username'); ?> <?= __('(Optional)'); ?></label>
          <input
          type="text"
          name="username" id="username"
          value="<?= sp_post('username', $t['user.username']); ?>"
          class="form-control"
          data-parsley-remote="<?= e_attr(url_for('dashboard.ajax.username_check')); ?>?username={value}&except=<?=e_attr($t['user.username']);?>"
          data-parsley-remote-reverse="true"
          data-parsley-remote-message="<?= e_attr(__('Username already exists in database.')); ?>"
          minlength="<?= e_attr(config('internal.username_minlength')); ?>"
          maxlength="<?= e_attr(config('internal.username_maxlength')); ?>"
          pattern="^[A-Za-z][A-Za-z_0-9]+$"
          placeholder="<?= e_attr(sprintf("A-z0-9_ only, between %d and %d characters", config('internal.username_minlength'), config('internal.username_maxlength'))); ?>"
          data-parsley-pattern-message="<?= e_attr(__('Please provide a valid username.')); ?>">
        </div>
        <div class="form-group col-md-6">
          <label class="form-label" for="role_id"><?= __('Role'); ?></label>
            <?php if (current_user_can('change_user_role')) : ?>
            <select name="role_id" class="form-control" id="role_id">
                <?php foreach ($t['role_list'] as $_role) : ?>
                  <option value="<?= e_attr($_role['role_id']); ?>"
                    <?php selected((int) sp_post('role_id', $t['user.role_id']), $_role['role_id']); ?>>
                    <?= e_attr($_role['role_name']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php else : ?>
              <input type="text" value="<?= e_attr($t['user.role_name']); ?>" class="form-control" disabled>
            <?php endif; ?>
        </div>
        <div class="form-group col-md-6">
          <label class="form-label" for="full_name"><?= __('Full Name'); ?></label>
          <input type="text" name="full_name" id="full_name" value="<?= sp_post('full_name', $t['user.full_name']); ?>"
          placeholder="<?= __('Tony Stark'); ?>" class="form-control"
          maxlength="200">
        </div>
        <div class="form-group col-md-6">
          <label class="form-label" for="user_ip"><?= __('User IP'); ?></label>
          <input type="text" name="user_ip" id="user_ip" value="<?= sp_post('user_ip', $t['user.user_ip']); ?>"
          data-parsley-pattern-message="<?= e_attr(__('Please provide a valid IPv4 address')); ?>"
          placeholder="<?= __('127.0.0.1'); ?>" class="form-control" pattern="\b(?:[0-9]{1,3}\.){3}[0-9]{1,3}\b" required>
        </div>
        <div class="form-group col-6">
          <label class="form-label" for="gender"><?= __('Gender'); ?></label>

          <div class="custom-controls-stacked">
            <?php foreach (sp_genders() as $_gender_id => $_gender_label) :?>
              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" name="gender" value="<?= e_attr($_gender_id); ?>" <?= (int) sp_post('gender', $t['user.gender']) == $_gender_id ? 'checked' : ''; ?>>
                <span class="custom-control-label"><?= e($_gender_label); ?></span>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        <?php if (current_user_can('change_user_status')) :?>
        <div class="form-group col-6">
          <label class="form-label" for="is_blocked"><?= __('Status'); ?></label>

          <div class="custom-controls-stacked">

              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" name="is_blocked" value="0" <?= $t['user.is_blocked'] == 0 ? 'checked' : ''; ?>>
                <span class="custom-control-label"><?= __('Active') ?></span>
              </label>
              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" name="is_blocked" value="1" <?= $t['user.is_blocked'] == 1 ? 'checked' : ''; ?>>
                <span class="custom-control-label"><?= __('Blocked') ?></span>
              </label>
          </div>
        </div>
        <?php endif; ?>
        <div class="form-group col-6">
          <label class="form-label" for="is_verified"><?= __('Email Verified'); ?></label>

          <div class="custom-controls-stacked">

              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" name="is_verified" value="1" <?= $t['user.is_verified'] == 1 ? 'checked' : ''; ?>>
                <span class="custom-control-label"><?= __('Verified') ?></span>
              </label>
              <label class="custom-control custom-radio custom-control-inline">
                <input type="radio" class="custom-control-input" name="is_verified" value="0" <?= $t['user.is_verified'] == 0 ? 'checked' : ''; ?>>
                <span class="custom-control-label"><?= __('Not Verified') ?></span>
              </label>
          </div>
        </div>

      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?=__('Update')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Update User'),
      'body_class' => 'users users-create',
      'page_heading' => __('Update User'),
      'page_subheading' => __('Modify existing user.'),
    ]
);
