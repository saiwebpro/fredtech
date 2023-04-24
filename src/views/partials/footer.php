<footer class="footer bg-transparent mx-3 mt-2">
  <div class="container-fluid">
    <div class="row align-items-center flex-row-reverse">
      <div class="col-12 col-sm-auto ml-lg-auto">
        <ul class="list-inline list-inline-dots mb-0">
          <li class="list-inline-item"><a href="<?= e_attr(url_for('dashboard.credits')); ?>"><?= __('Licenses')?></a></li>
          <li class="list-inline-item"><a href="https://gitHub.com/MirazMac" target="_blank"><?= __('GitHub')?></a></li>
          <li class="list-inline-item"><a href="https://fb.me/MirazMac" target="_blank"><?= __('Facebook')?></a></li>
          <li class="list-inline-item"><a href="https://twitter.com/miraz_mac" target="_blank"><?= __('Twitter')?></a></li>
        </ul>
      </div>
      <div class="col-auto">
      </div>
      <div class="col-12 col-lg-auto mt-3 mt-lg-0">
        <?= APP_NAME; ?> <?= APP_VERSION; ?> &middot; <small>Made with <?= svg_icon('love', 'text-red'); ?> by MirazMac Studios.</small>
      </div>
    </div>
  </div>
</footer>
