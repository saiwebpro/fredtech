<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('rewriter'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?=$t['csrf_html']?>
        <div class="card-body">
            <div class="form-group">
                <label class="form-label"><?= __('Feed name'); ?></label>
                      <input type="text" readonly class="form-control-plaintext" value="<?= e_attr($t['feed.feed_name']); ?>">
            </div>

            <div class="form-group">
              <label class="form-label" for="enable_rewriting"><?= __('Enable Rewriting'); ?></label>
              <label class="custom-switch mt-3">
                <input type="hidden" name="enable_rewriting" value="0">
                <input type="checkbox" id="enable_rewriting" name="enable_rewriting" value="1" class="custom-switch-input" <?php checked(1, (int) $t['enable_rewriting']); ?>>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"> <?= __('Enable Rewriting for this feed.'); ?></span>
            </label>
            <span class="form-text text-muted"><?= __('When enabled the plugin will attempt to rewrite the articles during import.'); ?></span>
        </div>

        <div class="form-group">
              <label class="form-label" for="dictionary"><?= __('Dictionary'); ?></label>
              <select name="dictionary" id="dictionary" class="form-control">
                  <?php foreach ($t['dicts'] as $dict) : ?>
                    <option value="<?= e_attr($dict); ?>"><?= e($dict); ?></option>
                  <?php endforeach; ?>
              </select>
            <span class="form-text text-muted"><?= __('Choose the language based synonyms dictionary to use when replacement.'); ?></span>
        </div>

        </div>

      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?=__('Save')?></button>
      </div>
    </form>
</div>
</div>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Manage Feed Rewriting'),
      'body_class' => 'plugins plugins-rewriter-manage-feed',
      'page_heading' => __('Feed Rewriting'),
      'page_subheading' => __('Manage Feed Rewriting'),
    ]
);
