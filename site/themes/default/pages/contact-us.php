<?php

/**
 * Custom template for page with the slug: contact-us
 * The page template name and the page slug name must be the exact same
 */

if ((bool) get_option('captcha_enabled')) {
    sp_enqueue_script('google-recaptcha');
}

// Enqueue form validator
sp_enqueue_script('parsley', 2, ['jquery']);
?>

<?php block('content'); ?>
<div class="container pt-2">
    <h1 class="site-heading">
        <span><?= e($t['page.content_title']); ?></span>
    </h1>
    <?= sp_alert_flashes('pages', true, false); ?>
    <div class="page-content">
        <?= $t['page.content_body']; ?>
    </div>

    <form method="post" action="<?= e_attr(url_for('site.contact_form_action')); ?>" data-parsley-validate>
        <?= $t['csrf_html']; ?>
        <div class="form-group">
            <label for="name"><?= __('Your Name', _T); ?> <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" class="form-control" value="<?= sp_post('name'); ?>" required>
        </div>
        <div class="form-group">
            <label for="email"><?= __('Your E-Mail', _T); ?> <span class="text-danger">*</span></label>
            <input type="email" name="email" id="email" value="<?= sp_post('email'); ?>" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="subject"><?= __('Subject', _T); ?></label>
            <input type="subject" name="subject" id="subject" value="<?= sp_post('subject'); ?>" class="form-control" minlength="10" maxlength="200">
            <span class="form-text text-muted">
                <?= __('A quick subject of why are you contacting us. (optional)', _T); ?>
            </span>
        </div>
        <div class="form-group">
            <label for="message"><?= __('Message', _T); ?> <span class="text-danger">*</span></label>
            <textarea name="message" id="message" class="form-control" rows="5" minlength="200" maxlength="5000" required><?= sp_post('message'); ?></textarea>
        </div>

        <?= sp_google_recaptcha('<div class="text-center">', '</div>'); ?>

        <div class="form-group text-right">
            <button type="submit" class="btn btn-primary">
                <?= __('Send Message', _T); ?>
            </button>
        </div>
    </form>

    <?= breadcrumb_render(); ?>
</div>

    <div class="d-md-none">
        <?php insert('partials/site_sidebar_left.php'); ?>
    </div>
<?php endblock(); ?>

<?php
extend(
    'layouts/basic.php',
    [
        'body_class' => 'page page-custom contact-us',
    ]
);


