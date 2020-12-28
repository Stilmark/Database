<?php

namespace Stilmark\Database;

use Stilmark\Database\Sqli;

class Dba
{
    protected $sqli;

    function __construct()
    {

    	$this->sqli = (isset($GLOBALS['Sqli'])) ? $GLOBALS['Sqli']:new Sqli();

        $this->table = '';
        $this->values = [];
        $this->columns = [];
        $this->join = [];
        $this->where = [];
        $this->orderBy = [];
        $this->groupBy = [];
        $this->having = [];
        $this->limit = [];

    }

    /*
     * Setters
     */

    function table($table)
    {
        $this->table = $table;
        return $this;
    }

    function columns($columns = [])
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }
        $this->columns = $columns;
        return $this;
    }

    function values($values = [])
    {
        $this->values = $values;
        return $this;
    }

    function where($where = [])
    {
        $this->where = $where;
        return $this;
    }

    function orderBy($orderBy = [])
    {
        if (!is_array($orderBy)) {
            $orderBy = [$orderBy];
        }
        $this->orderBy = $orderBy;
        return $this;
    }

    function groupBy($groupBy = [])
    {
        if (!is_array($groupBy)) {
            $groupBy = [$groupBy];
        }
        $this->groupBy = $groupBy;
        return $this;
    }

    function having($having = '')
    {
        $this->having = $having;
        return $this;
    }

    function limit($limit = '')
    {
        $this->limit = $limit;
        return $this;
    }

    function join($join = [])
    {
        $this->join = $join;
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
                $value = $this->sqli->val($value);
            }
            $values[$column] = $column.'='.$value;
        }
        return implode(', ', array_unique($values));
    }

    function getColumns()
    {
        $columns = $this->columns;
        if (count($columns) == 0) {
            $columns = ['*'];
            if (count($this->join) > 0) {
                foreach($this->join AS $table => $join) {
                    $columns[] = $table.'.*';
                }
            }
        }
        foreach($columns AS $key => $value) {
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

    			if (in_array($operator, ['=', '>=', '<=', '>', '<', 'LIKE'])) {
		            if (!preg_match('/^[a-z_]+\(.*\)$/i', $value)) {
		                $value = $this->sqli->val($value);
		            }
    			}

            	$where[] = (!strpos($value, '.') ? $this->table.'.':'').$key.' '.$operator.' '.$value;
            } else {
            	$where[] = $this->table.'.'.$key.' IN ('.implode(',', $value).')';
            }
        }

        /*
        if (isset($this->match)) {
            $scope[] = 'MATCH('.implode(',',$this->match['columns']).') AGAINST("'.$this->match['query'].'" IN BOOLEAN MODE)';
        }
        */

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
        return 'LIMIT '.$this->limit;
    }

    /*
     * Create / replace
     */

    function create()
    {
        if (count($this->values) == 0) {
            return false;
        }
        $sql = sprintf('INSERT INTO %s SET %s', $this->table, $this->getValues());
        $this->sqli->query($sql);
        return $this->sqli->insert_id();
    }

    function replace()
    {
        if (count($this->values) == 0) {
            return false;
        }
        $sql = sprintf('REPLACE INTO %s SET %s', $this->table, $this->getValues());
        $this->sqli->query($sql);
        return $this->sqli->insert_id();
    }

    /*
     * Update
     */

    function updateById(Int $id, Array $values = [])
    {
        if (count($values) > 0) {
            $this->values($values);
        }
        $sql = sprintf('UPDATE %s SET %s WHERE id=%d', $this->table, $this->getValues(), $id);
        $this->sqli->query($sql);
        return $this->sqli->affected_rows();
    }

    function update()
    {
        $sql = sprintf('UPDATE %s SET %s WHERE %s', $this->table, $this->getValues(), $this->getWhere());
        $this->sqli->query($sql);
        return $this->sqli->affected_rows();
    }

    /*
     * Delete
     */

    function delete() {
        if (empty($this->getWhere())) {
            dd('No WHERE for delete');
        }
        $sql = sprintf('DELETE FROM %s WHERE %s', $this->table, $this->getWhere());
        $this->sqli->query($sql);
        return $this->sqli->affected_rows();
    }

    /*
     * Fetch rows
     */

    function row()
    {
        return $this->sqli->row( $this->makeSelectQuery() );
    }

    function rowId( $id )
    {
        $this->where = ['id' => $id];
        return $this->row();
    }

    function first()
    {
    	return $this->row();
    }

    function last()
    {
    	return ['todo'];
    }

    /*
     * Fetch lists
     */

    function list()
    {
        return $this->sqli->list( $this->makeSelectQuery() );
    }

    function listId( $id = 'id' )
    {
        return $this->sqli->listId( $this->makeSelectQuery(), $id);
    }

    function listGroup( $id = 'id' )
    {
        return $this->sqli->groupId( $this->makeSelectQuery(), $id);
    }

}