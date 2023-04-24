<nav class="navbar navbar-has-logo navbar-expand-md bg-primary shadow navbar-dark p-0 fixed-top" id="topnavbar">

  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler sidebar-toggler" type="button" id="sidebarToggle">
    <span class="navbar-toggle-icon">
      <span class="icon-bar top-bar"></span>
      <span class="icon-bar middle-bar"></span>
      <span class="icon-bar bottom-bar"></span>
    </span>
  </button>

  <!-- Brand -->
  <a class="navbar-brand sidebar-brand text-white" href="<?=e_attr(url_for('dashboard')); ?>"><?= svg_icon('logo', 'site-logo navbar-logo'); ?></a>

  <!-- Toggler/collapsibe Button -->
  <button class="navbar-toggler collapsed" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
    <span class="navbar-toggle-icon navbar-icon-animate">
      <span class="icon-bar-round top-bar"></span>
      <span class="icon-bar-round middle-bar"></span>
      <span class="icon-bar-round bottom-bar"></span>
    </span>
  </button>

  <!-- Navbar links -->
  <div class="collapse navbar-collapse" id="collapsibleNavbar">
    <ul class="navbar-nav ml-auto">
      <?= sp_render_navbar_menu(); ?>

      <li class="nav-item dropdown">
        <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
                    <span class="avatar" style="background-image: url('<?=e_attr(current_user_avatar_uri())?>')"></span>
                    <span class="ml-2">
                      <span><?= e(current_user_field('full_name')); ?></span>
                      <small class="d-block mt-1"><?= e(current_user_field('role_name')); ?></small>
                    </span>
                    <span class="caret"></span>
                  </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="<?= e_attr(base_uri()); ?>" target="_blank"><?= __("Visit Site"); ?></a>
          <a class="dropdown-item" href="<?= e_attr(url_for('dashboard.account.settings')); ?>">
            <?= __("Account Settings"); ?>
        </a>
          <div class="dropdown-divider"></div>
          <form method="post" action="<?= e_attr(url_for('dashboard.account.logout')); ?>">
            <?= $t['csrf_html']; ?>
            <button class="dropdown-item" type="submit"><?= __('Log Out'); ?></button>
          </form>
        </div>
      </li>
    </ul>
  </div>
</nav>
