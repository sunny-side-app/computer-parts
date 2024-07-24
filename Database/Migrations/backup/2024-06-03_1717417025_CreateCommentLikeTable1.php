<?php

namespace Database\Migrations;

require_once "Database/SchemaMigration.php";

use Database\SchemaMigration;

class CreateCommentLikeTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return [
            "CREATE TABLE CommentLike (
                userID BIGINT,
                commentID INT,
                PRIMARY KEY (userID, commentID),
                FOREIGN KEY (userID) REFERENCES User(id) ON DELETE CASCADE,
                FOREIGN KEY (commentID) REFERENCES Comment(commentId) ON DELETE CASCADE
            )"
    ];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [];
    }
}