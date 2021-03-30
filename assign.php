<?php
require_once './config.php';
require_once $CFG->dirroot. '/apply/functions.php';
require_once $CFG->libdir . '/adminlib.php';
require_once $CFG->libdir . '/authlib.php';
require_once $CFG->dirroot . '/user/filters/lib.php';
require_once $CFG->dirroot . '/user/lib.php';

global $DB;

$stduedeny_array = array();

$sql = 'SELECT u.id as uid, c.id as cid,ma.id as maid,c.fullname,ma.name
FROM mdl_user u
INNER JOIN mdl_role_assignments ra ON ra.userid = u.id
INNER JOIN mdl_context ct ON ct.id = ra.contextid
INNER JOIN mdl_course c ON c.id = ct.instanceid
INNER JOIN mdl_assign ma ON c.id = ma.course
INNER JOIN mdl_role r ON r.id = ra.roleid
INNER JOIN mdl_course_categories cc ON cc.id = c.category
WHERE r.id =5 AND c.visible=1';
// $courses = $DB->get_records_sql("SELECT id from {course} WHERE visible=1");
$courses = $DB->get_records_sql($sql);


   // echo "<pre>";
   
   // print_r($courses);

   // die();
foreach ($courses as $key => $course) {
$sql = "SELECT u.firstname,u.lastname,u.email, u.id as userid, s.status as status, s.id as submissionid,s.attemptnumber as attemptnumber, uf.mailed as mailed, uf.locked as locked, uf.extensionduedate as extensionduedate, uf.workflowstate as workflowstate, uf.allocatedmarker as allocatedmarker , um.id as recordid ,s.assignment as assignmentid
                FROM mdl_user u
                         LEFT JOIN mdl_assign_submission s
                                ON u.id = s.userid
                               AND s.assignment = '$course->maid'
                               AND s.latest = 1
                         LEFT JOIN mdl_assign_grades g
                                ON u.id = g.userid
                               AND g.assignment = '$course->maid' LEFT JOIN (SELECT mxg.userid, MAX(mxg.attemptnumber) AS maxattempt
                                  FROM mdl_assign_grades mxg
                                 WHERE mxg.assignment = '$course->maid'
                              GROUP BY mxg.userid) gmx
                             ON u.id = gmx.userid
                            AND g.attemptnumber = gmx.maxattempt LEFT JOIN mdl_assign_user_flags uf
                         ON u.id = uf.userid
                        AND uf.assignment = '$course->maid' LEFT JOIN mdl_assign_user_mapping um
                             ON u.id = um.userid
                            AND um.assignment = '$course->maid'
                           WHERE u.id = '$course->uid'
                ORDER BY userid ASC";	
   $data = $DB->get_records_sql($sql);
if($data){
	foreach ($data as $key => $value) {
		$value->fullname = $course->fullname;
		$value->coursename = $course->name;
		array_push($stduedeny_array,$value);
	}
	

   
}

}
echo "<pre>";
   
   print_r($stduedeny_array);



?>