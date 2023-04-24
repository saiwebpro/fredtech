<!DOCTYPE html>
<html dir="ltr">
<head>
    <?php insert('partials/document_head.php'); ?>
</head>
<body class="<?= e_attr($t['body_class']); ?>">
    <div id="fb-root"></div>
    <?php
    // SVG Sprites
    insert('partials/sprites.svg');

    // Header
    if (!$t['hide_header']) {
        insert('partials/nav/header.php');
    }
    ?>

    <?php
    /**
     * Before content section
     */
    section('before_content');
    ?>

    <div id="content">
        <?php
        /**
         * Content section
         */
        section('content', 'No content block found');
        ?>
    </div>

    <?php
    /**
     * After content section
     */
    section('after_content');
    ?>

    <?php if (!$t['hide_footer']) : ?>
        <?php insert('partials/nav/footer.php'); ?>
    <?php endif; ?>
    <?php insert('partials/document_foot.php'); ?>

    <?php section('before_body_closure'); ?>
</body>
</html>
