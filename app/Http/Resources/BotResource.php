<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class BotResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'type' => $this->type,
            'creator' => $this->whenLoaded('creator', function () {
                return [
                    'id' => $this->creator->id,
                    'username' => $this->creator->username,
                    'link' => url('/api/users', $this->creator->id),
                ];
            }),
            'job' => $this->whenLoaded('currentJob', function () {
                return [
                    'id' => $this->currentJob->id,
                    'status' => $this->currentJob->status
                ];
            }),
        ];
    }
}
