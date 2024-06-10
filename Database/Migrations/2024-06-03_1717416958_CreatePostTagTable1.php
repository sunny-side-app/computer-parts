<?php

namespace Database\Migrations;

require_once "Database/SchemaMigration.php";

use Database\SchemaMigration;

class CreatePostTagTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE PostTag (
            postID INT,
            tagID INT,
            PRIMARY KEY (postID, tagID),
            FOREIGN KEY (postID) REFERENCES Post(id) ON DELETE CASCADE,
            FOREIGN KEY (tagID) REFERENCES Tag(tagID) ON DELETE CASCADE
        )"
    ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE PostTag"
        ];
    }
}