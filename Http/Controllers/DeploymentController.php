<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Process;

class DeploymentController extends Controller
{
    public function deploy()
    {
        $deployScript = realpath(public_path('deploy.sh'));

        $process = shell_exec("bash $deployScript 2>&1");

        \Log::info($process);

        return response()->json(['message' => 'Deployment in progress']);
    }
}
