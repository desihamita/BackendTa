<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutboundItemsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'updated_at' => $this->created_at != $this->updated_at ? $this->updated_at->toDayDateTimeString() : '',

            'sales_manager' => $this->sales_manager?->name,
            'shop' => $this->shop?->name,

            'quantity' => $this->quantity,
            'date' => $this->date,
            'keterangan' => $this->keterangan,
        ];
    }
}