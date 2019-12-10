<?php
$filename = "data.db";
$db = new SQLite3($filename);

$tablesquery = $db->query("SELECT * FROM 'THEATERS';");

while ($table = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
    echo("<tr>");
    foreach ($table as $value) {
         if(empty($value))
        {
            echo("<td tabindex='1'><span title=N/A>N/A</span></td>");
        } else {
            echo("<td tabindex='1'><span title=$value>".$value."</span></td>");
        } 
    }
    echo("</tr>");
} 


$results= $db->query("SELECT * FROM 'THEATERS';");

//Create array to keep all results
$data= array();

// Fetch Associated Array (1 for SQLITE3_ASSOC)
while ($res= $results->fetchArray(1))
{
    array_push($data, $res);
}

$jdata = json_encode($data);

$mapPoints = array();
foreach (json_decode($jdata, true) as $d){
    $x = str_replace("POINT ", "", $d['field1']);
    array_push($mapPoints, $x);
}

echo($mapPoints[1])

?>