<?php
require 'simplexlsx-master/src/SimpleXLSX.php'; // Adjust the path to where you saved SimpleXLSX.php
use Shuchkin\SimpleXLSX;

$agnId = 'AGN-00010';
// $dateFo = '';
$dateFo = '12-06-2024';

if ($dateFo == '') {
    $folder = 'archive-direct/' . $agnId; // Corrected with missing semicolon
} else {
    // echo "check"; 
    $folder = 'archive-direct/' . $agnId . '/' . $dateFo . '/verification_reports';
}

function getFilesRecursively($directory, &$allFiles = []) {
    $files = glob($directory . '/*');
    foreach ($files as $file) {
        if (is_dir($file)) {
            getFilesRecursively($file, $allFiles);
        } else {
            $allFiles[] = $file;
        }
    }
}

$excelFiles = [];
getFilesRecursively($folder, $excelFiles);

if (empty($excelFiles)) {
    echo "No files found in directory: $folder\n";
    exit;
}

$fileCount = count($excelFiles);
// echo "File Count: " . $fileCount . "\n";
$combined=array();
foreach ($excelFiles as $filePath) {
    // echo "Processing file: $filePath\n";
    if (pathinfo($filePath, PATHINFO_EXTENSION) == 'xlsx' && $xlsx = SimpleXLSX::parse($filePath)) {
        $arr=$xlsx->rows();
        $header=$arr[0];
        array_shift($arr);
        foreach ($arr as $row) {
            $combined[] = array_combine($header, $row);
        }
    } else {
        echo SimpleXLSX::parseError();
    }
    echo "\n";
}
echo "<pre>";

print_r($combined);

?>