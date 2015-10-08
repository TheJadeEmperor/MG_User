<?
include('header.php');


$user_read = $base_url.'sites/all/modules/guild/mg/user_read.php';
$user_update = $base_url.'sites/all/modules/guild/mg/user_update.php';
$user_create = $base_url.'sites/all/modules/guild/mg/user_create.php';
$schedule_update = $base_url.'sites/all/modules/guild/mg/schedule_update.php'; //user's time available
$schedule_read = $base_url.'sites/all/modules/guild/mg/schedule_read.php'; //user's time available
$user_profile_picture = $base_url.'sites/all/modules/guild/mg/user_profile_picture.php'; 

$course_read = 'http://modernguild.com/sites/all/modules/guild/mg/course_read.php';
$fieldArray = array( 'course_id', 'course_name', 'course_type' );

$cArray = processJson($course_read, $fieldArray);

foreach($cArray as $n => $c) {
    $courseList .= '<option value="'.$c['course_id'].'">'.$c['course_id'].' - '.$c['course_name'].' - '.$c['course_type'].'</option>';
}

$userTypeQuery = "SELECT * FROM mg_user_types order by user_type_id";
$userTypeResult = mysql_query($userTypeQuery) or die(mysql_error());

while($t = mysql_fetch_assoc($userTypeResult)) {
    $t['user_type_id'] = mysql_real_escape_string($t['user_type_id']);
    $t['user_type_value'] = mysql_real_escape_string($t['user_type_value']);
    $userTypeList .= '<option value="'.$t['user_type_id'].'">'.$t['user_type_value'].'</option>';
}


//ini_set('display_errors', 1);

?> 
<!-- Create User --> <!-- <form id="formAddNewRow" style="display: none;" title="Create New User">-->
<form id="formAddNewRow" style="display: none;">
    <input type="hidden" name="x" id="x" rel="0" />
    <input type="hidden" name="x" id="x" rel="1" />
    <input type="hidden" name="x" id="x" rel="2" />
    <input type="hidden" name="x" id="x" rel="3" />
    <input type="hidden" name="x" id="x" rel="4" />
    <input type="hidden" name="x" id="x" rel="5" />
    <input type="hidden" name="x" id="x" rel="6" />
    <input type="hidden" name="x" id="x" rel="7" />
    <input type="hidden" name="x" id="x" rel="8" />
</form>
<!-- Create User -->
        
<form id="userAcctCreate" style="display: none;" title="Create New User">
    <label>First Name</label><br />
    <input type="text" name="first_name" id="first_name" />
    <br /><br />

    <label>Last Name</label><br />
    <input type="text" name="last_name" id="last_name" />
    <br /><br />

    <label>Email Address / Username</label><br />
    <input type="text" name="email" id="email" />
    <br /><br />

    <label>Google Account</label><br />
    <input type="text" name="gmail" id="gmail" />
    <br /><br />

    <label>Password</label><br />
    <input type="password" name="password" id="password" />
    <br /><br />

    <label>User Type</label><br />
    <select name="user_type" id="user_type">
        <?=$userTypeList?>
    </select>
    <br /><br />

    <label>Course Enrolled</label><br />
    <select name="course_id" id="course_id"><option>===Choose One===</option><?=$courseList?></select>
    <br /><br />
    
    <label>Course Status</label>
    <select name="course_status" id="course_status">
        <option>Pending</option>
        <option>Purchased</option>
        <option>Enrolled</option>
        <option>Graduated</option>
        <option>FreeTrial</option>
    </select>
    <br /><br />

    <label>Career Coach</label><br />
    <select name="user_id_mentor" id="user_id_mentor"><option>===Choose One===</option>
        <?=$ccOptions?>
    </select>
    <br /><br />
</form>
                
<table style="margin: 0 auto;">
    <tr>
        <td align="center">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">User List</h3>
                        </div>

                        <div id="dataTable">
                        <? include('user_builder.php');?>  
                        </div>

                    </div>
                </div>
            </div>

        </td>
    </tr>
</table>
<? include('footer.php'); ?>