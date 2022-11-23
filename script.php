<?php

$host = DBHOST;
$user = DBUSER;
$password = DBPWD;
$db = DBNAME;


$conn = new mysqli($host,$user,$password,$db);

// Check connection
if ($conn -> connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}

$sql1 = "select pn, 
                last, 
                first, 
                iname, 
                DATE_FORMAT(from_date, '%m/%d/%y') as 'from', 
                DATE_FORMAT(to_date, '%m/%d/%y') as 'to' 
          from patient left join insurance on patient._id = insurance.patient_id 
          order by 'from', last desc";


$result = $conn->query($sql1);

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo $row["pn"]. " " . $row["last"]. " " . $row["first"] . " " . '"'.$row["iname"].'"' . " " . $row["from"] . " " . $row["to"] . PHP_EOL."\n";
  }
} else {
  echo "0 results";
}
echo "\n";
echo "\n";
echo 'statistika'."\n";
echo "\n";



$sql3 = "select lower(replace(substring(group_concat(first), 1, char_length(group_concat(first))), ',', '')) as string FROM patient";

$result = $conn->query($sql3);
if ($result->num_rows > 0) {

  while($row = $result->fetch_assoc()) {
    $uniqueCharacters = count_chars($row["string"]."\n",3);
    echo "String: " . $row["string"]. PHP_EOL;


    for ($i = 1; $i <= strlen($uniqueCharacters); $i++) {
      if (strlen(substr($uniqueCharacters, $i, 1)) > 0) {
        echo substr($uniqueCharacters, $i, 1) . "\t";
        echo $letterCount = substr_count($row["string"], substr($uniqueCharacters, $i, 1)) . "\t";
        echo round(($letterCount*100/strlen($row["string"])), 2)."%" . PHP_EOL;
        
      }  
    }
  }
} else {
  echo "0 results";
}




$conn->close();
?>