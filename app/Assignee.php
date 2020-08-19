<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Assignee;

class Assignee extends Model {

    protected $table = 'assignees';

    protected $fillable = [
        'member_id',
        'user_id',
        'backlog_id',
        'full_name',
        'role',
        'project_id'
    ];
}
