<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

    protected $table = 'comments';

    protected $fillable = [
        'comment',
        'first_name',
        'last_name',
        'user_id',
        'backlog_id'
    ];

    public function backlog()
    {
        return $this->belongsTo('App\Backlog', 'backlog_id', 'id');
    }
}
