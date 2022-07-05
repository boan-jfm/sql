<?php

use mindplay\sql\exceptions\SQLException;
use mindplay\sql\framework\pdo\PDOProvider;
use mindplay\sql\mysql\MySQLDatabase;
use mindplay\sql\postgres\PostgresDatabase;

test(
    'can connect to Postgres',
    function () use ($config) {
        $provider = new PDOProvider(
            PDOProvider::PROTOCOL_POSTGRES,
            $config["postgres"]["database"],
            $config["postgres"]["user"],
            $config["postgres"]["password"]
        );

        $db = new PostgresDatabase();

        $connection = $db->createConnection($provider->getPDO());

        eq($connection->fetch($db->sql('SELECT 123'))->firstCol(), 123);
    }
);

test(
    'can handle PDO exception error mode',
    function () use ($config) {
        $provider = new PDOProvider(
            PDOProvider::PROTOCOL_POSTGRES,
            $config["postgres"]["database"],
            $config["postgres"]["user"],
            $config["postgres"]["password"]
        );

        $db = new PostgresDatabase();
        $pdo = $provider->getPDO();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Default from PHP 8
        $connection = $db->createConnection($pdo);

        expect(SQLException::class, 'pdo exception mode is handled', function () use ($connection, $db) {
            $connection->fetch($db->sql('invalid syntax'))->firstCol();
        });
    }
);

test(
    'can connect to MySQL',
    function () use ($config) {
        $provider = new PDOProvider(
            PDOProvider::PROTOCOL_MYSQL,
            $config["mysql"]["database"],
            $config["mysql"]["user"],
            $config["mysql"]["password"]
        );

        $db = new MySQLDatabase();

        $connection = $db->createConnection($provider->getPDO());

        eq($connection->fetch($db->sql('SELECT 123'))->firstCol(), "123");
    }
);

// TODO tests for prepared statements

// TODO test for PreparedStatement::getRowsAffected()

// TODO integration test for Connection::lastInsertId()

// TODO integration test for driver-generated SQLException-types
