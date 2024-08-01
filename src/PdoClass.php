<?php

namespace Stilmark\Database;

use PDO;
use PDOException;

class PdoClass
{
    private PDO $pdo;
    private bool $debug = false;
    private array $result = [];

    public function __construct()
    {
        try {
            $dsn = "mysql:host={$_ENV['DB_HOST']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $options);
        } catch (PDOException $e) {
            // Log the error instead of exposing it
            error_log('Database connection failed: ' . $e->getMessage());
            throw new PDOException('Database connection failed');
        }
    }

    public function __call($method, $args)
    {
        if (method_exists($this, $method)) {
            return $this->$method(...$args);
        } else {
            throw new \Exception("Method $method doesn't exist");
        }
    }

    private function debug($sql, $params = [])
    {
        if ($this->debug) {
            echo "SQL: $sql\n";
            echo "Params: " . print_r($params, true) . "\n";
        }
    }

    private function query($sql, $params = [])
    {
        $this->debug($sql, $params);
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    private function row($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    private function key($sql, $params = [])
    {
        $row = $this->row($sql, $params);
        return $row ? array_keys($row)[0] : null;
    }

    private function keys($sql, $params = [])
    {
        $row = $this->row($sql, $params);
        return $row ? array_keys($row) : [];
    }

    private function value($sql, $params = [])
    {
        $row = $this->row($sql, $params);
        return $row ? reset($row) : null;
    }

    private function values($sql, $params = [])
    {
        $row = $this->row($sql, $params);
        return $row ? array_values($row) : [];
    }

    private function list($sql, $params = [])
    {
        return $this->query($sql, $params)->fetchAll();
    }

    private function listId($sql, $params = [], $key = 'id')
    {
        $stmt = $this->query($sql, $params);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[$row[$key]] = $row;
        }
        return $result;
    }

    private function groupId($sql, $params = [], $key = 'id')
    {
        $stmt = $this->query($sql, $params);
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[$row[$key]][] = $row;
        }
        return $result;
    }

    private function listFlat($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $result[$row[0]] = $row[1];
        }
        return $result;
    }

    public function insert_id()
    {
        return $this->pdo->lastInsertId();
    }

    public function affected_rows()
    {
        return $this->query->rowCount();
    }

    public function val($value)
    {
        return $this->pdo->quote($value);
    }

    public static function instance()
    {
        return new self();
    }

    public function close()
    {
        $this->pdo = null;
    }

    // Aliases
    private function idList($sql, $params = [])
    {
        return $this->listId($sql, $params);
    }

    private function flatList($sql, $params = [])
    {
        return $this->listFlat($sql, $params);
    }

    private function groupedList($sql, $params = [])
    {
        return $this->groupId($sql, $params);
    }
}