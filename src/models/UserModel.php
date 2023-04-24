<?php

namespace spark\models;

/**
* Model for Users
*
* @package spark
*/
class UserModel extends Model
{
    protected static $table = 'users';

    protected $queryKey = 'user_id';

    protected $autoTimestamp = true;

    protected $sortRules = [
        'newest'          => ['created_at' => 'DESC'],
        'oldest'          => ['created_at' => 'ASC'],
        'a2z'             => ['full_name'  => 'ASC'],
        'z2a'             => ['full_name'  => 'DESC'],
        'recently-active' => ['last_seen' => 'DESC'],
        'rarely-active'   => ['last_seen' => 'ASC'],
        'verified'        =>   ['is_verified' => 'DESC'],
        'not-verified'    => ['is_verified' => 'ASC'],
        'blocked'         =>   ['is_blocked' => 'DESC'],
        'not-blocked'      => ['is_blocked' => 'ASC'],
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function addUser($email, $password, array $data)
    {
        $data['email']    = $email;
        $data['password'] = sp_password_hash($password);

        if (empty($data['last_seen'])) {
            $data['last_seen'] = time();
        }

        if (empty($data['user_ip'])) {
            $data['user_ip'] = app()->request->getIp();
        }

        if (empty($data['role_id'])) {
            $data['role_id'] = RoleModel::TYPE_USER;
        }

        $id = $this->create($data);

        return $id;
    }

    public function updateUser($userID, array $data)
    {
        if (!empty($data['password'])) {
            $data['password'] = sp_password_hash($data['password']);
        }

        $status = $this->update($userID, $data);

        return $status;
    }

    public function blockUser($userID)
    {
        $data = [
            'is_blocked' => 1
        ];

        return $this->update($userID, $data);
    }

    public function deleteUser($userID)
    {
        $status = $this->delete($userID);
        return $status;
    }
}
