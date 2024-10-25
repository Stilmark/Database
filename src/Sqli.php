<?php

namespace Stilmark\Database;

class Sqli
{

    private bool $debug;
    private array $result;
    private object $mysqli;

    function __construct()
	{
        $this->debug = false;
        $this->result = [];
        $this->mysqli = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

        if ($this->mysqli->connect_error) {
            die('Database error: ' . $this->mysqli->connect_error);
        }

        $this->mysqli->set_charset('utf8');
	}

    public function __call($method, $args){

        $this->debug(current($args));

        if(method_exists($this, $method)) {
            return $this->$method(...$args);
        } else {
            throw new Exception("Method doesn't exist");
        }
    }

    private function debug($sql)
    {
        if ($this->debug) {
            die($sql.';'.PHP_EOL);
        }
    }

    private function query($sql)
    {
        return $this->mysqli->query($sql);        
    }

    private function row($sql)
    {
        if ($query = $this->query($sql)) {
            $this->result = $query->fetch_assoc();
            $query->free();
            return $this->result;
        }
    }

    private function key($sql)
    {
        return array_keys($this->row($sql))[0];
    }

    private function keys($sql)
    {
        $row = $this->row($sql) ?? [];
        return array_keys($row);
    }

    private function value($sql)
    {
        return array_values($this->row($sql))[0];
    }

    private function values($sql)
    {
        $row = $this->row($sql) ?? [];
        return array_values($row);
    }

    private function list($sql)
    {
        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[] = $row;
            }
            $query->free();
            return $this->result;
        }
    }

    private function listId($sql, $key = 'id')
    {
        if (strpos($key, ' ')) {
            $keys = explode(' ', $key);
        }

        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[$row[$key]] = $row;
            }
            $query->free();
            return $this->result;
        }
    }

    private function groupId($sql, $key = 'id')
    {
        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[$row[$key]][] = $row;
            }
            $query->free();
            return $this->result;
        }
    }

    private function listFlat($sql)
    {
        $this->debug($sql);

        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[current($row)] = next($row);
            }
            $query->free();
            return $this->result;
        }
    }

    function insert_id()
    {
        return $this->mysqli->insert_id;
    }

    function affected_rows()
    {
        return $this->mysqli->affected_rows;
    }

    function implodeVal($array, $isString = false)
    {
        foreach($array AS $n => $value) {
            $array[$n] = $this->val($value, $isString);
        }
        return implode(', ', $array);
    }

    function val($value, $isString = false)
    {
        // Quote if not a number or a numeric string
        if ($isString || !$this->isDecimalNumber($value)) {
            $value = "'" .$this->mysqli->real_escape_string($value ?? ''). "'";
        }

        return $value;
    }

    function isDecimalNumber($n)
    {
        return (string)(float)$n === (string)$n;
    }

    public static function instance()
    {
        return new sqli();
    }

    function info()
    {
        return $this->mysqli->info;
    }

    function close()
    {
        $this->mysqli->close();
    }

    // Aliases
    private function idList($sql)
    {
        return $this->listId($sql);
    }

    private function flatList($sql)
    {
        return $this->listFlat($sql);
    }

    private function groupedList($sql)
    {
        return $this->groupId($sql);
    }

}