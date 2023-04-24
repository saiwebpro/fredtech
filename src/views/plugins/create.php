<?php block('content'); ?>
<div class="row ">
  <div class="container">
    <?php echo sp_alert_flashes('plugins'); ?>
    <div class="py-5 px-4 text-center">
      <?php echo svg_icon('outlet', 'text-muted', ['style' => 'height:5rem;width:5rem']); ?>
    </div>
    <form method="post" action="?" enctype="multipart/form-data" class="card">
      <?php echo $t['csrf_html']?>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label" for="plugin_archive"><?php echo __('Choose Plugin Package'); ?></label>
          <div class="custom-file">
            <input type="file" class="custom-file-input" name="plugin_archive" id="plugin_archive" accept="application/zip" required>
            <label class="custom-file-label"><?php echo __('Choose file'); ?></label>
          </div>
        </div>
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-secondary ml-auto"><?php echo __('Upload')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>
<?php
extend(
  'admin::layouts/skeleton.php',
  [
    'title' => __('Add New Plugin'),
    'body_class' => 'plugins plugins-create',
    'page_heading' => __('Add New Plugin'),
    'page_subheading' => __('Upload a plugin.'),
    'page_heading_classes' => 'container'
  ]
);
