<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
class CheckForFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:file';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for a specific file in a folder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $folderPath = public_path('uploads');
        $filesInFolder = File::files($folderPath);

        //check if $filesInFolder is empty
        if (empty($filesInFolder)) {
            Log::info("No files found in {$folderPath}");
            return 0;
        }

        //check if the file is found, just get the first file
        $file = $filesInFolder[0];

        

        return 0;
    }
}
