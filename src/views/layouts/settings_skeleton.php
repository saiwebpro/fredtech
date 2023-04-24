<?php block('content'); ?>
<div class="row">
  <div class="col-md-2 col-sm-3 d-none d-md-block h-100 position-relative">
    <?php echo sp_render_tabs('settings', 'mb-2 tabs-vertical flex-column d-none d-md-flex'); ?>
  </div>
  <div class="col-md-10 col-sm-9">
    <?php echo sp_alert_flashes('settings'); ?>
    <form action="<?php echo sp_current_form_uri(); ?>" enctype="multipart/form-data" method="post" class="card" id="settings-form" data-parsley-validate>
        <?php echo $t['csrf_html']?>
      <div class="card-body">
        <?php do_action("settings.form_content_before", $t['type']); ?>
        <?php section('form-content'); ?>
        <?php do_action("settings.form_content_after", $t['type']); ?>
      </div>
      <div class="card-footer text-right">
          <button type="submit" class="btn btn-secondary ml-auto" id="form-submit"><?php echo $t->get('form_btn_label', __('Save Settings'))?></button>
      </div>
    </form>

  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(document).ready(function() {
    $(document).formToggle();
});
</script>
<?php endblock(); ?>
<?php
// Extends the base skeleton
extend('admin::layouts/skeleton.php', ['page_heading_classes' => 'container']);
