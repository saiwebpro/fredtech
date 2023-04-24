<?php

namespace spark\drivers\Auth;

use spark\drivers\Auth\Auth;
use spark\models\Model;
use spark\models\RoleModel;
use spark\models\UserModel;

/**
* CurrentUser
*
* @package spark
*/
class CurrentUser
{
    /**
     * Current user fields
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Current user's permissions
     *
     * @var array
     */
    protected $permissions = [];

    /**
     * Current user's logged in status
     *
     * @var boolean
     */
    protected $isLogged = false;

    /**
     * Current user's ID
     *
     * @var integer
     */
    protected $userID;

    /**
     * Check if current user has a permission or not
     *
     * @param  string|array  $perms  String to check a single permission,
     *                               Pass an array to make sure the user has all those permissions.
     *                               Pass a pipe separated string of permissions to check if user
     *                               has either one of the permissions
     * @return boolean
     */
    public function hasPermission($perms)
    {
        if (is_array($perms)) {
            $has = true;
            foreach ($perms as $_p) {
                if (!array_key_exists($_p, $this->permissions)) {
                    $has = false;
                    break;
                }
            }

            return $has;
        }

        // why the fuck am i even writing this comment
        if (array_key_exists($perms, $this->permissions)) {
            return true;
        }

        // Good old OR logic summed up in pipe separated string
        foreach (explode('|', $perms) as $_p) {
            if (array_key_exists($_p, $this->permissions)) {
                return true;
            }
        }

        // final fallback
        return false;
    }

    /**
     * Setup current user
     *
     * @return
     */
    public function setupUser()
    {
        $auth = new Auth;

        // Let's see if we're already in a session or not
        $this->userID = (int) session_get(Auth::SESSION_KEY);

        // If not, try to attempt an auto login
        if (!$this->userID) {
            $this->userID = (int) $auth->attemptAutoLogin();
        }

        // Either way if we have a valid user ID we'll continue
        if ($this->userID > 0) {
            // format current object with the user's data
            $this->handleLoggedUser();
            // intialize user roles
            $this->initRoles();
        }
    }

    /**
     * Get the user ID
     *
     * @return integer
     */
    public function getID()
    {
        return (int) $this->userID;
    }

    /**
     * Check if user is logged or not
     *
     * @return integer
     */
    public function isLogged()
    {
        return $this->isLogged;
    }

    /**
     * Check if user is blocked or not
     *
     * @return boolean
     */
    public function isBlocked()
    {
        return (bool) $this->getField('is_blocked');
    }

    public function getField($field, $fallback = null)
    {
        return isset($this->fields[$field]) ? $this->fields[$field] : $fallback;
    }

    /**
     * Get all user fields
     *
     * @return array
     */
    public function getAllFields()
    {
        return $this->fields;
    }

    protected function handleLoggedUser()
    {
        $this->isLogged = true;

        $userModel = new UserModel;
        $this->fields = $userModel->read($this->userID);

        // If you're blocked you're outta here fam
        if ($this->isBlocked()) {
            $auth = new Auth;
            $auth->logOut();
            $this->isLogged = false;
            return false;
        }

        // update last seen
        $userModel->update($this->userID, ['last_seen' => time()]);
    }

    protected function initRoles()
    {
        $db = app()->db;
        $roleModel = new RoleModel;
        $roleID = $this->getField('role_id', $roleModel::TYPE_USER);
        $this->permissions = $roleModel->getRolePerms($roleID);

        $this->fields['role_name'] = $roleModel->read($roleID, ['role_name'])['role_name'];
    }
}
