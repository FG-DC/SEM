<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipment;
use App\Models\Observation;
use Illuminate\Auth\Access\HandlesAuthorization;

class ObservationPolicy
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
        return true;
    }

    public function viewAnyObs(User $user, Equipment $equipment)
    {
        return $user->id === $equipment->user_id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Observation $observation)
    {
        return $user->id === $observation->user_id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewLast(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->type === 'P';
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Observation $observation)
    {
        return $user->type === 'P' || $user->id === $observation->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Observation $observation)
    {
        return $user->type === 'P' || $user->id === $observation->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Observation $observation)
    {
        return $user->id === $observation->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Observation  $observation
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Observation $observation)
    {
        return false;
    }
}
