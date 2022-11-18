<?php

namespace Stilmark\Database;

class Sqli
{

    function __construct()
	{
        $this->result = [];
        $this->mysqli = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']);

        if ($this->mysqli->connect_error) {
            die('Database error: ' . $this->mysqli->connect_error);
        }

        $this->mysqli->set_charset('utf8');
        $this->dryrun = false;
	}

    function query($sql)
    {
        if ($this->dryrun) {
            return true;
        }

        return $this->mysqli->query($sql);        
    }

    function row($sql)
    {
        if ($this->dryrun) {
            return true;
        }

        if ($query = $this->query($sql)) {
            $this->result = $query->fetch_assoc();
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('row', $sql);
        }
    }

    function key($sql) {

        if ($this->dryrun) {
            return true;
        }

        return array_keys($this->row($sql))[0];
    }

    function keys($sql) {

        if ($this->dryrun) {
            return true;
        }

        return array_keys($this->row($sql));
    }

    function value($sql) {

        if ($this->dryrun) {
            return true;
        }

        return array_values($this->row($sql))[0];
    }

    function values($sql) {

        if ($this->dryrun) {
            return true;
        }

        return array_values($this->row($sql));
    }

    function list($sql)
    {

        if ($this->dryrun) {
            return true;
        }

        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[] = $row;
            }
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('list', $sql);
        }
    }

    function listId($sql, $key = 'id')
    {

        if ($this->dryrun) {
            return true;
        }

        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[$row[$key]] = $row;
            }
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('listId', $sql);
        }
    }

    function groupId($sql, $key = 'id')
    {

        if ($this->dryrun) {
            return true;
        }

        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[$row[$key]][] = $row;
            }
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('groupId', $sql);
        }
    }

    function listFlat($sql)
    {

        if ($this->dryrun) {
            return true;
        }

        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[current($row)] = next($row);
            }
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('listFlat', $sql);
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

    function invalidQuery($type, $sql)
    {
        die('Invalid query ('.$type.'): ' . $sql);
    }

    function val($value, $isString = false)
    {
        // Quote if not a number or a numeric string
        if ($isString || !$this->isDecimalNumber($value)) {
            $value = "'" .$this->mysqli->real_escape_string($value). "'";
        }

        return $value;
    }

    function isDecimalNumber($n)
    {
        return (string)(float)$n === (string)$n;
    }

    static function instance()
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
    function idList($sql)
    {
        return $this->listId($sql);
    }

    function flatList($sql)
    {
        return $this->listFlat($sql);
    }

    function groupedList($sql)
    {
        return $this->groupId($sql);
    }

}