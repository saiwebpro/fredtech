<?php if ((int) get_option('disqus_enabled')) : ?>
<div id="disqus_thread"></div>
<script>
    (function() {  
        var d = document, s = d.createElement('script');
        s.src = '<?= e_attr(get_option('disqus_url')); ?>/embed.js';

        s.setAttribute('data-timestamp', +new Date());
        (d.head || d.body).appendChild(s);
    })();
</script>
<?php endif; ?>
