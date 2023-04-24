<?php

namespace spark\drivers\Nav;

/**
* BreadCrumbs Generator
*
* @package spark
*/
class BreadCrumbs
{
    protected $links = [];

    public function add($id, $label, $url)
    {
        $this->links[$id] = [
            'label' => $label,
            'url'   => $url
        ];
        return $this;
    }

    public function remove($id)
    {
        unset($this->links[$id]);
        return $this;
    }

    public function getAll()
    {
        return $links;
    }

    public function renderHtml($before = '', $after = '')
    {
        $links = $this->links;

        if (!is_array($links) || empty($links)) {
            return '';
        }

        $total = count($links);

        if ($total < 2) {
            return '';
        }

        $scope = 'itemscope itemtype="https://schema.org/BreadcrumbList"';

        $before = trim($before);
        $after = trim($after);

        if ($before === '' && $after === '') {
            $before = '<ol ' . $scope . ' class="breadcrumb">';
            $after = '</ol>';
        } else {
            $before = preg_replace('/^<(\w+)/', '<$1 '. $scope . '', $before);
        }

        $breadcrumbs = '';
        $breadcrumbs .= $before;
        $i = 1;

        $markup = ' itemprop="itemListElement" itemscope
        itemtype="https://schema.org/ListItem" ';

        foreach ($links as $link) {
            $label = html_escape($link['label'], false);
            $url = html_escape($link['url'], false);

            $labelMarkup = '<span itemprop="name">' . $label . '</span>';

            $meta = '<meta itemprop="position" content="' . $i . '" />';

            if ($i === $total) {
                $breadcrumbs .= '<li' . $markup . ' class="breadcrumb-item active">' . $labelMarkup . ' ' . $meta . '</li>';
            } else {
                $breadcrumbs .= '
                <li ' . $markup . ' class="breadcrumb-item">
                <a href="' . $url . '" itemscope itemtype="https://schema.org/WebPage"
                itemprop="item" itemid="'.$url.'">' . $labelMarkup . ' </a> ' . $meta . '
                </li>';
            }

            $i++;
        }
        $breadcrumbs .= $after;
        return $breadcrumbs;
    }
}
