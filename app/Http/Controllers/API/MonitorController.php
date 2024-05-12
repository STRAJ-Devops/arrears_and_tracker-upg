<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Monitor;
use Illuminate\Http\Request;

class MonitorController extends Controller
{
    public function getMonitors()
    {
        //get get request from the client called activity
        $activity = request()->get('activity');
        if ($activity == 'Marketing') {
            //get all monitors that are marketing
            $monitors = Monitor::where('activity', 'Marketing')->get();
            return response()->json(['monitors' => $monitors, 'message'=>"sales activities successfully fetched"], 200);
        }

        if ($activity == 'Appraisal') {
            //get all monitors that are appraisal
            $monitors = Monitor::where('activity', 'Appraisal')->get();
            return response()->json(['monitors' => $monitors, 'message'=>"sales activities successfully fetched"], 200);
        }

        if ($activity == 'Application') {
            //get all monitors that are application
            $monitors = Monitor::where('activity', 'Application')->get();
            return response()->json(['monitors' => $monitors, 'message'=>"sales activities successfully fetched"], 200);
        }

        //get all monitors
        $monitors = Monitor::all();

        return response()->json(['monitors' => $monitors, 'message'=>"sales activities successfully fetched"], 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $requestData = $request->all();
        $requestData['staff_id'] = auth()->user()->staff_id;
        Monitor::create($requestData);

        return response()->json(['message' => 'Monitor created successfully'], 200);
    }

    public function appraise()
    {
        try {
            $monitor = Monitor::findOrFail(request()->get('monitor_id'));
            $monitor->appraisal_date = (string) now();
            $monitor->save();

            return response()->json(['monitor' => $monitor, 'message'=>"appraisal successful"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function apply()
    {

        try {
            $monitor = Monitor::findOrFail(request()->get('monitor_id'));
            $monitor->application_date = (string) now();
            $monitor->save();
            return response()->json(['monitor' => $monitor, 'message'=>"apply successfull"], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
