<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Backlog extends Model {

    protected $table = 'backlogs';

    protected $fillable = [
        'story',
        'title',
        'project_id',
        'type_id',
        'status_id',
        'priority_id',
        'start_date',
        'end_date',
        'acceptance_criteria'
    ];

    public function comments()
    {
        return $this->hasMany('App\Comment', 'backlog_id', 'id');
    }

    public function type()
    {
        return $this->hasOne('App\Type', 'id', 'type_id');
    }

    public function status()
    {
        return $this->hasOne('App\Status', 'id', 'status_id');
    }

    public function priority()
    {
        return $this->hasOne('App\Priority', 'id', 'priority_id');
    }

    public function assignees()
    {
        return $this->hasMany('App\Assignee', 'backlog_id', 'id');
    }
}
