<?php

use Stilmark\Database\Dbi;

class User extends Dbi {

    const softDelete = true;
    const table = ['u' => 'users'];
    const fillable = [
    	'firstName',
    	'lastName',
    	'email',
        'category'
    ];
    const dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /*
    protected static $visible = [
    	'id',
    	'firstName first_name',
        'lastName',
    	'email'
    ];
    */
}