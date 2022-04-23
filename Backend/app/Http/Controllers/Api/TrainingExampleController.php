<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Consumption;
use App\Models\TrainingExample;
use App\Http\Controllers\Controller;
use App\Http\Requests\TrainingExamplePost;
use App\Http\Resources\TrainingExampleResource;
use Exception;

class TrainingExampleController extends Controller
{
    public function getUserTrainingExamples(User $user)
    {
        return TrainingExampleResource::collection($user->trainingExamples);
    }

    public function postUserTrainingExample(TrainingExamplePost $request, User $user)
    {
        $request->validated();
        $equipmentsON = !is_array($request->equipments_on) ? [] : $request->equipments_on;
        $count = 0;

        if (!is_null($request->consumptions)) {
            foreach ($request->consumptions as $consumption_id) {

                $consumption = Consumption::where('user_id', $user->id)->find($consumption_id);
                if ($consumption == null) {
                    return response(['error' => 'Consumption ' . $consumption_id . ' does not exist'], 404);
                }

                $this->storeExamplesFor($user, $consumption, $equipmentsON);
                $count++;
            }
        }

        if (!is_null($request->consumption_start) && !is_null($request->consumption_end)) {
            $start = $request->consumption_start;
            $end = $request->consumption_end;
            $consumptions = Consumption::where('user_id', $user->id)->whereBetween('id', [$start, $end])->get();

            foreach ($consumptions as $consumption) {
                $this->storeExamplesFor($user, $consumption, $equipmentsON);
                $count++;
            }
        }

        return response([$count . ' examples created with success'], 201);
    }

    private function getSeasonFrom($day_year)
    {
        if ($day_year < 79) return 'Winter';
        if ($day_year < 172) return 'Spring';
        if ($day_year < 266) return 'Summer';
        if ($day_year < 355) return 'Autumn';
        return 'Winter';
    }

    private function storeExamplesFor($user, $consumption, $equipmentsON)
    {
        try {
            foreach ($user->equipments as $equipment) {
                //CREATE ONE ROW FOR EACH EQUIPMENT
                $trainingExample = new TrainingExample();

                $equipmentStatusON = in_array($equipment->id, $equipmentsON);

                $trainingExample->user_id = $user->type == 'A' ? null : $user->id;
                $trainingExample->consumption = $consumption->value;
                $trainingExample->consumption_variance = $consumption->variance;
                $trainingExample->time = $consumption->created_at;
                $trainingExample->weekend = $consumption->created_at->format("w") == '0' || $consumption->created_at->format("w") == '6' ? "Yes" : "No";
                $trainingExample->day_week = $this->getDayWeekByInteger(intval($consumption->created_at->format("w")));
                $trainingExample->season = $this->getSeasonFrom(intval($consumption->created_at->format("z")) + 1);
                $trainingExample->equipment_id = $equipment->id;
                $trainingExample->equipment_consumption = $equipmentStatusON ? $equipment->consumption : 0;
                $trainingExample->equipment_division = $equipment->division->name;
                $trainingExample->equipment_type = $equipment->type->name;
                $trainingExample->equipment_activity = $equipment->activity;
                $trainingExample->equipment_status = $equipmentStatusON ? 'ON' : 'OFF';

                $trainingExample->save();
            }
        } catch (Exception $e) {
            return response(['error' => 'Something went wrong when creating the training examples'], 500);
        }
    }

    private function getDayWeekByInteger($number)
    {
        switch ($number) {
            case 0:
                return "Sunday";
            case 1:
                return "Monday";
            case 2:
                return "Tuesday";
            case 3:
                return "Wednesday";
            case 4:
                return "Thursday";
            case 5:
                return "Friday";
            case 6:
                return "Saturday";
        }
    }
}
