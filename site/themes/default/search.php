<?php block('content'); ?>
<script type="text/javascript"><?= $t['cse_script']; ?></script>
<style>
.gsc-control-cse .gs-spelling, .gsc-control-cse .gs-result .gs-title, .gsc-control-cse .gs-result .gs-title * {
    font-size: 1rem!important;
    font-weight: 500!important;
    text-decoration: none!important;
    line-height: initial!important;
}

.gsc-results .gsc-cursor-box {
  text-align: center!important;
}

.gsc-results .gsc-cursor-box .gsc-cursor-page {
    background-color: #FFFFFF!important;
    color: #2b2b2b!important;
    font-size: 16px!important;
    border: 1px solid #CCCCCC!important;
    border-radius: 3px!important;
    padding: .1rem .5rem!important;
    font-weight: normal!important;
}

.gsc-above-wrapper-area {
  border: 0!important;
}
.gsc-above-wrapper-area, .gsc-result-info-container{display:block!important}
.gcsc-more-maybe-branding-root, .gcsc-branding, .gsc-tabsArea, .gs-richsnippet-box, .gcsc-find-more-on-google-root, .gsc-search-box, .gsc-refinementsArea{display:none!important}
.gsc-control-cse{border:0!important}
.gsc-webResult.gsc-result,.gsc-results .gsc-imageResult,
.gsc-webResult.gsc-result:hover, .gsc-results .gsc-imageResult:hover{border:0!important}
.gsc-control-cse, .gsc-control-cse .gsc-table-result {
    font-family: inherit!important;
}
.cse .gsc-control-cse, .gsc-control-cse {
    background: transparent!important;
}
.gsc-wrapper b {
    font-weight: 500!important;
}
.cse .gsc-control-cse, .gsc-control-cse {
    padding: 0!important;
}

.gs-result a.gs-visibleUrl, .gs-result .gs-visibleUrl {
    color: #247b28!important;
    font-size: .9rem!important;
}

<?php if ((int) $t['engine.engine_show_thumb']) : ?>
.gs-image-box.gs-web-image-box.gs-web-image-box-landscape {
    width: auto!important;
}

.gs-result .gs-image, .gs-result .gs-promotion-image {
  border:0!important;
}

.gs-web-image-box-landscape img.gs-image, .gs-web-image-box-portrait img.gs-image {
    object-fit: cover!important;
    width: 140px!important;
    height: 84px!important;
    max-width: 140px!important;
    margin-right: 10px!important;
    max-height: 84px!important;
}
<?php else : ?>
.gsc-thumbnail {
    display: none!important;
}
<?php endif; ?>

<?php if (!(int) get_option('enable_search_ads', 0)) : ?>
 .gsc-adBlock {
  display: none!important;
 }
<?php endif; ?>
</style>

<div class="search-box">
    <div class="container container-fluid searchbox-container">
        <div class="row no-gutters flex-row-reverse">
          
        <div class="col-md-5 text-right">
        </div>
            <div class="col-md-7">
                <form method="get" action="?" id="searchForm">
                    <div class="input-group">
                    <div class="input-group-prepend searchbox-prepend">
                        <a href="<?= e_attr(base_uri()); ?>"><img src="<?= e_attr(sp_logo_uri()); ?>" class="searchbox-logo"></a>
                    </div>
                      <input type="text" class="form-control" placeholder="<?= __('Search something...', _T); ?>" value="<?= e_attr($t['q']); ?>" name="q" id="q">

                    <input type="hidden" name="engine" value="<?= e_attr($t['engine.engine_id']); ?>">
                      <div class="input-group-append">
                        <button class="btn btn-primary" type="submit"><?= svg_icon('search', 'svg-md'); ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</div>

<div class="engine-tabs-wrap">
    <div class="container results-container engine-overflow-hidden" id="engine-bar-container">
        <ul class="nav nav-tabs engine-tabs dragscroll fix-tab-scrollbar" id="engines-tab">
            <?php foreach ($t['engines'] as $engine) : ?>
                <li class="nav-item">
                        <a class="nav-link <?= ($engine['engine_id'] == $t['engine.engine_id']) ? 'active' : ''; ?>" href="?q=<?= e_attr($t['q']); ?>&amp;engine=<?=e_attr($engine['engine_id']); ?>">
                            <?= e($engine['engine_name']); ?>
                            </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    </div>

<div class="container container-fluid">
    <div class="row flex-row-reverse flex-md-row-reverse">
<div class="col-md-4 px-2 px-md-3 <?= $t['is_image'] ? 'd-none' : ''; ?>">
    <?php
    /**
     * @hook Fires at the sidebar of the search page
     *
     * @param string $q The search query
     * @param array  $engine Current engine
     */
    do_action('search_sidebar_before', $t['q'], $t['engine']);
    ?>

    <?php insert('partials/search_sidebar.php'); ?>

    <div class="ad-block">
        <?= get_option('ad_unit_5'); ?>
    </div>


    <?php
    /**
     * @hook Fires at after the sidebar of the search page
     *
     * @param string $q The search query
     * @param array  $engine Current engine
     */
    do_action('search_sidebar_after', $t['q'], $t['engine']);
    ?>

</div>
<div class="col-md-1">
  
</div>

<div class="<?= $t['is_image'] ? 'col-12' : 'col-md-7'; ?> px-2 px-md-3">
    <?php
    /**
     * @hook Fires before the search results
     *
     * @param string $q The search query
     * @param array  $engine Current engine
     */
    do_action('search_results_before', $t['q'], $t['engine']);
    ?>

    <div class="ad-block">
        <?= get_option('ad_unit_6'); ?>
    </div>

    <?= $t['cse_element']; ?>

    <div class="ad-block">
        <?= get_option('ad_unit_7'); ?>
    </div>
    <?php
    /**
     * @hook Fires after the search results
     *
     * @param string $q The search query
     * @param array  $engine Current engine
     */
    do_action('search_results_after', $t['q'], $t['engine']);
    ?>
</div>
    </div>

</div>

<?php endblock(); ?>
<?php block('before_body_closure'); ?>
<script type="text/javascript">
  jQuery(document).ready(function($) {
    $(document).on('click', '.infobox-toggle', function(e) {
      e.preventDefault();

      $('#infobox-list').toggleClass('infobox-expanded');

      $(this).toggleClass('upside-down');
    });
  });


   window.__gcse = {
    parsetags: 'onload', // Defaults to 'onload'
    initializationCallback: null,
    searchCallbacks: {
      web: {
        rendered: function (gname, query, promoElts, resultElts) {
                var items = $('.gs-visibleUrl');

                $.each(items, function(key, el) {
                    var item = $(this);
                    var url = item.text();
                    var urlParts = url.replace('http://','').replace('https://','').split(/[/?#]/);
                    var domain = urlParts[0];

                    var hostless = url.replace(/^https?:\/\//g, '');

                    // Add some favicons
                    if (hostless.trim().length) {
                      item.html('<img src="https://www.google.com/s2/favicons?domain=' + domain + '" class="result-favicon mr-1">' + hostless);
                    }
                    
                });
        },
      },
    },
  };
</script>
<?php endblock(); ?>
<?php
extend(
    'layouts/basic.php',
    [
        'body_class' => 'search',
        'hide_header' => true,
    ]
);

