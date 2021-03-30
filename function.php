<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'gegiApi.php';
require_once 'moodlApi.php';
require_once 'google_registration.php';

if (!empty($_POST["email"])) {
    // $userEmail = "exxzerpaerlix@xxxxx.xxx";
    $userEmail = $_POST["email"];
    $data_url = GEGI_URL . 'get_by_email?email=' . $userEmail;
    $jsonArrayResponse = gegi_api($data_url);

    if (isset($jsonArrayResponse->errors[0]->code) && $jsonArrayResponse->errors[0]->code = 'RequestedSourceNotFound') {
        $myObj->error = "nodata";
        $myObj->message = "The student with the email address" . $_POST["email"] . "is not found in Gegi system";

            $rec = new stdClass();
            $rec->msg = "The student with the email address" . $_POST["email"] . "is not found in Gegi system";
            $rec->type = "gegi";
            $rec->timecreate = time();
            $rec->email = $_POST["email"];
            $DB->insert_record('trouble', $rec);

        echo json_encode($myObj);
        die();
    }
    $idnumber = mt_rand(100000, 999999);
    $first_name = $jsonArrayResponse->data->first_name;
    $last_name = $jsonArrayResponse->data->last_name;
    $middle_name = $jsonArrayResponse->data->middle_name;
    $firstletter = substr($first_name,0,1);
    $email_string = strtolower($firstletter.$last_name.$idnumber);
    
    $email = preg_replace('/\s+/', '', $email_string) . '@gurnick.edu';
    $get_email = GEGI_URL . 'get_by_email?email=' . $email;

    $check_email = gegi_api($get_email);
    if (!isset($check_email->errors)) {
        $threeRandomDigit = mt_rand(100, 999);
        $email = preg_replace('/\s+/', '', $email_string) . $threeRandomDigit . '@gurnick.edu';
    }

    $jsonArrayResponse->data->email = $email;
    $jsonArrayResponse->data->idnumber = $idnumber;
    

    $checkEmail = checkUserEmail($email);

    if (!$checkEmail) {
        $resgister = google_Registration($jsonArrayResponse->data);
        if ($resgister) {
            echo "ok";
        }

    }
    if($checkEmail){
        $rec = new stdClass();
            $rec->msg = "The student with the email address" . $email . "is already in moodle system";
            $rec->type = "moodle";
            $rec->timecreate = time();
            $rec->email = $email;
            $DB->insert_record('trouble', $rec);
            die();
    }


    die();
}
