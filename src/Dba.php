<?php

namespace Stilmark\Database;

use Stilmark\Database\Pdo;
use Stilmark\Database\Sqli;

class Dba
{
    protected $db;

    function __construct()
    {
        if ($_ENV['DB_CONNECTION'] == 'mysqli') {
            $this->db = new Sqli();
        } else {
            $this->db = new PdoClass();
        }
    	
        $this->init();
    }

    function init() {
        $this->table = '';
        $this->tableAlias = null;
        $this->values = [];
        $this->columns = [];
        $this->visible = [];
        $this->hidden = [];
        $this->fillable = [];
        $this->dates = [];
        $this->softDelete = false;
        $this->join = [];
        $this->with = [];
        $this->where = [];
        $this->operators = ['=', '!=', '>=', '<=', '>', '<', 'LIKE', 'NOT LIKE', 'IS', 'IS NOT', 'IN', 'NOT IN'];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->limit = [];
        $this->persist = false;
        $this->subQuery = [];
        $this->debug = false;
    }

    public static function instance() {
        return new Dba();
    }

    /*
     * Setters
     */

    function table($table)
    {
        if (is_array($table)) {
            $this->tableAlias = key($table);
            $this->tableName = current($table);
            $this->table = $this->tableName.' '.$this->tableAlias;
        } else {
            $this->table = $this->tableName = $table;
        }
        return $this;
    }

    function dates($dates = [])
    {
        if (!is_array($dates)) {
            $dates = [$dates];
        }
        $this->dates = $dates;
        return $this;
    }

    function columns($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }

    function fillable($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->fillable = array_merge($this->fillable, $columns);
        return $this;
    }

    function hidden($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->hidden = $columns;
        return $this;
    }

    function visible($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        foreach($columns AS $column) {
            if (preg_match('/([a-z_]+)$/i', $column, $match)) {
                $this->visible[$match[0]] = $column;
            }
        }

        return $this;
    }

    function values($values = [])
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    function where($conditions = [])
    {
        $this->where[] = ['operator' => 'AND', 'conditions' => $conditions];
        return $this;
    }

    function orWhere($conditions = [])
    {
        $this->where[] = ['operator' => 'OR', 'conditions' => $conditions];
        return $this;
    }

    function setConditions($conditions)
    {
        if ($conditions) {
            if (is_array($conditions)) {
                $this->where($conditions);
            } else {
                $this->where(['id' => $conditions]);
            }
        }
    }

    function orderBy($orderBy = [])
    {
        if (!is_array($orderBy)) {
            $orderBy = [$orderBy];
        }
        $this->orderBy = array_merge($this->orderBy, $orderBy);
        return $this;
    }

    function groupBy($groupBy = [])
    {
        if (!is_array($groupBy)) {
            $groupBy = [$groupBy];
        }
        $this->groupBy = array_merge($this->groupBy, $groupBy);;
        return $this;
    }

    function having($having = '')
    {
        $this->having = $having;
        return $this;
    }

    function limit(int $limit = 0, int $offset = 0): Dba
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    function offset(int $offset = 0): Dba
    {
        $this->offset = $offset;
        return $this;
    }

    function join($join = [])
    {
        $this->join = $join;
        return $this;
    }

    function subQuery($key = 'id', $query = '')
    {
        $this->subQuery[$key] = $query;
        return $this;
    }

    function debug()
    {
        $this->db->debug = $this->debug = true;
        return $this;
    }

    /*
     * Assemble query
     */

    function makeSelectQuery()
    {
        $sql = sprintf(
            'SELECT %s FROM %s %s %s %s %s %s',
            $this->getColumns(),
            $this->table,
            $this->getJoin(),
            $this->getWhere(),
            $this->getGroup(),
            $this->getOrder(),
            $this->getLimit()
        );

        return trim($sql);
    }

    function getValues()
    {
        $values = $this->values;

        foreach($values AS $column => $value) {
            if ($this->fillable && !in_array($column, $this->fillable)) {
                unset($values[$column]);
                continue;
            }
            if (!in_array($value, ['NOW()','CURDATE()'])) {
               if (!is_null($value)) {
                    $value = $this->db->val($value);
                } else {
                    $value = 'null';
                }
            }
            $values[$column] = $column.'='.$value;
        }

        if (count($values)) {
            return 'SET '. implode(', ', array_unique($values));
        }
    }

    function getColumns()
    {
        $columns = $this->columns;

        if (!count($columns)) {
            $columns = array_keys($this->visible);
        }

        if (!count($columns) && !count($this->visible)) {
            $columns = ['*'];
            if (count($this->join) > 0) {
                foreach($this->join AS $table => $join) {
                    $columns[] = $table.'.*';
                }
            }
        }

        if ($this->hidden) {
            foreach($this->hidden AS $key) {
                $columns[$key] = 'null ' . $key;
            }
        }

        foreach($columns AS $key => $value) {
            if (count($this->visible)) {
                if (isset($this->visible[$value])) {
                    $columns[$key] = $this->visible[$value];
                } else {
                    unset($columns[$key]);    
                }
                continue;
            }
            if (!strpos($value, '.') && !strpos($value, '(') && !str_starts_with($value, 'null')) {
                $columns[$key] = ($this->tableAlias ?? $this->table).'.'.$value;
            }
        }

        return implode(', ', array_unique($columns));
    }

   function getJoin()
   {
        if (empty($this->join)) {
            return false;
        }
        $joins = [];
        foreach($this->join AS $table => $join) {
            $joins[] = $join;
        }
        return implode(' ', $joins);
    }

    function getWhere()
    {
        if (!$this->where) {
            return false;
        }

        if ($this->softDelete && !$this->persist) {
            array_unshift($this->where, [
                'operator' => 'AND',
                'conditions' => [
                    'deleted_at IS' => null
                ]
            ]);
        }

        foreach($this->where AS $n => $where) {

            $filter = [];

            foreach($where['conditions'] AS $column => $value) {

                $column = trim(preg_replace('/\s+/', ' ', $column));
                $operator = '=';

                if (strpos($column, ':')) {
                    $arg = explode(':',$column);
                    if (count($arg) == 2) {
                        $column = trim($arg[0]);
                        $operator = strtoupper(trim(end($arg)));
                    }
                } elseif (strpos($column, ' ')) {
                    $arg = explode(' ', $column);
                    $column = $arg[0];
                    $end = implode( ' ',array_slice($arg, 1));
                    $operator = strtoupper($end);
                }

                if (!is_array($value)) {

                    if (in_array($operator, $this->operators)) {
                        if (!is_null($value) && !preg_match('/^[a-z_]+\(.*\)$/i', $value)) {
                            $value = $this->db->val($value);
                        }
                    } else {
                        $operator = '=';
                    }

                    if (is_null($value)) {
                        $value = 'null';
                    }

                    $filter[] = (!strpos($column, '.') ? ($this->tableAlias ?? $this->table).'.':'').$column.' '.$operator.' '.$value;

                } else {

                    if (!in_array($operator, ['IN', 'NOT IN'])) {
                        $operator = 'IN';
                    }

                    $filter[] = (!strpos($column, '.') ? ($this->tableAlias ?? $this->table).'.':'').$column.' '.$operator.' ('.$this->db->implodeVal($value).')';
                }
            }

            if (!empty($this->subQuery)) {
                foreach($this->subQuery AS $key => $query) {
                    $filter[] = ($this->tableAlias ?? $this->table).'.'.$key.' IN ('.$query.')';
                }
            }

            if (count($filter) > 0) {
                $filterSet[] = ($n > 0 ? $where['operator'].' ':'').'('.implode(' AND ', $filter).')';
            }
        }

        if (count($filterSet)) {
            return 'WHERE '.implode(' ',$filterSet);
        }
    }

    function getGroup()
    {
        if (empty($this->groupBy)) {
            return false;
        }
        $str = 'GROUP BY '.implode(', ', $this->groupBy);
        if (!empty($this->having)) {
            $str .= ' HAVING '.implode(', ', $this->having);
        }
        return $str;
    }

    function getOrder()
    {
        if (empty($this->orderBy)) {
            return false;
        }
        return 'ORDER BY '.implode(', ', $this->orderBy);

    }

    function getLimit()
    {
        if (empty($this->limit)) {
            return false;
        }
        return 'LIMIT '.(isset($this->offset) ? $this->offset.',':'').$this->limit;
    }

    /*
     * Create / replace
     */

    function create()
    {
        $this->persist = true;

        if (!count($this->values)) {
            return false;
        }
        if (count($this->dates) && in_array('created_at', $this->dates)) {
            $this->values(['created_at' => 'NOW()']);
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('INSERT INTO %s %s', $this->tableName, $this->getValues());
        $this->db->query($sql);

        return ['id' => $this->db->insert_id() ?? null];
    }

    function replace()
    {
        $this->persist = true;

        if (!count($this->values)) {
            return false;
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('REPLACE INTO %s %s', $this->table, $this->getValues());
        $this->db->query($sql);

        return ['id' => $this->db->insert_id()];
    }

    /*
     * Update
     */

    function updateById(Int $id, Array $values = [])
    {
        $this->persist = true;

        if (count($values)) {
            $this->values($values);
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('UPDATE %s %s WHERE id=%d', $this->table, $this->getValues(), $id);
        $this->db->query($sql);

        return ['affected_rows' => $this->db->affected_rows()];
    }

    function update($conditions = false)
    {
        $this->persist = true;
        $this->setConditions($conditions);

        if ($this->dates && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }

        if (!$this->where) {
            die('Missing where conditions'.PHP_EOL);
        }

        $sql = sprintf('UPDATE %s %s %s', $this->table, $this->getValues(), $this->getWhere());
        $this->db->query($sql);

        return ['affected_rows' => $this->db->affected_rows()];
    }

    /*
     * Delete
     */

    function delete($conditions = false)
    {
        $this->persist = true;
        $this->setConditions($conditions);

        if (!count($this->where)) {
            die('Delete query requires WHERE scope');
        }
        if ($this->softDelete) {
            $sql = sprintf('UPDATE %s SET deleted_at = NOW() %s', $this->table, $this->getWhere());
            $this->db->query($sql);
        } else {
            $sql = sprintf('DELETE FROM %s %s', $this->table, $this->getWhere());
            $this->db->query($sql);
        }

        return ['affected_rows' => $this->db->affected_rows()];
    }

    function truncate()
    {
        $sql = sprintf('TRUNCATE %s', $this->table);
        $this->db->query($sql);

        return ['affected_rows' => $this->db->affected_rows()];
    }

    /*
     * Getters
     */

    function get(
        $conditions = null
    ){
        if (is_null($conditions)) {
            return [];
        }

        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }

        return $this->where($conditions)->row();
    }

    function getAll(
        array $conditions = []
    ){
        return $this->where($conditions)->list();
    }

    function getGrouped(
        string $column,
        array $conditions = []
    ){
        return $this->where($conditions)->groupId( $column );
    }

    function with(array $tables)
    {
        $this->with = $tables;
        return $this;
    }

    /*
     * Fetch rows
     */

    function row( $conditions = false )
    {
        $this->setConditions($conditions);
        return $this->db->row( $this->makeSelectQuery() );
    }

    function rowKeys() {
        return $this->db->keys( $this->makeSelectQuery() );
    }

    function rowValues( $conditions = false ) {
        $this->setConditions($conditions);
        return $this->db->values( $this->makeSelectQuery() );
    }

    /*
     * Fetch lists
     */

    function list( $id = null )
    {
    	if ($id != null) {
    		return $this->db->listId( $this->makeSelectQuery(), $id);
    	} else {
    		return $this->db->list( $this->makeSelectQuery() );
    	}
    }

    function groupId( $id = 'id' )
    {
        return $this->db->groupId( $this->makeSelectQuery(), $id);
    }

    function listId( $id = 'id' )
    {
        return $this->list( $id );
    }

    function listFlat()
    {
        return $this->db->listFlat( $this->makeSelectQuery() );
    }

    // Aliases

    function idList( $id = 'id' )
    {
        return $this->listId( $id );
    }

    function groupedList( $id = 'id' )
    {
        return $this->groupId( $id );
    }

    function flatList()
    {
        return $this->listFlat();
    }

    function insert()
    {
        return $this->create();
    }

    function set($values = [])
    {
        return $this->values($values);
    }

    function rowId( int $id )
    {
        return $this->row($id);
    }

}
