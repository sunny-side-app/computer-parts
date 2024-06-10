<?php

namespace Database\Migrations;

require_once "Database/SchemaMigration.php";

use Database\SchemaMigration;

class CreatePostLikeTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE PostLike (
                userID BIGINT,
                postID INT,
                PRIMARY KEY (userID, postID),
                FOREIGN KEY (userID) REFERENCES User(id) ON DELETE CASCADE,
                FOREIGN KEY (postID) REFERENCES Post(id) ON DELETE CASCADE
            )"
        ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE PostLike"
        ];
    }
}