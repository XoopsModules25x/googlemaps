<?php
// $Id: sqlupdate.php,v 1.1 2005/09/03 11:38:11 yoshis Exp $
// Original Script by aotake , http://xoops.bmath.org/
//
include('../../../include/cp_header.php');
if(
    (!defined('XOOPS_ROOT_PATH')) ||
    (!is_object($xoopsUser)) ||
    (!$xoopsUser->isAdmin()) ){
    exit();
}
//    MODULE/admin/ins_table.php?sqlfilename=$filename

$sqlfile = "indexes.sql"; // <-- change here if you need.


global $xoopsDB;
global $xoopsModule;
$error = false;
$dirname = $xoopsModule->getvar("dirname");
$db =& $xoopsDB;

$sql_file_path = XOOPS_ROOT_PATH."/modules/".$dirname."/sql/".$sqlfile;
if (!file_exists($sql_file_path)) {
    //$errs[] = "SQL file not found at <b>$sql_file_path</b>";
    //$error = true;
    print "SQL file not found at <b>$sql_file_path</b>";
    exit (1);
} else {
    $sql='';
    print "SQL file found at <b>$sql_file_path</b>.<br  /> adjusting data...";
    include_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php';
    $sql_query = fread(fopen($sql_file_path, 'r'), filesize($sql_file_path));
    foreach( explode("\n", $sql_query) as $tmp){
        if ( preg_match( "/^--/", $tmp ) ){ continue; }
        if ( preg_match( "/^\n/", $tmp ) ){ continue; }
        $sql .= $tmp;
    }
    $sql_query = trim($sql);
    $prefix = $xoopsDB->prefix();
    $sql_query = preg_replace( "/__PREFIX__/", $prefix."_", $sql_query);
    foreach( explode(";", $sql_query) as $tmp){
        $sql_array[] = $tmp;
    }
    foreach ($sql_array as $prefixed_query) {
        if ( $prefixed_query == "" ){ continue; }
        if (!$db->queryF($prefixed_query)) {
            $errs[] = $db->error();
            $error = true;
            break;
        }
    }
    if ( $error ){
        echo "Error Occured!<br />";
        foreach ($errs as $msg) {
           print "<b>$msg</b><br />";
        }
        exit(1);
    }
    redirect_header( XOOPS_URL."/modules/".$dirname."/",3, "Data transportation Successful");
}
