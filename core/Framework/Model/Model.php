<?php

namespace Core\Framework\Model;

use Core\Infrastructure\Database\DatabaseProvider;

class Model
{
    protected string $table;

    public function __construct(
        protected DatabaseProvider $databaseProvider
    ) {
    }

    // fazer transactions

    public function get(array $filters = []): array
    {
        $where = '';
        $params = [];
    
        if (!empty($filters)) {
            $conditions = [];
        
            foreach ($filters as $column => $value) {
                if (is_null($value)) {
                    $conditions[] = sprintf('%s IS NULL', $column);
                } else {
                    $conditions[] = sprintf('%s = :%s', $column, $column);
                    $params[$column] = $value;
                }
            }
        
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }
    
        $sql = sprintf(
            'SELECT * FROM %s %s',
            $this->table,
            $where
        );
    
        return $this->databaseProvider->fetchAll($sql, $params);
    }

    public function create(array $data): array
    {
        $columns = [];
        $values = [];

        foreach ($data as $column => $value) {
            $columns[] = $column;
            $values[] = ':' . $column;
        }

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->table,
            implode(', ', $columns),
            implode(', ', $values)
        );

        $this->databaseProvider->execute($sql, $data);

        $modelId = $this->databaseProvider->getLastInsertedId();

        return $this->findBy(['id' => $modelId]);
    }

    public function update(array $filters, array $data): array
    {
        $formattedFilters = '';
        foreach ($filters as $column => $value) {
            $formattedFilters .= " AND $column = '$value'";
        }

        $formattedData = '';
        foreach ($data as $column => $value) {
            $formattedData .= ", $column = :$column";
        }

        $formattedData = substr($formattedData, 2);
        $formattedFilters = substr($formattedFilters, 5);
        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $this->table,
            $formattedData,
            $formattedFilters
        );

        $this->databaseProvider->execute($sql, $data);

        return $this->findBy($filters);
    }

    public function findBy(array $filters = []): array
    {
        $where = '';
        $params = [];
    
        if (!empty($filters)) {
            $conditions = [];
        
            foreach ($filters as $column => $value) {
                $conditions[] = sprintf('%s = :%s', $column, $column);
                $params[$column] = $value;
            }
        
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }
    
        $sql = sprintf(
            'SELECT * FROM %s %s LIMIT 1',
            $this->table,
            $where
        );

        $result = $this->databaseProvider->fetchAll($sql, $params);

        return $result ? $result[0] : [];
    }

    public function updateBatch(array $ids, array $data): void
    {
        $formattedData = '';
        foreach ($data as $column => $value) {
            $formattedData .= ", $column = :$column";
        }
        $formattedData = substr($formattedData, 2);

        $idPlaceholders = [];
        foreach ($ids as $index => $id) {
            $placeholder = ":id_$index";
            $idPlaceholders[] = $placeholder;
            $data["id_$index"] = $id;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE id IN (%s)',
            $this->table,
            $formattedData,
            implode(', ', $idPlaceholders)
        );

        $this->databaseProvider->execute($sql, $data);
    }

    public function delete(array $filters): void
    {
        $formattedFilters = '';
        foreach ($filters as $column => $value) {
            $formattedFilters .= " AND $column = '$value'";
        }

        $formattedFilters = substr($formattedFilters, 5);

        $sql = sprintf(
            'DELETE FROM %s WHERE %s',
            $this->table,
            $formattedFilters
        );

        $this->databaseProvider->execute($sql);
    }
}
