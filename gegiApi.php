<?php
function gegi_api($data_url)
{
    $cURLConnection = curl_init();
    curl_setopt($cURLConnection, CURLOPT_URL, $data_url);
    curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, array(
        'X-Api-Access-Key: TechnocodzGroup_GSuite',
        'X-Api-Secret-Key:' . GEGI_TOKEN,
        'Content-Type: application/json',
    ));
    $apiResponse = curl_exec($cURLConnection);
    curl_close($cURLConnection);
    $jsonArrayResponse = json_decode($apiResponse);
    return $jsonArrayResponse;
}

function updateProfile($data)
{
    $ch = curl_init(GEGI_URL . 'set_email');
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'X-Api-Access-Key: TechnocodzGroup_GSuite',
        'X-Api-Secret-Key:' . GEGI_TOKEN,
        'Content-Type: application/json',
    ));

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function get_for_email_registration()
{
    $data = array();
    $studata = array();
    $get_count = GEGI_URL . 'get_for_email_registration?page=7';
    $stud = gegi_api($get_count);
    foreach ($stud->data as $key => $value) {
        $studata[] = array(
            'id' => $value->id,
            'first_name' => $value->first_name,
            'last_name' => $value->last_name,
            'email' => $value->email,
            'middle_name' => $value->middle_name,
        );
    }

    return $studata;

}







function get_reenrolled()
{
 $date = date("Y-m-d");
$date1 = date('Y-m-d', strtotime('-1 day', strtotime($date)));
 
 $data = array();
 $studata = array();
 $endata = GEGI_URL.'get_reenrolled?date='.$date1;
 $reenrolled = gegi_api($endata);

 foreach ($reenrolled->data as $key => $value) {
  $studata[] = array(
              'id'  => $value->id,
              'first_name'  => $value->first_name.' '.$value->last_name.' '.$value->last_name,
              'email'  => $value->email,
              'cell_phone'  => $value->cell_phone,
            );
 }

return $studata;


}

function get_droped()
{
 $date = date("Y-m-d");
 $date1 = date('Y-m-d', strtotime('-3 day', strtotime($date)));
 $data = array();
 $studata = array();
 $endata = GEGI_URL.'get_dropped?date='.$date1;
 $reenrolled = gegi_api($endata);

 foreach ($reenrolled->data as $key => $value) {
  $studata[] = array(
              'id'  => $value->id,
              'first_name'  => $value->first_name.' '.$value->last_name.' '.$value->last_name,
              'email'  => $value->email,
              'cell_phone'  => $value->cell_phone,
            );
 }

return $studata;


}


