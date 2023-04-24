 <?php sp_footer(); ?>

<script type="text/javascript">
    <?php if (sp_is_enqueued('parsley')) : ?>
    $("form").parsley({
       errorClass: 'is-invalid text-danger',
       successClass: 'is-valid',
       errorsWrapper: '<span class="form-text text-danger"></span>',
       errorTemplate: '<span></span>',
       trigger: 'focusout',
       focusInvalid: true,
   });
    <?php endif; ?>


    <?php if (sp_is_enqueued('jquery-autocomplete')) : ?>
        var suggestionEndpoint = '<?= js_string(url_for('site.suggest_queries')) ?>';
        var xhr;
        $('input[name="q"]').autoComplete({
          source: function(term, response){
            try { xhr.abort(); } catch(e){}
            xhr = $.getJSON(suggestionEndpoint, { q: term }, function(data){ response(data); });
          },
          onSelect : function (e, term, item) {
            $("#searchForm").submit();
          }
        });

    <?php endif;?>

    jQuery(document).ready(function($) {

    <?php if (sp_is_enqueued('jquery-unveil')) : ?>
        $("img").unveil();
    <?php endif; ?>

    if (window.screen.width >= 768) {
        var sidebar = $('.sidebar').stickySidebar({
          topSpacing: 66,
          bottomSpacing: 60,
          resizeSensor: false,
        });
      }

    });
</script>
