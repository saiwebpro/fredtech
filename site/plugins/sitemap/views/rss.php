<?php echo'<?xml version="1.0" encoding="utf-8"?>'; ?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0" version="2.0" xmlns:media="http://search.yahoo.com/mrss/" xmlns:snf="http://www.smartnews.be/snf">
    <channel>
        <title><?= e($t['rss_title']); ?></title>
        <link><?= e(base_uri()); ?></link>
        <description><?= e($t['rss_desc']) ?></description>
        <lastBuildDate><?= date(DATE_RSS, $t['last_build_date']); ?></lastBuildDate>
        <sy:updatePeriod>hourly</sy:updatePeriod>
        <sy:updateFrequency>1</sy:updateFrequency>
        <?php foreach ($t['entries'] as $item) : ?>
            <item>
                <title><?= e($item['post_title']); ?></title>
                <link><?= e(url_for('site.read', ['slug' => urlencode($t['slug']->generate($item['post_title'])), 'id' => $item['post_id']])); ?></link>
                <?php if ($t['show_fulltext']) : ?>
                <content:encoded><![CDATA[<?= $item['post_content']; ?>]]></content:encoded>
                <?php endif; ?>
                <description><?= sp_strip_tags($item['post_excerpt']); ?></description>
               <guid isPermaLink="true"><?= e(url_for('site.read', ['slug' => urlencode($t['slug']->generate($item['post_title'])), 'id' => $item['post_id']])); ?></guid>
                <pubDate><?= date(DATE_RSS, $item['created_at']); ?></pubDate>
                <media:thumbnail url="<?= e_attr(feat_img_url($item['post_featured_image'])); ?>"/>
            </item>
        <?php endforeach; ?>
    </channel>
</rss>
