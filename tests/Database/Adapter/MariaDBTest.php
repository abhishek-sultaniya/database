<?php

namespace Utopia\Tests\Adapter;

use PDO;
use Redis;
use Utopia\Cache\Adapter\Redis as RedisAdapter;
use Utopia\Cache\Cache;
use Utopia\Database\Adapter\MariaDB;
use Utopia\Database\Database;
use Utopia\Tests\Base;

class MariaDBTest extends Base
{
    /**
     * @var Database
     */
    public static $database = null;

    // TODO@kodumbeats hacky way to identify adapters for tests
    // Remove once all methods are implemented
    /**
     * Return name of adapter
     *
     * @return string
     */
    public static function getAdapterName(): string
    {
        return 'mariadb';
    }

    /**
     * Return row limit of adapter
     *
     * @return int
     */
    public static function getAdapterRowLimit(): int
    {
        return MariaDB::getRowLimit();
    }

    /**
     * @return Database
     */
    public static function getDatabase(): Database
    {
        if (! is_null(self::$database)) {
            return self::$database;
        }

        $dbHost = 'mariadb';
        $dbPort = '3306';
        $dbUser = 'root';
        $dbPass = 'password';

        $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};charset=utf8mb4", $dbUser, $dbPass, MariaDB::getPDOAttributes());
        $redis = new Redis();
        $redis->connect('redis', 6379);
        $redis->flushAll();
        $cache = new Cache(new RedisAdapter($redis));

        $database = new Database(new MariaDB($pdo), $cache);
        $database->setDefaultDatabase('utopiaTests');
        $database->setNamespace('myapp_'.uniqid());

        return self::$database = $database;
    }
}
