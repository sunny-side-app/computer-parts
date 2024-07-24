<?php

namespace Database\Migrations;

require_once "Database/SchemaMigration.php";

use Database\SchemaMigration;

class CreateTagTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE Tag (
            tagID INT PRIMARY KEY AUTO_INCREMENT,
            tagName VARCHAR(255)
        )"
    ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return ["DROP TABLE Tag"];
    }
}