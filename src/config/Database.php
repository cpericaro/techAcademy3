<?php

declare(strict_types=1);

final class Database
{
    protected PDO $connection;

    public function __construct()
    {
        $this->connect();
    }

    private function connect(): void
    {
        $env = parse_ini_file(dirname(__DIR__, 2) . '/.env');

        if ($env === false) {
            throw new RuntimeException('Não foi possível carregar o arquivo .env.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $env['DB_HOST'],
            $env['DB_PORT'] ?? '3306',
            $env['DB_NAME'],
            $env['DB_CHARSET'] ?? 'utf8mb4',
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $env['DB_USER'],
                $env['DB_PASSWORD'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw new RuntimeException('Erro ao conectar ao banco de dados.', 0, $e);
        }
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
    // https://www.w3schools.com/php/php_mysql_connect.asp
}
