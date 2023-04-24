<?php

use spark\controllers\Site\SiteController;

/**
 * Route for the homepage
 */
$app->get('/', CONTROLLER_NAMESPACE . 'Site\\SiteController:home')->name('site.home');

/**
 * Route for changing the frontend locale
 */
$app->get('/language/:locale', CONTROLLER_NAMESPACE . 'Site\\SiteController:changeLocale')->name('site.change_locale');

/**
 * Route for site pages
 */
$app->any('/page/:identifier', CONTROLLER_NAMESPACE . 'Site\\SiteController:page')->name('site.page');

/**
 * Log out route
 */
$app->post('/logout', CONTROLLER_NAMESPACE . 'Site\\SiteController:logOut')->name('site.logout');

/**
 * Route to handle contact form
 */
$app->post('/handleContactForm', CONTROLLER_NAMESPACE . 'Site\\SiteController:handleContactForm')->name('site.contact_form_action');

/**
 * Route for generating thumbnails
 */
$app->get('/thumbnail', CONTROLLER_NAMESPACE . 'Site\\SiteController:thumbnail')->name('thumbnail');

/**
 * Route for site search
 */
$app->get('/archive', CONTROLLER_NAMESPACE . 'Site\\SiteController:archive')->name('site.archive');


/**
 * Cron Job Tasks
 */
$app->get('/runtasks', CONTROLLER_NAMESPACE . 'Site\\SiteController:runTasks')->name('tasks');


/**
 * Route for 404 page
 */
$app->notFound(function () use ($app) {
    return (new SiteController)->notFound();
});
