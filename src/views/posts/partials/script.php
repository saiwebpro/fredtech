<script type="text/javascript">
  $(function() {
    $('#post_content').trumbowyg({
      autogrow: false,
      btnsDef: {
        image: {
          dropdown: ['insertImage', 'upload'],
          ico: 'insertImage'
        },
      },
      btns: [
      ['viewHTML'],
      ['formatting'],
      ['strong', 'em', 'del'],
      ['link'],
      ['image'],
      ['justifyLeft', 'justifyCenter', 'justifyRight'],
      ['unorderedList', 'orderedList'],
      ['horizontalRule'],
      ['removeformat'],
      ['fullscreen']
      ],

      plugins: {
        upload: {
          serverPath: "<?= e_attr(url_for('dashboard.gallery.create_post')); ?>",
          fileFieldName: 'file',
          urlPropertyName: "content_url",
          data: [{name: 'csrf_token', value: "<?= $t['csrf_token']?>"}],
        }
      },
  });

    $("#img-uploader").dropzone({
      url: "<?= url_for('dashboard.gallery.create_post'); ?>",
      maxFileSize: <?= format_bytes(get_max_upload_size()); ?>,
      acceptedFiles: 'image/*',
      params: {
        csrf_token: "<?= $t['csrf_token']; ?>",
      },
      success: function (dropzone, response) {
        if (response.content_url) {
          $('#post_featured_image').val(response.content_relative_url).focus();
        }
      },
    });
        
  });
</script>
