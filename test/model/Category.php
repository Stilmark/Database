<?php

namespace Stilmark\Test;

use Stilmark\Database\Dbi;

class Category extends Dbi {

    const softDelete = true;
    const table = ['c' => 'category'];
    const fillable = [
    	'name'
    ];
    const dates = [
        'deleted_at'
    ];
}