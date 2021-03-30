<?php
include 'function.php';
$data = get_for_email_registration();
$en_data = getenroledUser(43);
// die();
?>
<html>

<head>
    <title>Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css"
        href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.2.min.js">
    </script>
    <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<style type="text/css">
      .overlay{
        display: none;
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 999;
        background: rgba(255,255,255,0.8) url("LoaderIcon.gif") center no-repeat;
    }
    body{
        text-align: center;
    }
     body.loading{
        overflow: hidden;   
    }
    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay{
        display: block;
    }
</style>

</head>

<body>
    <div class="overlay"></div>
    <div class="container mt-3">
<a  href="cron.php" target="_blank" class="btn btn-success btn-sm">Registration Cron</a>
<a  href="enrol_cron.php" target="_blank" class="btn btn-success btn-sm">Re-enrolled Cron</a>
<a  href="unenrol_cron.php" target="_blank" class="btn btn-success btn-sm">Dropped Cron</a>

        <br>
        <br>
        <h2>Student Registration</h2>

        <table id='empTable' class='display dataTable'>
            <thead>
                <tr>
                    <th>Firstname</th>
                    <th>MiddleName</th>
                    <th>LastName</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $value) {
    ?>
                <tr>
                    <td><?=$value['first_name']?></td>
                    <td><?=$value['middle_name']?></td>
                    <td><?=$value['last_name']?></td>
                    <td><?=$value['email']?></td>
                    <td><a onclick="myfunction(this)" data-record="<?=$value['email']?>" data-id="<?=$value['id']?>"
                            class="btn btn-success btn-sm">registered</a></td>
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>
    <div class="container mt-3">

        <br>
        <br>
        <h2>Registered Student</h2>

        <table id='empTable1' class='display dataTable'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrolled Course Name</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($en_data as $value) {
    ?>
                <tr>
                    <td><?=$value['name']?></td>
                    <td><?=$value['email']?></td>
                    <td><?=$value['fullname']?></td>
                    <!--  <td><a  onclick="suspendfunction(this)" data-record="<?=$value['email']?>" data-id="<?=$value['id']?>" class="btn btn-danger btn-sm">suspend</a></td>
        <td><a  onclick="unrolledfunction(this)" data-record="<?=$value['email']?>" data-id="<?=$value['id']?>" class="btn btn-danger btn-sm">unenrolled</a></td> -->
                </tr>
                <?php }?>
            </tbody>
        </table>
    </div>

    <!-- Script -->

</body>

</html>
<script>
$(document).ready(function() {
    $('#empTable').DataTable();
    $('#empTable1').DataTable();
});
</script>



<script type="text/javascript">
function myfunction(data) {
    var id = $(data).attr('data-id');
    $(data).css("opacity", ".5");
    var email = $(data).attr('data-record');
     
    $.ajax({
        type: 'POST',
        url: 'function.php',
        data: {
            id: id,
            email: email
        },
        beforeSend: function() {
            // $('.submitBtn').attr("disabled","disabled");
            $("body").addClass("loading");
            // $('#fupForm').css("opacity", ".5");
        },
        success: function(response) { //console.log(response);
            $("body").removeClass("loading"); 
            if(JSON.parse(response).error=='nodata'){
                alert(JSON.parse(response).message);
            }
            else if(JSON.parse(response).error=='exit'){
                alert(JSON.parse(response).message);
            }else{
                location.reload();
            }
            
            
        }
    });




}



function suspendfunction(data) {
    var id = $(data).attr('data-id');
    $(data).css("opacity", ".5");

    $.ajax({
        type: 'POST',
        url: 'get.php',
        data: {
            id: id,
            email: email
        },
        beforeSend: function() {
            // $('.submitBtn').attr("disabled","disabled");
            $('#fupForm').css("opacity", ".5");
        },
        success: function(response) { //console.log(response);
            // location.reload();
        }
    });
}

function unrolledfunction(data) {
    var id = $(data).attr('data-id');
    $(data).css("opacity", ".5");

    $.ajax({
        type: 'POST',
        url: 'get.php',
        data: {
            id: id,
            email: email
        },
        beforeSend: function() {
            // $('.submitBtn').attr("disabled","disabled");
            $('#fupForm').css("opacity", ".5");
        },
        success: function(response) { //console.log(response);
            // location.reload();
        }
    });
}
</script>