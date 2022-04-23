<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Consumption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsumptionPost;
use App\Http\Resources\ConsumptionResource;
use App\Models\Observation;

class ConsumptionController extends Controller
{
    public function getUserConsumptions(User $user)
    {
        return  ConsumptionResource::collection($user->consumptions);
    }

    public function getUserConsumption(User $user, Consumption $observation)
    {
        return new ConsumptionResource($observation);
    }

    public function postUserConsumption(ConsumptionPost $request, User $user)
    {
        $consumption = new Consumption();
        $consumption->fill($request->validated());
        $consumption->user_id = $user->id;

        try {
            $consumption->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the consumption'], 500);
        }

        return new ConsumptionResource($consumption);
    }

    public function putUserConsumption(Request $request, User $user, Consumption $consumption)
    {
        $validated_data = $request->validated();
        $consumption->fill($validated_data);

        if ($request->observation_id != null) {
            $obs = Observation::find($request->observation_id);
            if ($obs == null) {
                return response(['error' => 'Observation ' . $request->observation_id . ' does not exist'], 404);
            }
            if ($obs->user_id != $user->id) {
                return response(['error' => 'Observation ' . $request->observation_id . ' does not belongs to you'], 400);
            }
        }

        try {
            $consumption->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when changing the consumption'], 500);
        }

        return new ConsumptionResource($consumption);
    }

    public function deleteUserConsumption(User $user, Consumption $consumption)
    {
        try {
            $consumption->delete();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when deleting the consumption'], 500);
        }
        return new ConsumptionResource($consumption);
    }
}
