<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?= sp_alert_flashes('roles'); ?>

    <div class="p-1 mb-3">
      <div class="row align-items-center">

        <div class="col-4 text-left">
            <?php
            /**
            * @event Fires before the "create" button at role list page
            */
            do_action('dashboard.roles.create_before');
            ?>
          <a href="<?= url_for('dashboard.roles.create'); ?>" class="btn btn-success"><?= svg_icon('add', 'mr-2'); ?><?= __('Create');?></a>

            <?php
            /**
            * @event Fires after the "create" button at role list page
            */
            do_action('dashboard.roles.create_after');
            ?>
        </div>
        <div class="col-8 ml-lg-auto text-right">
            <?php
          /**
           * @event Fires before the sort buttons at role list page
           */
            do_action('dashboard.roles.sort_before');
            ?>
            <?php if (!empty($t['sorting_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                <?= e(sprintf(__('Sort: %s'), sp_sort_label($t['sort_type']))); ?>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <?php foreach ($t['sorting_rules'] as $sort) : ?>
                  <a href="?<?= e_attr("page={$t['current_page']}&sort={$sort}{$t['query_str']}"); ?>" class="dropdown-item <?= $sort == $t['sort_type'] ? 'active' : '' ?>">
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
            do_action('dashboard.roles.sort_after');
            ?>
        </div>


      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover table-outline text-nowrap table-vcenter card-table">
          <thead>
            <tr>
              <th><?= __('ID'); ?></th>
              <th><?= __('Role Name'); ?></th>
              <th><?= __('Permissions'); ?></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $item) :?>
                <tr>
                  <td>
                    <div>
                      <?= e($item['role_id']); ?>
                    </div>
                  </td>
                  <td>
                    <div>
                      <?php if ($item['is_protected']) : ?>
                            <?= svg_icon(
                                'lock',
                                'text-danger',
                                ['data-toggle' => 'tooltip', 'title' => __('Protected'), 'data-placement' => 'auto']
                            );?>
                      <?php endif; ?>
                      <?= e($item['role_name']); ?>

                    </div>
                  </td>
                  <td>
                    <div class="tags">
                      <?= sp_array_wrap(
                          $item['permissions'],
                          '<span class="tag tag-success">',
                          '</span>',
                          3,
                          '<span class="tag">' . __('none') . '</span>',
                          '<span class="tag bg-transparent">' . __('...and %d more') . '</span>'
                      ); ?>
                    </div>
                  </td>
                  <td class="text-center">
                    <?php
                    /**
                    * @event Fires before roles actions
                    *
                    * @param array  $item   The role information
                    */
                    do_action('dashboard.roles.actions_before', $item);
                    ?>
                          <a href="<?= e_attr(url_for('dashboard.roles.update', ['id' => $item['role_id']])); ?>"
                            class="btn btn-sm btn-outline-dark <?= !current_user_can('edit_role') ? 'disabled' : ''; ?>">
                            <?= svg_icon('create', 'mr-1'); ?> <?= __('Edit'); ?>
                          </a>
                          <a href="<?= e_attr(url_for('dashboard.roles.delete', ['id' => $item['role_id']])); ?>"
                            class="btn btn-sm btn-danger <?= $item['is_protected'] || !current_user_can('delete_role') ? 'disabled' : 'delete-entry'; ?>">
                            <?= svg_icon('trash', 'mr-1'); ?> <?= __('Delete'); ?>
                          </a>
                          <a class="btn btn-sm btn-outline-primary" href="<?= e_attr(url_for('dashboard.users')); ?>?role_id=<?=$item['role_id']?>">
                            <?= svg_icon('contacts', 'mr-1'); ?> <?= __('Users'); ?>
                          </a>
                          <?php
                            /**
                            * @event Fires after roles actions
                            *
                            * @param array  $item   The role information
                            */
                            do_action('dashboard.roles.actions_after', $item);
                            ?>
                  </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="7" class="p-0"><div class="alert alert-light m-0 border-0"><?= __('No entries found'); ?></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- ./table-responsive -->

      <div class="card-footer">
        <?php
        /**
         * @event Fires before roles table footer
         */
         do_action('dashboard.roles.table.footer_before');
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
        <?php
        /**
         * @event Fires after roles table footer
         */
        do_action('dashboard.roles.table.footer_after');
        ?>
      </div>
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
        content: '<?= __("Are you sure you want to delete this role?"); ?>',
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
    'title' => __('User Roles'),
    'body_class' => 'roles role-list',
    'page_heading' => __('User Roles'),
    'page_subheading' => __('Manage user roles.'),
    ]
);
