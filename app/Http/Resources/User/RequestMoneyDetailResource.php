<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class RequestMoneyDetailResource extends JsonResource
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
            'email'          => optional($this)->email,
            'phone'          => optional($this)->phone,
            'amount'         => optional($this)->amount,
            'currency'       => optional($this->currency)->code,
            'currency_id'    => optional($this->currency)->id,
            'currencySymbol' => optional($this->currency)->symbol,
        ];
    }
}
