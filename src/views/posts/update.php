<?php block('content'); ?>
<form method="post" action="?" class="row" data-parsley-validate>
  <?=$t['csrf_html']?>
  <div class="col-md-8">
    <div class="px-lg-4 px-0">
      <?= sp_alert_flashes('posts'); ?>
      <div class="form-group">
        <label class="form-label" for="post_title"><?= __('Post Title'); ?></label>
        <input type="text" name="post_title" id="post_title" class="form-control" value="<?= sp_post('post_title', $t['post.post_title']); ?>" required>
      </div>
      <div class="form-group">
        <textarea name="post_content" id="post_content" class="form-control" required><?= sp_post('post_content', $t['post.post_content']); ?></textarea>

        <div class="form-text text-muted">
          <?= __('Post body, HTML enabled'); ?>
        </div>
      </div>


      <div class="form-group">
        <label class="form-label" for="post_excerpt"><?= __('Post Excerpt'); ?></label>
        <textarea name="post_excerpt" id="post_excerpt" class="form-control"><?= sp_post('post_excerpt', $t['post.post_excerpt']); ?></textarea>
      </div>
    </div>
  </div>

  <div class="col-md-4">
    <div class="card">
      <div class="card-body">

        <div class="form-group">
          <label class="form-label" for="post_category_id"><?= __('Post Category'); ?></label>
          <select name="post_category_id" id="post_category_id" class="form-control" required>
            <?php foreach ($t['categories'] as $category) :?>
              <option value="<?= e_attr($category['category_id']); ?>" <?= selected($category['category_id'], sp_post('post_category_id', $t['post.post_category_id'])); ?>><?= e($category['category_name']); ?></option>
            <?php endforeach; ?>
          </select>
          <div class="form-text text-muted">
            <?= __('Category of the post'); ?>
          </div>
        </div>


        <div class="form-group">
          <label class="form-label" for="post_featured_image"><?= __('Post Featured Image'); ?></label>
          <input type="text" name="post_featured_image" id="post_featured_image" class="form-control" value="<?= sp_post('post_featured_image', $t['post.post_featured_image']); ?>" required>
          <div class="form-text text-muted">
            <?= __('Featured image URL for the post'); ?>
          </div>

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
          <div class="form-label"><?= __('Post Type'); ?></div>
          <select class="form-control" name="post_type">
            <?php foreach ($t['post_types'] as $key => $label) : ?>
              <option value="<?= e_attr($key); ?>" <?= selected($key, sp_post('post_type', $t['post.post_type'])); ?>>
                <?= e($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="post_source"><?= __('Post Source'); ?></label>
          <input type="text" name="post_source" id="post_source" class="form-control" value="<?= sp_post('post_source', $t['post.post_source']); ?>">
          <div class="form-text text-muted">
            <?= __('Source link to the post'); ?>
          </div>
        </div>
      </div>
    </div>

    <div class="form-group">
      <button type="submit" class="btn btn-primary btn-block"><?=__('Update')?></button>
    </div>
  </form>
  <?php endblock(); ?>

  <?php block('body_end'); ?>
  <?php insert('admin::posts/partials/script.php'); ?>
  <?php endblock(); ?>
  <?php
  extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Update Post'),
      'body_class' => 'posts posts-create',
      'page_heading' => __('Update Post'),
      'page_subheading' => __('Modify existing post.'),
    ]
  );
