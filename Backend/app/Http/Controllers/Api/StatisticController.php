<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Consumption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    public function getUserConsumptionsStatistics(Request $request, User $user)
    {
        $num = 12; //Use query string parameter to change number of results
        $consumos = Consumption::where('user_id', $user->id)->orderBy('created_at', 'desc')->limit($num)->get();

        $statistics = [];
        $aux = [];

        foreach ($consumos as $consumo) {
            $dateTime = explode(".", $consumo->created_at);
            array_push($aux, $dateTime[0], $consumo->value);
            array_push($statistics, $aux);
            $aux = [];
        }

        return array_reverse($statistics);
    }

    public function getUserActivityStatistics(Request $request, User $user)
    {
        $num = 12; //Use query string parameter to change number of results
        $consumptions = Consumption::where('user_id', $user->id)->whereNotNull('observation_id')->orderBy('created_at', 'desc')->limit($num)->get();
        $activities = [];
        $aux = [];
        foreach ($consumptions as $consumption) {
            $isActive = false;
            foreach ($consumption->observation->equipments as $equipment) {
                if ($equipment->category->activity == 'Yes') {
                    $isActive = true;
                    break;
                }
            }
            array_push($aux, $consumption->observation->created_at, $isActive ? 'Yes' : 'No');
            array_push($activities, $aux);
            $aux = [];
        }
        return array_reverse($activities);
    }

    public function getUserEquipmentsStatistics(Request $request, User $user)
    {
        $num = 12; //Use query string parameter to change number of results
        $consumptions = Consumption::where('user_id', $user->id)->whereNotNull('observation_id')->orderBy('created_at', 'desc')->limit($num)->get();

        $statistics = [];
        $aux = [];

        foreach ($consumptions as $consumption) {
            $dateTime = explode(".", $consumption->created_at);
            array_push($aux, $dateTime[0], count($consumption->observation->equipments));
            array_push($statistics, $aux);
            $aux = [];
        }

        return array_reverse($statistics);
    }
}
