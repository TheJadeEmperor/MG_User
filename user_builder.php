<?
$user_delete = 'http://modernguild.com/sites/all/modules/guild/mg/user_delete.php';
$user_update = 'http://modernguild.com/sites/all/modules/guild/mg/user_update.php';
$user_create = 'http://modernguild.com/sites/all/modules/guild/mg/user_create.php';
$user_read = "http://modernguild.com/sites/all/modules/guild/mg/user_read.php";

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
    }  
}
    
$userArray = $jArray;

foreach ($userArray as $num => $user) {
    
    if($num > 50) echo '<div></div>';
    
    $id = $user['user_id'];
    
    //if not logged in as admin
    if($is_admin != 1) {
        $disabledButton = 'disabled';
    }
    
    if($id == 1) {
        $deleteButton = '';
        $loginAsButton = '';
       
    }
    else {
    /*    $deleteButton = " <a href='javascript:void(0)'><input type='button' class='btn btn-danger' value='Delete User' onclick='deleteUser(".$id.")' ".$disabledButton." /></a>";*/
        $loginAsButton = "<a href='login_as.php?user_id=".$id."'><input type='button' class='btn btn-success' value='Log in as user' ".$disabledButton." /></a>";
    }
    
    $pictureButton = "<input type='button' class='btn btn-info' value='Picture' onclick='updatePictureDialog(".$id.")' />";
    $editButton = "<input type='button' class='btn btn-info' value='Profile' onclick='updateUserDialog(".$id.");' />";
    $scheduleButton = "<input type='button' class='btn btn-warning' value='Edit Schedule' onclick='updateScheduleDialog(".$id.")' />";
    
    if(!$user['user_type_value']) {
        $user['user_type_value'] = 'N/A';
    }
    
    $userData[$num] = array(
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
}

echo '.';

$dataSet = '';
foreach ($userData as $num => $thisUser) {
    $dataSet .= '{
        ';

    $colNumber = 0;
    foreach ($thisUser as $dbField => $dbValue) { 
        
        $dataSet .= '"'.addslashes($dbField).'": "'.addslashes($dbValue).'"';
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

$aoColumnsData = '{"bVisible": false},
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
            "sPaginationType": "full_numbers",
            "aaData": [<?= $dataSet ?>],
            "aoColumns": [<?= $aoColumnsData ?>],
        }).makeEditable({
            aoTableActions: [
                {
                    sAction: "EditData", 
                    sServerActionURL: "<?=$user_update?>",
                    oFormOptions: { autoOpen: false, show: "explode", hide: "explode", modal:true }
                }
            ],
            sAddHttpMethod: "GET",
            sAddURL: "<?=$user_create?>",
            sDeleteHttpMethod: "POST",
            sDeleteURL: "<?=$user_delete?>",
                    
            oAddNewRowButtonOptions: {label: "Create",
                icons: {primary: 'ui-icon-plus'}
            },
            oDeleteRowButtonOptions: {label: "Delete",
                icons: {primary: 'ui-icon-trash'}
            },
            oAddNewRowOkButtonOptions: {	
                label: "OK", class: "back-class",
                name:"action",
                value:"add-new",
                icons: {primary:'ui-icon-check'}
            },            
            oAddNewRowCancelButtonOptions: { 
                label: "Cancel",
                name:"action",
                value:"cancel-add",
                icons: {primary:'ui-icon-close'}
            },
            oAddNewRowFormOptions: {
           
            },
            sAddDeleteToolbarSelector: ".dataTables_length",
            fnOnAdded: function() {
                 setTimeout(function (){
                    $("#dataTable").load("user_builder.php"); 
                }, 200); //delay refreshing dataTable
            }
        });
        $("td:nth-child(1)").unbind("dblclick"); /* disable inline editting */
        $("td:nth-child(2)").unbind("dblclick");
        $("td:nth-child(3)").unbind("dblclick");
        $("td:nth-child(4)").unbind("dblclick");
        $("td:nth-child(5)").unbind("dblclick");
        $("td:nth-child(6)").unbind("dblclick");
        $("td:nth-child(7)").unbind("dblclick");
        
        $('#btnAddNewRow').unbind('click');
        $('#btnAddNewRow').off();
        
        //add_row ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary
        $('#btnAddNewRow').click( function(){
            //alert('create');
            $('#userAcctCreate').dialog({
                modal: true,
                position: 'top',
                width: 600,
                show: {
                    effect: "explode",
                    duration: 500
                },
                hide: {
                    effect: "explode",
                    duration: 500
                },
                close: function() {
                    location.reload();
                },
                buttons: {
                    Save: function () {
                        userAcctCreate();
                        $( this ).dialog( "close" );
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" ); 
                    },                        
                }
            });
        });
    });
</script>
<body>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dashboard">
    <thead>
    </thead>
    <tfoot>
    </tfoot>
    <tbody>
    </tbody>
</table>
</body>