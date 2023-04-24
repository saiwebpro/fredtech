<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('gallery'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?=$t['csrf_html']?>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label" for=""><?= __(''); ?></label>
          <input type="text" name="" id="" class="form-control">
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
      'title' => __('Update Gallery'),
      'body_class' => 'gallery gallery-create',
      'page_heading' => __('Update Gallery'),
      'page_subheading' => __('Modify existing gallery.'),
    ]
);
