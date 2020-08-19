<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model {

    protected $table = 'projects';

    protected $fillable = [
        'name',
        'description',
        'due_date',
        'status_id',
        'manager_id'
    ];

    public function manager()
    {
        return $this->hasOne('App\User', 'id', 'manager_id');
    }

    public function status()
    {
        return $this->hasOne('App\Status', 'id', 'status_id');
    }

    public function members()
    {
        return $this->hasMany('App\Member', 'project_id', 'id');
    }

    public function backlogs()
    {
       return $this->hasMany('App\Member', 'project_id', 'id');
    }
}
