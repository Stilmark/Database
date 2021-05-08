<?php

namespace Stilmark\Database;

use Stilmark\Database\Dba;

class Dbi {

    public static function __callStatic($name, $arguments)
    {
        $dba = Dba::instance();
        return $dba->$name($arguments);
    }

}

