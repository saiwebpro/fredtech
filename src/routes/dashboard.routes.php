<?php
/**
 * Dashboard Route Groups
 *
 */
$app->group('/dashboard', function () use ($app) {

    $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardController:dashboard')->name('dashboard');
    $app->get('/credits', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardController:credits')->name('dashboard.credits');

    /**
     * Account
     */
    $app->group('/account', function () use ($app) {
        // Register
        $app->get('/register', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:register')
            ->name('dashboard.account.register');

        // Register POST
        $app->post('/register', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:registerPOST')
            ->name('dashboard.account.register_post');

        // Sign In
        $app->get('/signin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:signIn')
            ->name('dashboard.account.signin');

        // Sign In POST
        $app->post('/signin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:signInPOST')
            ->name('dashboard.account.signin_post');

        // Forgot password
        $app->get('/forgotpass', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:forgotPass')
            ->name('dashboard.account.forgotpass');

        // Forgot password POST
        $app->post('/forgotpass', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:forgotPassPOST')
            ->name('dashboard.account.forgotpass_post');

        // Reset password
        $app->get('/resetpass/:token', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:resetPass')
            ->name('dashboard.account.resetpass');

        // Forgot password POST
        $app->post('/resetpass/:token', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:resetpassPOST')
            ->name('dashboard.account.resetpass_post');

        // Email verification
        $app->get('/activation', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:emailActivation')
            ->name('dashboard.account.activation');

        // Email verification request
        $app->post('/activation', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:emailActivationPOST')
            ->name('dashboard.account.activation_post');

        // Email verification action
        $app->get('/verify/:token', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:emailVerifyAction')
            ->name('dashboard.account.verify_action');


        $app->get('/settings', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:accountSettings')
            ->name('dashboard.account.settings');

        $app->post('/settings', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:accountSettingsPOST')
            ->name('dashboard.account.settings_post');


        // Sign Out
        $app->post('/logout', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAccountController:logOut')
            ->name('dashboard.account.logout');
    });


    /**
     * Ajax
     */
    $app->group('/ajax', function () use ($app) {

        $app->get('/emailCheck', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAjaxController:emailCheck')
            ->name('dashboard.ajax.email_check');

        $app->get('/usernameCheck', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardAjaxController:usernameCheck')
            ->name('dashboard.ajax.username_check');
    });

    /**
     * Pages
     */
    $app->group('/pages', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:index')
            ->name('dashboard.pages');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:create')
            ->name('dashboard.pages.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:createPOST')
            ->name('dashboard.pages.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:update')
            ->name('dashboard.pages.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:updatePOST')
            ->name('dashboard.pages.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:delete')
            ->name('dashboard.pages.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPagesController:deletePOST')
            ->name('dashboard.pages.delete_post');
    });

    /**
     * Gallery
     */
    $app->group('/gallery', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardGalleryController:index')
            ->name('dashboard.gallery');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardGalleryController:createPOST')
            ->name('dashboard.gallery.create_post');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardGalleryController:deletePOST')
            ->name('dashboard.gallery.delete_post');
    });


    /**
     * Roles
     */
    $app->group('/roles', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:index')
            ->name('dashboard.roles');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:create')
            ->name('dashboard.roles.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:createPOST')
            ->name('dashboard.roles.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:update')
            ->name('dashboard.roles.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:updatePOST')
            ->name('dashboard.roles.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:delete')
            ->name('dashboard.roles.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardRolesController:deletePOST')
            ->name('dashboard.roles.delete_post');
    });
    /**
     * Users
     */
    $app->group('/users', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:index')
            ->name('dashboard.users');

        // List POST
        $app->post('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:indexPOST')
            ->name('dashboard.users_post');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:create')
            ->name('dashboard.users.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:createPOST')
            ->name('dashboard.users.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:update')
            ->name('dashboard.users.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:updatePOST')
            ->name('dashboard.users.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:delete')
            ->name('dashboard.users.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardUsersController:deletePOST')
            ->name('dashboard.users.delete_post');
    });

    /**
     * Plugins
     */
    $app->group('/plugins', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPluginsController:index')
            ->name('dashboard.plugins');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPluginsController:create')
            ->name('dashboard.plugins.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPluginsController:createPOST')
            ->name('dashboard.plugins.create_post');

        // Enable
        $app->post('/enable/:plugin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPluginsController:enable')
            ->name('dashboard.plugins.enable');

        // Disable
        $app->post('/disable/:plugin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPluginsController:disable')
            ->name('dashboard.plugins.disable');

        // Delete
        $app->get('/delete/:plugin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPluginsController:delete')
            ->name('dashboard.plugins.delete');

        // Delete POST
        $app->post('/delete/:plugin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPluginsController:deletePOST')
            ->name('dashboard.plugins.delete_post');
    });


    /**
     * Themes
     */
    $app->group('/themes', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:index')
            ->name('dashboard.themes');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:create')
            ->name('dashboard.themes.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:createPOST')
            ->name('dashboard.themes.create_post');

        // Apply
        $app->post('/apply/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:applyTheme')
            ->name('dashboard.themes.apply');

        // Delete
        $app->get('/delete/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:delete')
            ->name('dashboard.themes.delete');

        // Delete POST
        $app->post('/delete/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardThemesController:deletePOST')
            ->name('dashboard.themes.delete_post');
    });



    /**
     * Settings
     */
    $app->group('/settings', function () use ($app) {
        $app->get('/:type', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:index')
            ->name('dashboard.settings');

        $app->post('/:type', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:indexPOST')
            ->name('dashboard.settings_post');

        $app->get('/plugin/:plugin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:pluginOptions')
            ->name('dashboard.settings.plugin');

        $app->post('/plugin/:plugin', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:pluginOptionsPOST')
            ->name('dashboard.settings.plugin_post');

        $app->get('/theme/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:themeOptions')
            ->name('dashboard.settings.theme');

        $app->post('/theme/:theme', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardSettingsController:themeOptionsPOST')
            ->name('dashboard.settings.theme_post');
    });

    /**
     * Engines
     */
    $app->group('/engines', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:index')
            ->name('dashboard.engines');
        // Post request of setting default engine
        $app->post('/setDefault/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:setDefaultPOST')
            ->name('dashboard.engines.set_post');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:create')
            ->name('dashboard.engines.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:createPOST')
            ->name('dashboard.engines.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:update')
            ->name('dashboard.engines.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:updatePOST')
            ->name('dashboard.engines.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:delete')
            ->name('dashboard.engines.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardEnginesController:deletePOST')
            ->name('dashboard.engines.delete_post');
    });

    
    /**
     * Categories
     */
    $app->group('/categories', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardCategoriesController:index')
            ->name('dashboard.categories');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardCategoriesController:create')
            ->name('dashboard.categories.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardCategoriesController:createPOST')
            ->name('dashboard.categories.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardCategoriesController:update')
            ->name('dashboard.categories.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardCategoriesController:updatePOST')
            ->name('dashboard.categories.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardCategoriesController:delete')
            ->name('dashboard.categories.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardCategoriesController:deletePOST')
            ->name('dashboard.categories.delete_post');
    });

    /**
     * Feeds
     */
    $app->group('/feeds', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:index')
            ->name('dashboard.feeds');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:create')
            ->name('dashboard.feeds.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:createPOST')
            ->name('dashboard.feeds.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:update')
            ->name('dashboard.feeds.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:updatePOST')
            ->name('dashboard.feeds.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:delete')
            ->name('dashboard.feeds.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:deletePOST')
            ->name('dashboard.feeds.delete_post');

        // refresh POST
        $app->post('/refreshFeed/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:refreshFeed')
            ->name('dashboard.feeds.refresh_post');

        // feed actions
        $app->post('/feedActions', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardFeedsController:feedActions')
            ->name('dashboard.feeds.actions_post');
    });

    /**
     * Posts
     */
    $app->group('/posts', function () use ($app) {

        // List
        $app->get('', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:index')
            ->name('dashboard.posts');


        // actions POST
        $app->post('/postActions', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:postActions')
            ->name('dashboard.posts.actions_post');
        // actions POST
        $app->post('/deleteOldPosts', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:deleteOldPosts')
            ->name('dashboard.posts.delete_old');

        // Create
        $app->get('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:create')
            ->name('dashboard.posts.create');

        // Create POST
        $app->post('/create', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:createPOST')
            ->name('dashboard.posts.create_post');

        // Update
        $app->get('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:update')
            ->name('dashboard.posts.update');

        // Update POST
        $app->post('/update/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:updatePOST')
            ->name('dashboard.posts.update_post');

        // Delete
        $app->get('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:delete')
            ->name('dashboard.posts.delete');

        // Delete POST
        $app->post('/delete/:id', CONTROLLER_NAMESPACE . 'Dashboard\\DashboardPostsController:deletePOST')
            ->name('dashboard.posts.delete_post');
    });
});
