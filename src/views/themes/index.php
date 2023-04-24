<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?php echo sp_alert_flashes('themes'); ?>

    <div class="p-1 mb-3">
      <div class="row align-items-center">

        <div class="col-4 text-left">
          <?php
          /**
           * @event Fires before the "Add new" button at theme list page
           */
          do_action('dashboard.themes.create_before');
          ?>
          <a href="<?php echo e_attr(url_for('dashboard.themes.create')); ?>" class="btn btn-success"><?php echo svg_icon('add', 'mr-1'); ?><?php echo __('Add new');?></a>
          <?php
          /**
           * @event Fires after the "Add new" button at theme list page
           */
          do_action('dashboard.themes.create_after');
          ?>
        </div>

        <div class="col-8 ml-lg-auto text-right">
          <?php
          /**
           * @event Fires before the sort buttons at theme list page
           */
          do_action('dashboard.themes.sort_before');
          ?>

          <?php
          /**
           * @event Fires after the sort buttons at theme list page
           */
          do_action('dashboard.themes.sort_after');
          ?>
        </div>


      </div>
    </div>
  </div>
</div>

<?php if (!empty($t['list_entries'])) :?>
  <div class="row row-cards row-deck">
    <?php foreach ($t['list_entries'] as $theme => $item) :?>
      <div class="col-md-4">
        <div class="card">
          <div class="row">
            <div class="col-4">
              <div class="p-3 h-100">
                <img src="<?php echo e_attr($item['icon']); ?>" class="m-auto rounded shadow-sm">
              </div>
            </div>
            <div class="col-8">
              <div class="py-3 px-2">
              <span class="lead d-block text-truncate"><?php echo $item['meta']['name']; ?></span>
                <p class="text-muted small"><?php echo limit_string($item['meta']['description'], 200, '<span  tabindex="0" class="form-help" data-toggle="popover" data-trigger="focus" data-placement="auto" data-html="true" data-content="<p>' . js_string($item['meta']['description']) .'</p>
                              ">...</span>'); ?></p>
              </div>
            </div>
          </div>
          <div class="card-footer">

                    <?php
                    /**
                    * @event Fires before themes actions
                    *
                    * @param string $theme The theme name
                    * @param array  $item   The theme information
                    */
                    do_action('dashboard.themes.actions_before', [$theme, $item]);
                    ?>
                <?php if ($item['active']) : ?>
                    <button class="btn btn-sm btn-success disabled"><?php echo svg_icon('checkmark', 'mr-1'); ?><?php echo __('Currently Active'); ?></button>
                    <?php if (theme_has_options($theme)) : ?>

                              <a href="<?php echo e_attr(url_for('dashboard.settings.theme', ['theme' => $theme])); ?>" class="btn btn-sm btn-outline-primary"><?php echo svg_icon('options', 'mr-1'); ?>
                                <?php echo __('Options'); ?></a>
                    <?php endif; ?>
                <?php else : ?>
                  <form class="d-inline-block" method="post" action="<?php echo e_attr(url_for('dashboard.themes.apply', ['theme' => $theme])); ?>">
                    <?php echo $t['csrf_html']; ?>
                 <button type="submit" class="btn btn-sm btn-success"><?php echo svg_icon('checkmark', 'mr-1'); ?><?php echo __('Activate'); ?></button>
                  </form>
                <a href="<?php echo e_attr(url_for('dashboard.themes.delete', ['theme' => $theme])); ?>" class="btn btn-sm btn-outline-danger delete-entry"><?php echo svg_icon('trash', 'mr-1'); ?><?php echo __('Delete'); ?></a>
                <?php endif; ?>

                    <?php
                    /**
                    * @event Fires after themes actions
                    *
                    * @param string $theme The theme name
                    * @param array  $item   The theme information
                    */
                    do_action('dashboard.themes.actions_after', [$theme, $item]);
                    ?>
              </div>
            </div>
          </div>
    <?php endforeach; ?>
      </div>
<?php else : ?>
<?php endif; ?>

        <?php
        /**
         * @event Fires before themes footer
         */
        do_action('dashboard.themes.footer_before');
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
         * @event Fires before themes footer
         */
        do_action('dashboard.themes.footer_after');
        ?>

    <?php endblock(); ?>

    <?php block('body_end'); ?>
    <script type="text/javascript">
      $(function() {
        $('.delete-entry').on('click', function (e) {
          e.preventDefault();
          var endpoint = $(this).attr('href');

          lnv.confirm({
            title: '<?php echo __("Confirm Deletion"); ?>',
            content: '<?php echo __("Are you sure you want to delete this theme?"); ?>',
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
        'title' => __('Themes'),
        'body_class' => 'themes themes-list',
        'page_heading' => __('Themes'),
        'page_subheading' => __('Manage themes.'),
        ]
    );
