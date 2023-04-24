<?php block('content'); ?>
<div class="row row-cards row-deck">
  <?php if (current_user_can('manage_posts')) : ?>
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-status bg-danger"></div>
      <div class="card-header">
        <?= __('Delete Old Posts'); ?>
      </div>
      <form method="post" action="<?= e_attr(url_for('dashboard.posts.delete_old')); ?>" class="m-0 p-0" data-parsley-validate>
        <?= $t['csrf_html']; ?>
        <div class="card-body">
          <div class="form-group">
            <label class="form-control-label" for="num_of_days"><?= __('Delete posts older than:'); ?></label>
            <div class="input-group">
              <input type="number" class="form-control" name="num_of_days" id="num_of_days" min="1" value="<?= sp_post('num_of_days', 1); ?>"  maxlength="11">
              <div class="input-group-append">
                <span class="input-group-text"><?= __('days'); ?></span>
              </div>
            </div>
            <span class="form-text text-muted small">
                <?= __('You can delete old imported posts manually from here.'); ?></span>
          </div>
        </div>
        <div class="card-footer text-right">
          <button type="submit" class="btn btn-danger"><?= __('Delete'); ?></button>
        </div>
      </form>
    </div>
  </div>
  <?php endif; ?>
  <?php if (current_user_can('change_settings')) : ?>
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-status bg-warning"></div>
      <div class="card-header">
        <?= __('Setup Cron Job'); ?>
      </div>
      <div class="card-body">
        <?= __('Dont forget to set-up cron jobs otherwise auto news import won\'t work.'); ?>
      </div>
      <div class="card-footer text-right">
        <a href="<?= e_attr(url_for('dashboard.settings', ['type' => 'debug'])); ?>" class="btn btn-success"><?= __('Go now'); ?></a>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-status bg-primary"></div>
      <div class="card-header">
        <?= __('Want Disqus Comments?'); ?>
      </div>
      <div class="card-body">
        <?= __('You can enable Disqus comments on the articles if you want by filling the Disqus URL.'); ?>
      </div>
      <div class="card-footer text-right">
        <a href="<?= e_attr(url_for('dashboard.settings', ['type' => 'services'])); ?>#disqus_enabled" class="btn btn-primary"><?= __('Set now'); ?></a>
      </div>
    </div>
  </div>
    <div class="col-sm-6 col-lg-3">
    <div class="card">
      <div class="card-status bg-primary"></div>
      <div class="card-header">
        <?= __('Want Facebook Comments?'); ?>
      </div>
      <div class="card-body">
        <?= __('You can enable Facebook comments on the articles if you want by filling the Facebook App Id.'); ?>
      </div>
      <div class="card-footer text-right">
        <a href="<?= e_attr(url_for('dashboard.settings', ['type' => 'services'])); ?>#fb_comments_enabled" class="btn btn-info"><?= __('Set now'); ?></a>
      </div>
    </div>
  </div>
<?php endif; ?>
</div>
<?php endblock(); ?>

<?php
// Extends the base skeleton
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Dashboard'),
    'body_class' => 'dashboard dashboard-index',
    'page_heading' => __('Dashboard'),
    'page_subheading' => __('Site overview'),
    ]
);
