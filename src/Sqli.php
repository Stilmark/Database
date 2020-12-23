<?php

namespace Stilmark\Database;

class Sqli
{

    function __construct()
	{
        $this->result = [];
        $this->db_connection = mysqli_connect(HOST, USERNAME, PASSWORD, DATABASE);

        if ($this->db_connection->connect_error) {
            die('Database error: ' . $this->db_connection->connect_error);
        }

        $this->db_connection->set_charset('utf8');
	}

    function query($sql)
    {
        $this->result = [];
        return $this->db_connection->query($sql);
    }

    function row($sql)
    {
        if ($query = $this->query($sql)) {
            $this->result = $query->fetch_assoc();
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('row', $sql);
        }
    }

    function key($sql) {
        return array_keys($this->row($sql))[0];
    }

    function keys($sql) {
        return array_keys($this->row($sql));
    }

    function value($sql) {
        return array_values($this->row($sql))[0];
    }

    function values($sql) {
        return array_values($this->row($sql));
    }

    function list($sql)
    {
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

    function listId($sql)
    {
        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[$row['id']] = $row;
            }
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('listId', $sql);
        }
    }

    function groupId($sql)
    {
        if ($query = $this->query($sql)) {
            while ($row = $query->fetch_assoc()) {
                $this->result[$row['id']][] = $row;
            }
            $query->free();
            return $this->result;
        } else {
            return $this->invalidQuery('groupId', $sql);
        }

    }

    function verbose($sql) {
        return $sql;
    }

    function insert_id() {
        return $this->db_connection->insert_id;
    }

    function affected_rows() {
        return $this->db_connection->affected_rows;
    }

    function invalidQuery($type, $sql) {
        die('Invalid query ('.$type.'): ' . $sql);
    }

    function val($value) {
        // Quote if not a number or a numeric string
        if (!$this->isDecimalNumber($value)) {
           $value = "'" .$this->real_escape_string($value). "'";
        }
        return $value;
    }

    function isDecimalNumber($n) {
        return (string)(float)$n === (string)$n;
    }

    static function instance() {
        return new sqli();
    }

}