<?php

namespace Core\Infrastructure\Database;

interface DatabaseProvider
{
    public function execute(string $statement, array $params = []): void;
    public function fetchAll(string $query, array $params = []): array;
    public function getLastInsertedId(): int;
    public function initTransaction(): void;
    public function commitTransaction(): void;
    public function rollbackTransaction(): void;
}