<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

include_once(dirname(__FILE__).'/../../php/MySQLDatabase/MySQLDatabaseConn.php');

function create_new_p_contact($conn,$info_array)
{
    $query = "INSERT INTO pro_contact(title,l_name,f_name,m_name,suf,m_phone,w_phone,mail)
                VALUES ('".$info_array['title']."','".$info_array['l_name']."','".$info_array['f_name']."',
                    '".$info_array['m_name']."','".$info_array['suf']."',".$info_array['m_phone'].",
                        ".$info_array['w_phone'].",'".$info_array['mail']."')";
 
    try{
        $conn->query($query);
    }
    catch(Exception $e){
        echo $e;
    }
    
    $query2 = "SELECT id FROM pro_contact ORDER BY id DESC Limit 0,1";
    try{
        $conn->query($query2);
    }
    catch(Exception $e){
        echo $e;
    }
    // get id of last program inserted
    $conn->query($query2);
    $p_id = $conn->fetchRowAsAssoc();
    
    return $p_id['id'];
}
?>
