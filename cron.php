
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';
require_once 'gegiApi.php';
require_once 'moodleApi2.php';
require_once 'googlecron.php';
global $DB;
$studata = array();
$get_count = GEGI_URL . 'get_for_email_registration?page=8';
$stud = gegi_api($get_count);
foreach ($stud->data as $key => $value) {
    $studata[] = array(
        'email' => $value->email,

    );
}

for ($i = 10; $i < 20; $i++) {
    $error = true;
    $data_url = GEGI_URL . 'get_by_email?email=' . $studata[$i]['email'];

    $jsonArrayResponse = gegi_api($data_url);
	if(!empty($jsonArrayResponse->data->email))
		$uEmail=$jsonArrayResponse->data->email;
	else
		$uEmail=$studentEmailArr[$i];	

    if (isset($jsonArrayResponse->errors[0]->code) && $jsonArrayResponse->errors[0]->code = 'RequestedSourceNotFound') {
        $myObj->error = "nodata";			
		$myObj->message = "The student with the email address " . $uEmail . "is not found in Gegi system";		
        $rec = new stdClass();
		$rec->msg = "The student with the email address " . $uEmail . "is not found in Gegi system";		
        $rec->type = "gegi";
        $rec->timecreate = time();
        $rec->email = $uEmail;
        $rec->isStatus = "Not registered in google and moodle system";
        $DB->insert_record('error_logs', $rec);
        $error = false;
        // echo json_encode($myObj);
        // die();
    }

    if (count($jsonArrayResponse->data->active_groups) == 0) {

            $rec = new stdClass();
            $rec->type = "gegi";
            $rec->msg = "The student with the email address " . $uEmail . " has no group and compus";
            $rec->modulestatus = "not registered in google and moodle system";
            $rec->email = $uEmail;
            $rec->timecreate = time();
            // print_r($rec);
            $DB->insert_record('error_logs', $rec);
       $error = false;

    }

     if (count($jsonArrayResponse->data->active_groups) > 1) {
       
       foreach ($jsonArrayResponse->data->active_groups as $key => $value) {
           $campus_name = $value->campus_name;
           $groupName = $value->name;
           
            $rec = new stdClass();
            $rec->msg = "The student with the email address " . $uEmail . " has multiple group and compus";
            $rec->type = "gegi";
            $rec->timecreate = time();
            $rec->email = $uEmail;
            $rec->errordata = $groupName.' '.$campus_name;
            $rec->isStatus = "Not registered in google and moodle system";
            $DB->insert_record('error_logs', $rec);
       }
       $error = false;
        
    }

    $first_name = $jsonArrayResponse->data->first_name;
    $last_name = $jsonArrayResponse->data->last_name;
    $middle_name = $jsonArrayResponse->data->middle_name;
    $firstletter = substr($first_name, 0, 1);
    $idnumber = mt_rand(100000, 999999);
    $email_string = strtolower($firstletter . $last_name . $idnumber);
    $newemail = preg_replace('/\s+/', '', $email_string) . '@gurnick.edu';
    $get_email = GEGI_URL . 'get_by_email?email=' . $newemail;

    $check_email = gegi_api($get_email);
    if (!isset($check_email->errors)) {
        $threeRandomDigit = mt_rand(100, 999);
        $newemail = preg_replace('/\s+/', '', $email_string) . $threeRandomDigit . '@gurnick.edu';
    }

    

    $checkEmail = checkUserEmail($newemail);
     if ($checkEmail) {
            $rec = new stdClass();
            $rec->msg = "The student with the email address" . $newemail . "is already in moodle system";
            $rec->type = "moodle";
            $rec->timecreate = time();
            $rec->email = $newemail;
            $rec->isStatus = "Not registered in google and moodle system";
            $DB->insert_record('error_logs', $rec);
            $error = false;
            
        }
       
    if (!$checkEmail && $error==true) {
     
        $jsonArrayResponse->data->email = $newemail;
        $jsonArrayResponse->data->idnumber = $idnumber;
        // $jsonArrayResponse->data->oldemail = $jsonArrayResponse->data->email;
        $resgister = google_Registration($jsonArrayResponse->data);
        // if ($resgister) {
        //     echo "ok";
        // }

    }
   
if($i == 19){
    $errorDataa = $DB->get_records_sql("SELECT * FROM mdl_error_logs");
       if($errorDataa){
        $output = '<table style="width:100%">
          <tr>
            <th>Email</th>
            <th>Message</th>
            <th>Data</th>
            <th>Type</th>
            <th>Status</th>
            <th>Date</th>
          </tr>';
          foreach ($errorDataa as $key => $value) {
            $output.='<tr>
            <td>'.$value->email.'</td>
            <td>'.$value->msg.'</td>
            <td>'.$value->errordata.'</td>
            <td>'.$value->type.'</td>
            <td>'.$value->modulestatus.'</td>
            <td>'.date('m-d-Y h:i',$value->timecreate).'</td></tr>';
          }
          
       $output .= '</table>';
       echo $output;
	   /******Cron send mail start******/
		$subject = "Dummy Data";
		$user->firstname = 'Rohit';
		$user->lastname = 'katoch';
		$user->email = 'katoch.rohit@gmail.com';
		//$user->email     = "joydeep.php.developer@gmail.com,joyd.dj";
		$user->id = -99;
		$user->maildisplay = true;
		$user->mailformat = 1;
		$emailTemp = $output;
		$noreplyuser = core_user::get_noreply_user();
		if (!$mailresults = email_to_user($user, $noreplyuser, $subject, $emailTemp)) {                
			echo "could not send email!<br>";
		} else {
			$DB->delete_records('error_logs',null);
			echo 'Mail sent';
		}
		/******Cron send mail end******/
	   
    }else{
     echo "no error found";
    }
}
  
}


        
die();


