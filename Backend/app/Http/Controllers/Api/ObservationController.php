<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use App\Models\Division;
use App\Models\Equipment;
use App\Models\Consumption;
use App\Models\Observation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\ObservationPost;
use App\Http\Resources\ConsumptionResource;
use App\Http\Resources\ObservationResource;



class ObservationController extends Controller
{
    public function getUserObservations(User $user, Request $request)
    {
        $response = [];
        $hasNoLimit = $request->query('limit') == null;
        $num = $request->query('limit');
        $consumptions = $hasNoLimit ? $user->observations : Consumption::where('user_id', $user->id)->whereNotNull('observation_id')->orderBy('created_at', 'desc')->limit($num)->get();
        foreach ($consumptions as $consumption) {

            ObservationResource::$detail = true;
            $item = new \stdClass();
            $item->observation = new ObservationResource($consumption->observation);
            $item->consumption = new ConsumptionResource($consumption);

            array_push($response, $item);
        }

        return response($response, 200);
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
        if ($observation == null)
            return response(null, 404);

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
        foreach ($request->equipments as $key => $value) {
            $equipment = Equipment::find($value);
            if ($equipment == null) {
                return response(['error' => 'Equipment ' . $value . ' does not exist'], 404);
            }
            if ($equipment->user_id != $user->id) {
                return response(['error' => 'Equipment ' . $value . ' does not belongs to you'], 400);
            }

            $observation->equipments()->attach($equipment->id, ['consumptions' => $request->consumptions[$key]]);

            $isActive = $isActive || $equipment->activity == "Yes";
        }

        foreach ($request->expected_divisions as $value) {
            $division = Division::find($value);
            if ($division == null) {
                return response(['error' => 'Division ' . $value . ' does not exist'], 404);
            }
            if ($division->user_id != $user->id) {
                return response(['error' => 'Division ' . $value . ' does not belongs to you'], 400);
            }

            $observation->divisions()->attach($division->id);
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
