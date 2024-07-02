<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\TransactionBahanBaku;

class TransactionBahanBakuListResource extends JsonResource
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
            'amount' => $this->amount,
            'created_at' => $this->created_at->toDayDateTimeString(),
            'supplier_name' => $this->supplier?->name,
            'supplier_phone' => $this->supplier?->phone,
            'payment_method_name' => $this->payment_method?->name,
            'account_number' => $this->payment_method?->account_number,
            'status' => $this->status == TransactionBahanBaku::STATUS_SUCCESS ? 'Success' : 'Failed',
            'transaction_type' => $this->transaction_type == TransactionBahanBaku::CREDIT ? 'Credit' : 'Debit',
            'trxIngredients_id' => $this->trxIngredients_id,
            'transaction_by' => $this->transactionable?->name,
        ];
    }
}