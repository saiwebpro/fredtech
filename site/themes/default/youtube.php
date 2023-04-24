<?php block('content'); ?>
<div class="container px-0 px-md-3 pb-4">
  <div class="row no-gutters">
    <div class="col-12 col-md-2 p-0">
        <div class="sidebar sidebar-left">
            <?php insert('partials/site_sidebar_left.php'); ?>
        </div>
    </div>

    <div class="col-12 col-md-7">
        <?php if ($t['video_info']) : ?>
            <div class="embed-responsive embed-responsive-16by9">
                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?= e_attr($t['video_id']); ?>?autoplay=0&fs=1&iv_load_policy=3&showinfo=1&rel=0" allowfullscreen></iframe>
            </div>

            <h3 class="site-heading"><span><?= e($t['video_info']->getTitle()); ?></span></h3>
            <ul class="list-unstyled video-description px-2">
                <li><?= __('Uploader:', _T); ?>&nbsp;<span class="text-muted"><?= e($t['video_info']->getAuthor()); ?></span></li>
                <li><?= __('Duration:', _T); ?>&nbsp;<span class="text-muted"><?= e($t['video_info']->getDuration()); ?></span></li>
                <li><?= __('Views:', _T); ?>&nbsp;<span class="text-muted"><?= e($t['video_info']->getViews()); ?></span></li>
                <li><?= __('Rating:', _T); ?>&nbsp;<span class="text-muted"><?= e($t['video_info']->getRating()); ?></span></li>
                <?php if (has_items($t['video_info']->getKeywords())) : ?>
                <li><?= __('Keywords:', _T); ?>&nbsp;<span class="text-muted"><?= e(join(', ', $t['video_info']->getKeywords())); ?></span></li>
            <?php endif; ?>
            </ul>

            <h3 class="site-heading"><span><?= __('Download Links', _T); ?></span></h3>
            <div class="download-links px-2 mb-3">
             <?php
             $i = 0;
             foreach ($t['download_links'] as $itag => $v) : ?>
             <?php
             $class = 'outline-success';
             if ($i % 2 == 0) {
                $class = 'success';
            }
            $i++;
            ?>
            <a href="<?=$v->getLink()?>" rel="nofollow" class="btn btn-<?= e_attr($class); ?> btn-block">
                <?=strtoupper($v->getExtension())?> -
                <?php if ($v->isAudioOnly()) : ?>
                    <?=$v->getAudioBitrate()?>
                    <span> [AUDIO ONLY]</span>
                <?php elseif ($v->isVideoOnly()) : ?>
                    <?=$v->getHeight()?>P
                    <span> [VIDEO ONLY]</span>
                <?php elseif ($v->hasBoth()) : ?>
                    <?=$v->getHeight()?>P
                    <span> [FULL VIDEO]</span>
                <?php endif;?>
                - <?php echo $v->getSize(); ?>
            </a>
        <?php endforeach; ?>
    </div>
<?php else : ?>
    <?php insert('partials/empty.php'); ?>
<?php endif; ?>
</div>
<div class="col-12 col-md-3 p-0 px-md-1">
    <div class="sidebar sidebar-right">
        <?php insert('partials/site_sidebar_right.php'); ?>
    </div>
</div>
</div>
</div>
<?php endblock(); ?>
<?php
extend(
    'layouts/basic.php',
    [
        'body_class' => 'download',
    ]
);

