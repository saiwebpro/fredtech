<?php block('content'); ?>

<?= sp_alert_flashes('pages'); ?>
<form method="post" action="?" class="row" data-parsley-validate>
  <div class="col-md-9">
    <div class="px-lg-4 px-0">
        <?=$t['csrf_html']?>
      <div class="">
        <div class="form-group">
          <label class="form-label" for="content_title"><?= __('Page Title'); ?></label>
          <input type="text" name="content_title" id="content_title" class="form-control" value="<?= sp_post('content_title', $t['page.content_title']); ?>" required>
        </div>
        <div class="form-group">
          <label class="form-label"><?= __('Page Content <small>Full HTML Supported</small>'); ?></label>
          <textarea rows="10" name="content_body" id="content_body" class="form-control" required><?= sp_post('content_body', $t['page.content_body']); ?></textarea>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card">
      <div class="card-header"><h3 class="card-title"><?= __('Page Meta'); ?></h3></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label" for="content_slug"><?= __('Page Slug'); ?></label>
          <input type="text" value="<?= sp_post('content_slug', $t['page.content_slug']); ?>" name="content_slug" id="content_slug" class="form-control" maxlength="200">
          <small class="form-text text-muted"><?= __('Unique URL slug. Leave empty to generate automatically
'); ?></small>
        </div>

        <?php if ($t['custom_template']) : ?>
        <div class="form-group">
          <label for="custom_template">
            <?= __('Custom template found at:'); ?>
          </label>
          <input type="text" id="custom_template" class="form-control" value="<?= e_attr($t['custom_template']); ?>" disabled>
        </div>
        <?php endif; ?>
      </div>

      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?=__('Update')?></button>
      </div>
    </div>
  </div>
</form>
<?php endblock(); ?>

<?php block('body_end'); ?>
<?php insert('admin::pages/partials/script.php', ['update_page' => true]); ?>
<?php endblock(); ?>

<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Update Page'),
      'body_class' => 'pages pages-create',
      'page_heading' => __('Update Page'),
      'page_subheading' => __('Modify existing page.'),
    ]
);
