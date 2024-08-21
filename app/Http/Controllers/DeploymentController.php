<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Process;

class DeploymentController extends Controller
{
    public function deploy()
    {
        //run deploy.sh in public folder
        $process = Process::run(['sh', 'deploy.sh']);

        return response()->json([
            'message' => 'Deployment in progress',
            'output' => $process->output(),
            'success' => $process->successful(),
            'failed' => $process->failed(),
        ]);
    }
}
