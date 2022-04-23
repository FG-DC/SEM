<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConsumptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data =  explode("T", $this->created_at);

        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "observation_id" => $this->observation_id,
            "value" => $this->value,
            "variance" => $this->variance,
            "date" => $data[0]
        ];
    }
}
