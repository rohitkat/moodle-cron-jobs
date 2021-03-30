<?php
include 'function.php';
$en_data = getenroledUser(43);
$data = get_droped();
?>
<html>

<head>
    <title>Dropped student</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript" charset="utf8" src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.2.min.js"></script>
    <link href='//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css' rel='stylesheet' type='text/css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <style type="text/css">
        .jumbotron {
            padding: 1rem 2rem !important;
        }
   
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
    /* Turn off scrollbar when body element has the loading class */
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

        <br>
        <br>

        <div class="container">
            <div class="jumbotron">
                <h1>Dropped Student</h1>
            </div>
        </div>
        <div class="container">
         <!--    <form name="form1" id="renrolleddata" style="width: 100%;">
                <div class="row">
                    <div class="col-sm">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Transaction Date</label>
                            <input type='date' name="date" id='search_fromdate' class="form-control" placeholder='From date'>

                        </div>

                    </div>
                    <div class="col-sm">
                        <input type='submit' class="btn-success" id='searchbtn' value="Search" style="margin-top: 7%;float: left;">
                    </div>

                </div>
            </form> -->
        </div>
        <br><br>
        <table id='empTable' class='display dataTable'>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mob.No</th>
                    <th>Action</th>
                </tr>
            </thead>
         <tbody>
                <?php foreach($data as $value)
                {
                ?>
              <tr>
                <td><?= $value['first_name'] ?></td>
                <td><?= $value['email'] ?></td>
                <td><?= $value['cell_phone'] ?></td>
                <td><button class='btn-success'  onclick='myfunction("<?=$value["email"]?>")'>dropped</button></td>
              </tr>
              <?php } ?>
            </tbody>
        </table>
    </div>

<div class="container mt-3">

  <br>
  <br>
  <h2>Enrolled Student Course</h2>
  
  <table id='empTable1' class='display dataTable'>
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Enrolled Course Name</th>
  
      </tr>
    </thead>
    <tbody>
        <?php foreach($en_data as $value)
        {
        ?>
      <tr>
        <td><?= $value['name'] ?></td>
        <td><?= $value['email'] ?></td>
        <td><?= $value['fullname'] ?></td>
       <!--  <td><a  onclick="suspendfunction(this)" data-record="<?= $value['email'] ?>" data-id="<?= $value['id'] ?>" class="btn btn-danger btn-sm">suspend</a></td>
        <td><a  onclick="unrolledfunction(this)" data-record="<?= $value['email'] ?>" data-id="<?= $value['id'] ?>" class="btn btn-danger btn-sm">unenrolled</a></td> -->
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
    <!-- Script -->

</body>

</html>




<script type="text/javascript">
    var datatable = $("#empTable").dataTable();
    var datatable1 = $("#empTable1").dataTable();
 
    $("#renrolleddata").submit(function(e) {
        $('#empTable').dataTable().fnClearTable();

        var data = new FormData(this);
        data.append("add_type", 'search_student_droped');
        e.preventDefault();
         $("body").addClass("loading"); 
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                $("body").removeClass("loading"); 
                var myArr = JSON.parse(this.responseText);
                renderresult(myArr);

            }
        };
        xmlhttp.open('POST', 'get.php', true);
        xmlhttp.send(data);



    });




    function renderresult(arr) {
        $('#empTable').DataTable().destroy();
        var tr_str = "<tr>";
        arr.forEach(function(entry, value) {
             var action = "<button class='btn-success' onclick='myfunction(" + JSON.stringify(entry['email']) + ")'>dropped</button>";
            tr_str += "<td>" + entry['first_name'] + "</td>" +
                "<td>" + entry['email'] + "</td>" +
                "<td>" + entry['cell_phone'] + "</td>" +
                "<td>" + action + "</td>" +
                "</tr>";
        });

        $("#empTable tbody").append(tr_str);
        datatable = $('#empTable').DataTable();



    }

function myfunction(email)
{
        var data = new FormData();
        data.append("add_type", 'droped_reenrolled');
        data.append("email", email);
       
         $("body").addClass("loading"); 
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                
                $("body").removeClass("loading"); 
                var myArr = JSON.parse(this.responseText);
                // if(myArr.status)
                // {

                  alert("User suspend");
                  location.reload();
                 
                // }

            }
        };
        xmlhttp.open('POST', 'function.php', true);
        xmlhttp.send(data);
}
</script>