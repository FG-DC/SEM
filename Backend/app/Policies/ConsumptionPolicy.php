<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Consumption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConsumptionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return $user->type === 'P' || Auth::user()->id === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consumption  $consumption
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Consumption $consumption)
    {
        return $user->type === 'P' || ($user->id === $consumption->user_id && Auth::user()->id === $user->id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->type === 'C';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consumption  $consumption
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Consumption $consumption)
    {
        return $user->id === $consumption->user_id && Auth::user()->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Consumption  $consumption
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Consumption $consumption)
    {
        return $user->id === $consumption->user_id && Auth::user()->id === $user->id;
    }
}
