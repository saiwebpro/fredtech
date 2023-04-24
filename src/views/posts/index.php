<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?= sp_alert_flashes('posts'); ?>

    <div class="px-1 pb-2">
      <div class="row align-items-center">

        <div class="col-12 col-md-4 text-left">
            <?php
            /**
            * @event Fires before the "create" button at post list page
            */
            do_action('dashboard.posts.create_before');
            ?>
          <a href="<?= e_attr(url_for('dashboard.posts.create')); ?>" class="btn btn-success d-block d-md-inline-block"><?= svg_icon('add', 'mr-2'); ?><?= __('Create');?></a>
            <?php
            /**
            * @event Fires after the "create" button at post list page
            */
            do_action('dashboard.posts.create_after');
            ?>
        </div>

        <div class="col-12 col-md-8 ml-lg-auto text-md-right text-left">
            <form class="d-inline" method="post" action="<?= e_attr(url_for('dashboard.posts.actions_post')); ?>" id="empty-all">
              <?= $t['csrf_html']; ?>
              <button type="submit" name="action" value="flush" class="btn btn-danger" data-toggle="tooltip" title="<?= e_attr(__('WARNING: Deletes all the imported posts')); ?>"><?= svg_icon('trash', 'mr-1'); ?> <?= __('Empty Imported Posts'); ?></button>
            </form>
            <?php
            /**
            * @event Fires before the sort buttons at role list page
            */
            do_action('dashboard.posts.sort_before');
            ?>
            <?php if (!empty($t['sorting_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" id="sort-button-text">
                <?= e(sprintf(__('Sort: %s'), sp_sort_label($t['sort_type']))); ?>
              </button>
              <div class="dropdown-menu">
                <?php foreach ($t['sorting_rules'] as $sort) : ?>
                  <a href="?<?= e_attr("page={$t['current_page']}&sort={$sort}{$t['query_str']}"); ?>" class="dropdown-item <?= $sort === $t['sort_type'] ? 'active' : '' ?>">
                    <?= e(sp_sort_label($sort)); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
            <?php
            /**
            * @event Fires after the sort buttons at role list page
            */
            do_action('dashboard.posts.sort_after');
            ?>
        </div>
      </div>
    </div>

    <form class="card" id="multi-form" method="post" action="<?= e_attr(url_for('dashboard.posts.actions_post')); ?>">
        <?= $t['csrf_html']; ?>
      <div class="table-responsive">
        <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
          <thead>
            <tr>
            <th class="w-1 text-center"><label class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" id="check-all"><span class="custom-control-label"></span>
              </label></th>
              <th><?= __('Post'); ?></th>
              <th><?= __('Category'); ?></th>
              <th><?= __('Source'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $item) :?>
                <tr>
                     <td>
                       <label class="custom-control custom-checkbox custom-control-inline">
              <input type="checkbox" class="custom-control-input" name="item_multi[]" value="<?= e_attr($item['post_id']); ?>"><span class="custom-control-label"></span>
              </label>
                     </td>
                  <td>
                    <div>
                      <div style="max-width:250px;" class="text-truncate"><?= e($item['post_title']); ?></div>
                      <div class="text-muted"><?= __('Hits:'); ?> <?= e_attr($item['post_hits']); ?></div>
                    </div>
                  </td>
                  <td>
                    <div><a href="?<?= e_attr(request_build_query(['category_id'], 1)); ?>category_id=<?= e_attr($item['category_id']); ?>"><?= e($item['category_name']); ?></a></div>
                  </td>
                  <td>
                    <div>
                      <strong><?= e($item['feed_name']); ?></strong><br>
                      <small>
                      <?= __('Imported:'); ?> <span class="text-muted"><?= time_ago($item['created_at']); ?></span><br>
                      <?= __('Original Publish Date:'); ?> <span class="text-muted"><?= date('M d, Y h:i A', $item['post_pubdate']); ?></span></small>
                    </div>
                  </td>
                  <td class="text-center">

                    <?php
                    /**
                    * @event Fires before posts actions
                    *
                    * @param array  $item   The role information
                    */
                    do_action('dashboard.posts.actions_before', $item);
                    ?>

                          <a href="<?= e_attr(url_for('dashboard.posts.update', ['id' => $item['post_id']])); ?>"
                            class="btn btn-sm btn-outline-dark">
                            <?= svg_icon('create', 'mr-1'); ?> <?= __('Edit'); ?>
                            </a>
                          <a href="<?= e_attr(url_for('dashboard.posts.delete', ['id' => $item['post_id']])); ?>"
                            class="delete-entry btn btn-sm btn-danger ">
                            <?= svg_icon('trash', 'mr-1'); ?> <?= __('Delete'); ?>
                          </a>

                          <?php
                            /**
                            * @event Fires after posts actions
                            *
                            * @param array  $item   The role information
                            */
                            do_action('dashboard.posts.actions_after', $item);
                            ?>
                  </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="7" class="p-0"><div class="alert alert-light m-0 rounded-0 border-0"><?= __('No entries found'); ?></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- ./table-responsive -->

      <div class="card-footer">

        <?php
        /**
         * @event Fires before posts table footer
         */
        do_action('dashboard.posts.table.footer_before');
        ?>

        <div class="container">
          <div class="row align-items-end flex-row-reverse">

            <div class="col-md-4 col-xs-12 mb-5 text-right">
                <?= sprintf(__('Showing %s-%s of total %s entries.'), $t['offset'], $t['current_items'], $t['total_items']); ?>

            </div>
            <div class="col-md-8 col-xs-12 text-left">
              <nav class="table-responsive mb-2">
                <?= $t['pagination_html']; ?>
            </nav>
            </div>

          </div>
        </div>
      </div>

        <?php
        /**
         * @event Fires after posts table footer
         */
        do_action('dashboard.posts.table.footer_after');
        ?>


        <div class="px-4 py-4 bg-light border-top">
            <label class="form-label" for="action">
            <?= __('With Selected:'); ?> </label>
          <div class="d-flex">
            <select name="action" class="form-control w-25" id="action">
              <option value="delete"><?= __('Delete'); ?></option>
            </select>

            <button type="submit" class="btn btn-primary"><?= __('Apply'); ?></button>

                          </div>

        </div>
    </form>

  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function() {
    $('.delete-entry').on('click', function (e) {
      e.preventDefault();
      var endpoint = $(this).attr('href');

      lnv.confirm({
        title: '<?= __("Confirm Deletion"); ?>',
        content: '<?= __("Are you sure you want to delete this post?"); ?>',
        confirmBtnText: '<?= __("Confirm"); ?>',
        confirmHandler: function () {
          $spark.ajaxPost(endpoint, {}, function () {
            $spark.selfReload();
          });
        },
        cancelBtnText: '<?= __("Cancel"); ?>',
        cancelHandler: function() {
        }
      })
    });

    $(document).on('submit', '#empty-all', function(event) {
      if (!confirm("WARNING: This will delete ALL the Imported posts from the database, this can't be reverted, continue?")){
         event.preventDefault();
      }
    });


    // check all boxes
    $('#check-all').on('change', function () {
      var check_all = $(this);
      if (check_all.prop('checked')) {
        $("input[name='item_multi[]").prop('checked', true);
      } else {
        $("input[name='item_multi[]").prop('checked', false);
      }
    });
  });
</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Posts'),
    'body_class' => 'posts posts-list',
    'page_heading' => __('Posts'),
    ]
);
