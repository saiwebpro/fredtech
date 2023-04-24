<?php

namespace spark\controllers\Dashboard;

use spark\controllers\Dashboard\DashboardController;
use spark\models\UserModel;

/**
* DashboardAjaxController
*
* For handling generic mainstream AJAX Requests
*
* @package spark
*/
class DashboardAjaxController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * @event Fires before DashboardAjaxController is initialized
         */
        do_action('dashboard.ajax_controller_init_before');

        /**
         * @event Fires after DashboardAjaxController is initialized
         */
        do_action('dashboard.ajax_controller_init_after');
    }

    public function logoUpload()
    {
    }

    /**
     * Checks if a email exists or not
     *
     * Http status 200 if exists or 404
     *
     * @return
     */
    public function emailCheck()
    {
        $app = app();
        $email = $app->request->get('email', '');
        $except = trim($app->request->get('except'));
        $userModel = new UserModel;

        $filters['where'][] = ['email', '=', $email];

        if ($except) {
            $filters['where'][] = ['email', '!=', $except];
        }

        $count = (bool) $userModel->countRows(null, $filters);

        if ($count) {
            return response_status(200);
        }

        return response_status(404);
    }

    /**
     * Checks if a username exists or not
     *
     * Http status 200 if exists or 404
     *
     * @return
     */
    public function usernameCheck()
    {
        $app = app();
        $username = $app->request->get('username', '');
        $except = trim($app->request->get('except'));
        $userModel = new UserModel;

        $filters['where'][] = ['username', '=', $username];

        if ($except) {
            $filters['where'][] = ['username', '!=', $except];
        }

        $count = (bool) $userModel->countRows(null, $filters);

        if ($count) {
            return response_status(200);
        }

        return response_status(404);
    }
}
