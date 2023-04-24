<div class="text-center py-4 bg-white shadow">
    <?= svg_icon('sad', 'svg-xl mb-2 text-danger'); ?>

    <h4><?= $t->get('empty_heading', __('nothing-found', _T)); ?></h4>
    <p class="text-muted">
        <?= $t->get('empty_message', __('nothing-found-text', _T)); ?>
    </p>
    <p>
        <button class="btn btn-primary" onclick="document.location.reload(true);"><?= __('refresh', _T); ?></button>
    </p>
</div>
