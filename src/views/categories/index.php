<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?= sp_alert_flashes('categories'); ?>

    <div class="px-1 pb-2">
      <div class="row align-items-center">

        <div class="col-4 text-left">
            <?php
            /**
            * @event Fires before the "create" button at category list page
            */
            do_action('dashboard.categories.create_before');
            ?>
          <a href="<?= e_attr(url_for('dashboard.categories.create')); ?>" class="btn btn-success"><?= svg_icon('add', 'mr-2'); ?><?= __('Create');?></a>
            <?php
            /**
            * @event Fires after the "create" button at category list page
            */
            do_action('dashboard.categories.create_after');
            ?>
        </div>

        <div class="col-8 ml-lg-auto text-right">
            <?php
            /**
            * @event Fires before the sort buttons at role list page
            */
            do_action('dashboard.categories.sort_before');
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
            do_action('dashboard.categories.sort_after');
            ?>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover table-outline table-vcenter text-nowrap card-table">
          <thead>
            <tr>
              <th><?= __('Category Name'); ?></th>
              <th><?= __('Category Order'); ?></th>
              <th><?= __('Category Icon'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $item) :?>
                <tr>
                  <td>
                    <div><?= e($item['category_name']); ?></div>
                  </td>
                  <td>
                    <?= e($item['category_order']); ?>
                  </td>
                  <td>
                    <img src="<?= e_attr(category_icon_url($item['category_icon'])); ?>" style="width:30px;height:30px">
                  </td>
                  <td class="text-center">

                    <?php
                    /**
                    * @event Fires before categories actions
                    *
                    * @param array  $item   The role information
                    */
                    do_action('dashboard.categories.actions_before', $item);
                    ?>

                          <a href="<?= e_attr(url_for('dashboard.categories.update', ['id' => $item['category_id']])); ?>"
                            class="btn btn-sm btn-outline-dark">
                            <?= svg_icon('create', 'mr-1'); ?> <?= __('Edit'); ?>
                            </a>
                          <a href="<?= e_attr(url_for('dashboard.categories.delete', ['id' => $item['category_id']])); ?>"
                            class="delete-entry btn btn-sm btn-danger ">
                            <?= svg_icon('trash', 'mr-1'); ?> <?= __('Delete'); ?>
                          </a>

                          <?php
                            /**
                            * @event Fires after categories actions
                            *
                            * @param array  $item   The role information
                            */
                            do_action('dashboard.categories.actions_after', $item);
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
         * @event Fires before categories table footer
         */
        do_action('dashboard.categories.table.footer_before');
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
         * @event Fires after categories table footer
         */
        do_action('dashboard.categories.table.footer_after');
        ?>
    </div>

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
        content: '<?= __("Are you sure you want to delete this category?"); ?>',
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
  });
</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Categories'),
    'body_class' => 'categories categories-list',
    'page_heading' => __('Categories'),
    'page_subheading' => __('Manage categories.'),
    ]
);
