<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Member extends Model {

    protected $table = 'members';

    protected $fillable = [
        'project_id',
        'member_id',
        'full_name',
        'role'
    ];
}
