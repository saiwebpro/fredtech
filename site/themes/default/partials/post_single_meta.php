
        <div class="post-single-meta py-2 px-md-4 px-2 text-muted">
            <img src="<?= feed_logo_url($t['post.feed_logo_url']); ?>" class="feed-logo-img">
            <?= svg_icon('time', 'ml-1'); ?>
            <?= time_ago($t['post.post_pubdate'], _T); ?>

            <?= svg_icon('eye-outline'); ?>
            <?= localize_numbers($t['post.post_hits'], _T); ?>

            <?php if (current_user_can('manage_posts')) : ?>
                <a href="<?= e_attr(url_for('dashboard.posts.update', ['id' => $t['post.post_id']])); ?>" class="btn btn-success btn-sm ml-1">
                    <?= svg_icon('create'); ?>
                    <?= __('edit', _T); ?>
                </a>
                <a href="<?= e_attr(url_for('dashboard.posts.delete', ['id' => $t['post.post_id']])); ?>" class="btn btn-outline-danger btn-sm ml-1">
                    <?= svg_icon('trash'); ?>
                    <?= __('delete', _T); ?>
                </a>
            <?php endif; ?>
        </div>
