<?php block('content'); ?>
<div class="row"><div class="container">
    <?= sp_alert_flashes('posts'); ?>
    <form method="post" action="?" class="card" data-parsley-validate>
        <?=$t['csrf_html']?>
      <div class="card-body">
        
            <div class="form-group">
            <label class="form-label" for="post_title"><?= __('Post Title'); ?></label>
            <input type="text" name="post_title" id="post_title" class="form-control" value="<?= sp_post('post_title'); ?>" required>
            <div class="form-text text-muted">
                <?= __('Title for the post'); ?>
            </div>
            </div>
            <div class="form-group">
            <label class="form-label" for="post_content"><?= __('Post Content'); ?></label>
            <textarea name="post_content" id="post_content" class="form-control" required><?= sp_post('post_content'); ?></textarea>
            
            <div class="form-text text-muted">
                <?= __('Post body, HTML enabled'); ?>
            </div>
            </div>
            <div class="form-group">
            <label class="form-label" for="post_excerpt"><?= __('Post Excerpt'); ?></label>
            <textarea name="post_excerpt" id="post_excerpt" class="form-control" required><?= sp_post('post_excerpt'); ?></textarea>
            
            <div class="form-text text-muted">
                <?= __('Post excerpt'); ?>
            </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="post_category_id"><?= __('Post Category'); ?></label>
              <select name="post_category_id" id="post_category_id" class="form-control" required>
                <?php foreach ($t['categories'] as $category) :?>
                  <option value="<?= e_attr($category['category_id']); ?>" <?= selected($category['category_id'], sp_post('post_category_id')); ?>><?= e($category['category_name']); ?></option>
                <?php endforeach; ?>
              </select>
              <div class="form-text text-muted">
                <?= __('Category of the post'); ?>
              </div>
            </div>
            <div class="form-group">
            <label class="form-label" for="post_featured_image"><?= __('Post Featured Image'); ?></label>
            <input type="text" name="post_featured_image" id="post_featured_image" class="form-control" value="<?= sp_post('post_featured_image'); ?>" required>
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
      </div>
      <div class="card-footer text-right">
        <button type="submit" class="btn btn-primary ml-auto"><?=__('Create')?></button>
      </div>
    </form>
  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<?php insert('admin::posts/partials/script.php'); ?>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
      'title' => __('Create Post'),
      'body_class' => 'posts posts-create',
      'page_heading' => __('Create Post'),
      'page_subheading' => __('Add a new post.'),
    ]
);
