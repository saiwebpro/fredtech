 <?php if (has_items($t['related_posts'])) : ?>
  <div class="sidebar-block mt-3 mx-0">
    <h4 class="sidebar-heading"><span><?= __('related', _T); ?></span></h4>
    <div class="sidebar-body">
      <div class="row no-gutters">
        <?php foreach ($t['related_posts'] as $post) : ?>
          <div class="col-md-6">
            <?php insert('partials/related_loop.php', ['loop' => $post]); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>
