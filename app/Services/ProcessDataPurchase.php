<?php

namespace App\Services;

class ProcessDataPurchase
{

    public function handle($content): array
    {
        // Split input into lines
        $lines = explode("\n", $content);

        $lineCounter = 0;
        $linesToProcess = [];
        $lineFull = '';

        // Process each line
        foreach ($lines as $line) {
            $lineCounter++;
            // Remove newline and tab characters
            $replaceBarraN = str_replace("\n", " ", $line);
            $replaceBarraT = str_replace("\t", " ", $replaceBarraN);
            $lineFull = $lineFull . $replaceBarraT;

            // After every third line, add to $linesToProcess and reset lineFull
            if ($lineCounter == 3) {
                $linesToProcess[] = $lineFull;
                $lineCounter = 0;
                $lineFull = '';
            }
        }
        $records = [];
        $amount = 0;
        foreach ($linesToProcess as $line){
            //dd($line);
            $record = $this->applyRegex($line);
            $records[] = $record;
            $amount= $amount+$record['total_price'];
        }

        //dump(array_keys($records[0]),$records);

        //dump('Total: '.$amount);
        //dump('total items:' . count($records));
        //dd(789);
        return $records;
    }
    private function applyRegex($line): array
    {
        $inputString = $line;
// Define a regular expression pattern
        $pattern = '/^(.*?)\s+\(Código:\s+(\d+)\s+\)\s+Qtde\.:(.*?)\s+UN:\s+(.*?)\s+Vl\. Unit\.:\s+([\d,]+)\s+Vl\. Total\s+([\d,]+)\s*$/u';

// Use preg_match to extract data
        if (preg_match($pattern, $inputString, $matches)) {
            $productName = trim($matches[1]);
            $productCode = $matches[2];
            $quantity = str_replace(',', '.', $matches[3]); // Convert ',' to '.' for decimal
            $unitOfMeasurement = trim($matches[4]);
            $unitPrice = (float)str_replace(',', '.', preg_replace('/\s+/', '', $matches[5])); // Remove spaces and replace ',' with '.'
            $totalPrice = (float)str_replace(',', '.', preg_replace('/\s+/', '', $matches[6])); // Remove spaces and replace ',' with '.'

            return [
                "product_name" => strtoupper($productName),
                "product_code" => $productCode,
                "quantity" => $quantity,
                "unit_measure" => $unitOfMeasurement,
                "unit_price" => $unitPrice,
                "total_price" => $totalPrice,
            ];
        } else {
            echo "Failed to extract data from the input string.\n";
            exit();
        }
    }
}
