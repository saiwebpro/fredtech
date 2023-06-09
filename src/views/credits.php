<?php block('content'); ?>
<div class="px-0 px-lg-4">
    <p>
        <?= __('These libraries, packages and services helped building this web app a lot faster and easier. Here we mention them for their honorable work.'); ?>
    </p>
    <h5 class="page-title border-bottom border-gray mb-3"><?= __('PHP Libraries'); ?></h5>
    <div class="row row-cards row-deck">
        <?php foreach ($t['php_libraries'] as $lib) : ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-blue mr-3">
                      <?= svg_icon('php-logo', 'svg-md'); ?>
                  </span>
                  <div>
                      <h5 class="m-0 text-truncate">
                        <a href="<?= e_attr($lib['url']); ?>" target="_blank"><?= e($lib['name']); ?></a>
                    </h5>
                      <small class="text-muted"><?= e($lib['desc']); ?></small>
                    <div><span class="badge badge-success font-weight-normal"><?= e($lib['license']); ?></span></div>
                  </div>
              </div>
          </div>
      </div>
        <?php endforeach; ?>
    </div>

    <h5 class="page-title border-bottom border-gray mb-3"><?= __('Javascript Libraries'); ?></h5>
    <div class="row row-cards row-deck">
        <?php foreach ($t['js_libraries'] as $lib) : ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-yellow mr-3">
                      <?= svg_icon('js-logo', 'svg-md text-dark'); ?>
                  </span>
                  <div>
                      <h5 class="m-0 text-truncate">
                        <a href="<?= e_attr($lib['url']); ?>" target="_blank"><?= e($lib['name']); ?></a>
                    </h5>
                      <small class="text-muted"><?= e($lib['desc']); ?></small>
                    <div><span class="badge badge-success font-weight-normal"><?= e($lib['license']); ?></span></div>
                  </div>
              </div>
          </div>
      </div>
        <?php endforeach; ?>
    </div>

    <h5 class="page-title border-bottom border-gray mb-3"><?= __('Other Libraries & Services'); ?></h5>
    <div class="row row-cards row-deck">
        <?php foreach ($t['others'] as $lib) : ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card p-3">
                  <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-orange mr-3">
                      <?= svg_icon('options', 'svg-md'); ?>
                  </span>
                  <div>
                      <h5 class="m-0 text-truncate">
                        <a href="<?= e_attr($lib['url']); ?>" target="_blank"><?= e($lib['name']); ?></a>
                    </h5>
                      <small class="text-muted"><?= e($lib['desc']); ?></small>
                    <div><span class="badge badge-success font-weight-normal"><?= e($lib['license']); ?></span></div>
                  </div>
              </div>
          </div>
      </div>
        <?php endforeach; ?>
    </div>
    <p>
        <?= __("In case we missed anyone. We're really sorry about that. Thanks everyone for their effort."); ?>
    </p>

</div>
<?php endblock(); ?>

<?php
// Extends the base skeleton
extend(
    'admin::layouts/skeleton.php',
    [
        'title' => __('Credits'),
        'body_class' => 'dashboard credits',
        'page_heading' => __('Credits'),
        'page_subheading' => sprintf(__('Those who made %s possible'), APP_NAME),
    ]
);
