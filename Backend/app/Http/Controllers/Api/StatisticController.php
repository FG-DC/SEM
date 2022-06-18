<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Consumption;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\Observation;

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

    public function getAdminStatistics(Request $request)
    {
        $date = null;
        if ($request->query('timestamp')) {
            $date = strtotime($request->query('timestamp'));
        } else {
            $date = time();
        }

        $day = date("d", $date);
        $month = date("m", $date);
        $year = date("Y", $date);

        $timeStart = mktime(0, 0, 0, $month, $day, $year);
        $timeEnd = mktime(23, 59, 59, $month, $day, $year);

        $stats = new \stdClass();
        $stats->clients = 0;
        $stats->producers = 0;
        $stats->admins = 0;

        $stats->observations = Observation::whereRaw('created_at >= FROM_UNIXTIME(' . $timeStart . ')')
            ->whereRaw('created_at <= FROM_UNIXTIME(' . $timeEnd . ')')
            ->count();

        $stats->consumptions = Consumption::whereRaw('timestamp >= FROM_UNIXTIME(' . $timeStart . ')')
            ->whereRaw('timestamp <= FROM_UNIXTIME(' . $timeEnd . ')')
            ->avg();

        $stats->alerts = Alert::whereRaw('created_at >= FROM_UNIXTIME(' . $timeStart . ')')
            ->whereRaw('created_at <= FROM_UNIXTIME(' . $timeEnd . ')')
            ->count();
    }
}
