<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user, User $model)
    {
        return $user->type === 'P' || $model->id === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Equipment $equipment, User $model)
    {
        return $user->type === 'P' || ($user->id === $equipment->user_id && $model->id === $user->id);
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
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Equipment $equipment, User $model)
    {
        return $user->id === $equipment->user_id && $model->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Equipment  $equipment
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Equipment $equipment, User $model)
    {
        return $user->id === $equipment->user_id && $model->id === $user->id;
    }
}
