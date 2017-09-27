<?php
/**
 * Created by PhpStorm.
 * User: humingtang
 * Date: 9/22/17
 * Time: 3:47 PM
 */

namespace AppBundle\Service\database;


class MySQLPatientConnector
{
    var $username;
    var $password;
    var $host;
    var $port;
    var $dbname;
    var $pdo;


    function init()
    {
        try {
            $this->pdo = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            echo "New record created successfully";
        }
        catch(PDOException $e)
        {
            echo $e->getMessage();
        }

    }

    function checkPatientExists($firstname, $lastname, $email)
    {
        if ($this->pdo == null)
        {
            $this->init();
        }
        $sql = "SELECT FROM PATIENT id
                    WHERE first_name=:FIRSTNAME, last_name=:LASTNAME, email=:EMAIL LIMIT 1";
        $query = $this->pdo->prepare($sql);
        // use exec() because no results are returned
        $query->execute(array(
            'FIRSTNAME'=>$firstname,
            'LASTNAME'=>$lastname,
            'EMAIL'=>$email));
        $count = $query->rowCount();
        if ($count>0)
        {
            $row = $query->fetch();
            return $row['id'];
        }
        return false;
    }

    /**
     *
     * Insert Patient, however if patient exists, return existing user id
     *
     * @param $firstname
     * @param $lastname
     * @param $email
     * @return String
     */
    function insertPatient($firstname, $lastname, $email)
    {
        if ($this->pdo == null)
        {
            $this->init();
        }

        $patientID = $this->checkPatientExists($firstname, $lastname, $email);

        if ($patientID !==false)
            return $patientID;

        $sql = "INSERT INTO PATIENT (firstname, lastname, email)
                    VALUES (:FIRSTNAME, :LASTNAME, :EMAIL)";
        $query = $this->pdo->prepare($sql);
        // use exec() because no results are returned
        $query->execute(array(
            'FIRSTNAME'=>$firstname,
            'LASTNAME'=>$lastname,
            'EMAIL'=>$email));

        return $this->pdo->lastInsertId("id");

    }

    /**
     *
     * Always overwrite data for patient
     *
     * @param $patientID
     * @param $patientData
     */
    function insertData($patientID, $patientData)
    {
        //delete existing patient data
        $sql = "DELETE FROM PATIENT_DATA WHERE patient_id=
                    :PATIENTID)";
        $query = $this->pdo->prepare($sql);
        // use exec() because no results are returned
        $query->execute(array(
            'PATIENTID'=>$patientID,
        ));
        $query->execute();

        //insert
        $sql = "INSERT INTO PATIENT_DATA (patient_id, data)
                    VALUES (:data, :patientID)";
        $query = $this->pdo->prepare($sql);
        // use exec() because no results are returned
        $query->execute(array(
            'data'=>$patientData,
            'patientID'=>$patientID
        ));
        
    }
}