<?php

namespace Database\Migrations;

require_once "Database/SchemaMigration.php";

use Database\SchemaMigration;

class CreateUserSettingTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE UserSetting (
            entryId INT PRIMARY KEY AUTO_INCREMENT,
            userId BIGINT,
            metaKey VARCHAR(255),
            metaValue VARCHAR(255),
            FOREIGN KEY (userId) REFERENCES User(id) ON DELETE CASCADE
        )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE UserSetting"
    ];
    }
}