<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'project_name' => $this->name,
            'description' => $this->description,
            'due_date' => $this->due_date,
            'created_at' => (string) $this->created_at,
            'updated_at' => (string) $this->updated_at,
            'project_manager'=> $this->manager,
            'members'=> $this->members,
            'status'=> $this->status

        ];
    }
}
