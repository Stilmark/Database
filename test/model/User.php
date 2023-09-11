<?php

namespace Stilmark\Test;

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

    public static function get(
        int $id,
        array $with = []
    ) {
        $result = parent::get($id);

        if (in_array('category', $with)) {
            $result['category'] = Category::get($result['category_id']);
        }

        return $result;
    }

}