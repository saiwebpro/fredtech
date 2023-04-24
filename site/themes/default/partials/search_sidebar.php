<?php if (!empty($t['answer.Abstract'])) : ?>
    <?php $count = 0; ?>
        <div class="card my-md-3 shadow-sm">
            <div class="card-body">
                <?php if ($t['answer.Image']) : ?>
                    <?php if (!filter_var($t['answer.Image'], FILTER_VALIDATE_URL)) : ?>
                        <?php $t['answer.Image'] = 'https://duckduckgo.com' . $t['answer.Image']; ?>
                    <?php endif; ?>
                    <img src="<?= e_attr($t['answer.Image']); ?>" class="abstract-img mb-2">
                <?php endif; ?>


                        <h4 class="heading"><?= $t['answer.Heading']; ?></h4>
                        <p><?= limit_words($t['answer.Abstract'], 30); ?>
                            <?php if (!empty($t['answer.AbstractSource'])) : ?>
                                <a href="<?= e_attr($t['answer.AbstractURL']); ?>" rel="nofollow noreferer" target="_blank" class="font-weight-bold">
                                    <?= e($t['answer.AbstractSource']); ?>
                                    </a>
                            <?php endif; ?>
                        </p>

                        <?php if (is_array($t['answer.Infobox.content'])) : ?>
                            <?php $t['infobox_count'] = count($t['answer.Infobox.content']); ?>
                            <ul class="list-unstyled infobox m-0" id="infobox-list">
                            <?php

                            $socials  = [];

                            foreach ($t['answer.Infobox.content'] as $t['info']) :?>
                                <?php

                                switch ($t['info.data_type']) {
                                    case 'facebook_profile':
                                        $socials[1] = [
                                            'label' => $t['info.label'],
                                            'url'   => "https://facebook.com/{$t['info.value']}",
                                            'class' => 'facebook'
                                        ];
                                        break;
                                    case 'twitter_profile':
                                        $socials[2] = [
                                            'label' => $t['info.label'],
                                            'url' => "https://twitter.com/{$t['info.value']}",
                                            'class' => 'twitter'
                                        ];
                                        break;
                                    case 'instagram_profile':
                                        $socials[3] = [
                                            'label' => $t['info.label'],
                                            'url' => "https://instagram.com/{$t['info.value']}",
                                            'class' => 'instagram'
                                        ];
                                        break;
                                    case 'youtube_channel':
                                        $socials[4] = [
                                            'label' => $t['info.label'],
                                            'url' => "https://youtube.com/channel/{$t['info.value']}",
                                            'class' => 'youtube',
                                        ];
                                        break;
                                    case 'github_profile':
                                        $socials[5] = [
                                            'label' => $t['info.label'],
                                            'url' => "https://github.com/{$t['info.value']}",
                                            'class' => 'github'
                                        ];
                                        break;
                                }

                                if ($t['info.data_type'] != 'string') {
                                    continue;
                                }

                                if (mb_strtolower($t['info.label']) === 'website') {
                                    $t['info.value'] = trim($t['info.value'], '[]');
                                    $socials[0] = [
                                        'label' => $t['info.label'],
                                        'url' => "http://{$t['info.value']}",
                                        'class' => 'website',
                                    ];
                                }

                                $count++;
                                ?>
                                <li class="text-truncate py-1" title="<?= e_attr($t['info.value']); ?>">
                                <span class="text-dark font-weight-bold"><?= e($t['info.label']) ?>:</span>&nbsp;
                                <span class="text-muted"><?= e($t['info.value']) ?></span>
                            </li>
                            <?php endforeach; ?>
                                <li class="text-truncate py-1">
                                    <span class="text-dark font-weight-bold"><?= __('Data source:', _T); ?></span>&nbsp;
                                    <span class="text-muted">
                                        <a href="https://duckduckgo.com" rel="nofollow" target="_blank">DuckDuckGo</a></span>
                                    </li>
                                </ul>

                            <?php if (has_items($socials)) : ?>
                        <ul class="d-flex list-unstyled flex-wrap m-0 justify-content-center py-1"> 
                                <?php
                                ksort($socials);

                                foreach ($socials as $id => $social) : ?>
                                <li>
                                <a href="<?= e_attr($social['url']); ?>" class="btn btn-ia-social border-0 rounded-0 
                                    btn-outline-<?= $social['class']; ?>" target="_blank" rel="nofollow" title="<?= e_attr($social['label']); ?>" data-toggle="tooltip">
                                        <?= svg_icon($social['class']); ?>
                                </a>
                            </li>
                                <?php endforeach; ?>
                        </ul>
                            <?php endif; ?>
                        <?php endif; ?>

            </div>


                        <?php if ($count > 3) : ?>
                            <div class="card-footer p-0">
                                <a class="btn btn-block btn-link infobox-toggle py-1">
                                    <?= svg_icon('arrow-down', 'svg-md'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
        </div>
<?php endif; ?>
