<?php
namespace Mixins;


use CustomExceptions\AuthenticationException;
use Models\User;

trait AuthenticateUser
{
    /**
     * @throws AuthenticationException if there's no authenticated user.
     */
    public function authenticateUser(): void
    {
        if(! isset($_SERVER['PHP_AUTH_USER']) && ! isset($_SERVER['PHP_AUTH_PW']))
        {
            throw new AuthenticationException("use basic authentication with your credentials please.");
        }

        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        /**
         * @var User $user
         */
        $user =
            User::query()
                ->where('username', $username)
                ->first();

        if(! $user || $user->password != md5($password))
        {
            throw new AuthenticationException();
        }

    }

}