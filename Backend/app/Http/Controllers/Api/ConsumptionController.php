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
    public function getUserConsumptions(User $user, Request $request)
    {
        

        $hasLimit = $request->query('limit') != null;
        $limit = $request->query('limit');
        $hasObservation = $request->query('observation') != null;
        $observation = $request->query('observation');

        $query = Consumption::where('user_id', $user->id);
        if ($hasObservation) {
            if ($observation == '0') {
                $query = $query->whereNull('observation_id');
            } elseif ($observation == '1') {
                $query = $query->whereNotNull('observation_id');
            }
        }
        $query = $query->orderBy('timestamp', 'desc');
        if ($hasLimit) {
            $query = $query->limit($limit);
        }
        return ConsumptionResource::collection($query->get());
    }

    public function getUserConsumption(User $user, Consumption $observation)
    {
        return new ConsumptionResource($observation);
    }

    public function postUserConsumption(ConsumptionPost $request, User $user)
    {
        $consumptions = $request->consumptions;
        $count = 0;

        foreach ($consumptions as $consumption) {
            $new_consumption = new Consumption();
            $new_consumption->fill($consumption);
            $new_consumption->timestamp = date("Y-m-d H:i:s", $consumption["timestamp"]);

            $new_consumption->user_id = $user->id;

            try {
                $new_consumption->save();
            } catch (Exception $e) {
                return response(['error' => 'Something went wrong when creating the consumption'], 500);
            }

            $count += 1;
        }

        return response(['msg' => $count . ' consumptions created with success'], 201);
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
