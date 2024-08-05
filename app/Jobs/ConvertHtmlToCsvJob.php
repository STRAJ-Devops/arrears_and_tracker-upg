<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
//import the job to upload the csv file to remote storage
use UploadCsvToRemoteStorageJob;

class ConvertHtmlToCsvJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;

    /**
     * Create a new job instance.
     *
     * @param string $filePath
     * @return void
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Read the HTML file
        $html = file_get_contents($this->filePath);

        // Initialize DOMDocument to parse HTML
        $doc = new \DOMDocument();
        @$doc->loadHTML($html); // Suppress warnings due to HTML5 tags

        // Initialize an array to store CSV data
        $data = [];

        // Get all the table rows
        $rows = $doc->getElementsByTagName('tr');
        foreach ($rows as $row) {
            $values = [];
            foreach ($row->childNodes as $cell) {
                // Check if the node is an element node (ignores text nodes)
                if ($cell->nodeType === XML_ELEMENT_NODE) {
                    // Clean non-breaking spaces and trim whitespace
                    $cellValue = trim($cell->textContent);
                    $cellValue = str_replace("\xC2\xA0", '', $cellValue); // Remove non-breaking space
                    $values[] = $cellValue;
                }
            }
            $data[] = $values;
        }

        // Path to the output CSV file
        $csvFile = str_replace('.xls', '.csv', $this->filePath);

        // Open the file for writing
        $fileHandle = fopen($csvFile, 'w');

        // Check if file handle is valid
        if ($fileHandle === false) {
            throw new \Exception('Could not open CSV file for writing.');
        }

        // Write each row to the CSV file
        foreach ($data as $row) {
            fputcsv($fileHandle, $row);
        }

        // Close the file handle
        fclose($fileHandle);

        //delete the xls file
        // unlink($this->filePath);

        //delete the xls file
        unlink($this->filePath);

        //upload the output csv file
        UploadCsvToRemoteStorage ::dispatch($csvFile);
    }
}
