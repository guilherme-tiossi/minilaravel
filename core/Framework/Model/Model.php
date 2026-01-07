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
                $conditions[] = sprintf('%s = :%s', $column, $column);
                $params[$column] = $value;
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
    
        return $this->databaseProvider->fetchAll($sql, $params);
    }


    // começar a retornar dados no futuro (boolean)
    public function create(array $data): void
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
    }

    public function update(array $filters, array $data): void
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
