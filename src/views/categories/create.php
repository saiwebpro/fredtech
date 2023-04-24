<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('categories'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?=$t['csrf_html']?>
      <div class="card-body">
        
        <div class="form-group">
          <label class="form-label" for="category_name"><?= __('Category Name'); ?></label>
          <input type="text" name="category_name" id="category_name" class="form-control" value="<?= sp_post('category_name'); ?>" maxlength="200" required>
          <div class="form-text text-muted">
            <?= __('The category name'); ?>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="category_slug"><?= __('Category Slug'); ?></label>
          <input type="text" name="category_slug" id="category_slug" class="form-control" value="<?= sp_post('category_slug'); ?>" maxlength="200">
          <div class="form-text text-muted">
            <?= __('Unique slug for category'); ?>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label" for="category_icon"><?= __('Category Icon'); ?></label>
          <input name="category_icon" id="category_icon" class="form-control" value="<?= sp_post('category_icon'); ?>">
          
            <?php if (current_user_can('manage_gallery')) : ?>
            <span class="form-text text-muted"><?= __('You may provide a URL or upload via the uploader given below.'); ?></span>
            <div id="img-uploader" class="dz my-5">
              <div class="dz-message dz-small"><strong>
                <?= __('Drop image here or click to upload.'); ?></strong>
              </div>
            </div>
            <?php endif; ?>
        </div>

            <div class="form-group">
            <label class="form-label" for="category_order"><?= __('Category Order'); ?></label>
            <input type="number" name="category_order" id="category_order" class="form-control" value="<?= sp_post('category_order', $t['order']); ?>" maxlength="10" required>
            <div class="form-text text-muted">
                <?= __('Category order, this will be used for homepage listing'); ?>
            </div>
            </div>
        
        <div class="form-group d-none">
          <label class="form-label" for="category_feat_at_home"><?= __('Feature category at homepage'); ?></label>
          <label class="custom-switch mt-3">
            <input type="hidden" name="category_feat_at_home" value="0">
            <input type="checkbox" id="category_feat_at_home" name="category_feat_at_home" value="1" class="custom-switch-input" <?php checked(1, (int) sp_post('category_feat_at_home', '1')); ?>>
            <span class="custom-switch-indicator"></span>
            <span class="custom-switch-description"> <?= __('Feature category at homepage'); ?></span>
          </label>
          <span class="form-text text-muted"><?= __('If enabled latest posts from this category will be shown on homepage'); ?></span>
        </div>

      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?=__('Create')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>
<?php block('body_end'); ?>
<script type="text/javascript">
  $(function () {
    $("#img-uploader").dropzone({
      url: "<?= url_for('dashboard.gallery.create_post'); ?>",
      maxFileSize: <?= format_bytes(get_max_upload_size()); ?>,
      acceptedFiles: 'image/*',
      params: {
        csrf_token: "<?= $t['csrf_token']; ?>",
      },
      success: function (dropzone, response) {
        if (response.content_url) {
          $('#category_icon').val(response.content_relative_url).focus();
        }
      },
    });

    var slug_input = $('#category_slug');
    var title_input = $('#category_name');

    title_input.on('keyup', function (e) {
      slug = title_input.val();
      slug = $spark.slugify(slug);
      slug_input.val(slug);
      return true;
    });

    slug_input.on('blur', function (e) {
      slug = slug_input.val();
      slug = $spark.slugify(slug);
      slug_input.val(slug);
      return true;
    });

  });
</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Create Category'),
    'body_class' => 'categories categories-create',
    'page_heading' => __('Create Category'),
    'page_subheading' => __('Add a new category.'),
    ]
);
