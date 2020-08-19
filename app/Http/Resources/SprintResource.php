<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SprintResource extends JsonResource{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'vision' => $this->vision,
            'project_id' => $this->project_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'tasks'=> $this->tasks
        ];
    }
}
