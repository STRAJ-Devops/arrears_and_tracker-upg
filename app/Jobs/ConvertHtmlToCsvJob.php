<?php

namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        //set max_execution_time to 30mins
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

        //convert $csv[$i][30], $csv[$i][32] from a format '19 JUN 2024' to 19-Jun-24
        //read the csv file
        $csv = array_map('str_getcsv', file($csvFile));

        // Loop through the CSV file
        for ($i = 5; $i < count($csv); $i++) {
            try {
                // Convert and update column 30 if it exists
                if (isset($csv[$i][30])) {
                    $date = Carbon::createFromFormat('d M Y', $csv[$i][30])->format('d-M-y');
                    $csv[$i][30] = $date;
                }

                // Convert and update column 32 if it exists
                if (isset($csv[$i][32])) {
                    $date = Carbon::createFromFormat('d M Y', $csv[$i][32])->format('d-M-y');
                    $csv[$i][32] = $date;
                }

                // Convert and update column 32 if it exists
                if (isset($csv[$i][31])) {
                    $date = Carbon::createFromFormat('d M Y', $csv[$i][31])->format('d-M-y');
                    $csv[$i][31] = $date;
                }
            } catch (\Exception $e) {
                // Handle the error, e.g., log the issue, skip the row, etc.
                // For now, just skip invalid dates
                continue;
            }
        }

        // Write the updated data back to the CSV file
        $fileHandle = fopen($csvFile, 'w');
        if ($fileHandle === false) {
            throw new \Exception('Could not open CSV file for writing.');
        }

        foreach ($csv as $row) {
            fputcsv($fileHandle, $row);
        }

        fclose($fileHandle);

        //delete the xls file
        unlink($this->filePath);

        //dispatch UploadCsvToRemoteStorage immediately
        UploadCsvToRemoteStorage::dispatch($csvFile);
    }
}
