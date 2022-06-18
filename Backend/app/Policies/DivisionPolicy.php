<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Division;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class DivisionPolicy
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
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Division $division)
    {
        return $user->type === 'P' || ($user->id === $division->user_id && Auth::user()->id === $user->id);
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
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Division $division)
    {
        return $user->id === $division->user_id && Auth::user()->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Division $division)
    {
        return $user->id === $division->user_id && Auth::user()->id === $user->id;
    }
}
