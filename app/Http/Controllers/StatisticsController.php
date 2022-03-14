<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function dashboard(){
        try {
            // Return retrieved stats from cache
            return response()->json(cache()->get('stats'), 200);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function addElement(Request $request){
        try {
            $log =  new Log();
            $log->params =   json_encode($request->get('params'));
            $log->ended_at_date =  new \DateTime($request->get('ended_at_date'));
            $log->ended_at=  new \DateTime($request->get('ended_at'));
            $log->position=  $request->get('position');
            $log->save();

            return response()->json($log, 200);
        }catch (\Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
