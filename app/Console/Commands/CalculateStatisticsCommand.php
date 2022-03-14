<?php

namespace App\Console\Commands;

use App\Models\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as Logg;

class CalculateStatisticsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:calculate_stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used to calculate the logs received from our website';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Query all logs stats
        $logs = DB::select( "select count(st.id) as number, st.ended_at_date, st.countryCode FROM(SELECT id, ended_at_date, json_unquote(json_extract(params, '$.geolocation.countryCode')) as countryCode from logs) as st GROUP BY st.ended_at_date, st.countryCode order by st.countryCode");

        // Reduce $all $logs stats by country and date
        $res = array_reduce($logs, array($this,"reduce"));

        // Try to put stats in cache
        try {
            cache()->put('stats', $res);
        } catch (\Exception $e) {
            Logg::debug($e);
        }
    }

    function reduce($arr, $item)
    {
        $countryCode = $item->countryCode;
        $arr[$countryCode][$item->ended_at_date] = $item->number;
        return $arr;
    }

}
