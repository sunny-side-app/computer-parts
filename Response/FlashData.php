<?php

namespace Response;

// システムがデータをフラッシュできるようにするためのメカニズムです。これは、クラスがセッションにメッセージやデータを一時的に保存する機能を提供し、次の HTTP リクエストでアクセスでき、その後自動的に削除されることを意味します。これは、フォーム送信後の成功やエラーメッセージなど、ユーザーに一度だけ通知するメッセージを表示するのに特に役立ちます。


// FlashData クラスには、この一時データを設定（フラッシュ）し、取得するためのメソッドが通常含まれており、単一のユーザーセッション遷移（一つのページから次のページへ）の間だけ持続することを保証します。現在の使用例では、コントローラーはフラッシュにデータを書き込み、HTMLRender ページのHTTP リクエストの最後に、このフラッシュデータは常に取得され、一度取得されるとメッセージは削除されます。

class FlashData {
    public static function setFlashData(string $name, $data): void {
        if(session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'][$name] = $data;
    }

    public static function getFlashData(string $name): mixed {
        if(session_status() === PHP_SESSION_NONE) session_start();

        if (isset($_SESSION['flash'][$name])) {
            $message = $_SESSION['flash'][$name];
            unset($_SESSION['flash'][$name]);
            return $message;
        }

        return null;
    }
}