<?php

namespace Core\Infrastructure\Database;

interface DatabaseProvider
{
    public function execute(string $statement, array $params = []): void;
    public function fetchAll(string $query, array $params = []): array;
}