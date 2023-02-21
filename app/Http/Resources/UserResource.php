<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        if (!empty(auth()->user()->id)) {
            $isAdmin = auth()->user()->isAdmin();
        } else {
            $isAdmin = $this->isAdmin();
        }

        $myPermission = auth()->user()->id === $this->id;

        $with = $request->get('with') ?? '';
        $with = explode(',', $with);

        $ignoreInAuthRequests = stripos($request->getRequestUri(), 'auth') !== false;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'timezone' => $this->timezone ?? $this->business->timezone ?? config('app.timezone'),
            'language' => $this->language ?? config('app.locale'),
            $this->mergeWhen($isAdmin || $myPermission, [
                $this->mergeWhen(!$ignoreInAuthRequests, [
                    'permissions' => $this->permissions,
                ]),
                'created_at' => $this->created_at,
                'updated_at' => $this->updated_at,
            ]),
            $this->mergeWhen(in_array('business', $with) && !$ignoreInAuthRequests, [
                'business' => BusinessResource::make($this->business),
            ]),
        ];
    }
}
