<?php

namespace App\Console\Commands;

use App\Jobs\ConvertHtmlToCsvJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Bus;



class CheckUploads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'uploads:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for files in the public/uploads directory every 30 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $directory = public_path('uploads');

        if (!is_dir($directory)) {
            $this->error('Uploads directory does not exist.');
            return;
        }

        $files = array_diff(scandir($directory), ['.', '..']);

        if (empty($files)) {
            $this->info('No files found.');
            return;
        }

        // Perform the desired action with the files
        foreach ($files as $file) {
            $filePath = $directory . '/' . $file;

            //get the full path of the file
            $fullPath = public_path('uploads/'.$file);
            //If the file ends with .xls, not html dispatch a job to convert it to csv
            if (pathinfo($fullPath, PATHINFO_EXTENSION) == 'xls') {
                $this->info('Converting file: ' . $file);
                ConvertHtmlToCsvJob::dispatch($fullPath);
            }
            // Example action: Log file names
            Log::info('File found: ' . $file);

            // Add additional file handling logic here
        }

        $this->info('Files check completed.');
    }
}
