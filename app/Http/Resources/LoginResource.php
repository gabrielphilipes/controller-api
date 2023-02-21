<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $with = $request->get('with') ?? '';
        $with = explode(',', $with);

        return [
            'token' => $this->plainTextToken ?? $request->bearerToken(),
            'expired_at' => $this->expires_at,
            'expired_at_tz' => Carbon::parse($this->expires_at)->tz($this->user->timezone)->toDateTimeString(),
            'user_tz' => $this->user->timezone,
            'permission' => $this->user->permissions,
            $this->mergeWhen(in_array('user', $with), [
                'user' => UserResource::make($this->user),
            ]),
            $this->mergeWhen(in_array('business', $with), [
                'business' => BusinessResource::make($this->user->business),
            ]),
        ];
    }
}
