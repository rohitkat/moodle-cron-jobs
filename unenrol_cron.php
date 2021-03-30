
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
require_once 'config.php';
require_once 'gegiApi.php';
require_once 'moodleApi2.php';

global $DB;

$date = date("Y-m-d");
$date1 = date('Y-m-d', strtotime('-9 day', strtotime($date)));
$get_count = GEGI_URL . 'get_dropped?date=' . $date1;

$droped = gegi_api($get_count);

if ($droped->meta) {
    $i = 1;
    foreach ($droped->data as $key => $value) {
        $user = $DB->get_record('user', array('email' => $value->email));
        if ($user) {

            $drop_data = core_enrol_get_users_courses($user->id);
            foreach ($drop_data as $key => $course) {
                $dropdata = userDropped($userid, $course->id, $user->firstname);

            }

            if ($dropdata) {
                $ddata = array('status' => 'ok');
                echo json_encode($dropdata);
            }
        } else {

            $recc = new stdClass();
            $recc->msg = $value->email . "This user is not found in moodle system.";
            $recc->type = "gegi";
            $recc->timecreate = time();
            $recc->email = $value->email;
            $recc->errordata = "Dropped student API";
            $recc->modulestatus = "Not dropped from moodle course";
            $DB->insert_record('error_logs', $recc);
        }

        if ($i == count($droped->data)) {
            $errorDataa = $DB->get_records_sql("SELECT * FROM mdl_error_logs");
            if ($errorDataa) {
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
                    $output .= '<tr>
            <td>' . $value->email . '</td>
            <td>' . $value->msg . '</td>
            <td>' . $value->errordata . '</td>
            <td>' . $value->type . '</td>
            <td>' . $value->modulestatus . '</td>
            <td>' . date('m-d-Y h:i', $value->timecreate) . '</td></tr>';
                }

                $output .= '</table>';
                echo $output;
                /******Cron send mail start******/
                $subject = "Dummy Data";
                $user->firstname = 'Rohit';
                $user->lastname = 'katoch';
                $user->email = 'katoch.rohit@gmail.com';

                $user->id = -99;
                $user->maildisplay = true;
                $user->mailformat = 1;
                $emailTemp = $output;
                $noreplyuser = core_user::get_noreply_user();
                if (!$mailresults = email_to_user($user, $noreplyuser, $subject, $emailTemp)) {
                    echo "could not send email!<br>";
                } else {
                    $DB->delete_records('error_logs', null);
                    echo 'Mail sent';
                }
                /******Cron send mail end******/

            } else {
                echo "no error found";
            }
        }
        $i++;
    }
} else {
    $recc = new stdClass();
    $recc->msg = "GEGI Api is not working.";
    $recc->type = "gegi (Dropped student API)";
    $recc->timecreate = time();
    $recc->email = $email;
    sendErrorMail($recc);
}
?>