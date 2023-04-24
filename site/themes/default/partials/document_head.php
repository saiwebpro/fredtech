
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge,chrome=1">
    <link rel="dns-prefetch" href="//www.google.com">
    <link rel="dns-prefetch" href="//cse.google.com">
    <?php sp_head(); ?>
    <link rel="shortcut icon" type="image/x-icon" href="<?=base_uri('favicon.ico')?>">

    <script type="text/javascript">
        // Base URI to the site
        var base_uri = "<?=base_uri()?>";
        // current route URL
        var current_route_uri = "<?= js_string(get_current_route_uri()) ?>";
        // CSRF token
        var csrf_token = "<?= $t['csrf_token'] ?>";
        // CSRF token with & prefixed
        var csrf_token_amp = "&<?= $t['csrf_key']?>=<?= $t['csrf_token'] ?>";
    </script>

    <style type="text/css">
        <?= e(get_option('default_theme_custom_css', '')); ?>
    </style>
