<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?php echo sp_alert_flashes('plugins'); ?>

    <div class="p-1 mb-3">
      <div class="row align-items-center">

        <div class="col-4 text-left">
            <?php
          /**
           * @event Fires before the "Add new" button at plugin list page
           */
            do_action('dashboard.plugins.create_before');
            ?>
          <a href="<?php echo e_attr(url_for('dashboard.plugins.create')); ?>" class="btn btn-success"><?php echo svg_icon('add', 'mr-1'); ?><?php echo __('Add new');?></a>
            <?php
          /**
           * @event Fires after the "Add new" button at plugin list page
           */
            do_action('dashboard.plugins.create_after');
            ?>
        </div>

        <div class="col-8 ml-lg-auto text-right">
            <?php
          /**
           * @event Fires before the sort buttons at plugin list page
           */
            do_action('dashboard.plugins.sort_before');
            ?>

            <?php
          /**
           * @event Fires after the sort buttons at plugin list page
           */
            do_action('dashboard.plugins.sort_after');
            ?>
        </div>


      </div>
    </div>

    <div class="card">
      <div class="table-responsive">
        <table class="table table-hover table-outline table-vcenter card-table">
          <thead>
            <tr>
              <th><?php echo __('Plugin'); ?></th>
              <th class="w-50"><?php echo __('Description'); ?></th>
              <th></th>
          </thead>
          <tbody>
            <?php if (!empty($t['list_entries'])) :?>
                <?php foreach ($t['list_entries'] as $plugin => $item) :?>
                <tr>
                  <td>
                    <div>
                      <span class="d-block mb-1"><?php echo e($item['meta']['name']); ?></span>
                      <span class="text-muted small">v<?php echo e($item['meta']['version']); ?> |
                      <?php echo sprintf(__('By <a href="%1$s">%2$s</a>'), e_attr($item['meta']['author_uri']), e_attr($item['meta']['author'])); ?>  |
                      <a href="<?php echo e_attr($item['meta']['uri']); ?>"><?php echo __('Details'); ?></a></span>
                    </div>
                  </td>
                  <td>
                    <div><?php echo sp_xss_filter($item['meta']['description']); ?></div>
                  </td>
                  <td class="text-right">
                    <?php
                    /**
                    * @event Fires before plugins actions
                    *
                    * @param string $plugin The plugin name
                    * @param array  $item   The plugin information
                    */
                    do_action('dashboard.plugins.actions_before', [$plugin, $item]);
                    ?>
                    <?php if ($item['active']) : ?>
                          <form action="<?php echo e_attr(url_for('dashboard.plugins.disable', ['plugin' => $plugin])); ?>" method="post" class="d-inline-block">
                            <?php echo $t['csrf_html']; ?>
                            <?php if (plugin_has_options($plugin)) : ?>
                              <a href="<?php echo e_attr(url_for('dashboard.settings.plugin', ['plugin' => $plugin])); ?>" class="btn btn-sm btn-primary"><?php echo svg_icon('options', 'mr-1'); ?>
                                <?php echo __('Options'); ?></a>
                            <?php endif; ?>
                            <button class="btn btn-sm btn-outline-danger" type="submit">
                            <?php echo svg_icon('close', 'mr-1'); ?> <?php echo __('Disable'); ?>
                            </button>
                          </form>
                    <?php else : ?>
                          <form action="<?php echo e_attr(url_for('dashboard.plugins.enable', ['plugin' => $plugin])); ?>" method="post" class="d-inline-block">
                            <?php echo $t['csrf_html']; ?>
                            <button class="btn btn-sm btn-success" type="submit">
                            <?php echo svg_icon('checkmark', 'mr-1'); ?> <?php echo __('Enable'); ?>
                            </button>
                          </form>

                          <a href="<?php echo e_attr(url_for('dashboard.plugins.delete', ['plugin' => $plugin])); ?>"
                            class="delete-entry btn btn-sm btn-outline-danger">
                            <?php echo svg_icon('trash', 'mr-1'); ?> <?php echo __('Delete'); ?>
                          </a>
                    <?php endif; ?>

                    <?php
                    /**
                    * @event Fires after plugins actions
                    *
                    * @param string $plugin The plugin name
                    * @param array  $item   The plugin information
                    */
                    do_action('dashboard.plugins.actions_after', [$plugin, $item]);
                    ?>
                  </td>
                </tr>
                <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="7" class="p-0"><div class="alert alert-light m-0 rounded-0 border-0"><?php echo __('No entries found'); ?></div></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- ./table-responsive -->

      <div class="card-footer">
        <?php
        /**
         * @event Fires before plugins table footer
         */
        do_action('dashboard.plugins.table.footer_before');
        ?>

        <div class="container">
          <div class="row align-items-end flex-row-reverse">

            <div class="col-md-4 col-xs-12 mb-5 text-right">
                <?php echo sprintf(__('Showing %s-%s of total %s entries.'), $t['offset'], $t['current_items'], $t['total_items']); ?>

            </div>
            <div class="col-md-8 col-xs-12 text-left">
              <nav class="table-responsive mb-2">
                <?php echo $t['pagination_html']; ?>
            </nav>
            </div>

          </div>
        </div>

        <?php
        /**
         * @event Fires after plugins table footer
         */
        do_action('dashboard.plugins.table.footer_after');
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
        title: '<?php echo __("Confirm Deletion"); ?>',
        content: '<?php echo __("Are you sure you want to delete this plugin?"); ?>',
        confirmBtnText: '<?php echo __("Confirm"); ?>',
        confirmHandler: function () {
          $spark.ajaxPost(endpoint, {}, function () {
            $spark.selfReload();
          });
        },
        cancelBtnText: '<?php echo __("Cancel"); ?>',
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
    'title' => __('Plugins'),
    'body_class' => 'plugins plugins-list',
    'page_heading' => __('Plugins'),
    'page_subheading' => __('Manage plugins.'),
    ]
);
