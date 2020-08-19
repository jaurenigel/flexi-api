<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BacklogResource extends JsonResource{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'story' => $this->story,
            'acceptance_criteria' => $this->acceptance_criteria,
            'start_date' => $this->start_date,
            'project_id' => $this->project_id,
            'end_date' => $this->end_date,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'type'=> $this->type,
            'status'=> $this->status,
            'priority'=> $this->priority,
            'assignees'=> $this->assignees,
            'comments'=> $this->comments
        ];
    }
}
