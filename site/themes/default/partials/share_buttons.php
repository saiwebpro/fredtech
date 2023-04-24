<div class="post-single-share py-1 px-md-4 px-0 text-md-left text-center">
    <a href="https://facebook.com/sharer/sharer.php?u=<?= e_attr(get_current_route_uri()); ?>" class="btn btn-facebook rounded-0 btn-share" data-toggle="tooltip" title="<?= e_attr(__('Share on Facebook', _T)); ?>" target="_blank" rel="noopener">
        <?= svg_icon('facebook'); ?>
    </a>
    <a href="http://twitter.com/share?text=<?= e_attr($t['post.post_title']); ?>&url=<?= e_attr(get_current_route_uri()); ?>" class="btn btn-twitter rounded-0 btn-share" data-toggle="tooltip" title="<?= e_attr(__('Share on Twitter', _T)); ?>" target="_blank" rel="noopener">
        <?= svg_icon('twitter'); ?>
    </a>
    <a href="https://api.whatsapp.com/send?text=<?= e_attr(get_current_route_uri()); ?>" class="btn btn-whatsapp rounded-0 btn-share" data-toggle="tooltip" title="<?= e_attr(__('Send via Whatsapp', _T)); ?>" target="_blank" rel="noopener">
        <?= svg_icon('whatsapp'); ?>
    </a>
    <a href="http://vk.com/share.php?url=<?= e_attr(get_current_route_uri()); ?>&title=<?= e_attr($t['post.post_title']); ?>" class="btn btn-vk rounded-0 btn-share" data-toggle="tooltip" title="<?= e_attr(__('Share on VK', _T)); ?>" target="_blank" rel="noopener">
        <?= svg_icon('vk'); ?>
    </a>
</div>
