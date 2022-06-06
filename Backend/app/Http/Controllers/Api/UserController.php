<?php

namespace App\Http\Controllers\Api;

use \DateTime;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserPut;
use App\Http\Requests\UserPost;
use App\Http\Requests\UserPatch;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\EquipmentResource;
use App\Models\TrainingExample;

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

            //$newUser = User::where('email', $user->email)->first();
            //$newUser->sendEmailVerificationNotification();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the user'], 500);
        }
        return new UserResource($user);
    }

    public function putUser(UserPut $request, User $user)
    {
        $user->name = $request->name;
        $user->energy_price = $request["energy_price"];
        $user->no_activity_start = date("Y-m-d H:i:s", $request["no_activity_start"]);
        $user->no_activity_end = date("Y-m-d H:i:s", $request["no_activity_end"]);
        $user->notifications = $request["notifications"];
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

    public function patchUserEnergyPrice(Request $request, User $user)
    {
        if ($request["energy_price"] && is_numeric($request["energy_price"])) {
            $user->energy_price = $request["energy_price"];
            try {
                $user->save();
            } catch (Exception $e) {
                return response(['error' => 'Something went wrong when changing the energy price'], 500);
            }
        }
        return response(['error' => 'Energy price is not in a valid format'], 400);
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

    public function patchGetStarted(Request $request, User $user){
        if (is_numeric($request["get_started"])) {
            $user->get_started = $request["get_started"];
            try {
                $user->save();
                return $user->get_started;
            } catch (Exception $e) {
                return response(['error' => 'Something went wrong when changing get started status'], 500);
            }
        }
        return response(['error' => 'Get started is not in a valid format'], 400);
    }


    public function getUserStats(User $user){
        $equipments = $user->equipments;
        $trainCount = [];
        foreach($equipments as $equipment){
            $item = new \stdClass();
            $item->equipment_name = $equipment->name;
            $item->count = $equipment->examples;
            array_push($trainCount, $item);
        }
        $stats = new \stdClass();
        $stats->divisions = $user->divisions()->count();
        $stats->equipments =$user->equipments()->count();
        $stats->training_examples = $trainCount;
        return $stats;
    }

}
