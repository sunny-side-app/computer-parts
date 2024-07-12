<?php

namespace Database\DataAccess;

// ComputerPartDAO に複数の実装があるため、環境設定に基づいて特定の DAO 実装を返す静的なクリエーター関数を持つクラスを作成できます。DAOFactory というクラスを作り、静的なファクトリーメソッド（静的な生成メソッド）を使用します。これらのメソッドはオブジェクトを生成して返します。

require_once __DIR__ . '/Interfaces/ComputerPartDAO.php';
require_once __DIR__ . '/Implementations/ComputerPartDAOImpl.php';
require_once __DIR__ . '/Implementations/ComputerPartDAOMemcachedImpl.php';
require_once __DIR__ . '/../../Helpers/Settings.php';

use Database\DataAccess\Implementations\ComputerPartDAOImpl;
use Database\DataAccess\Implementations\ComputerPartDAOMemcachedImpl;
use Database\DataAccess\Interfaces\ComputerPartDAO;
use Helpers\Settings;

class DAOFactory
{
    public static function getComputerPartDAO(): ComputerPartDAO{
        $driver = Settings::env('DATABASE_DRIVER');

        return match ($driver) {
            'memcached' => new ComputerPartDAOMemcachedImpl(),
            default => new ComputerPartDAOImpl(),
        };
    }
}

// 静的なファクトリーメソッドをファクトリーメソッドパターン、抽象ファクトリー、シンプルファクトリーと混同しないでください。ファクトリーメソッドはサブクラス化に依存し、サブクラスはファクトリーメソッドを実装するだけで、親の手順で異なるオブジェクトが使用されます。抽象ファクトリーはオブジェクトを作成する一連の抽象メソッドを定義し、具体的なクラスはすべての作成方法を実装する必要があります。これにより、MySQLDAOFactory、MemcachedDAOFactory、MockDAOFactoryなど、異なるファミリーのセットが提供されます。

// 最後に、シンプルファクトリーは、タイプ文字列を入力し、そのタイプに基づいて異なるオブジェクトを返す単一の作成機能のみを含みます（例：public static function create(string $type): mixed）静的ファクトリーは元々「静的な生成メソッド」と呼ばれるべきですが、静的なファクトリーメソッドと頻繁に言及されるため、4 種類のファクトリー（静的なファクトリーメソッド、ファクトリーメソッドパターン、シンプルファクトリー、抽象ファクトリー）があります。