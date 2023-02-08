<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
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
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'full_name'      => $this->full_name,
            'email'          => $this->email,
            'formattedPhone' => $this->formattedPhone,
            'picture'        => $this->picture,
            'defaultCountry' => $this->defaultCountry,
            'address'        => optional($this->user_detail)->address_1,
            'city'           => optional($this->user_detail)->city,
            'state'          => optional($this->user_detail)->state,
            'timezone'       => optional($this->user_detail)->timezone,
          ];
    }
}
