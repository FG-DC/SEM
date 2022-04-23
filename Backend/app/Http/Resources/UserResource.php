<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \DateTime;

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
        if (gettype($this->birthdate) == "string")
            $birthdate = new DateTime($this->birthdate);
        else
            $birthdate = $this->birthdate;

        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            "birthdate" => date_format($birthdate, "d-m-Y"),
            "divisions" => $this->divisions == null ? [] : DivisionResource::Collection($this->divisions),
            "type" => $this->type
        ];
    }
}
