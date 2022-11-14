<?php

use Stilmark\Database\Dbi;

class User extends Dbi {

    protected static $table = 'users';
    protected static $fillable = [
    	'firstName',
    	'lastName',
    	'email',
        'category'
    ];
    protected static $visible = [
    	'id',
    	'firstName',
    	'email'
    ];
    protected static $dates = [
    	'created_at',
    	'updated_at'
    ];

}