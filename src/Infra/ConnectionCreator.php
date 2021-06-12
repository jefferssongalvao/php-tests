<?php

namespace PhpTest\Infra;

use PDO;

class ConnectionCreator
{
    private static $pdo = null;

    public static function getConnection(): \PDO
    {
        if (is_null(self::$pdo)) {
            $databasePath = __DIR__ . '/../../db.sqlite';
            self::$pdo = new PDO('sqlite:' . $databasePath);
            self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }

        return self::$pdo;
    }
}