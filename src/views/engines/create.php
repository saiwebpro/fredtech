<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('engines'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?=$t['csrf_html']?>
      <div class="card-body">
        
            <div class="form-group">
            <label class="form-label" for="engine_name"><?= __('Engine Name'); ?></label>
            <input type="text" name="engine_name" id="engine_name" class="form-control" value="<?= sp_post('engine_name'); ?>" maxlength="200" required>
            <div class="form-text text-muted">
                <?= __('A short name for the engine'); ?>
            </div>
            </div>
            <div class="form-group">
            <label class="form-label" for="engine_cse_id"><?= __('Google Custom Search ID'); ?></label>
            <input type="text" name="engine_cse_id" id="engine_cse_id" class="form-control" value="<?= sp_post('engine_cse_id'); ?>" maxlength="200" required>
            <div class="form-text text-muted">
                <?= __('Google CSE ID for the engine.'); ?>
            </div>
            </div>
        <div class="form-group">
          <label class="form-label" for="engine_is_image"><?= __('Image Search'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="engine_is_image" value="0">
            <input type="checkbox" id="engine_is_image" name="engine_is_image" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('engine_is_image', '0')); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?= __('Image Search'); ?></span>
          </label>
          <span class="form-text text-muted"><?= __('Choose if the result type is image or not'); ?></span>
        </div>
        <div class="form-group">
          <label class="form-label" for="engine_show_thumb"><?= __('Display Thumbnails'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="engine_show_thumb" value="0">
            <input type="checkbox" id="engine_show_thumb" name="engine_show_thumb" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('engine_show_thumb', '0')); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?= __('Display Thumbnails'); ?></span>
          </label>
          <span class="form-text text-muted"><?= __('Select if thumbnails will be shown when if available (web result only)'); ?></span>
        </div>
                <div class="form-group">
          <label class="form-label" for="default_engine"><?= __('Set Engine as Default'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="default_engine" value="0">
            <input type="checkbox" id="default_engine" name="default_engine" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('default_engine', 0)); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?= __('Set Engine as Default'); ?></span>
          </label>
          <span class="form-text text-muted"><?= __('Choose if the engine is the default engine or not.'); ?></span>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?=__('Create')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Create Engine'),
      'body_class' => 'engines engines-create',
      'page_heading' => __('Create Engine'),
      'page_subheading' => __('Add a new engine.'),
    ]
);
