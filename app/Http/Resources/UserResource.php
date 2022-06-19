<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'color' => $this->card_color,
            'description' => $this->description,
            'job_title' => $this->job_title,
            'photo' => $this->rofile_photo_path,
            'default_photo' => $this->profile_photo_url,
            'details' => [
                'contactInfo' => $this->contactInfos,
                'socialLink' => $this->socialLinks,
                'socialnetwork' => $this->socialNetworks,
            ],
        ];
    }
}
