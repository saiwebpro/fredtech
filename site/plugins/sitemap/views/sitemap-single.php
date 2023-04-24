<?= '<?xml version="1.0" encoding="utf-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach ($t['entries'] as $item) : ?>
          <url>
                <loc><?= e(url_for('site.read', ['slug' => $t['slug']->generate($item['post_title']), 'id' => $item['post_id']])); ?></loc>
                <lastmod><?= date('c', $item['updated_at']); ?></lastmod>
                <changefreq>daily</changefreq>
          </url>
    <?php endforeach; ?>
</urlset>
