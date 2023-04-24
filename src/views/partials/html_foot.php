
    <script type="text/javascript">
    <?php if (sp_is_enqueued('parsley')) : ?>
          $("form").parsley(parsleyOptions);
    <?php endif; ?>

    $(function () {
        // Enable Tooltips
        $('[data-toggle="tooltip"]').tooltip();
        // Enable Popovers
        $('[data-toggle="popover"]').popover()
    });
    </script>
