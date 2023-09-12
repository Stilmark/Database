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
            if (is_string($arguments) || is_int(key($arguments))) {
                return $dba->$name($arguments); // function takes single parameter
            } else {
                return $dba->$name(...$arguments); // function takes multiple parameters
            }
        } else {
            return $dba->$name();
        }
    }

}

