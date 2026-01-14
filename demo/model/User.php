<?php

namespace Stilmark\Demo;

use Stilmark\Database\Dbi;

class User extends Dbi {

    const table = ['u' => 'users'];
    const fillable = [
        'firstName',
        'lastName',
        'email',
        'category_id'
    ];
    const hidden = ['password'];
    const json = ['message'];
    const dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    const softDelete = true;
}