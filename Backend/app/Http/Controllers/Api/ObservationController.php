<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ObservationPost;
use App\Http\Resources\ConsumptionResource;
use App\Http\Resources\ObservationResource;
use App\Models\Consumption;
use App\Models\Equipment;
use App\Models\Observation;
use Illuminate\Http\Request;
use Exception;
use App\Models\User;



class ObservationController extends Controller
{
    public function getUserObservations(User $user)
    {
        ObservationResource::$detail = false;
        return  ObservationResource::collection($user->observations);
    }

    public function getUserEquipmentObservations(Equipment $equipment)
    {
        ObservationResource::$detail = false;
        return new ObservationResource($equipment->observations);
    }

    public function getUserLastObservation(User $user)
    {
        $response = new \stdClass();
        $observation = Observation::where('user_id', $user->id)->orderBy('created_at', 'desc')->first();

        ObservationResource::$detail = true;
        $response->observation = new ObservationResource($observation);
        $response->consumption = new ConsumptionResource($observation->consumption);
        return $response;
    }

    public function getUserObservation(User $user, Observation $observation)
    {
        ObservationResource::$detail = true;
        return new ObservationResource($observation);
    }


    public function postUserObservation(ObservationPost $request, User $user)
    {
        $observation = new Observation();
        $observation->fill($request->validated());
        $observation->user_id = $user->id;

        $consumption = Consumption::find($request->consumption_id);
        if ($consumption == null) {
            return response(['error' => 'Consumption ' . $request->consumption_id . ' does not exist'], 404);
        }
        if ($consumption->observation_id != null) {
            $consumption->observation->delete();
        }

        try {
            $observation->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the observation'], 500);
        }

        $isActive = false;
        foreach ($request->equipments as $value) {
            $equipment = Equipment::find($value);
            if ($equipment == null) {
                return response(['error' => 'Equipment ' . $value . ' does not exist'], 404);
            }

            $observation->equipments()->attach($value);

            $isActive = $isActive || $equipment->activity == "Yes";
        }

        $observation->activity = 'No';
        if ($isActive) {
            $observation->activity = 'Yes';
        }

        $consumption->observation_id = $observation->id;

        try {
            $observation->save();
            $consumption->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the observation'], 500);
        }

        ObservationResource::$detail = true;
        return new ObservationResource($observation);
    }

    public function putUserObservation(Request $request, User $user, Observation $observation)
    {
        $observation->fill($request->validated());
        try {
            $observation->save();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when changing the observation'], 500);
        }
        ObservationResource::$detail = true;
        return new ObservationResource($observation);
    }

    public function deleteUserObservation(User $user, Observation $observation)
    {
        try {
            $observation->delete();
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when deleting the observation'], 500);
        }
        ObservationResource::$detail = true;
        return new ObservationResource($observation);
    }
}
