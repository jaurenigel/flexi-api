<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model {

    protected $table = 'tasks';

    protected $fillable = [
        'sprint_id',
        'backlog_id',
    ];
}
