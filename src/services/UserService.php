<?php
namespace App\Services;

use App\Interfaces\IUser;
use App\Repositories\UserRepository;
use App\Helpers\HTTPHelper;
use App\Helpers\DataCleanerHelper;
use App\Models\UserModel;
use App\Traits\Feedback;
/**
 * 
 */
class UserService
{
    use Feedback;
    
    public function getUserWithUsername(string $username)
    {
        if (! empty($username)) {
            $repo = new UserRepository();
            $data = $repo->getUserWithUsername($username);
            if (is_array($data)) {
                return $this->userModel($data);
            } else {
                $this->mergeFeedback($repo);
                return false;
            }
        }
        return false;
    }
    public function getUserWithID(int $id)
    {
        if ($id >= 0) {
            $repo = new UserRepository();
            $data = $repo->getUserWithID($id);
            if (is_array($data)) {
                return $this->userModel($data);
            } else {
                $this->mergeFeedback($repo);
                return false;
            }
        }
        return false;
    }
    public function getUsers()
    {
        $repo = new UserRepository();
        $data = $repo->getUsers();
        if (is_array($data)) {
            $users = [];
            foreach ($data as $key => $value) {
                $users[] = $this->userModel($value);
            }
            return $users;
        } else {
            $this->mergeFeedback($repo);
            return false;
        }
    }
    public function getUsersWithID(array $id)
    {
        $repo = new UserRepository();
        $data = $repo->getUsersWithID($id);
        if (is_array($data)) {
            $users = [];
            foreach ($data as $key => $value) {
                $users[] = $this->userModel($value);
            }
            return $users;
        } else {
            $this->mergeFeedback($repo);
            return false;
        }
    }
    public function updateUser(IUser $oldUser, array $changes)
    {
        $id = $oldUser->id;

        if (! empty($id) && is_numeric($id) && $id > 0) {
            // create factory to make new model with existing data
            $newUser = new UserModel();
            $newUser->id = $oldUser->id;
            $newUser->username = $oldUser->username;
            $newUser->email = $oldUser->email;
            $newUser->firstName = $oldUser->firstName;
            $newUser->lastName = $oldUser->lastName;
            
            foreach ($changes as $key => $value) {
                if (! is_numeric($key) && isset($newUser->$key)) {
                    if (! empty($value)) {
                        $newUser->$key = $value;
                    }
                }
            }
            if ($oldUser != $newUser) {
                $repo = new UserRepository();
                if ($newUser->username !== $oldUser->username) {
                    if (! $repo->uniqueUsername($newUser->username)) {
                        $this->feedback('Username is not unique');
                        return false;
                    }
                }
                if ($newUser->email !== $oldUser->email) {
                    if (! $repo->uniqueEmail($newUser->email)) {
                        $this->feedback('Email is not unique');
                        return false;
                    }
                }
                if ($repo->updateUserFromID($id, $newUser)) {
                    return $newUser;
                } else {
                    $this->mergeFeedback($repo);
                    return false;
                }
            } else {
                $this->feedback('No changes were made to user');
                return false;
            }
        }
        $this->feedback('Failed to update user');
        return false;
    }

    public function getUserUploads(int $id)
    {
        if ($id >= 0) {
            $repo = new UserRepository();
            $data = $repo->getUserUploadsWithID($id);
            if (is_array($data)) {
                return $data;
            } else {
                $this->mergeFeedback($repo);
                return false;
            }
        }
        return false;
    }

    private function userModel(array $data){
        $user = new UserModel();
        $user->id = $data['id'] ?? null;
        $user->username = $data['username'] ?? null;
        $user->email = $data['email'] ?? null;
        $user->firstName = $data['first_name'] ?? null;
        $user->lastName = $data['last_name'] ?? null;
        return $user;
    }
}