<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sprint extends Model {

    protected $table = 'sprints';

    protected $fillable = [
        'project_id',
        'name',
        'vision',
        'start_date',
        'end_date',
        'status_id'
    ];

    public function tasks()
    {
        return $this->hasMany('App\Task', 'sprint_id', 'id');
    }
}
