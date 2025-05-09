<?php

namespace Modules\User\Guards;

use Modules\User\Entities\User;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\Authenticatable;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel as SentinelFacade;

class Sentinel implements Guard
{
    /**
     * Determine if the current user is a guest.
     *
     * @return User|bool
     */
    public function guest()
    {
        return SentinelFacade::guest();
    }


    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        return SentinelFacade::getUser();
    }


    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|null
     */
    public function id()
    {
        if ($user = SentinelFacade::check()) {
            return $user->id;
        }

        return null;
    }


    /**
     * Determine if the current user is authenticated.
     *
     * @return User|bool
     */
    public function check()
    {
        return SentinelFacade::check();
    }


    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        return SentinelFacade::validForCreation($credentials);
    }


    /**
     * Set the current user.
     *
     * @param Authenticatable $user
     * @param User|bool
     */
    public function setUser(Authenticatable $user)
    {
        return SentinelFacade::login($user);
    }


    /**
     * Alias to set the current user.
     *
     * @param Authenticatable $user
     *
     * @return User|bool
     */
    public function login(Authenticatable $user)
    {
        return $this->setUser($user);
    }


    /**
     * Attempt to logging in user.
     *
     * @param array $credentials
     * @param bool $remember
     *
     * @return User|bool
     */
    public function attempt(array $credentials, $remember = false)
    {
        return SentinelFacade::authenticate($credentials, $remember);
    }


    /**
     * Logout user.
     *
     * @return bool
     */
    public function logout()
    {
        return SentinelFacade::logout();
    }


    /**
     * Login using user id.
     *
     * @param int $userId
     *
     * @return User|bool
     */
    public function loginUsingId($userId)
    {
        $user = User::findOrFail($userId);

        return $this->login($user);
    }


    public function hasUser()
    {
        // TODO: Implement hasUser() method.
    }
    public function onceUsingId($userId)
    {
        $user = $this->loginUsingId($userId);
        if (!empty($user)) {
            return $user;
        } else {
            return null;
        }
    }
}
