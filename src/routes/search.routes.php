<?php

use spark\controllers\Site\SiteController;

/**
 * Route for the search page
 */
$app->get('/search', CONTROLLER_NAMESPACE . 'Site\\SiteController:search')->name('site.search');
$app->get('/suggestQueries', CONTROLLER_NAMESPACE . 'Site\\SiteController:suggestQueries')->name('site.suggest_queries');

$app->get('/category/:slug', CONTROLLER_NAMESPACE . 'Site\\SiteController:category')->name('site.category');
$app->get('/external/:id', CONTROLLER_NAMESPACE . 'Site\\SiteController:feedRedirect')->name('site.redirect');
$app->get('/:slug-:id.html', CONTROLLER_NAMESPACE . 'Site\\SiteController:readArticle')->name('site.read');
