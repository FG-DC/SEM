<?php

namespace App\Http\Controllers\Api;

use \DateTime;
use Exception;
use App\Models\User;
use App\Http\Requests\UserPut;
use App\Http\Requests\UserPost;
use App\Http\Requests\UserPatch;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


class UserController extends Controller
{

    public function getAuthUser()
    {
        return Auth::user();
    }

    public function getUsers()
    {
        return  UserResource::collection(User::all());
    }

    public function getUser(User $user)
    {
        return new UserResource($user);
    }

    public function postUser(UserPost $request)
    {
        $type = $request->type;
        if ($type == null) {
            $type = 'C';
        }

        $user = new User();
        $user->fill($request->validated());
        $user->type = $type;
        $user->password = Hash::make($request->password);
        $user->birthdate = DateTime::createFromFormat('!d/m/Y', $request->birthdate);

        try {
            $user->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the user'], 500);
        }
        return new UserResource($user);
    }

    public function putUser(UserPut $request, User $user)
    {
        $user->fill($request->validated());
        $user->birthdate = DateTime::createFromFormat('!d/m/Y', $request->birthdate);
        try {
            $user->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when changing the user'], 500);
        }
        return new UserResource($user);
    }

    public function patchUserPassword(UserPatch $request, User $user)
    {
        $request->validated();
        if (!password_verify($request->oldPassword, $user->password)) {
            return response(['error' => 'Password is incorrect'], 400);
        }
        $user->password = Hash::make($request->newPassword);
        try {
            $user->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when changing the user'], 500);
        }
        return new UserResource($user);
    }

    public function deleteUser(User $user)
    {
        try {
            $user->delete();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when deleting the user'], 500);
        }
        return new UserResource($user);
    }
}
