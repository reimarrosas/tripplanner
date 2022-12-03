<?php

namespace app\models;

class UserModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    // Getting all users
    public function getAllUsers(): array
    {
        $query = 'SELECT * FROM user';
        return $this->fetchAll($query);
    }

    public function getUserByUsername(string $username): array
    {
        $query = 'SELECT * FROM user WHERE username = :username';
        return $this->fetchSingle($query, ['username' => $username]);
    }

    // Getting a user by id 
    public function getUserById(int $user_id): array
    {
        $query = 'SELECT * FROM user WHERE user_id = :user_id';
        return $this->fetchSingle($query, ['user_id' => $user_id]);
    }

    // Adding a user
    public function createUser(array $user): int
    {
        $query =
            'INSERT INTO user ' .
            '(username, password, permission) ' .
            'VALUES ' .
            '(:username, :password, :permission)';
        return $this->execute($query, $user);
    }

    // Updating a user 
    public function updateUser(array $user): int
    {
        $query = 'UPDATE user SET';
        foreach ($user as $key => $value) {
            if ($key != 'user_id') {
                $query .= " $key = :$key,";
            }
        }
        $query = rtrim($query, ',') . ' WHERE user_id = :user_id';
        return $this->execute($query, $user);
    }

    // Deleting a user
    public function deleteUser(int $user_id): int
    {
        $query = 'DELETE FROM user WHERE user_id = :user_id';
        return $this->execute($query, ['user_id' => $user_id]);
    }
}