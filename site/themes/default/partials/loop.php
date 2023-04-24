<?php
/**
 * Fires before basic news loop
 */
do_action('news_loop_before');
?>
<div class="post-item shadow-sm d-flex position-relative">
    <div class="d-flex p-0">
        <div class="post-feat-image d-flex align-items-center"><a href="<?= e_attr(post_url($t['loop'])); ?>" class="post-img-link" <?= post_attrs($t['loop']); ?>>
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAIAAAACCAYAAABytg0kAAAAFElEQVQYV2N8+vTpfwYGBgZGGAMAUNMHXwvOkQUAAAAASUVORK5CYII=" data-src="<?= e_attr(feat_img_url($t['loop.post_featured_image'])); ?>" alt="<?= e_attr($t['loop.post_title']); ?>" class="post-feat-img img-zoom"></a>
        </div>
        <div class="post-info px-2 mt-2 position-relative d-flex flex-column"><a href="<?= e(post_url($t['loop']), true); ?>" <?= post_attrs($t['loop']); ?>>
            <h3 class="post-title" title="<?= e_attr($t['loop.post_title']); ?>">
                <?= e(limit_string($t['loop.post_title'], 50), false); ?>
            </h3></a>

            <div class="post-footer text-muted">
                <span class="post-feed-logo d-block py-1">
                    <a href="<?= e_attr(url_for('site.archive')); ?>?feed=<?= e_attr($t['loop.feed_id']) ?>">
                        <img src="<?= feed_logo_url($t['loop.feed_logo_url']); ?>" class="feed-logo-img">
                    </a>
                </span>
            <span  class="d-block">
                <?= svg_icon('time', 'text-success'); ?>
                <?= time_ago($t['loop.post_pubdate'], _T); ?> 

                <span class="post-views">
                <?= svg_icon('eye-outline', 'text-primary'); ?>
                <?= localize_numbers($t['loop.post_hits'], _T); ?></span>


            <?php if (current_user_can('manage_posts')) : ?>
                <a href="<?= e_attr(url_for('dashboard.posts.update', ['id' => $t['loop.post_id']])); ?>" class="btn btn-success btn-sm ml-1">
                    <?= svg_icon('create'); ?>
                </a>
                <a href="<?= e_attr(url_for('dashboard.posts.delete', ['id' => $t['loop.post_id']])); ?>" class="btn btn-outline-danger btn-sm ml-1">
                    <?= svg_icon('trash'); ?>
                </a>
            <?php endif; ?>
            </span>
            </div>
        </div>


    </div>
</div>
<?php
/**
 * Fires after basic news loop
 */
do_action('news_loop_after');
?>

<?php
$t['__loop'] = $t['__loop'] + 1;

if ($t['__loop'] === 4) :
    $t['__loop'] = 0;
    ?>
<div class="adblock">
    <?= get_option('ad_unit_3'); ?>
</div>
<?php endif; ?>
