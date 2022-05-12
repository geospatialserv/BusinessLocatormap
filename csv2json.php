<?php
header('Content-type: application/json');
// Set your CSV feed
$feed = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vTI9aA9qML9h8nqselVDxtxOk_YUFuKokRIIcLbxT9llojK9Phsmy9jOkTa_X5KmOhRcCoK8F-2tb7w/pub?gid=0&single=true&output=csv';
 
// Arrays we'll use later
$keys = array();
$newArray = array();
 
// Function to convert CSV into associative array
function csvToArray($file, $delimiter) { 
  if (($handle = fopen($file, 'r')) !== FALSE) { 
    $i = 0; 
    while (($lineArray = fgetcsv($handle, 4000, $delimiter, '"')) !== FALSE) { 
      for ($j = 0; $j < count($lineArray); $j++) { 
        $arr[$i][$j] = $lineArray[$j]; 
      } 
      $i++; 
    } 
    fclose($handle); 
  } 
  return $arr; 
} 
// Do it
$data = csvToArray($feed, ',');
 
// Set number of elements (minus 1 because we shift off the first row)
$count = count($data) - 1;
 
//Use first row for names  
$labels = array_shift($data);  
 
foreach ($labels as $label) {
  $keys[] = $label;
}
 
// Add Ids, just in case we want them later
$keys[] = 'id';
 
for ($i = 0; $i < $count; $i++) {
  $data[$i][] = $i;
}
 
// Bring it all together
for ($j = 0; $j < $count; $j++) {
  $d = array_combine($keys, $data[$j]);
  $newArray[$j] = $d;
}
 
// Print it out as JSON
$jsonData = json_encode($newArray);
$original_data = json_decode($jsonData, true);
$features = array();
foreach($original_data as $key => $value) {
    $features[] = array(
        'type' => 'Feature',
        'properties' => array(
							'ID' => $value['id'],
							'BusinessName' => $value['businessName'],
							'Phone' => $value['telephone'],
							'Address' => $value['address'],
							'Website' => $value['website'],
							'BusinessType' => $value['businessType'],
							'Legend' => $value['legend'],
							'ImageLink' => $value['imageLink']
							),
        'geometry' => array(
             'type' => 'Point', 
             'coordinates' => array(
                  (float)$value['longitude'], 
                  (float)$value['latitude'], 
             ),
         ),
    );
}
$new_data = array(
    'type' => 'FeatureCollection',
    'features' => $features,
);

$final_data = json_encode($new_data, JSON_PRETTY_PRINT);
print_r($final_data);
?>