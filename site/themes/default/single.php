<?php block('content'); ?>
<div class="container px-0 px-md-3 mt-3 post-container">
    <div class="post-single shadow no-shadow-xs bg-white py-3 mt-0">
        <h1 class="post-single-title h3 px-md-4 px-2 mb-0">
            <?= e($t['post.post_title'], false); ?>
        </h1>
        
        <?php insert('partials/post_single_meta.php'); ?>
        <?php insert('partials/share_buttons.php'); ?>

        <div class="post-single-image" style="background-image:url(<?= e_attr(feat_img_url($t['post.post_featured_image'])); ?>);">
        </div>

        <div class="post-single-content py-2 px-md-4 px-2">
            <?php
            /**
             * @hook Fires before the content
             */
            do_action('feed_content_before');
            ?>

            <div class="adblock">
                <?= get_option('ad_unit_8'); ?>
            </div>
            <?= $t['post.post_content']; ?>
            <?php
            /**
             * @hook Fires after the content
             */
            do_action('feed_content_after');
            ?>
        </div>
        <?php if (!empty($t['post.post_source'])) : ?>
            <div class="post-single-source py-2 px-3 text-center">
                <a href="<?= e($t['post.post_source'], false); ?>" class="btn btn-primary" rel="nofollow noreferrer noopener" target="_blank">
                    <?= __('read-entire-article', _T); ?>
                    <?= svg_icon('arrow-forward'); ?>
                </a>
            </div>
        <?php endif; ?>

    </div>

    <?= breadcrumb_render('<ol class="breadcrumb my-1 px-2">', '</ol>'); ?>

    <div class="single-comments py-3 px-2 clearfix">
        <?php insert('partials/disqus.php'); ?>
        <?php insert('partials/facebook.php'); ?>
    </div>
    
<?php if (has_items($t['related_posts'])) : ?>
<div class="sidebar-block mt-4 mb-2">
  <h4 class="sidebar-heading"><span><?= __('related', _T); ?></span></h4>
        <div class="row">
        <?php foreach ($t['related_posts'] as $post) : ?>
            <div class="col-md-4">
                <?php insert('partials/related_loop.php', ['loop' => $post]); ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>


    <div class="d-md-none">
        <?php insert('partials/site_sidebar_left.php'); ?>
    </div>
</div>
<?php endblock(); ?>

<?php
extend(
    'layouts/basic.php',
    [
        'body_class' => "post post-{$t['post.post_id']}",
    ]
);

