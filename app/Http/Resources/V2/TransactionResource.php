<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'id'                     => $this->id,
            'user_id'                => $this->user_id,
            'end_user_id'            => $this->end_user_id,

            'user_first_name'        => optional($this->user)->first_name,
            'user_last_name'         => optional($this->user)->last_name,
            'user_full_name'         => optional($this->user)->full_name,
            'user_photo'             => optional($this->user)->picture,

            'end_user_first_name'    => optional($this->end_user)->first_name,
            'end_user_last_name'     => optional($this->end_user)->last_name,
            'end_user_photo'         => optional($this->end_user)->picture,

            'transaction_type_id'    => $this->transaction_type_id,
            'transaction_type'       => $this->transaction_type->name,
            'curr_code'              => $this->currency->code,
            'curr_symbol'            => $this->currency->symbol,
            'charge_percentage'      => $this->charge_percentage,
            'charge_fixed'           => $this->charge_fixed,

            'subtotal'               => moneyFormat($this->currency->symbol, formatNumber($this->subtotal, $this->currency_id)),
            'total'                  => moneyFormat($this->currency->symbol, formatNumber($this->total, $this->currency_id)),

            'status'                 => $this->status,
            'email'                  => $this->email,
            'phone'                  => $this->phone,
            'transaction_created_at' => dateFormat($this->t_created_at, $this->user_id),

            'payment_method_id'      => $this->payment_method_id,
            'payment_method_name'    => optional($this->payment_method)->name,
            'company_name'           => settings('name'),
            'company_logo'           => settings('logo'),

            'merchant_id'            => $this->merchant_id,
            'merchant_name'          => optional($this->merchant)->business_name,
            'logo'                   => optional($this->merchant)->logo,

            'bank_id'                => $this->bank_id,
            'bank_name'              => optional($this->bank)->bank_name,
            'bank_logo'              => optional(optional($this->bank)->file)->filename
        ];
    }
}
