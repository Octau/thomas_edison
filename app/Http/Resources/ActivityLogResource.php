<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = $this->properties['attributes'];
        $oldAttribute = $this->properties['old'] ?? null;
        $changelogs = [];
        foreach ($attributes as $attribute => $value) {
            $modified = [
                'new' => null,
            ];
            if(!!$oldAttribute && $value !== $oldAttribute[$attribute]) {
                $modified = [
                    'new' => $value,
                    'old' => $oldAttribute[$attribute]
                ];
            }
            $changelogs[] = __('audits.'.$this->event.'.message', array_merge($modified, ['attribute' => $attribute]));
        }
        return [
            'id'                => $this->id,
            'user_type'         => $this->causer_type,
            'user_id'           => $this->causer_id,
            'user'              => [
                'name'      => ucwords($this->causer->name),
                'email'     => $this->causer->email,
            ],
            'event'             => $this->event,
            'subject_type'      => $this->subject_type,
            'subject_id'        => $this->subject_id,
            'properties'        => $this->properties,
            'changelogs'        => $changelogs,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            'description'       => $this->description,
            'log_name'          => $this->log_name,
            'batch_id'          => $this->batch_uuid,
        ];
    }
}
