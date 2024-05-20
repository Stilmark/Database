<?php

namespace Stilmark\Test;

use Stilmark\Database\Dbi;

class User extends Dbi {

    const table = ['u' => 'users'];
    const fillable = [
        'firstName',
        'lastName',
        'email'
    ];
    const hidden = ['password'];
    const dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    const softDelete = true;
}