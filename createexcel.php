<?php

$mysqli1= mysqli_connect('localhost', 'root', '', 'mounac53_centraldb');
$mysqli= mysqli_connect('localhost', 'root', '', 'mounac53_vocoxp3_0');
    
 require __DIR__.'/simplexlsx-master/src/SimpleXLSXGen.php';
 use \Shuchkin\SimpleXLSXGen as SimpleXLSXGen;


// Get today's date
$today = date('d');

// Handle the month's first day
if ($today == 1) {
    // Get the last day of the previous month
    $yesterday = date('t', strtotime('last day of last month'));
    $whDate=date('Y-m-').$yesterday;
} else {
    // Subtract one day from today
    $yesterday = $today - 1;
    if($yesterday==31 || $yesterday==30){
        $month=date('m');
        $newmonth;
        if($month==1){
           $newmonth=12;
        }else{
        $newmonth=date('m')-1;
        }
        $whDate=date('Y-'.$newmonth.'-').$yesterday;
    }else{
        $whDate=date('Y-m-').$yesterday;
    }
}

 $getDirectVeriQuery="SELECT `direct_id`, `application_id`, `agency_id`, `verification_id`, `linked_table`, `completed_on` FROM `direct_verification_details_all` WHERE DATE(`completed_on`) = '$whDate' AND `activity_status`='1'
";
$getDirectVeriQueryres=$mysqli1->query($getDirectVeriQuery);


$agencyData = array();

while ($arr = mysqli_fetch_assoc($getDirectVeriQueryres)) {
    $table = $arr['linked_table'];
    $direct_id = $arr['direct_id'];
    $application_id = $arr['application_id'];
    $agency_id = $arr['agency_id'];
    $verification_id = $arr['verification_id'];

    $getData = "
            SELECT * 
            FROM `$table` 
            WHERE  
           `agency_id` = '$agency_id' 
            AND DATE(`completed_on`) = '$whDate'
    ";
    $getDatares = $mysqli1->query($getData);

    if (!$getDatares) {
        die("Error in second query: " . $mysqli1->error);
    }

    if (!isset($agencyData[$agency_id])) {
        $agencyData[$agency_id] = array(); // Initialize array for this agency_id if not already set
    }

    while ($arr1 = mysqli_fetch_assoc($getDatares)) {
        $arr1['table'] = $table; 
        $agencyData[$agency_id][] = $arr1;
    }
}

$header= [
   '<left><b>id</b></left>', '<b>direct_id</b>', '<b>application_id</b>', '<b>agency_id</b>', '<b>admin_id</b>', '<b>initiated_on</b>', '<b>completed_on</b>', '<b>activity_status</b>', '<b>pan_number</b>', '<b>name</b>', '<b>father_name</b>', '<b>dob</b>', '<b>user_photo</b>', '<b>front_photo</b>', '<b>table</b>'
];

foreach ($agencyData as $agency_id => $data) {
    array_unshift($data, $header);
    $agencyData[$agency_id] = $data;
    $getAgName="SELECT `name` FROM `agency_header_all` WHERE `agency_id`='$agency_id'";
    $getAgNameres=$mysqli->query($getAgName);
    $getAgNameArr=mysqli_fetch_assoc($getAgNameres);
    $name = $getAgNameArr['name'];
// Remove spaces from the string
$nameWithoutSpaces = str_replace(' ', '', $name);
// Get the first 5 characters from the string without spaces
$firstFiveChars = substr($nameWithoutSpaces, 0, 5);

    // Directory name
    $directoryName = "archive-direct/".$agency_id."/".date("d-m-Y", strtotime($whDate))."/verification_reports";
    
    // Check if the directory doesn't exist
    if (!is_dir($directoryName)) {
        // Create directory
        mkdir($directoryName, 0755, true); // The third argument 'true' creates nested directories recursively
        
    } else {
        // echo "Directory '$directoryName' already exists.\n";
    }
    
    // File name and content
    $fileName = $directoryName . "/".$firstFiveChars."_".date("d-m-Y", strtotime($whDate)).".xlsx";
    $fileContent = "This is some example content.";
    
   
    //print_r($data);
    SimpleXLSXGen::fromArray($data)
    ->setDefaultFont('Courier New')
    ->setDefaultFontSize(14)
    ->setColWidth(1, 35)
    ->mergeCells('A20:B20')
    ->saveAs($fileName);

  
}


?>