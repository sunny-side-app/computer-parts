<?php

namespace Database\Migrations;

require_once __DIR__ . '/../../Database/SchemaMigration.php';
require_once __DIR__ . '/../../Database/DatabaseManager.php';

use Database\SchemaMigration;
use Database\DatabaseManager;

class UpdateComputerPartsTable1 implements SchemaMigration
{
    public function up(): array
    {
        // // マイグレーションロジックをここに追加してください
        // return [
        //     "ALTER TABLE computer_parts ADD COLUMN IF NOT EXISTS submitted_by BIGINT",
        //     "ALTER TABLE computer_parts ADD CONSTRAINT IF NOT EXISTS fk_computer_parts_user FOREIGN KEY (submitted_by) REFERENCES user(id)"
        // ];
        $queries = [];

        // submitted_byカラムの存在をチェック
        $checkColumnQuery = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'computer_parts' AND COLUMN_NAME = 'submitted_by'";
        $result = DatabaseManager::getMysqliConnection()->query($checkColumnQuery);
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            $queries[] = "ALTER TABLE computer_parts ADD COLUMN submitted_by BIGINT";
        }

        // 外部キー制約の存在をチェック
        $checkForeignKeyQuery = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_NAME = 'computer_parts' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_computer_parts_user'";
        $result = DatabaseManager::getMysqliConnection()->query($checkForeignKeyQuery);
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            $queries[] = "ALTER TABLE computer_parts ADD CONSTRAINT fk_computer_parts_user FOREIGN KEY (submitted_by) REFERENCES user(id)";
        }

        return $queries;
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            // 外部キー制約を削除します
            "ALTER TABLE computer_parts DROP FOREIGN KEY IF EXISTS fk_computer_parts_user",
            // submitted_byカラムを削除します
            "ALTER TABLE computer_parts DROP COLUMN IF EXISTS submitted_by"
        ];
    }
}