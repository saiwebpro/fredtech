<?php if ((int) get_option('fb_comments_enabled')) : ?>
<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v6.0&appId=<?= e_attr(get_option('facebook_app_id')); ?>&autoLogAppEvents=1"></script>

<div class="fb-comments clearfix" data-numposts="10" data-width="100%" data-href="<?= e_attr(get_current_route_uri()); ?>"></div>
<?php endif; ?>
