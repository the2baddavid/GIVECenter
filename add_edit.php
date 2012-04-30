<?php

include_once(dirname(__FILE__).'/php/MySQLDatabase/MySQLDatabaseConn.php');
include_once(dirname(__FILE__).'/php/ini/GIVECenterIni.php');
include_once(dirname(__FILE__).'/sql/update/update_generic.php');
include_once(dirname(__FILE__).'/sql/update/update_hours.php');
include_once(dirname(__FILE__).'/sql/update/update_issues.php');
include_once(dirname(__FILE__).'/sql/update/update_season.php');
include_once(dirname(__FILE__).'/sql/add/create_new_addr.php');
include_once(dirname(__FILE__).'/sql/add/create_new_agency.php');
include_once(dirname(__FILE__).'/sql/add/create_new_hours.php');
include_once(dirname(__FILE__).'/sql/add/create_new_issue.php');
include_once(dirname(__FILE__).'/sql/add/create_new_p_contact.php');
include_once(dirname(__FILE__).'/sql/add/create_new_program.php');
include_once(dirname(__FILE__).'/sql/add/create_new_s_contact.php');

$conn = new MySQLDatabaseConn($GIVE_MYSQL_SERVER, $GIVE_MYSQL_DATABASE, $GIVE_MYSQL_UNAME, $GIVE_MYSQL_PASS);
$update = array();

//to hold various error states
$error = array();

//set to 'failed' if previous login attempt yielded no matches for the uname/pass combo
if(isset($_GET['login'])) {
	$error['login'] = $_GET['login'];
}
//set to 'true' if user was redirected to the login page via another page's logout button
if(isset($_GET['logout'])) {
	$error['logout'] = $_GET['logout'];
}
//set to 'conn' if an exception was thrown during login connecting to the database
//set to 'query' if an exception was thrown during login querying the database
if(isset($_GET['except'])) {
	$error['except'] = $_GET['except'];
}
//set to the error code resulting from an exception during login
if(isset($_GET['code'])) {
	$error['code'] = $_GET['code'];
}

/*****************************************************************************
 * Edit Condition!
 * 
 * -Checks that flag for update is set
 * -Updates the objects that have an id set
 * 
 * TODO: Mail,Phone,Fax for agency
 * TODO: Duration for program
 *****************************************************************************/
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
if($_POST['mode']=='edit'){     //  EDIT CONDITION
    
    // Only update if address exists and is edited 
    if($_POST['addr_id'] && $_POST['addr_id']!=-1){
        $update['addr']['id'] = $_POST['addr_id'];
        $update['addr']['street'] = $_POST['street'];
        $update['addr']['city'] = $_POST['city'];
        $update['addr']['state_us'] = $_POST['state_us'];
        $update['addr']['zip'] = $_POST['zip'];
        
        try{
            update_generic($conn, 'addr', $_POST['addr_id'], $update['addr']);
        }
        catch(Exception $e){
            echo $e;
        }
    }
    //  If address doesnt exist, and was edited create it
    elseif($_POST['addr_id'] && $_POST['addr_id']==-1){
        $update['addr']['id'] = $_POST['addr_id'];
        $update['addr']['street'] = $_POST['street'];
        $update['addr']['city'] = $_POST['city'];
        $update['addr']['state_us'] = $_POST['state_us'];
        $update['addr']['zip'] = $_POST['zip'];
        
        $addr_id = create_new_addr($conn, $update['addr']);
        
        $update['addr']['id'] = $addr_id;
    }
    
    if(isset($_POST['Hours'])){
        $update['hours'] = $_POST['Hours'];
        
        update_hours($conn, $_POST['program_id'], $_POST['Hours']);
    }
    
    if(isset($_POST['selectInterests'])){
        update_issues($conn, $_POST['program_id'],$_POST['selectInterests']);
    }
    
    if(isset($_POST['p_contact_id'])){   
        $update['p_contact']['id'] = $_POST['p_contact_id'];
        $update['p_contact']['title'] = $_POST['title'];
        $update['p_contact']['f_name'] = $_POST['f_name'];
        $update['p_contact']['l_name'] = $_POST['l_name'];
        $update['p_contact']['m_name'] = $_POST['m_name'];
        $update['p_contact']['suf'] = $_POST['suf'];
        $update['p_contact']['m_phone'] = $_POST['m_phone'];
        $update['p_contact']['w_phone'] = $_POST['w_phone'];
        $update['p_contact']['mail'] = $_POST['mail'];
        
        update_generic($conn, 'pro_contact', $_POST['p_contact_id'], $update['p_contact']);   
    }
    
    if(isset($_POST['s_contact_id'])){ //might also give errors
        $update['s_contact']['id'] = $_POST['s_contact_id'];
        $update['s_contact']['f_name'] = $_POST['s_f_name'];
        $update['s_contact']['f_name'] = $_POST['s_l_name'];
        $update['s_contact']['m_name'] = $_POST['s_m_name'];
        $update['s_contact']['suf'] = $_POST['s_suf'];
        $update['s_contact']['m_phone'] = $_POST['s_m_phone'];
        $update['s_contact']['w_phone'] = $_POST['s_w_phone'];
        $update['s_contact']['mail'] = $_POST['s_mail'];
        
        update_generic($conn, 'student_contact',$update['s_contact']['id'] , $update['s_contact']);
      
    }
    
    if(isset($_POST['season'])){
        update_season($conn, $_POST['program_id'], $update['season']);
    }
    
    if($_POST['program']<0){
        $update['agency']['id'] = $_POST['agency_id'];
        $update['agency']['name'] = $_POST['name'];
        $update['agency']['descript'] = $_POST['descript'];
        $update['agency']['p_contact'] = $update['p_contact']['id'];;
        $update['agency']['addr'] = $update['addr']['id'];
        $update['agency']['mail'] = $_POST['agency_mail'];
        $update['agency']['phone'] = $_POST['agency_phone'];
        $update['agency']['fax'] = $_POST['agency_fax'];
        
        update_generic($conn, 'agency', $_POST['agency_id'], $_POST['agency']);
    }
        
    else{
        $update['program']['id'] = $_POST['program_id'];
        $update['program']['agency'] = $_POST['agency_id'];
        $update['program']['addr'] = $update['addr']['id'];
        $update['program']['name'] = $_POST['name'];
        $update['program']['p_contact'] = $update['p_contact']['id'];
        $update['program']['s_contact'] = $update['s_contact']['id'];
        $update['program']['descript'] = $_POST['descript'];
        $update['program']['referal'] = $_POST['ref_type'];
        $update['program']['notes'] = $_POST['notes'];
    //    $update['program']['duration'];
        
        update_generic($conn, 'program', $update['program']['id'], $update['program']);
    }
    //header('Location: Admin.php?edit=success');
}



/*****************************************************************************
 * Add Condition!
 * 
 * -Checks that the values are set before setting them, so that only the
 *      minimal number of calls are made
 * -Program however uses the name inside program to prevent false positives,
 *      as some will set values inside program in case that a program
 *      needs to be created
 *****************************************************************************/


elseif ($_POST['mode']=='add'){     
    
    if(isset($_POST['street'])){
        //  Need to return addr id
        $update['addr']['street'] = $_POST['street'];
        $update['addr']['city'] = $_POST['city'];
        $update['addr']['state_us'] = $_POST['state_us'];
        $update['addr']['zip'] = $_POST['zip'];
        
        $addr_id = create_new_addr($conn, $update['addr']);
        $update['addr']['id'] = $addr_id;
    }
    else
        $update['addr']['id'] = 'null';
    
    if(isset($_POST['f_name']) || isset($_POST['l_name'])){
        $update['p_contact']['title'] = $_POST['title'];
        $update['p_contact']['f_name'] = $_POST['f_name'];
        $update['p_contact']['l_name'] = $_POST['l_name'];
        $update['p_contact']['m_name'] = $_POST['m_name'];
        $update['p_contact']['suf'] = $_POST['suf'];
        $update['p_contact']['m_phone'] = $_POST['m_phone'];
        $update['p_contact']['w_phone'] = $_POST['w_phone'];
        $update['p_contact']['mail'] = $_POST['mail'];
        
        $p_id = create_new_p_contact($conn, $update['p_contact']);
        $update['p_contact']['id'] = $p_id;
    }
    else
        $update['p_contact']['id'] = 'null';
        
    if(isset($_POST['s_f_name']) || isset($_POST['s_l_name'])){
        $update['s_contact']['f_name'] = $_POST['s_f_name'];
        $update['s_contact']['l_name'] = $_POST['s_l_name'];
        $update['s_contact']['m_name'] = $_POST['s_m_name'];
        $update['s_contact']['suf'] = $_POST['s_suf'];
        $update['s_contact']['m_phone'] = $_POST['s_m_phone'];
        $update['s_contact']['w_phone'] = $_POST['s_w_phone'];
        $update['s_contact']['mail'] = $_POST['s_mail'];
        
        $s_id = create_new_s_contact($conn, $update['s_contact']);
        $update['s_contact']['id'] = $s_id;
    }
    else
        $update['s_contact']['id'] = 'null';
    
    if($_POST['program']<0){
        $update['agency']['name'] = $_POST['name'];
        $update['agency']['descript'] = $_POST['descript'];
        $update['agency']['p_contact'] = $update['p_contact']['id'];
        $update['agency']['addr'] = $update['addr']['id'];
        $update['agency']['mail'] = $_POST['agency_mail'];
        $update['agency']['phone'] = $_POST['agency_phone'];
        $update['agency']['fax'] = $_POST['agency_fax'];
        
        $agency_id = create_new_agency($conn, $update['agency']);
        $update['program']['agency'] = $agency_id;
    }
    else{
        $update['program']['agency'] = $_POST['agency_id'];
        $update['program']['addr'] = $update['addr']['id'];
        $update['program']['name'] = $_POST['name'];
        $update['program']['p_contact'] = $update['p_contact']['id'];
        $update['program']['s_contact'] = $update['s_contact']['id'];
        $update['program']['descript'] = $_POST['descript'];
        $update['program']['referal'] = $_POST['ref_type'];
        $update['program']['notes'] = $_POST['notes'];
     // $update['program']['duration'];
        
        $update['program']['id'] = create_new_program_existing_agency($conn, $update['program']);
    }
    
    if(isset($_POST['Hours'])){
        $update['hours'] = $_POST['Hours'];
        
        create_new_hours($conn, $update['program']['id'], $update['hours']);
    }
    if(isset($_POST['issue'])){
        create_new_issue($conn, $update['program']['id'], $_POST['selectInterests']);
    }
    if(isset($_POST['season'])){
        $update['season'] = $_POST['season'];
        
        create_new_seasons($conn,$_POST['program_id'], $update['season']);
    }
    if(isset($update['program']['s_contact'])){
        create_new_contact_history($conn, $update['program']['id'], $update['program']['s_contact']);
    }
    //header('Location: Admin.php?add=success');
}


?>
