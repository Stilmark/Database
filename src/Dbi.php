<?php

namespace Stilmark\Database;

use Stilmark\Database\Dba;

class Dbi {

    public static function __callStatic($name, $arguments)
    {
        $dba = Dba::instance();

        if (isset(static::$table)) {
            $dba->table(static::$table);
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
}

