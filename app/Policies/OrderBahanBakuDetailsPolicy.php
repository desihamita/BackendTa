<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\OrderBahanBakuDetails;
use App\Models\User;

class OrderBahanBakuDetailsPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, OrderBahanBakuDetails $orderBahanBakuDetails): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, OrderBahanBakuDetails $orderBahanBakuDetails): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, OrderBahanBakuDetails $orderBahanBakuDetails): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, OrderBahanBakuDetails $orderBahanBakuDetails): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, OrderBahanBakuDetails $orderBahanBakuDetails): bool
    {
        //
    }
}
