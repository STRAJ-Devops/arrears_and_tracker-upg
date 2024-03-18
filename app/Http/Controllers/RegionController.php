<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::all();
        return view('regions', compact('regions'));
    }

    public function getAllRegions(){
       //return in the format { "results":[{"id":1,"text":"Region 1"}, {"id":2,"text":"Region 2"}]}
         $regions = Region::all();
            $data = array();
            foreach ($regions as $region) {
                $data[] = array('id' => $region->region_id, 'text' => $region->region_name);
            }

            return response()->json(['results' => $data]);
    }
}
