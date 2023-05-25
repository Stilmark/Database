<?php

namespace Stilmark\Database;

use Stilmark\Database\Sqli;

class Dba
{
    protected $sqli;

    function __construct()
    {
    	$this->sqli = (isset($GLOBALS['Sqli'])) ? $GLOBALS['Sqli']:new Sqli();
        $this->init();
    }

    function init() {
        $this->table = '';
        $this->values = [];
        $this->columns = [];
        $this->visible = [];
        $this->fillable = [];
        $this->dates = [];
        $this->join = [];
        $this->where = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->limit = [];
        $this->subQuery = [];
        $this->dryrun = false;
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
            $table = current($table);
        }
        $this->table = $table;
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

    function where($where = [])
    {
        $this->where = array_merge($this->where, $where);
        return $this;
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

    function dryrun()
    {
        $this->sqli->dryrun = $this->dryrun = true;
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
            if (!in_array($value, ['NOW()','CURDATE()'])) {
               if (!is_null($value)) {
                    $value = $this->sqli->val($value);
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

        foreach($columns AS $key => $value) {
            if (count($this->visible)) {
                if (isset($this->visible[$value])) {
                    $columns[$key] = $this->visible[$value];
                } else {
                    unset($columns[$key]);    
                }
                continue;
            }
            if (!strpos($value, '.') && !strpos($value, '(')) {
                $columns[$key] = $this->table.'.'.$value;
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
        $where = [];
        foreach($this->where AS $key => $value) {
		
        	if (!is_array($value)) {

                unset($operator);
			
                if (strpos($key, ':')) {
                    $arg = explode(':',$key);
                    if (count($arg) == 2) {
                        $key = trim($arg[0]);
                        $operator = strtoupper(trim($arg[1]));
                    }
                }

                if (!isset($operator)) {
                    $operator = '=';
                }

                if (in_array($operator, ['=', '>=', '<=', '>', '<', 'LIKE', 'NOT LIKE', '!=', 'IS', 'IS NOT'])) {
                    if (!preg_match('/^[a-z_]+\(.*\)$/i', $value) && !is_null($value)) {
                        $value = $this->sqli->val($value);
                    }
                } else {
                    $operator = '=';
                }

	            if (is_null($value)) {
	            	$value = 'null';
	            }

            	$where[] = (!strpos($value, '.') ? $this->table.'.':'').$key.' '.$operator.' '.$value;

            } else {

            	$where[] = $this->table.'.'.$key.' IN ('.$this->sqli->implodeVal($value).')';

            }
        }

        if (!empty($this->subQuery)) {
            foreach($this->subQuery AS $key => $query) {
                $where[] = $this->table.'.'.$key.' IN ('.$query.')';
            }
        }

        if (count($where) > 0) {
        	return 'WHERE '.implode(' AND ', $where);
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
        if (!count($this->values)) {
            return false;
        }
        if (count($this->dates) && in_array('created_at', $this->dates)) {
            $this->values(['created_at' => 'NOW()']);
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('INSERT INTO %s %s', $this->table, $this->getValues());
        $this->sqli->query($sql);

        return ['id' => $this->sqli->insert_id() ?? null, 'statement' => $sql];
    }

    function replace()
    {
        if (!count($this->values)) {
            return false;
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('REPLACE INTO %s %s', $this->table, $this->getValues());
        $this->sqli->query($sql);

        return ['id' => $this->sqli->insert_id(), 'statement' => $sql];
    }

    /*
     * Update
     */

    function updateById(Int $id, Array $values = [])
    {
        if (count($values)) {
            $this->values($values);
        }
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }
        $sql = sprintf('UPDATE %s %s WHERE id=%d', $this->table, $this->getValues(), $id);
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows(), 'statement' => $sql];
    }

    function update()
    {
        if (count($this->dates) && in_array('updated_at', $this->dates)) {
            $this->values(['updated_at' => 'NOW()']);
        }

        $sql = sprintf('UPDATE %s %s %s', $this->table, $this->getValues(), $this->getWhere());
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows(), 'statement' => $sql];
    }

    /*
     * Delete
     */

    function delete()
    {
        if (!count($this->where())) {
            die('Delete query requires WHERE scope');
        }
        $sql = sprintf('DELETE FROM %s %s', $this->table, $this->getWhere());
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows(), 'statement' => $sql];
    }

    function truncate()
    {
        $sql = sprintf('TRUNCATE %s', $this->table);
        $this->sqli->query($sql);

        return ['affected_rows' => $this->sqli->affected_rows(), 'statement' => $sql];
    }

    /*
     * Fetch rows
     */

    function row( int $id = 0 )
    {
        if ($id > 0) {
            $this->where = ['id' => $id];
        }
        return $this->sqli->row( $this->makeSelectQuery() );
    }

    function rowId( $id )
    {
        $this->where = ['id' => $id];
        return $this->row();
    }

    function rowKeys() {
        return $this->sqli->keys( $this->makeSelectQuery() );
    }

    function rowValues() {
        return $this->sqli->values( $this->makeSelectQuery() );
    }

    function first()
    {
    	return $this->row();
    }

    function last( $key = 'id')
    {
        // todo: whereSubquery
        /*
        $this->where([$key => '(SELECT MAX('.$key.') FROM '.$this->table.')']);
        die($this->makeSelectQuery());
    	return $this->sqli->row( $this->makeSelectQuery() );
        */
    }

    /*
     * Fetch lists
     */

    function list( $id = null )
    {
    	if ($id != null) {
    		return $this->sqli->listId( $this->makeSelectQuery(), $id);
    	} else {
    		return $this->sqli->list( $this->makeSelectQuery() );
    	}
    }

    function groupId( $id = 'id' )
    {
        return $this->sqli->groupId( $this->makeSelectQuery(), $id);
    }

    function listId( $id = 'id' )
    {
        return $this->list( $id );
    }

    function listFlat()
    {
        return $this->sqli->listFlat( $this->makeSelectQuery() );
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

    function set($values = [])
    {
        return $this->values($values);
    }

    function sql()
    {
        return $this->makeSelectQuery();
    }

}
