<?php

function getenroledUser($id)
{
    $enrolstudata = array();
    $studata = array('courseid' => $id);
    $enrol_data = json_encode($studata);

    $url = MOODLE_URL . '/webservice/rest/server.php?wstoken=' . TOKEN . '&wsfunction=' . METHOD_GET_ENROL . "&moodlewsrestformat=json";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($studata));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    $jsonArrayResponse = json_decode($result);
    foreach ($jsonArrayResponse as $key => $value) {
        $enrolstudata[] = array(
            'name' => $value->fullname,
            'fullname' => $value->enrolledcourses[0]->fullname,
            'cid' => $value->enrolledcourses[0]->id,
            'id' => $value->id,
            'email' => $value->email,

        );

    }
    return $enrolstudata;
}

function moodleRegisterUser($data, $group = null)
{
global $DB;

    $id =  user_create_user($data, false, false);
    $context = context_course::instance(43);
    $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
    if (!is_enrolled($context, $id)) {
        // Not already enrolled so try enrolling them.
        if (!enrol_try_internal_enrol(43, $id, $studentroleid, time())) {
            // There's a problem.
            throw new moodle_exception('unabletoenrolerrormessage', 'langsourcefile');
        }
    }

    if ($group) {

        $groupdata = moodleAddUserGroup($id, $group,$data->email);
    }

}

function moodleAddUserGroup($userid, $group,$email)
{
    global $DB;
         
		/*if($group[0]->class_start_am_pm =='AM/PM'){
			$recc = new stdClass();
			$recc->msg = $email . "This email have multiple session(AM/PM) and its not accepted in the moodle system";
			$recc->type = "gegi";
			$recc->timecreate = time();
			$recc->email = $email;
			$recc->errordata = "Other field are inserted in the moodle system";
			$recc->modulestatus = "Registration done google and moodle system";
			$DB->insert_record('error_logs', $recc);				
		}
		
		$fields = $DB->get_records_sql("SELECT * from {user_info_data} where data='".$group[0]->program_name."'");		
		if(!$fields)
		{			
			$recc = new stdClass();
			$recc->msg = $email . "This email have different program and its not accepted in the moodle system";
			$recc->type = "gegi";
			$recc->timecreate = time();
			$recc->email = $email;
			$recc->errordata = "Other field are inserted in the moodle system";
			$recc->modulestatus = $group[0]->program_name;
			$DB->insert_record('error_logs', $recc);
		}*/		 
           

$fields = $DB->get_records_sql("SELECT shortname,id from {user_info_field}");
    foreach ($fields as $key => $field) {
        /*if ($field->shortname == 'Campus') {
            $rec = new stdClass();
            $rec->userid = $userid;
            $rec->fieldid = $field->id;
            $rec->data = $group[0]->campus_name;
            $DB->insert_record('user_info_data', $rec);

        }*/
		if ($field->shortname == 'Group') {
			$rec = new stdClass();
			$rec->userid = $userid;
			$rec->fieldid = $field->id;
			$rec->data = $group[0]->name;
			$DB->insert_record('user_info_data', $rec);
		}
		/*if ($field->shortname == 'Program') {
			$rec = new stdClass();
			$rec->userid = $userid;
			$rec->fieldid = $field->id;
			$rec->data = $group[0]->program_name;
			$DB->insert_record('user_info_data', $rec);
		}
		if ($field->shortname == 'Session') {
				if($group[0]->class_start_am_pm !=='AM/PM'){
				$rec = new stdClass();
				$rec->userid = $userid;
				$rec->fieldid = $field->id;
				$rec->data = $group[0]->class_start_am_pm . ' classes';
				$DB->insert_record('user_info_data', $rec);
			}
        }*/

    }
    $response = "ok";
    return $response;

    
die();
}

// if (isset($_POST['add_type']) && $_POST['add_type'] == 'droped_reenrolled') {

//     $email = $_POST['email'];
//     $user = $DB->get_record('user', ['email' => $email]);

//     if (isset($_POST['type']) && $_POST['type'] == 'student_reenroll') {
//         $data = enroledUser($user->id);
//         if ($data == 'null') {
//             $endata = array('status' => 'ok');
//             echo json_encode($endata);
//         }
//     } else {
//         $drop_data = core_enrol_get_users_courses($id);

//         foreach ($drop_data as $key => $course) {
//             $dropdata = userDropped($userid, $course->id, $user->firstname);

//         }

//         if ($dropdata) {
//             $ddata = array('status' => 'ok');
//             echo json_encode($dropdata);
//         }

//     }

//     die();
// }

function enroledUser($id,$courseid)
{
    $course = $DB->get_record('course', ['id' => $courseid]);  
    $user = $DB->get_record('user', array('id' => $id));
    if($course){
        $context = context_course::instance(43);
        $studentroleid = $DB->get_field('role', 'id', array('shortname' => 'student'));
        if (!is_enrolled($context, $id)) {
            // Not already enrolled so try enrolling them.
            if (!enrol_try_internal_enrol(43, $id, $studentroleid, time())) {
                // There's a problem.
                throw new moodle_exception('unabletoenrolerrormessage', 'langsourcefile');
            }
        }
    }else{
        $recc = new stdClass();
        $recc->msg = $user->email . "This user course is not found in the moodle system.";
        $recc->type = "gegi";
        $recc->timecreate = time();
        $recc->email = $user->email;
        $rec->isStatus = "Not enrol in moodle course"; 
        $DB->insert_record('error_logs', $recc);
    }
    die();
}

function core_enrol_get_users_courses($id)
{
    global $DB;
    $sql = "SELECT DISTINCT c.id AS courseid,u.id AS userid,c.fullname as coursename, u.firstname
            FROM mdl_user u
            JOIN mdl_user_enrolments ue ON ue.userid = u.id
            JOIN mdl_enrol e ON e.id = ue.enrolid
            JOIN mdl_role_assignments ra ON ra.userid = u.id
            JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel = 50
            JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id
            WHERE u.suspended = 0 AND u.deleted = 0 and u.id=$id";
    
    $course = $DB->get_records_sql($sql);
    if($course){
        return $course;
    }else{
        $recc = new stdClass();
        $recc->msg = $email . "This user have not any course in moodle system.";
        $recc->type = "gegi";
        $recc->timecreate = time();
        $recc->email = $email;
        $recc->errordata = "Dropped student API";
        $recc->modulestatus = "Not dropped from moodle course";
        $DB->insert_record('error_logs', $recc);
    }    
    die();
}

function userDropped($id, $courseid, $name)
{
    require_once 'emailTemp.php';

    $userenrol = new stdClass();
    $userenrol->roleid = 5;
    $userenrol->userid = $id;
    $userenrol->courseid = $courseid;
    $userenrolData = array($userenrol);
    $enrol_data = array('enrolments' => $userenrolData);

    $url = MOODLE_URL . '/webservice/rest/server.php?wstoken=' . TOKEN . '&wsfunction=' . METHOD_UNENROL . "&moodlewsrestformat=json";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($enrol_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    if($result){
        $data = sendMail($name);
        return $data;
    }else{
        return false;
    }    
    die();
}

function checkUserEmail($email)
{

    global $DB;
    $users = $DB->get_records('user', ['email' => $email]);
    if ($users) {
        return $users;
    }

}

function sendErrorMail($recc){
    
    $output = '<table style="width:100%">
              <tr>
                <th>Email</th>
                <th>Message</th>            
                <th>Type</th>            
                <th>Date</th>
              </tr>
              <tr>
                <td>'.$recc->email.'</td>
                <td>'.$recc->msg.'</td>            
                <td>'.$recc->type.'</td>           
                <td>'.date('m-d-Y h:i',$recc->timecreate).'</td>
               </tr>
           </table>';
           
    /******Cron send mail start******/
        
        $subject = "Error Dummy Data";
        $user = new stdClass();
        $user->firstname = 'Rohit';
        $user->lastname = 'katoch';
        $user->email = 'technocodz.rahul@gmail.com';
        //$user->email     = "joydeep.php.developer@gmail.com,joyd.dj";
        $user->id = -99;
        $user->maildisplay = true;
        $user->mailformat = 1;
        $emailTemp = $output;
        $noreplyuser = core_user::get_noreply_user();
        
        if (!$mailresults = email_to_user($user, $noreplyuser, $subject, $emailTemp)) {         
            echo "could not send email!<br>";
        } else {                
            echo 'Mail sent';
        }
        die;
        /******Cron send mail end******/
}