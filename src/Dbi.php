<?php

namespace Stilmark\Database;

use Stilmark\Database\Dba;

class Dbi {

    public static function __callStatic($name, $arguments)
    {
        $dba = Dba::instance();

        if (defined('static::table')) {
            $dba->table(static::table);
        }
        if (isset(static::$join)) {
            $dba->join(static::$join);
        }
        if (isset(static::$visible)) {
            $dba->visible(static::$visible);
        }
        if (defined('static::fillable')) {
            $dba->fillable(static::fillable);
        }
        if (defined('static::dates')) {
            $dba->dates(static::dates);
            $dba->fillable(static::dates);
        }
        if (defined('static::softDelete')) {
            $dba->softDelete = static::softDelete;
        }

        if (count($arguments) == 1 && isset($arguments[0])) {
            $arguments = $arguments[0];
        }

        if (!empty($arguments)) {
            return $dba->$name($arguments);
        } else {
            return $dba->$name();
        }
    }

    public static function get($conditions) {
        if (!is_array($conditions)) {
            $conditions = ['id' => $conditions];
        }

        if (static::softDelete) {
            $conditions['deleted_at : IS'] = null;
        }
        return self::where($conditions)->row();
    }

    public static function getAll(array $conditions = []) {

        if (static::softDelete) {
            $conditions['deleted_at : IS'] = null;
        }
        return self::where($conditions)->list();
    }

}

