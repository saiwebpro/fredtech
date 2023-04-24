<?php block('content'); ?>
<div class="row">
  <div class="col-12">

    <?= sp_alert_flashes('gallery'); ?>

    <?php if (current_user_can('manage_gallery')) : ?>
      <form action="<?= e_attr(url_for('dashboard.gallery.create_post')); ?>" class="dropzone my-4" id="gallery">
        <div class="dz-message dz-small"><strong class="h4"><?= __('Drop files here or click to upload'); ?></strong>
          <div class="dz-allowed mt-2">
            <p>
              <small class="d-block"><?= strtoupper(join(', ', $t['allowed_filetypes'])); ?></small>
              <small class="d-block"><?= sprintf(__('Maximum file size: %s MB'), $t['max_upload_size']); ?></small>
            </p>
          </div>
        </div>
        <?= $t['csrf_html']; ?>
      </form>
    <?php endif; ?>

    <div class="px-1 py-3">
      <div class="row align-items-center">

        <div class="col-4 text-left">
            <?php if (!empty($t['mime_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown"><?= ucfirst(e($t['mime_type'])); ?>
              </button>
              <div class="dropdown-menu">
                <?php foreach ($t['mime_rules'] as $mime) : ?>
                  <a href="?<?= e_attr("page={$t['current_page']}&sort={$t['sort_type']}&type={$mime}{$t['query_str']}"); ?>" class="dropdown-item <?= $mime == $t['mime_type'] ? 'active' : '' ?>">
                    <?= ucfirst(e($mime)); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-8 ml-lg-auto text-right">
            <?php if (!empty($t['sorting_rules'])) : ?>
            <div class="dropdown">
              <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
                <?= e(sprintf(__('Sort: %s'), sp_sort_label($t['sort_type']))); ?>
              </button>
              <div class="dropdown-menu dropdown-menu-right">
                <?php foreach ($t['sorting_rules'] as $sort) : ?>
                  <a href="?<?= e_attr("page={$t['current_page']}&sort={$sort}&type={$t['mime_type']}{$t['query_str']}"); ?>" class="dropdown-item <?= $sort == $t['sort_type'] ? 'active' : '' ?>">
                    <?= e(sp_sort_label($sort)); ?>
                  </a>
                <?php endforeach; ?>
              </div>
            </div>
            <?php endif; ?>
        </div>


      </div>
    </div>

    <?php if (!empty($t['list_entries'])) :?>
      <div class="row">
        <?php foreach ($t['list_entries'] as $item) :?>
          <div class="col-md-3 col-sm-3 col-lg-2 col-6 gallery-item-wrap">
            <div class="gallery-item card">
            <div class="gallery-item-inner">
              <div class="gallery-thumbnail view-entry"
              data-thumbnail="<?= e_attr($item['content_thumbnail']); ?>"
              data-relative-url="/<?= e_attr($item['content_rel_path']); ?>"
              data-ext="<?= e_attr($item['content_ext']); ?>"
              data-name="<?= e_attr($item['content_title']); ?>"
              data-size="<?= e_attr($item['content_readable_size']); ?>"
              data-filetype="<?= e_attr($item['content_file_type']); ?>"
              data-url="<?= e_attr($item['content_url']); ?>">
                <div class="centered">
                  <img src="<?= e_attr($item['content_thumbnail']); ?>" class="gallery-img-thumb">
                </div>
              </div>
                <?php if (current_user_can('manage_gallery')) : ?>
                <button data-endpoint="<?= e_attr(url_for('dashboard.gallery.delete_post', ['id' => $item['content_id']])); ?>" class="delete-entry btn btn-sm btn-danger"><?= svg_icon('trash', 'mr-1'); ?></button>
                <?php endif; ?>
              <?php if ($item['content_file_type'] !== 'image') : ?>
                      <div class="gallery-item-title text-truncate"><?= e($item['content_filename']); ?></div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <?php endforeach; ?>
      </div>

      <div class="p-5">
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
    <?php else : ?>
      <div class="text-center py-8">
        <span class="h3 text-muted"><?= __('No entries found.'); ?></span>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Attachment Preview Modal -->
<div class="modal fade" id="preview-modal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><?= __('View Attachment'); ?></h5>
        <button type="button" class="close" data-dismiss="modal">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <div id="preview-image" class="text-center mb-lg-0 mb-2" style="display: none"></div>
          </div>
          <div class="col-md-6 mt-lg-2">
            <div class="form-group">
              <label class="form-label"><?= __('File Name'); ?></label>
              <div id="content-name" class="form-control-plaintext text-muted"></div>
            </div>
            <div class="form-group">
              <label class="form-label"><?= __('File Size'); ?></label>
              <div id="readable-size" class="form-control-plaintext text-muted"></div>
            </div>
            <div class="form-group">
              <label class="form-label" for="preview-url"><?= __('Absolute URL'); ?></label>
              <input class="form-control" type="text" onclick="this.setSelectionRange(0, this.value.length)" id="preview-url">
            </div>
            <div class="form-group">
              <label class="form-label" for="relative-url"><?= __('Relative URL'); ?></label>
              <input class="form-control" type="text" onclick="this.setSelectionRange(0, this.value.length)" id="relative-url">
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endblock(); ?>

<?php block('body_end'); ?>
<script type="text/javascript">
  $(function() {

    $('#preview-modal').on('hidden.bs.modal', function () {
      $spark.ajaxLoader('hide');
      $('#preview-image').html('');
    });

    $('.view-entry').on('click', function (e) {
      e.preventDefault();
      var item = $(this);
      var filetype = item.data('filetype');
      var image = $('#preview-img-src');

      $('#preview-url').val(item.data('url'));
      $('#relative-url').val(item.data('relative-url'));
      $('#readable-size').text(item.data('size'));
      $('#content-name').text(item.data('name') + '.' + item.data('ext'));
      $('#preview-modal').modal('show');

      var imgSrc = item.data('thumbnail');

      if (filetype == 'image') {
        imgSrc = item.data('url');
      }


      $spark.ajaxLoader('show');

      var newImage = document.createElement('img');
      newImage.src = imgSrc;
      newImage.onload = function () {

        $('#preview-image').html(newImage);
        $spark.ajaxLoader('hide');
      };

      $('#preview-image').show();
    });

    $('.delete-entry').on('click', function (e) {
      e.preventDefault();
      var endpoint = $(this).data('endpoint');

      lnv.confirm({
        title: '<?= __("Confirm Deletion"); ?>',
        content: '<?= __("Are you sure you want to delete this gallery item?"); ?>',
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


    Dropzone.options.gallery = {
      paramName: "file",
      maxFilesize: "<?= e_attr($t['max_upload_size']); ?>",
      createImageThumbnails: false,
      addRemoveLinks: true,
      acceptedFiles: ".<?= e_attr(join(',.', $t['allowed_filetypes'])); ?>",
      accept: function(file, done) {
        done();
      },
      init: function() {
        this.on('queuecomplete', function () {
          });
      }
    };
  });
</script>
<?php endblock(); ?>
<?php
extend(
    'admin::layouts/skeleton.php',
    [
    'title' => __('Gallery'),
    'body_class' => 'gallery gallery-list',
    'page_heading' => __('Gallery'),
    'page_subheading' => __('Manage gallery.'),
    ]
);
