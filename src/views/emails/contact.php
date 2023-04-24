<?php block('body_content'); ?>
<h3>
<?= sprintf(__('Hello, you have a message from your contact form.'));?>
</h3>
<hr/>
<?= nl2br($t['message'], true); ?>
<br/>
<hr/>
<?= __('Sender Name:'); ?> <span class="text-gray"><?= e($t['name']); ?></span><br/>
<?= __('Sender E-Mail:'); ?> <span class="text-gray"><?= e($t['email']); ?></span><br/>
<?= __('Sender IP:'); ?> <span class="text-gray"><?= e($t['user_ip']); ?></span><br/>
<?= __('Sender Browser:'); ?> <span class="text-gray"><?= e($t['user_agent']); ?></span><br/>
<?php endblock(); ?>

<?php block('email_footer'); ?>
<?= sprintf(__('This email was sent via the contact form on %s, please note the information provided here should not be trusted as they can be faked.'), base_uri()); ?>
<?php endblock(); ?>

<?php

extend(
    'admin::layouts/email_basic.php',
    [
    ]
);
