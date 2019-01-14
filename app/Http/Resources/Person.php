<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Person extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request);

        //customize return
        /*return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName
        ];*/
    }

    public function with($request){
        return [
            'created_by' => 'Sheina Vi Paclibar'
        ];

    }
}
