<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeploymentController extends Controller
{
    public function deploy()
    {
        //excute the shell script in public folder called deploy.sh
        $output = shell_exec('sh '.base_path().'/public/deploy.sh');

        return response()->json([
            'message' => 'deployed successfully',
            'output' => $output
        ], 200);
    }
}
