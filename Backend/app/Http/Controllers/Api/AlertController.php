<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Alert;
use App\Models\Observation;
use App\Http\Requests\AlertPost;
use App\Http\Controllers\Controller;
use App\Http\Resources\AlertResource;

class AlertController extends Controller
{
    public function getUserAlerts(User $user)
    {
        return AlertResource::collection($user->alerts);
    }

    public function getUserAlert(User $user, Alert $alert)
    {
        return new AlertResource($alert);
    }

    public function postUserAlert(User $user, AlertPost $request)
    {
        $alert = new Alert();
        $alert->fill($request->validated());
        $alert->user_id = $user->id;

        $obs = Observation::find($request->observation_id);
        if ($obs == null) {
            return response(['error' => 'Observation ' . $request->observation_id . ' does not exist'], 404);
        }

        try {
            $alert->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the alert'], 500);
        }

        return new AlertResource($alert);
    }

    public function putUserAlert(User $user, Alert $alert, AlertPost $request)
    {
        $alert->fill($request->validated());

        $obs = Observation::find($request->observation_id);
        if ($obs == null) {
            return response(['error' => 'Observation ' . $request->observation_id . ' does not exist'], 404);
        }

        try {
            $alert->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when changing the alert'], 500);
        }

        return new AlertResource($alert);
    }

    public function deleteUserAlert(User $user, Alert $alert)
    {
        try {
            $alert->delete();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when deleting the alert'], 500);
        }
        return new AlertResource($alert);
    }
}
