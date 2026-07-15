<?php

namespace Arjunyuvanesh\CommonAuth\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the user model into a strict, secure JSON array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'username'   => $this->username,
            'mobile'     => $this->mobile,
            
            // If the host application eager loads roles, we return an array of role names
            'roles'      => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            
            // Format timestamps cleanly, hiding exact system milliseconds
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
            
            // Notice: 'password', 'remember_token', and 'email_verified_at' are completely hidden!
        ];
    }
}
