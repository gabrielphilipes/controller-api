<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * @param $request
     * @return array
     */
    public function toArray($request): array
    {
        $isAdmin = auth()->user()?->isAdmin();
        $document = $this->document;
        $document = preg_replace('/[^0-9]/', '', $document);
        $documentLength = strlen($document);
        if (in_array($documentLength, [11, 14])) {
            $format = [11, '/([0-9]{3})([0-9]{3})([0-9]{3})([0-9]{2})/', '$1.$2.$3-$4'];

            if ($documentLength === 14) {
                $format = [14, '/([0-9]{2})([0-9]{3})([0-9]{3})([0-9]{4})([0-9]{2})/', '$1.$2.$3/$4-$5'];
            }

            $document = str_pad($document, $format[0], '0', STR_PAD_LEFT);
            $document = preg_replace($format[1], $format[2], $document);
        }

        if (!$isAdmin && strlen($document) > 4) {
            $document = substr($document, 0, 3) . '...' . substr($document, -2);
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'document' => $document,
            'timezone' => $this->timezone,
            'language' => $this->language,
        ];
    }
}
