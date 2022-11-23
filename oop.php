<?php

include('config.php');

//Add database info in constructor

function getData($patientNumber) {
    return "select p._id, 
                   last, 
                   first, 
                   dob, 
                   group_concat(iname SEPARATOR ', ') as insurances 
            from patient p left join insurance i on p._id = i.patient_id 
            where pn = ".$patientNumber;
}

function getInsuranceData($id) {
    return "select * from insurance where _id = ".$id;
}

function getPatientInsuranceData($insuranceName) {
    return "select * from insurance where iname = ".$insuranceName;
}


interface PatientRecord {
    
    public function getPatientId();
    public function getPatientNr(); 
}

class Patient implements PatientRecord {
    protected $id;
    protected $patientNumber;
    protected $firstName;
    protected $lastName;
    protected $dateOfBirth;
    protected $insuranceRecords;

    public function __construct($patientNumber) {
        $host = DBHOST;
        $user = DBUSER;
        $password = DBPWD;
        $db = DBNAME;


        $conn = new mysqli($host,$user,$password,$db);
        $result = $conn->query(getData($patientNumber));
        $row = $result->fetch_assoc();
        
        $this->id = $row["_id"];
        $this->firstName = $row["first"];
        $this->lastName = $row["last"];
        $this->dateOfBirth = $row["dob"];
        $this->insuranceRecords = $row["insurances"];
        $this->patientNumber = $patientNumber;

        $conn->close();
    }

    public function getPatientId() {
        return $this->id;
    }
    
    public function getPatientNr() {
        return $this->patientNumber;
    }

    public function getPatientName() {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getInsuranceRecords() {
        return $this->insuranceRecords;
    }

    public function returnTable() {
        echo $this->getPatientNr() . ', ' .
             $this->getPatientName() . ', ' .
             $this->getInsuranceRecords(); 
    }

    
}


class Insurance implements PatientRecord {
    protected $id;
    protected $patientId;
    protected $insuranceName;
    protected $fromDate;
    protected $toDate;


    public function __construct($id) {
        $host = DBHOST;
        $user = DBUSER;
        $password = DBPWD;
        $db = DBNAME;


        $conn = new mysqli($host,$user,$password,$db);
        $result = $conn->query(getInsuranceData($id));
        $row = $result->fetch_assoc();

        $this->id = $row["_id"];
        $this->patientId = $row["patient_id"];
        $this->insuranceName = $row["iname"];
        $this->fromDate = $row["from_date"];
        $this->toDate = $row["to_date"];
    }

    public function getPatientId() {
        return $this->patientId;
    }
    
    public function getPatientNr() {
        return $this->patientNumber;
    }

    public function isValid($date) {
        $startDate = strtotime(date('m-d-Y', strtotime($this->fromDate)));
        $endDate = strtotime(date('m-d-Y', strtotime($this->toDate)));
        $curDate = strtotime(date("m-d-Y", strtotime($date)));

        if ((($endDate >= $curDate) OR (strlen($endDate) == 0)) AND ($startDate <= $curDate)) {
            echo 'Yes';  
        } else {
            echo 'No';  
        }   
    }
}
$patient = new Patient(1);

echo $patient->returnTable();

$insurance = new Insurance(1);
?>