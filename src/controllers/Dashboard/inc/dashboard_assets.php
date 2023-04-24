<?php

// Register global dashboard assets
sp_register_style(
    'dashboard-core-style',
    site_uri('assets/css/bundle.css'),
    [
        'abspath' => sitepath('assets/css/bundle.css')
    ]
);

// Google font
sp_register_style('google-font-dashboard', '//fonts.googleapis.com/css?family=Public+Sans:400,500&display=swap');

// heavyweight ugly ass bootstrap js ugh </3
sp_register_script('bootstrap-bundle-js', site_uri('assets/js/bootstrap.bundle.min.js'));

// Register dashboard's core JS
sp_register_script(
    'dashboard-core-js',
    site_uri('assets/js/spark.js'),
    [
        'abspath' => sitepath('assets/js/spark.js')
    ]
);


// IOS Style Dialogs
sp_register_script('ios-dialog', site_uri('assets/js/ios-dialog.min.js'));

// Dropzone file uploader
sp_register_script('dropzone-js', site_uri('assets/js/dropzone.min.js'));

// jQuery inview event
sp_register_script('jquery-inview', site_uri('assets/js/jquery.inview.min.js'));

// jQuery form toggle
sp_register_script('jquery-form-toggle', site_uri('assets/js/jquery.form-toggle.min.js'));

// jQuery countdown
sp_register_script('jquery-countdown', site_uri('assets/js/jquery.countdown.min.js'));

// trumbowyg editor
sp_register_script('trumbowyg-editor', site_uri('assets/js/trumbowyg/trumbowyg.min.js'));
sp_register_script('trumbowyg-editor-upload-plugin', site_uri('assets/js/trumbowyg/plugins/upload/trumbowyg.upload.min.js'));

sp_register_style('trumbowyg-editor-style', site_uri('assets/js/trumbowyg/ui/trumbowyg.min.css'));

// Enqueue Google font css
sp_enqueue_style('google-font-dashboard');

// Core style
sp_enqueue_style('dashboard-core-style');

// jQuery
sp_enqueue_script('jquery', 2);

// Ios popup dialogs
sp_enqueue_script('ios-dialog', 2);

// Bootstrap bundle js
sp_enqueue_script('bootstrap-bundle-js', 2);

// Core dashboard JS
sp_enqueue_script('dashboard-core-js', 2, ['jquery', 'bootstrap-bundle-js']);
sp_enqueue_script('polyfill-io', 2);
