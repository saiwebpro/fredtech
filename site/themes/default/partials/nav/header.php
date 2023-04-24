
<header class="site-navbar navbar-light" id="site-navbar">
    <div class="container">
        <div class="row">
            <div class="col-md-2 col-12 d-flex d-md-block px-md-4"><a class="navbar-brand navbar-logo py-0" href="<?= e_attr(base_uri()); ?>">
                            <img src="<?= e_attr(sp_logo_uri()); ?>" class="site-logo"></a>

<div class="flex-1 text-right">
        <button class="navbar-toggler d-md-none d-inline-block text-dark" type="button" data-action="offcanvas-open" data-target="#topnavbar" aria-controls="topnavbar" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
    </div>
            </div>
            <div class="col-md-7 col-12">
                <form method="get" action="<?= e_attr(url_for('site.search')); ?>" id="searchForm" class="home-search-box">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="<?= __('Search the web...', _T); ?>" name="q" id="q" autocomplete="off" value="<?= e_attr($t['search_value']); ?>">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="submit"><?= svg_icon('search', 'svg-md'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-3 text-right d-none d-md-block px-1">
        <button class="navbar-toggler d-lg-none d-inline-block text-dark" type="button" data-action="offcanvas-open" data-target="#topnavbar" aria-controls="topnavbar" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
                <?php if (is_logged() && current_user_can('access_dashboard')) : ?>
                    <a href="<?= e_attr(url_for('dashboard')); ?>" target="_blank" class="btn btn-outline-dark">
                       <?= svg_icon('analytics'); ?> <?= __('Dashboard', _T); ?>
                    </a>
                <?php endif; ?>
            </div>
            </div>
        </div>

</header>

<!-- Mobile Nav -->
<nav class="navbar-light position-absolute" style="top:-9999999rem">
  <div class="navbar-collapse offcanvas-collapse" id="topnavbar">
    <button data-action="offcanvas-close" data-target="#topnavbar" class="btn btn-link close d-lg-none">&times;</button>
    <h6 class="dropdown-header font-weight-600 d-lg-none px-0 mb-2"><?= __('site-menu', _T); ?></h6>

    <ul class="nav navbar-categories flex-column">

        <li class="nav-item">
          <a class="nav-link <?= e_attr($t["home_active"]); ?>" href="<?= e_attr(url_for('site.home')); ?>">
            <?= svg_icon('paper', 'mr-1'); ?>
            <?= __('category-label-everything', _T); ?>
          </a>
        </li>

        <?php foreach ($t['categories'] as $category) : ?>
        <li class="nav-item">
          <a class="nav-link <?= e_attr($t["{$category['category_id']}_active"]); ?>" href="<?= e_attr(url_for('site.category', ['slug' => $category['category_slug']])); ?>">
            <img src="<?= e_attr(category_icon_url($category['category_icon'])); ?>" class="category-icon mr-1">
            <?= e(__(
                "category-label-{$category['category_slug']}",
                _T,
                ['defaultValue' => $category['category_name']]
            )); ?>
          </a>
        </li>
        <?php endforeach; ?>
    </ul>
  </div>
</nav>
