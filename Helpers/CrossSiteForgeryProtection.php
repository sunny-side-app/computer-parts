<?php
// CSRF トークンを取得するためのヘルパークラス
namespace Helpers;

class CrossSiteForgeryProtection{
    public static function getToken(){
        return $_SESSION['csrf_token'];
    }
}