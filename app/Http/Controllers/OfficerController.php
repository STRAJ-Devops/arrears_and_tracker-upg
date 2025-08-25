<?php

namespace App\Http\Controllers;

use App\Models\Officer;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Branch;
use Illuminate\Support\Facades\Log;

class OfficerController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->get('search');
        $perPage = 10;
        if (!empty($keyword)) {
            $users = Officer::where('names', 'LIKE', "%$keyword%")
                ->orWhere('staff_id', 'LIKE', "%$keyword%")
                ->paginate($perPage);
        } else {
            $users = Officer::paginate($perPage);
        }
        return view('user-management', compact('users'));
    }

    public function create()
    {
        //get all regions
        $regions = Region::all();

        //get all branches
        $branches = Branch::all();

        // Log::info('Create User: Regions count', ['count' => $regions->count()]);
        // Log::info('Create User: Branches count', ['count' => $branches->count()]);

        // // log actual data (be careful in prod, this can be a lot!)
        // Log::debug('Create User: Regions data', $regions->toArray());
        // Log::debug('Create User: Branches data', $branches->toArray());
        return view('users.create', compact('regions', 'branches'));
    }

    public function store(Request $request)
    {

        // log raw input
        Log::info('User store request received', $request->all());
            //validate form data
            $this->validate($request, [
                'names' => 'required',
                'staff_id' => 'required|integer|unique:officers,staff_id',
                'username' => 'required|unique:officers,username',
                'password' => 'required',
            ], [
            'staff_id.unique' => 'This Officer ID is already taken.',
            'username.unique' => 'This Username is already taken.',
        ]);

            $requestData = $request->all();

            
            //un hash password
            $requestData['un_hashed_password'] = $requestData['password'];
            //hash password
            $requestData['password'] = bcrypt($requestData['password']);

        Officer::create($requestData);

        // log what was saved
        // Log::info('New officer created', $officer->toArray());

            return redirect('user-management')->with('success', 'New User Added!');
    }

    // public function edit($id)
    // {
    //     $user = Officer::findOrFail($id);

    //     return view('users.edit', compact('user'));
    // }

    public function edit($id)
    {
        $user = Officer::findOrFail($id);
        $regions = Region::all();
        $branches = Branch::all();

        return view('users.edit', compact('user', 'regions', 'branches'));
    }


    public function update(Request $request, $id)
    {
        $user = Officer::findOrFail($id);

        if ($request->input('reset_password') === 'yes') {
            $user->password = bcrypt($user->username);
            $user->un_hashed_password = $user->username;
            $user->force_password_reset = true;
        }

        // Update other fields except password/reset_password
        $user->fill($request->except(['password', 'reset_password']));

        $user->save();

        // return redirect('user-management')->with('flash_message', 'User Updated!');
        return redirect('user-management')->with('success', 'User Updated!');
    }


    public function destroy($id)
    {
        try {
            Officer::destroy($id);
            return redirect('user-management')->with('success', 'User Deleted!');
        } catch (\Exception $e) {
            return redirect('user-management')->with('error', 'User cannot be deleted!');
        }
    }

}
