<?php
/**
 * Base Skeleton for Admin Dashboard's non logged pages
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
<div class="page">
      <div class="page-single">
        <div class="container">
          <div class="row">
            <div class="col col-login mx-auto"><div class="auth-wrap px-6">
              <div class="py-1 mb-2">
                <a href="<?= e_attr(base_uri()); ?>"><img src="<?= e_attr(sp_logo_uri()); ?>" style="width:100px;height:auto" alt="<?= e_attr(get_option('site_name')); ?>"></a>
              </div>

                <?= sp_alert_flashes('account', true, false); ?>

              <form class="mb-3" action="<?= sp_current_form_uri(); ?>" method="post" id="account-form" data-parsley-validate>
                <?= $t['csrf_html']; ?>
                <div class="py-1">
                  <div class="card-title"><?= $t['form_heading']; ?></div>
                    <?php
                  /**
                   * @event Fires before dashboard's non logged form content
                   */
                    do_action('dashboard.nonlogged.form_content_before'); ?>
                    <?php section('form_content'); ?>
                    <?php
                  /**
                   * @event Fires after dashboard's non logged form content
                   */
                    do_action('dashboard.nonlogged.form_content_after'); ?>
                </div>
              </form>
              <hr>
                <?php section('form-after'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- .page -->

    <?php sp_footer(); ?>
    <?php do_action('dashboard.footer_assets'); ?>
    <?php section('body_end'); ?>
    <?php insert('admin::partials/html_foot.php'); ?>
</body>
</html>
