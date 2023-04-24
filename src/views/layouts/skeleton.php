<?php
/**
 * Base Skeleton for Admin Dashboard
 *
 * @view \weed\admin
 */
?>
<!doctype html>
<html>
<head>
    <?php insert('admin::partials/html_head.php'); ?>
</head>
<body class="<?= e_attr($t['body_class']); ?>">
    <?php insert('admin::partials/svg_sprites.svg'); ?>
    <?php insert('admin::partials/overlays.php'); ?>
    <?php insert('admin::partials/navbar.php'); ?>
    <!-- Start Body Wrapper -->
    <div id="wrapper" class="row-offcanvas">

        <!-- Start Sidebar -->
        <div id="sidebar" class="shadow">
            <?php insert('admin::partials/sidebar.php'); ?>
        </div>
        <!-- #/sidebar -->

        <!-- Content -->
        <div id="content">

            <!-- Content overlay -->
            <div id="overlay"></div>

            <!-- Body Content -->
            <div id="body-content">

<?php if (is_admin() && $t['updates.available']) : ?>
    <div class="px-lg-4 px-0 py-2">
        <div class="alert alert-light bg-white shadow border-primary" role="alert" style="border:0;border-left:4px solid">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
          <span class="lead"><?= __('Update available!'); ?></span><br>
          A new version of <strong><?= APP_NAME ?></strong> (<em><?= $t['updates.latest_version'] . ' ' . $t['updates.latest_version_codename']; ?></em>) was released <?= time_ago($t['updates.updated_at']); ?>. Please download and migrate to the latest version as soon as possible.
          <hr>
          <a href="<?= e_attr(!empty($t['updates.download_uri']) ? $t['updates.download_uri'] : $t['updates.download_page']); ?>" class="btn btn-sm btn-primary" target="_blank">Download</a>
      </div>
  </div>
<?php endif; ?>

<?php if ($t['parent_tabs_key']) : ?>
    <?= sp_render_tabs($t['parent_tabs_key'], 'mb-2 flex-column flex-lg-row d-none d-md-flex px-1 mx-2'); ?>
<?php endif; ?>


                <!-- Page Heading -->
                <div class="<?php echo e_attr($t->get('page_header_classes', 'page-header-container')); ?>">
                <div class="page-header dash-header m-0 px-lg-4 px-0 py-5 m-0 <?php echo e_attr($t['heading_classes']); ?>">
                  <h1 class="page-title col-md-6">
                    <?php echo$t['page_heading']?>
                    <?php if ($t['page_subheading']) : ?>
                        <small class="d-block d-lg-inline text-truncate"><?php echo$t['page_subheading']?></small>
                    <?php endif;?>
                </h1>

                <?php echo breadcrumb_render('<div class="col-md-6"><ol class="breadcrumb small bg-transparent m-lg-0 p-0 mt-2 float-md-right">', '</ol></div>'); ?>
            </div>
        </div>
                <!-- Page Content -->
                <div class="container-fluid content-section px-lg-5">
                    <?= sp_alert_flashes('dashboard'); ?>
                    <div id="global-xhr-response"></div>
  
                    <?php
                    /**
                     * @event Fires before dashboard content section
                     */
                    do_action('dashboard.content_before');
                    ?>
                    <?php section('content', __('No content block found')); ?>
                    <?php
                    /**
                     * @event Fires after dashboard content section
                     */
                    do_action('dashboard.content_after');
                    ?>

                </div>
                <!-- ./content-section -->

            </div>
            <!-- #/body-content -->
        </div>
        <!-- #/content -->

        <?php insert('admin::partials/footer.php'); ?>
    </div>
    <!-- #/wrapper -->

    <?php sp_footer(); ?>
    <?php do_action('dashboard.footer_assets'); ?>
    <?php section('body_end'); ?>
    <?php insert('admin::partials/html_foot.php'); ?>
</body>
</html>
