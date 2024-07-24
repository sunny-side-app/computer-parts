<?php

// ユーザー認証を管理するための静的メソッドを含むクラスです。セッションデータを利用して、USER_ID_SESSION_KEY をキーとし、ユーザーの ID をセッションに書き込みます。認証されたユーザーがデータストアから取得されたら、それをプライベート静的変数 $authenticatedUser に保存し、HTTP リクエストのライフサイクル全体で使用します。

namespace Helpers;

require_once __DIR__ . '/../Database/DataAccess/DAOFactory.php';
require_once __DIR__ . '/../Models/User.php';

use Database\DataAccess\DAOFactory;
use Models\User;

class Authenticate
{
    // 認証されたユーザーの状態をこのクラス変数に保持します
    private static ?User $authenticatedUser = null;
    private const USER_ID_SESSION_KEY = 'user_id';

    public static function loginAsUser(User $user): bool{
        if($user->getId() === null) throw new \Exception('Cannot login a user with no ID.');
        if(isset($_SESSION[self::USER_ID_SESSION_KEY])) throw new \Exception('User is already logged in. Logout before continuing.');

        $_SESSION[self::USER_ID_SESSION_KEY] = $user->getId();
        return true;
    }

    public static function logoutUser(): bool {
        if (isset($_SESSION[self::USER_ID_SESSION_KEY])) {
            unset($_SESSION[self::USER_ID_SESSION_KEY]);
            self::$authenticatedUser = null;
            return true;
        }
        else throw new \Exception('No user to logout.');
    }

    private static function retrieveAuthenticatedUser(): void{
        if(!isset($_SESSION[self::USER_ID_SESSION_KEY])) return;
        $userDao = DAOFactory::getUserDAO();
        self::$authenticatedUser = $userDao->getById($_SESSION[self::USER_ID_SESSION_KEY]);
    }

    public static function isLoggedIn(): bool{
        self::retrieveAuthenticatedUser();
        return self::$authenticatedUser !== null;
    }

    public static function getAuthenticatedUser(): ?User{
        self::retrieveAuthenticatedUser();
        return self::$authenticatedUser;
    }
}