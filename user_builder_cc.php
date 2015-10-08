<?

$user_id_protege = $_SESSION['user']['user_id'];

$user_delete = 'http://modernguild.com/sites/all/modules/guild/mg/user_delete.php';
$user_update = 'http://modernguild.com/sites/all/modules/guild/mg/user_update.php';
$user_create = 'http://modernguild.com/sites/all/modules/guild/mg/user_create.php';
$user_read = "http://modernguild.com/sites/all/modules/guild/mg/user_read_cc.php?user_id=".$user_id_protege;

//echo $user_read;

$jsonString = file_get_contents($user_read);
    
if ($jsonString === FALSE) {
    echo 'Cannot read file';
} 
else {
    $jsonObject = json_decode($jsonString, TRUE);
}

$jArray = array();
$n = 0;

$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator($jsonObject),
    RecursiveIteratorIterator::SELF_FIRST);

foreach ($jsonIterator as $key => $val) {
    if (is_array($val)) {
        foreach ($val as $field => $value) {
            if (!is_array($value))
                $jArray[$n][$field] = ($value);
        }
        $n++;
    }  //
}
    
$userArray = $jArray;

//print_r($userArray);

foreach ($userArray as $num => $user) {
    
    
    

    if($num > 50) echo '<div></div>';
    
    $id = $user['user_id'];
        $my_course_read = $base_url . 'sites/all/modules/guild/mg/usercourse_read.php?user_id=' . $id;
$my_course_id = 0;
$my_course_id = file_get_contents($my_course_read);

$my_course_read_name = $base_url . 'sites/all/modules/guild/mg/usercoursename_read.php?user_id=' . $id;
$my_course_name = 0;
$my_course_name = file_get_contents($my_course_read_name);
    
    //if not logged in as admin
   
        //$disabledButton = 'disabled';
    
        if (empty($user['profile_picture'])) {
        $thumbnail2 = 'https://modernguild.com/sites/default/files/styles/avatar_main/public/default_images/profile_default-a.png';
    }
    else
        $thumbnail2 = "http://modernguild.com/sites/all/modules/guild/mg/jquery-datatable/files/images/" . $user['profile_picture'];

    if($id == 1) {
        $deleteButton = '';
        $loginAsButton = '';
       
    }
    else {
    /*    $deleteButton = " <a href='javascript:void(0)'><input type='button' class='btn btn-danger' value='Delete User' onclick='deleteUser(".$id.")' ".$disabledButton." /></a>";*/
        $loginAsButton = "<a href='login_as.php?user_id=".$id."' target='_blank'><input type='button' class='btn btn-primary' value='Go To Protege' ".$disabledButton." /></a>";
    }
    
   // $pictureButton = "<input type='button' class='btn btn-info' value='Picture' onclick='updatePictureDialog(".$id.")' />";
//    $editButton = "<input type='button' class='btn btn-info' value='Profile' onclick='updateUserDialog(".$id.");' />";
//    $scheduleButton = "<input type='button' class='btn btn-warning' value='Edit Schedule' onclick='updateScheduleDialog(".$id.")' />";
    
    if(!$user['user_type_value']) {
        $user['user_type_value'] = 'N/A';
    }
        $userData2[$num] = array(
        'DT_RowId' => $id,
        '0' => " ",
        '1' => $user['user_id'],
        '2' => $user['user_name'],
        '3' => $user['user_type_value'],
        '4' => $user['full_name'].' ', 
        '5' => $user['email'], 
        '6' => "<span title='user_id: ".$id." | user_name: ".$user['user_name']." | user_type: ".$user['user_type']."'> $editButton <br />".$pictureButton." <br /><br /></span>",
        '7' => $loginAsButton."<br />".$scheduleButton,
        '8' => $user['course_id']
    );
    
    
    $userData[$num] = array(
        'DT_RowId' => $id,
        '0' => " ",
        '1' => "<img src='" . $thumbnail2 . "' width=60 height=60>",
        '2' => $user['full_name'].' ', 
        '3' => $my_course_name,
        '4' => $loginAsButton."<br />".$scheduleButton,
        '5' => $user['course_id']
    );
}

echo '.';

$dataSet = '';
foreach ($userData as $num => $thisUser) {
    $dataSet .= '{
        ';

    $colNumber = 0;
    foreach ($thisUser as $dbField => $dbValue) { 
        
        $dataSet .= '"'.$dbField.'": "'.$dbValue.'"';
        $colNumber++;

        if ($colNumber == 10) 
            $dataSet .= '';
        else
            $dataSet .= ',
';
    }
    $dataSet .= ' },
        ';
}



$aoColumnsData2 = '{"bVisible": false},
    {"sTitle": "User ID"},
    {"sTitle": "User Name"},
    {"sTitle": "User Type"},
    {"sTitle": "Full Name"},
    {"sTitle": "Email / Username"},
    {"sTitle": "Edit",
        "bSearchable": false},
    {"sTitle": "Actions",
        "bSearchable": false},
    {"sTitle": "Course",
        "bSearchable": false,
        "bVisible": false}';

$aoColumnsData = '{"bVisible": false},
    {"sTitle": "Protege Photo"},
    {"sTitle": "Protege Name"},
    {"sTitle": "Protege Course",
        "bSearchable": false},
    {"sTitle": "Actions",
        "bSearchable": false},
    {"sTitle": "Course",
        "bSearchable": false,
        "bVisible": false}';

?>
<!--dataTable scripts-->
<script src="<?= $srcDir ?>media/js/jquery.dataTables.min.js" type="text/javascript"></script>

<script type="text/javascript" charset="utf-8">
    var oTable;
    
    function userAcctCreate() {
        var data = $('#userAcctCreate').serialize();
        $.ajax({
            type: "POST",
            url: "<?=$user_create?>",
            data: data,
            success: function(msg) {
                //alert(msg); 
                location.reload();
            },
        })
    }
       
    function deleteUser(id) {
        var name = 'id';
        var dataObject = {};
        dataObject[name] = id;

        if (confirm("Are you sure you want to delete record "+id+"?")) {
            $.ajax({
                type: "POST",
                url: "<?=$user_delete?>",
                data: dataObject,
                success: function(msg) {
                    setTimeout(function (){
                        $("#dataTable").load("user_builder.php");
                    }, 50); //delay refreshing dataTable
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    alert("Status: " + textStatus + " Error: " + errorThrown); 
                }
            })
        }    
    }
   
    $(document).ready( function () {        

        oTable = $('#dashboard').dataTable({  
            "bJQueryUI": true,
            //"sPaginationType": "full_numbers",
             "bPaginate": false,
    "bLengthChange": false,
    "bFilter": false,
    "bInfo": false,
    "bAutoWidth": false,
                'bSortable': false,

       
            "aaData": [<?= $dataSet ?>],
            "aoColumns": [<?= $aoColumnsData ?>],
     
        });
        $("td:nth-child(1)").unbind("dblclick"); /* disable inline editting */
        $("td:nth-child(2)").unbind("dblclick");
        $("td:nth-child(3)").unbind("dblclick");
        $("td:nth-child(4)").unbind("dblclick");
      $("td:nth-child(5)").unbind("dblclick");
        $("td:nth-child(6)").unbind("dblclick");
        $("td:nth-child(7)").unbind("dblclick");
        
       // $('#btnAddNewRow').unbind('click');
  
    });
</script>
<body>
<table style="width:100%! important;" cellpadding="0" cellspacing="0" border="0" class="display" id="dashboard">
    <thead>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    </tbody>
</table>
</body>