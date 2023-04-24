<?php

sp_add_sidebar_menu(
    '_management_heading',
    [
        'type' => 'heading',
        'label' => __('Management')
    ]
);

sp_add_sidebar_menu(
    'dashboard',
    [
        'type' => 'link',
        'label' => __('Dashboard'),
        'url' => url_for('dashboard'),
        'icon_html' => svg_icon('analytics'),
        'active_var' => 'dashboard__active',
    ]
);


sp_add_sidebar_menu(
    'pages',
    [
        'type' => 'link',
        'label'      => __('Pages'),
        'icon_html'  => svg_icon('paper'),
        'active_var' => 'pages__active',
        'permission' => 'manage_pages',
        'url' => url_for('dashboard.pages')
    ]
);


sp_add_sidebar_menu(
    'gallery',
    [
        'type' => 'link',
        'label'      => __('Gallery'),
        'icon_html'  => svg_icon('image'),
        'active_var' => 'gallery__active',
        'url' => url_for('dashboard.gallery'),
        'permission' => 'access_gallery|manage_gallery'
    ]
);


sp_add_sidebar_menu(
    'users',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.users'),
        'label'      => __('Users'),
        'icon_html'  => svg_icon('person'),
        'active_var' => 'users__active',
        'permission' => 'add_user|edit_user|delete_user',
    ]
);


sp_add_sidebar_menu(
    'roles',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.roles'),
        'label'      => __('Roles'),
        'icon_html'  => svg_icon('finger-print'),
        'active_var' => 'roles__active',
        'permission' => 'add_role|edit_role|delete_role'
    ]
);


sp_add_sidebar_menu(
    '_content_heading',
    [
        'type' => 'heading',
        'label' => __('Content'),

        'permission' => 'manage_engines|manage_categories|manage_feeds|manage_posts|manage_pages'
    ]
);


sp_add_sidebar_menu(
    'engines',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.engines'),
        'label'      => __('Engines'),
        'icon_html'  => svg_icon('search'),
        'active_var' => 'engines__active',
        'permission' => 'manage_engines'
    ]
);

sp_add_sidebar_menu(
    'categories',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.categories'),
        'label'      => __('Categories'),
        'icon_html'  => svg_icon('bookmark'),
        'active_var' => 'categories__active',
        'permission' => 'manage_categories'
    ]
);

sp_add_sidebar_menu(
    'feeds',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.feeds'),
        'label'      => __('Feeds'),
        'icon_html'  => svg_icon('rss'),
        'active_var' => 'feeds__active',
        'permission' => 'manage_feeds'
    ]
);

sp_add_sidebar_menu(
    'posts',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.posts'),
        'label'      => __('Posts'),
        'icon_html'  => svg_icon('copy'),
        'active_var' => 'posts__active',
        'permission' => 'manage_posts',
    ]
);





sp_add_sidebar_menu(
    '_customization_heading',
    [
        'type' => 'heading',
        'label' => __('Customization'),
        'permission' => 'manage_themes|manage_plugins|change_settings',
    ]
);

sp_add_sidebar_menu(
    'themes',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.themes'),
        'label'      => __('Themes'),
        'icon_html'  => svg_icon('color-palette'),
        'active_var' => 'themes__active',
        'permission' => 'manage_themes'
    ]
);

sp_add_sidebar_menu(
    'plugins',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.plugins'),
        'label'      => __('Plugins'),
        'icon_html'  => svg_icon('outlet'),
        'active_var' => 'plugins__active',
        'permission' => 'manage_plugins'
    ]
);

sp_add_sidebar_menu(
    'settings',
    [
        'type' => 'parent',
        'label' => __('Settings'),
        'icon_html' => svg_icon('settings'),
        'active_var' => 'settings__active',
        'permission' => 'change_settings',
        'children' => [
            'general' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'general']),
                'label' => __('General'),
                'active_var' => 'settings_general__active'
            ],
            'site' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'site']),
                'label' => __('Site'),
                'active_var' => 'settings_site__active'
            ],
            'social' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'social']),
                'label' => __('Social'),
                'active_var' => 'settings_social__active'
            ],
            'services' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'services']),
                'label' => __('Services'),
                'active_var' => 'settings_services__active'
            ],
            'email' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'email']),
                'label' => __('E-Mail'),
                'active_var' => 'settings_email__active'
            ],
            'ads' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'ads']),
                'label' => __('Advertisement'),
                'active_var' => 'settings_ads__active'
            ],
            'debug' => [
                'type' => 'link',
                'url' => url_for('dashboard.settings', ['type' => 'debug']),
                'label' => __('Debugging'),
                'active_var' => 'settings_debug__active'
            ],
        ]
    ]
);


sp_add_sidebar_menu(
    '_misc_heading',
    [
        'type' => 'heading',
        'label' => __('Misc.')
    ]
);

sp_add_sidebar_menu(
    'account',
    [
        'type'       => 'link',
        'url'        => url_for('dashboard.account.settings'),
        'label'      => __('Account Settings'),
        'icon_html'  => svg_icon('contact'),
        'active_var' => 'account__active',
    ]
);
