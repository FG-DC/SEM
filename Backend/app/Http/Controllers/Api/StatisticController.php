<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Consumption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    public function getUserEnergyStatistics(Request $request, User $user)
    {
        $n = $request->query('months') ?? 1;
        $times = [];

        $n_time = new \stdClass();
        $n_time->year = intval(date('Y'));
        $n_time->month = intval(date('n'));

        for ($i = 0; $i < $n; $i++) {
            $item = $this->getMonthTimes($n_time);
            array_push($times, $item);

            $this->getPrevMonth($n_time);
        }

        $response = [];
        foreach ($times as $time) {
            $avg = Consumption::where('user_id', $user->id)
                ->whereRaw('timestamp >= FROM_UNIXTIME(' . $time->start . ')')
                ->whereRaw('timestamp <= FROM_UNIXTIME(' . $time->end . ')')
                ->avg('value');

            $time_diff_seconds = $time->end - $time->start;
            $time_diff_hour = $time_diff_seconds / 3600;

            $totalW = floatval($avg) ?? 0;

            $kwh = $totalW * $time_diff_hour / 1000;

            $item = new \stdClass();
            $item->timestamp = $time->desc;
            $item->value = number_format($kwh, 2);
            array_push($response, $item);
        }

        return response($response);
    }

    private function getMonthTimes($time)
    {
        $item = new \stdClass();

        $item->start = mktime(0, 0, 0, $time->month, 1, $time->year);
        $item->end = mktime(0, 0, 0, $time->month + 1, 0, $time->year);
        $item->desc = date('M Y', $item->start);

        return $item;
    }

    private function getPrevMonth($time)
    {
        if ($time->month > 1) {
            $time->month--;
            return;
        }
        $time->year--;
        $time->month = 12;
    }
}
