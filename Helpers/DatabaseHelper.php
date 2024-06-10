<?php

namespace Helpers;

require_once("Database/MySQLWrapper.php");

use Database\MySQLWrapper;
use Exception;

class DatabaseHelper
{
    public static function getRandomComputerPart(): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        $result = $stmt->get_result();
        // https://www.php.net/manual/ja/mysqli-result.fetch-assoc.php 連想配列として単一の行を返すため、１レコードを取得する場合に適している
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartById(int $id): array{
        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $part = $result->fetch_assoc();

        if (!$part) throw new Exception('Could not find a single part in database');

        return $part;
    }

    public static function getComputerPartsByType(string $type, int $page, int $perPage): array{
        $db = new MySQLWrapper();

        $offset = ($page - 1) * $perPage;
        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ? LIMIT ? OFFSET ?");
        $stmt->bind_param('sii', $type, $perPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        // fetch_all(): 連想配列の配列として全行を返すため、指定された条件に合致する複数のレコードを取得する場合に適している
        $parts = $result->fetch_all(MYSQLI_ASSOC);

        return $parts; // 空のリストが返されることを許容
    }

    public static function getNewestComputerParts(int $page, int $perPage): array{

        $db = new MySQLWrapper();

        $stmt = $db->prepare("SELECT * FROM computer_parts ORDER BY created_at  LIMIT ? OFFSET ?");
        $stmt->bind_param('ii', $perPage, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $parts = $result->fetch_all(MYSQLI_ASSOC);

        if (!$parts) throw new Exception('Could not find a single part in database');

        return $parts;

    }

    public static function getPerformanceComputerParts(string $type, string $order): array {
        $db = new MySQLWrapper();
        // 画面からASCかDESCか指定することは想定しておらず外部からの入力によって変更される可能性がないと考えているためbind_param()で渡さない
        $order = strtoupper($order) === 'ASC' ? 'ASC' : 'DESC';
        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ? ORDER BY performance_score $order LIMIT 50");
        $stmt->bind_param('s', $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $parts = $result->fetch_all(MYSQLI_ASSOC);

        if (!$parts) throw new Exception('Could not find any parts in database');

        return $parts;
    }

    public static function getRandomComputerPartByType(string $type): array {
        $db = new MySQLWrapper();
    
        $stmt = $db->prepare("SELECT * FROM computer_parts WHERE type = ? ORDER BY RAND() LIMIT 1");
        $stmt->bind_param('s', $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $part = $result->fetch_assoc();
    
        if (!$part) throw new Exception('Could not find a single part in database');
    
        return $part;
    }
}