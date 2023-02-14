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

        $with = $request->get('with') ?? '';
        $with = explode(',', $with);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            $this->mergeWhen($isAdmin, [
                'permissions' => $this->permissions,
            ]),
            $this->mergeWhen(in_array('business', $with), [
                'business' => BusinessResource::make($this->business)
            ]),
        ];
    }
}
