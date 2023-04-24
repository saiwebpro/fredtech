
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">

    <?= sp_head(); ?>
    <link rel="shortcut icon" type="image/x-icon" href="<?=base_uri('favicon.ico')?>">
    <?php section('html_head'); ?>

    <script type="text/javascript">
        var base_uri = "<?=base_uri()?>";
        var current_route_uri = "<?= js_string(get_current_route_uri()) ?>";
        var csrf_token = "<?= $t['csrf_token'] ?>";
        var csrf_token_amp = "&<?= $t['csrf_key']?>=<?= $t['csrf_token'] ?>";
        var spark_i18n = {
            ajax_err_title: "<?= js_string(__('Ajax Error')); ?>",
            ajax_err_desc: "<?= js_string(__('Failed to communicate to server via AJAX. Please check your internet connection or reload this page and try again.')); ?>",
            okay: "<?= js_string(__('Okay')); ?>",
            cancel: "<?= js_string(__('Cancel')); ?>",
            confirm: "<?= js_string(__('Confirm')); ?>",
        };
    </script>
