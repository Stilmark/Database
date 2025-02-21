<?php

namespace Stilmark\Demo;

use Stilmark\Database\Dbi;

class Category extends Dbi {

    const softDelete = true;
    const table = 'category';
    const fillable = [
    	'name'
    ];
    const dates = [
        'deleted_at'
    ];
}