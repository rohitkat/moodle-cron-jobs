<?php
require_once 'vendor/autoload.php';

function google_Registration($data)
{

    $client = new Google\Client();
    $client->setApplicationName('createuser');
    $client->setAuthConfig('userregistration-304416-52cd8db73f53.json');
    $client->setScopes([SCOPE]);
    $client->setSubject(SUBJECT);
    $client->setAccessType('offline');
    $service = new \Google_Service_Directory($client);

    $givenName = $data->first_name . ' ' . $data->middle_name;
    $familyName =$data->last_name;
    
    $password = 'Qwerty@123';
    $imgurl = $data->picture;

    $type = end(explode("/", image_type_to_mime_type(exif_imagetype($imgurl))));

    $user = new Google_Service_Directory_User();
    $name = new Google_Service_Directory_UserName();
    $photo = new Google_Service_Directory_UserPhoto();
    $unit = new Google_Service_Directory_OrgUnit();

    if (count($data->active_groups) > 0) {

        $campus_name = $data->active_groups[0]->campus_name;
        $groupName = $data->active_groups[0]->name;

        $newuserorg[0] = new Google_Service_Directory_UserOrganization();
        $newuserorg[0]->setTitle('Student');
        $newuserorg[0]->setDepartment($groupName);
        
        $newuserExternalids[0] = new Google_Service_Directory_UserExternalId();
        $newuserExternalids[0]->setType('organization');
        $newuserExternalids[0]->setValue('S' . $data->idnumber);

        $checkOU = createOU($campus_name);
        if ($checkOU == false) {
            $checkOU = createOU($campus_name, $groupName);
        }
        if ($checkOU == true) {
            $checkOU = createOU($campus_name, $groupName);
        }

        $user->setorganizations($newuserorg);
        $user->setExternalIds($newuserExternalids);
        $user->setOrgUnitPath('/GURNICK STUDENTS/' . $campus_name . ' Campus/' . $groupName);

    }

    $name->setFamilyName($familyName);
    $name->setGivenName($givenName);
    $user->setName($name);
    $user->setPrimaryEmail($data->email);
    $user->setPassword($password);

    try {

        $result = $service->users->insert($user);
        if ($imgurl) {
            $photo->setHeight(0);
            $photo->setWidth(0);
            $photo->setMimeType($type);
            $photo->setPhotoData(base64_encode(file_get_contents($imgurl)));
            $photo->setKind("admin#directory#user#photo");
            $userKey = $result->primaryEmail;
            $service->users_photos->update($userKey, $photo);
        }

       
        $user_data = new stdClass();
        $user_data->username = $data->email;
        $user_data->password = hash_internal_user_password($password,true);
        $user_data->firstname = $givenName;
        $user_data->lastname = $familyName;
        $user_data->email = $data->email;
        $user_data->maildisplay = 2;
        $user_data->confirmed  = 1;
        $user_data->timecreated = time();
        $user_data->auth = 'manual';
        $user_data->idnumber = $data->idnumber;
        $user_data->mnethostid = 1;
     

        if (count($data->active_groups) > 0) {
            $group = $data->active_groups;
            $return_data = moodleRegisterUser($user_data, $group);
        } else {
            $return_data = moodleRegisterUser($user_data);
        }

        $postRequest = array();
        $postRequest['id'] = $data->id;
        $postRequest['email'] = $result->primaryEmail;
        $postdata = json_encode($postRequest);
        $returndata = updateProfile($postdata);
        return $returndata;
    } catch (exception $e) {
        $err = $e->getMessage();
        if (json_decode($err)->error->message == 'Entity already exists.') {
            $myObj->error = "exit";
            $myObj->message = $data->email . " This email already exists in google system";
            echo json_encode($myObj);
        }
        // print_r($e->getMessage());
    }

    die();

}

function createOU($campus_name, $groupName = null)
{

    $client = new Google\Client();
    $client->setApplicationName('createuser');
    $client->setAuthConfig('userregistration-304416-52cd8db73f53.json');
    $client->setScopes([SCOPE]);
    $client->setSubject(SUBJECT);
    $client->setAccessType('offline');
    $service = new \Google_Service_Directory($client);
    $unit = new Google_Service_Directory_OrgUnit();

    if ($campus_name && $groupName) {
        $unit->setname($groupName);
        $unit->setParentOrgUnitPath('/GURNICK STUDENTS/' . $campus_name . ' Campus');
        $unit->setBlockInheritance(false);
        $unit->setkind('directory#orgUnit');
        $customerId = CUSTOMERID;
    }
    if ($campus_name && !$groupName) {
        $unit->setname($campus_name);
        $unit->setParentOrgUnitPath('/GURNICK STUDENTS');
        $unit->setBlockInheritance(false);
        $unit->setkind('directory#orgUnit');
        $customerId = CUSTOMERID;
    }

    try {
        $result = $service->orgunits->insert($customerId, $unit);
        if ($result) {
            return true;
        }
    } catch (Exception $e) {
        $err = $e->getMessage();
        if (json_decode($err)->error->message == 'Invalid Ou Id') {
            return false;
        }

    }

}
