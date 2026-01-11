<?php

namespace Core\Infrastructure\Database;

use Core\Application\Http\Exceptions\AppException;
use PDO;
use PDOException;

class PdoDatabaseProvider implements DatabaseProvider
{
    private PDO $conn;

    public function __construct()
    {
        try {
            $host = getenv('MYSQL_HOST');
            $port = getenv('MYSQL_PORT');
            $username = getenv('MYSQL_USERNAME');
            $password = getenv('MYSQL_PASSWORD');
            $database = getenv('MYSQL_DATABASE');

            $this->conn = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
        } catch (PDOException $e) {
            throw new AppException(500, 'Failed database provider connection: ' . $e->getMessage());
        }
    }

    public function execute(string $query, array $params = []): void
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
    }

    public function fetchAll(string $query, array $params = []): array
    {
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastInsertedId(): int
    {
        return (int) $this->conn->lastInsertId();
    }    
}