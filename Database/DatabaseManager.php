<?php

namespace Database;

// DatabaseManager は、データベース接続を接続が必要なコードから分離し、HTTP リクエストごとに単一の接続を保持する、接続の開閉、およびデータベース接続の管理に関連するその他の必要な作業を行うことができます。DAO の実装は DatabaseManager を使用して mysql インスタンスを取得します。実際の接続の管理方法はバックエンドの技術スタックによって異なることに注意してください。
// データベース接続を管理するための構造です。静的変数、クラス変数を使用して状態を管理します。その主な使用目的は、HTTP リクエストごとに単一の接続を保持することです。リクエストライフサイクルが終了すると、mysqli は自動的にその接続を閉じるため、リクエスト中に複数の mysqli 接続を作成して閉じる予定がない限り、close 関数を実行する必要はありません。

require_once __DIR__ . '/../Helpers/Settings.php';

use Helpers\Settings;
use Memcached;

class DatabaseManager
{
    protected static array $mysqliConnections = [];
    protected static array $memcachedConnections = [];

    public static function getMysqliConnection(string $connectionName = 'default'): MySQLWrapper {
        if (!isset(static::$mysqliConnections[$connectionName])) {
            static::$mysqliConnections[$connectionName] = new MySQLWrapper();
        }

        return static::$mysqliConnections[$connectionName];
    }
    public static function getMemcachedConnection(string $connectionName = 'default'): Memcached {
        if (!isset(static::$memcachedConnections[$connectionName])) {
            $memcached = new Memcached();
            $memcached->addServer(Settings::env('MEMCACHED_HOST'), Settings::env('MEMCACHED_PORT'));
            static::$memcachedConnections[$connectionName] = $memcached;
        }

        return static::$memcachedConnections[$connectionName];
    }
}