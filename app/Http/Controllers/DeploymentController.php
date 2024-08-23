<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Process;

class DeploymentController extends Controller
{
    public function deploy()
    {
        $deployScript = public_path('deploy.sh');

        //run deploy.sh in public folder
        $process = Process::run(['sh', $deployScript]);

        return response()->json([
            'message' => 'Deployment in progress',
            'output' => $process->output(),
            'success' => $process->successful(),
            'failed' => $process->failed(),
        ]);
    }
}
