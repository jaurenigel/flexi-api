<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'backlog_id' => $this->backlog_id,
            'comment' => $this->comment,
            'user' => $this->full_name,
            'user_id' => $this->user_id,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'backlog'=> $this->backlog

        ];
    }
}
