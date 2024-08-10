<?php

namespace App\Http\Controllers;

use App\Models\Monitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('monitor.index');
    }

    public function getMonitors()
    {
        //get get request from the client called activity
        $activity = request()->get('activity');
        if ($activity == 'Marketing') {
            //get all monitors that are marketing
            $monitors = Monitor::where('activity', 'Marketing')->with('officer')->get();
            return response()->json(['monitors' => $monitors], 200);
        }

        if ($activity == 'Appraisal') {
            //get all monitors that are appraisal
            $monitors = Monitor::where('activity', 'Appraisal')->with('officer')->get();
            return response()->json(['monitors' => $monitors], 200);
        }


        if ($activity == 'Application') {
            //get all monitors that are application
            $monitors = Monitor::where('activity', 'Application')->with('officer')->get();
            return response()->json(['monitors' => $monitors], 200);
        }

        //get all monitors
        $monitors = Monitor::all()->load('officer');

        return response()->json(['monitors' => $monitors], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {

        return view('monitor.create');
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
        if($requestData['activity'] == '6'){
            $requestData['activity'] = $requestData['other_activity'];
        }
        Monitor::create($requestData);

        return redirect('monitors')->with('flash_message', 'sales activity added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {

        $monitor = DB::table('monitors')->where('id', $id)->join('officers', 'monitors.staff_id', '=', 'officers.staff_id')->first();
        $monitor_comments = DB::table('sales_activity_comments')->where('sales_activity_id', $id)->join('officers', 'sales_activity_comments.officer_id', '=', 'officers.staff_id')->get();
        return view('monitor.show', compact('monitor', 'monitor_comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $monitor = Monitor::findOrFail($id);

        return view('monitor.edit', compact('monitor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {

        $requestData = $request->all();

        $monitor = Monitor::findOrFail($id);
        $monitor->update($requestData);

        return redirect('admin/monitor')->with('flash_message', 'monitor updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        Monitor::destroy($id);

        return redirect('admin/monitor')->with('flash_message', 'monitor deleted!');
    }

    public function appraise()
    {
        try {
            $monitor = Monitor::findOrFail(request()->get('monitor_id'));
            $monitor->appraisal_date = (string) now();
            $monitor->activity = 'Appraisal';
            $monitor->save();

            return response()->json(['monitor' => $monitor], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function apply()
    {

        try {
            $monitor = Monitor::findOrFail(request()->get('monitor_id'));
            $monitor->application_date = (string) now();
            $monitor->activity = 'Application';
            $monitor->save();
            return response()->json(['monitor' => $monitor], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function add_comment()
    {
        try{
        $save = DB::table('sales_activity_comments')->insert([
            'sales_activity_id' => request()->get('sales_activity_id'),
            'officer_id' => auth()->user()->staff_id,
            'comment' => request()->get('comment'),
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }catch(\Exception $e){
        return response()->json(['error' => $e->getMessage()], 500);
    }
        return response()->json(['comment' => $save], 200);
    }
}
