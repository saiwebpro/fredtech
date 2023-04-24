<?php

namespace spark\controllers\Dashboard;

use Upload\File;
use Upload\Storage\FileSystem;
use Upload\Validation\Extension;
use Upload\Validation\Size;
use Valitron\Validator;
use spark\controllers\Dashboard\DashboardController;
use spark\drivers\Auth\Auth;
use spark\drivers\Mail\Mailer;
use spark\models\AttemptModel;
use spark\models\TokenModel;
use spark\models\UserModel;

/**
* DashboardAccountController
*
* @package spark
*/
class DashboardAccountController extends DashboardController
{
    public function __construct()
    {
        parent::__construct();

        /**
         * @event Fires before DashboardAccountController is initialized
         */
        do_action('dashboard.account_controller_init_before');

        // robots may crawl the auth pages
        view_set('allow_robots', true);

        // Load form validator
        sp_enqueue_script('parsley', 2, ['dashboard-core-js']);

        /**
         * @event Fires after DashboardAccountController is initialized
         */
        do_action('dashboard.account_controller_init_after');
    }

    /**
     * Sign In Page
     *
     * @return
     */
    public function signIn()
    {
        if (is_logged()) {
            return follow_redirect_to_uri(url_for('dashboard'));
        }

        sp_enqueue_script('jquery-countdown', 2, ['dashboard-core-js']);

        $auth = new Auth;
        $blockedTime = $auth->getSignInAttemptBlockedTime();

        $data = [
            'form_countdown' => $blockedTime
        ];

        return view('admin::account/sign_in.php', $data);
    }

    /**
     * Handles sign in proccess
     *
     * @return
     */
    public function signInPOST()
    {
        if (is_logged()) {
            return follow_redirect_to_uri(url_for('dashboard'));
        }

        $app = app();
        $req = $app->request;

        $data = [
            'email'       => $req->post('email'),
            'password'    => $req->post('password'),
            'remember_me' => (int) $req->post('remember_me'),
        ];

        $auth = new Auth;

        $blockedTime = $auth->getSignInAttemptBlockedTime();

        if ($blockedTime) {
            $timeLeftStr = gmdate('i\m\, s\s', $blockedTime);
            flash(
                'account-danger',
                sprintf(
                    __('You have reached maximum amount of failed login attempts. Please wait %s before trying to log in again.'),
                    $timeLeftStr
                )
            );
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $v = new Validator($data);
        $v->labels([
            'email' => __("E-Mail"),
            'password' => __("Password"),
        ])->rule('required', ['email', 'password'])
          ->rule('email', 'email')
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'));

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        try {
            $userID = $auth->attempt($data['email'], $data['password']);
        } catch (\Exception $e) {
            flash('account-danger', $e->getMessage());

            $attemptModel = new AttemptModel;
            $attemptModel->logSignInAttempt();

            sp_store_post($data);
            return redirect_to_current_route();
        }

        // Everything all right?
        $auth->buildSession($userID, $data['remember_me']);
        return follow_redirect_to_uri(url_for('dashboard'));
    }

    /**
     * Registration page
     *
     * @return
     */
    public function register()
    {
        if (is_logged()) {
            return follow_redirect_to_uri(url_for('dashboard'));
        }

        if (get_option('captcha_enabled')) {
            sp_enqueue_script('google-recaptcha', 2);
        }

        $data = [
        ];

        return view('admin::account/register.php', $data);
    }

    /**
     * Handles registration
     *
     * @return
     */
    public function registerPOST()
    {
        $registrationDefaultRedirectURI = url_for('dashboard');

        if (is_logged()) {
            return follow_redirect_to_uri($registrationDefaultRedirectURI);
        }


        $app = app();
        $req = $app->request;

        $data = [
            'email'       => $req->post('email'),
            'password'    => $req->post('password'),
            'gender'      => (int) $req->post('gender'),
            'full_name'   => sp_strip_tags($req->post('full_name'), true),
        ];

        if (!config('site.registration_enabled', true)) {
            flash('account-danger', __('Sorry, registration is disabled by the site administrator'));
            sp_store_post($data);
            return redirect_to_current_route();
        }

        if (!sp_verify_recaptcha()) {
            flash('account-danger', __('Invalid captcha. Please fill the captcha properly'));
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $v = new Validator($data);

        $v->labels([
            'email'     => __('E-Mail'),
            'password'  => __('Password'),
            'full_name' => __('Full Name'),
        ])->rule('required', ['email', 'password', 'full_name'])
          ->rule('email', 'email')
          ->rule('uniqueEmail', 'email')
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMax', 'full_name', 200);

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        $userModel = new UserModel;
        $auth = new Auth;
        $filters = [];
        $filters['where'][] = ['user_ip', '=', $req->getIp()];
        $accountsUnderThisIP = $userModel->countRows(null, $filters);
        $maxAccountsPerIP = (int) config('auth.max_account_per_ip', 0);

        if ($maxAccountsPerIP && $accountsUnderThisIP >= $maxAccountsPerIP) {
            sp_store_post($data);
            flash('account-danger', sprintf(__('You have reached the limit of accounts per IP address. We only allow %s accounts per IP.'), $maxAccountsPerIP));
            return redirect_to_current_route();
        }

        $userID = $userModel->addUser($data['email'], $data['password'], $data);

        if (!$userID) {
            flash('account-danger', $userModel::DB_ERROR);
            return redirect_to_current_route();
        }

        $auth->buildSession($userID, true);
        $auth->sendActivationEmail($data['email'], $userID, $data);

        return follow_redirect_to_uri($registrationDefaultRedirectURI);
    }


    /**
     * Email Activation
     *
     * @return
     */
    public function emailActivation()
    {
        if (current_user_field('is_verified')) {
            return follow_redirect_to_uri();
        }

        sp_enqueue_script('jquery-countdown', 2, ['dashboard-core-js']);

        $app = app();
        $auth = new Auth;
        $blockedTime = $auth->getEmailTokenRequestWaitingTime();

        $data = [
            'user' => $app->user->getAllFields(),
            'form_countdown' => $blockedTime,
        ];

        return view('admin::account/email_activation.php', $data);
    }

    /**
     * Handles email activation token request
     *
     * @return
     */
    public function emailActivationPOST()
    {
        if (current_user_field('is_verified')) {
            return follow_redirect_to_uri();
        }

        $user = get_logged_user();
        $auth = new Auth;
        $blockedTime = $auth->getEmailTokenRequestWaitingTime();

        if ($blockedTime) {
            $timeLeftStr = gmdate('i\m\, s\s', $blockedTime);
            flash(
                'account-danger',
                sprintf(
                    __('Please wait %s before requesting any more account verification E-mails'),
                    $timeLeftStr
                )
            );

            return redirect_to_current_route();
        }

        $data = $user->getAllFields();

        if ($auth->sendActivationEmail($data['email'], $data['user_id'], $data)) {
            flash(
                'account-success',
                sprintf(__('An email with instructions on how to activate your account was sent to %s'), $data['email'])
            );
        } else {
            flash('account-danger', __('Unknown error at mailer. Please check your server configuration.'));
        }

        return redirect_to_current_route();
    }

    /**
     * Email verification page
     *
     * @param  string $token
     * @return
     */
    public function emailVerifyAction($token)
    {
        // but not this one!
        view_set('allow_robots', false);

        if (is_logged() && current_user_field('is_verified')) {
            return redirect_to('dashboard');
        }

        $tokenModel = new TokenModel;
        $dbToken = $tokenModel->getToken($token, $tokenModel::TYPE_EMAIL);

        $type = 'danger';

        $msg = __('Thanks for verifying your E-Mail address. Now you can continue using the site');

        if (!$dbToken || $dbToken['token_expires'] < time()) {
            $msg = __('Sorry, the link is invalid or expired. Please request a new one');
        } else {
            $type = 'success';
            $userModel = new UserModel;
            $userModel->updateUser($dbToken['user_id'], ['is_verified' => 1]);

            $tokenModel->deleteTokens($tokenModel::TYPE_EMAIL, $dbToken['user_id']);
        }


        $data = [
            'message' => $msg,
            'type' => $type,
        ];

        return view('admin::account/email_verify_action.php', $data);
    }

    /**
     * Forgot password page
     *
     * @return
     */
    public function forgotPass()
    {
        sp_enqueue_script('jquery-countdown', 2, ['dashboard-core-js']);

        if (get_option('captcha_enabled')) {
            sp_enqueue_script('google-recaptcha', 2);
        }

        $app = app();
        $auth = new Auth;
        $blockedTime = $auth->getForgotPassTokenRequestWaitingTime();

        $data = [
            'form_countdown' => $blockedTime,
        ];

        return view('admin::account/forgot_pass.php', $data);
    }

    /**
     * Processes Forgot Password Request
     *
     * @return
     */
    public function forgotPassPOST()
    {
        if (is_demo()) {
            flash('account-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to_current_route();
        }

        $app = app();
        $req = $app->request;

        $email = trim($req->post('email'));


        if (!sp_verify_recaptcha()) {
            flash('account-danger', __('Invalid captcha. Please fill the captcha properly'));
            sp_store_post(['email' => $email]);
            return redirect_to_current_route();
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('account-danger', __('Please provide a valid E-Mail'));
            return redirect_to_current_route();
        }

        $auth = new Auth;
        $blockedTime = $auth->getForgotPassTokenRequestWaitingTime();

        if ($blockedTime) {
            $timeLeftStr = gmdate('i\m\, s\s', $blockedTime);
            flash(
                'account-danger',
                sprintf(
                    __('Please wait %s before requesting any more password reset email'),
                    $timeLeftStr
                )
            );

            return redirect_to_current_route();
        }

        if (!is_logged()) {
            $userModel = new UserModel;
            $user = $userModel->fetchRow('email', $email, ['full_name', 'user_id', 'email']);
        } else {
            $user = get_logged_user()->getAllFields();
        }

        if (!$user) {
            flash(
                'account-success',
                sprintf(__('An email with instructions on how to reset your password was sent to %s'), $email)
            );

            return redirect_to_current_route();
        }

        $auth = new Auth;
        if ($auth->sendForgotPassEmail($user['email'], $user['user_id'], $user)) {
            flash(
                'account-success',
                sprintf(__('An email with instructions on how to reset your password was sent to %s'), $user['email'])
            );
        } else {
            flash('account-danger', __('Unknown error at mailer. Please check your server configuration.'));
        }

        return redirect_to_current_route();
    }

    /**
     * Reset password page
     *
     * @param  string $token
     * @return
     */
    public function resetPass($token)
    {
        // but not this one!
        view_set('allow_robots', false);

        $tokenModel = new TokenModel;
        $dbToken = $tokenModel->getToken($token, $tokenModel::TYPE_FORGOT_PASS);
        $invalid = false;

        if (!$dbToken || $dbToken['token_expires'] < time()) {
            $invalid = true;
        } else {
            $userModel = new UserModel;
            $user = $userModel->read($dbToken['user_id'], ['email']);
            if (!$user) {
                $invalid = true;
            }
        }


        $data = [
            'invalid' => $invalid,
        ];

        return view('admin::account/reset_pass.php', $data);
    }

    /**
     * Reset password request processing
     *
     * @param  string $token
     * @return
     */
    public function resetPassPOST($token)
    {
        if (is_demo()) {
            flash('account-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to_current_route();
        }

        $tokenModel = new TokenModel;
        $dbToken = $tokenModel->getToken($token, $tokenModel::TYPE_FORGOT_PASS);


        if (!$dbToken || $dbToken['token_expires'] < time()) {
            return redirect_to_current_route();
        }

        $app = app();
        $req = $app->request;

        $data = [
            'password'         => $req->post('password'),
            'confirm_password' => $req->post('confirm_password'),
        ];


        $v = new Validator($data);

        $v->labels([
            'password'  => __('New Password'),
            'confirm_password'  => __('Confirm New Password'),
        ])->rule('required', ['password', 'confirm_password'])
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMin', 'confirm_password', (int) config('internal.password_minlength'))
          ->rule('equals', 'password', 'confirm_password')
          ->message(__("Passwords doesn't match"));

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('account-danger', $errors);
            return redirect_to_current_route();
        }

        $userModel = new UserModel;
        $user = $userModel->read($dbToken['user_id'], ['email']);

        if (!$user) {
            flash('account-danger', __('The user account associated with this link has been removed.'));
            return redirect_to_current_route();
        }

        // finally some peace of mind

        $userModel->updateUser($dbToken['user_id'], ['password' => $data['password']]);

        $tokenModel = new TokenModel;
        $tokenModel->deleteTokens($tokenModel::TYPE_FORGOT_PASS, $dbToken['user_id']);

        $auth = new Auth;
        $auth->logOut();

        flash('account-success', __('Your password was changed successfully'));
        return redirect_to('dashboard.account.signin');
    }

    /**
     * Account settings page
     *
     * @return
     */
    public function accountSettings()
    {
        // but not this one!
        view_set('allow_robots', false);

        breadcrumb_add('dashboard.account.setings', __('Account Settings'));

        $app = app();

        $data = [
            'user' => $app->user->getAllFields(),
            'account__active' => 'active'
        ];

        return view('admin::account/account_settings.php', $data);
    }

    /**
     * Processes user account settings
     *
     * @return
     */
    public function accountSettingsPOST()
    {
        if (is_demo()) {
            flash('account-info', $GLOBALS['_SPARK_I18N']['demo_mode']);
            return redirect_to_current_route();
        }

        $app = app();
        $req = $app->request;

        $data = [
            'email'     => $req->post('email'),
            'password'  => $req->post('password'),
            'old_password'  => $req->post('old_password'),
            'username'  => $req->post('username'),
            'full_name' => sp_strip_tags($req->post('full_name'), true),
            'gender'    => (int) $req->post('gender')
        ];


        $v = new Validator($data);

        $v->labels([
            'email'     => __('E-Mail'),
            'password'  => __('Password'),
            'username'  => __('Username'),
            'full_name' => __('Full Name'),
        ])->rule('required', ['email'])
          ->rule('email', 'email')
          ->rule('uniqueEmail', 'email', current_user_field('email'))
          ->rule('uniqueUsername', 'username', current_user_field('username'))
          ->rule('lengthMin', 'password', (int) config('internal.password_minlength'))
          ->rule('lengthMax', 'full_name', 200)
          ->rule('username', 'username');

        if (!$v->validate()) {
            $errors = sp_valitron_errors($v->errors());
            flash('account-danger', $errors);
            sp_store_post($data);
            return redirect_to_current_route();
        }

        // Password changing logic
        if (!empty($data['password'])) {
            // You need the old password
            if (empty($data['old_password'])) {
                flash('account-danger', __("You must provide your current password in order to change your password"));
                sp_store_post($data);
                return redirect_to_current_route();
            }

            // And that should be correct
            if (!password_verify($data['old_password'], current_user_field('password'))) {
                flash('account-danger', __("Your current password is incorrect"));
                sp_store_post($data);
                return redirect_to_current_route();
            }
        } else {
            unset($data['password']);
        }

        $forceGravatar = (bool) $req->post('force_gravatar');
        $currentAvatar = current_user_field('avatar');

        // Let's handle avatar upload
        if (!empty($_FILES['avatar']['name']) && !$forceGravatar) {
            $dir = sitepath('avatars');
            $storage = new FileSystem($dir, true);
            $file = new File('avatar', $storage);
            $fileName = md5(current_user_ID()) . uniqid();

            $file->setName($fileName);

            $file->addValidations([
                new Extension(['jpg', 'jpeg', 'png', 'gif']),
                new Size(config('internal.avatar_maxsize'))
            ]);

            try {
                $file->upload();
                $data['avatar'] = trailingslashit(SITE_DIR) . 'avatars/' . $fileName . '.' . $file->getExtension();

                if (is_file($currentAvatar)) {
                    @unlink($currentAvatar);
                }
            } catch (\Exception $e) {
                $errors = join($file->getErrors(), "<br>");
                flash('account-warning', sprintf(__("Failed to change avatar. Reason: %s"), $errors));
            }
        }

        if ($forceGravatar) {
            if (is_file($currentAvatar)) {
                @unlink($currentAvatar);
            }

            $data['avatar'] = null;
        }


        unset($data['old_password']);

        /*/ if the email is a new one, mark the user as unverified
        if ($data['email'] !== current_user_field('email')) {
            $data['is_verified'] = 0;
        }*/

        $userModel = new UserModel;
        $userModel->updateUser(current_user_ID(), $data);

        flash('account-success', __("Your account was updated successfully"));
        return redirect_to_current_route();
    }

    /**
     * Handles log out process
     *
     * @return
     */
    public function logOut()
    {
        $auth = new Auth;
        $auth->logOut();

        return follow_redirect_to_uri(url_for('dashboard.account.signin'));
    }
}
