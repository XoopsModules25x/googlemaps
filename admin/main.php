<?php
###############################################################################
#                  Phatblokes adaption of google maps v.1.0 for Xoops 2.x     #
#             Writen by   Phatbloke (phatbloke@world-net.co.nz)               #
#   ------------------------------------------------------------------------- #
#                                                                             #
#   ------------------------------------------------------------------------- #
#   This program is free software; you can redistribute it and/or modify      #
#   it under the terms of the GNU General Public License as published by      #
#   the Free Software Foundation; either version 2 of the License, or         #
#   (at your option) any later version.                                       #
#                                                                             #
#   This program is distributed in the hope that it will be useful,           #
#   but WITHOUT ANY WARRANTY; without even the implied warranty of            #
#   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             #
#   GNU General Public License for more details.                              #
#                                                                             #
#   You should have received a copy of the GNU General Public License         #
#   along with this program; if not, write to the Free Software               #
#   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA  #
#   ------------------------------------------------------------------------  #
###############################################################################
include '../../../include/cp_header.php';
if (file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
    include_once "../language/".$xoopsConfig['language']."/main.php";
}else{
    include_once "../language/english/main.php";
}
//include '../include/functions.php';
include_once XOOPS_ROOT_PATH.'/class/xoopstree.php';
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/include/xoopscodes.php";
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
include XOOPS_ROOT_PATH."/class/xoopsformloader.php";

/*TODO:
    * Look into form issue. missing .js

*/

$myts = MyTextSanitizer::getInstance();
$eh = new ErrorHandler; //TODO: DEPRECATED

// Display the main contents

function main()
{
    global $xoopsDB, $xoopsModule;
    xoops_cp_header();

    xoops_cp_footer();
}

function insertgscript($options)
{
    $lng = 0;
    $lat = 0;
    $zoom = 1;

    if($options['lng']){
        $lng =$options['lng'];
    }

    if($options['lat']){
        $lat = $options['lat'];
    }

    if($options['zoom']){
        $zoom = $options['zoom'];
    }

    echo"<script type=\"text/javascript\">\n";
    echo"//&lt;![CDATA[ \n";
    echo "var map;

    function initialize() {
        var myLatLng = new google.maps.LatLng({lat: ".$lat.", lng: ".$lng."});
    map = new google.maps.Map(document.getElementById('map'), {
        center: myLatLng,
        zoom: ".$zoom."
    });
    
    marker = new google.maps.Marker({
        position: myLatLng,
        map: map
    });

    putMarker(myLatLng);
    google.maps.event.addListener(map, 'click', function(event) {
        putMarker(event.latLng);
        });
    }

    function putMarker(location) {
    if (marker) {
            marker.setPosition(location);
            map.setCenter(location);
    } else {
        marker = new google.maps.Marker({
        center:location,
        map: map
        });
    }
    if(location){      
        document.getElementById(\"lat\").value = location.lat();
        document.getElementById(\"lon\").value = location.lng();
        document.getElementById(\"zoom\").value = map.getZoom();
    }
    }";
    // arrange for our onload handler to 'listen' for onload events\n"
    echo"if (window.attachEvent) {\n";
    echo"window.attachEvent(\"onload\", function() {\n";
    echo"initialize();	// Internet Explorer\n";
    echo"});\n";
    echo"} else {\n";
    echo "google.maps.event.addDomListener(window, 'load', initialize);";
    echo "};\n";
    echo"//]]&gt;\n";
    echo"</script >\n";
}

// Add in a new category
function catAdd()
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig;
    xoops_cp_header();
    //Display the map
    echo "<script src=\"https://maps.googleapis.com/maps/api/js?key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script>\n";
    insertgscript(array('lng'=>0,'lat'=>0,'zoom'=>1));
    $form = new XoopsThemeForm(_MD_ADDNEWCAT,'form', 'main.php');
    $gmap = new XoopsFormLabel('', "<div id='map' style='height: 400px'></div >");
    $gmap->setNocolspan(true);
    $form->addElement($gmap);
    $form->addElement(new XoopsFormText(_MD_CAT,'title',50,100));
    $form->addElement(new XoopsFormText(_MD_CATLONGITUDE,'lon',50,250));
    $form->addElement(new XoopsFormText(_MD_CATLATITUDE,'lat',50,250));
    $form->addElement(new XoopsFormText(_MD_ZOOM,'zoom',50,250));
    $form->addElement(new XoopsFormHidden('op','catInsert'));
    $form->addElement(new XoopsFormButton('','post',_MD_ADD,'submit'));
    //->setExtra('accesskey="s\"');
    $form->render();
    echo $form->display();
    xoops_cp_footer();
}

//Insert the category into the DB

function catInsert()
{
    global $xoopsConfig, $xoopsDB, $myts, $xoopsUser, $xoopsModule, $eh;
    $title = $myts->htmlSpecialChars($_POST["title"]);
    $lat = $myts->htmlSpecialChars($_POST["lat"]);
    $lon = $myts->htmlSpecialChars($_POST["lon"]);
    $zoom = $myts->htmlSpecialChars($_POST["zoom"]);
    $errormsg = '';
    // Check if Title exist
    if($title == "") {
        $errormsg .= "<h4 style='color: #ff0000'>";
        $errormsg .= _MD_ERRORTITLE."</h4>";
            $error =1;
        }
        if ( $error == 1 ) {
        xoops_cp_header();
        echo $errormsg;
        xoops_cp_footer();
        exit();
        }
    list($order) = $xoopsDB->fetchRow($xoopsDB->query("SELECT MAX(`order`) FROM ".$xoopsDB->prefix("gmap_category")));
    $order++;
    //$sql = "INSERT INTO ".$xoopsDB->prefix("gmap_category")." ( `map_id` , `lat` , `lon` , `name` , `zoom` ,`active` , `order`) VALUES ('', '$lat', '$lon', '$title', $zoom, '1', '$order')";
    $sql = "INSERT INTO ".$xoopsDB->prefix("gmap_category")." ( `lat` , `lon` , `name` , `zoom` ,`active` , `order`) VALUES ('$lat', '$lon', '$title', $zoom, '1', '$order')";
    $xoopsDB->query($sql) or $eh->show("0013");
    if($newid == 0) {
        $newid = $xoopsDB->getInsertId();
    }
    redirect_header("main.php?op=catMod",1,_MD_NEWCATADDED);
}

//Modify a category select page to choose which cat to mod

function catMod($fct)
{
    global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig;
    if('modCat' == $fct){
        modCat();
    }else{
        $result2 = $xoopsDB->query("select count(*) from ".$xoopsDB->prefix("gmap_category")."");
        list($numrows2) = $xoopsDB->fetchRow($result2);
        if ( $numrows2 > 0 ) {
            $result1 = $xoopsDB->query("select map_id, name from ".$xoopsDB->prefix("gmap_category")."");
            $i = 0;
            while ($array = $xoopsDB->fetchArray($result1)) {
                $entry[$i]['map_id']   = $array['map_id'];
                $entry[$i]['name']   = $array['name'];
                $i++;
            }
            xoops_cp_header();
            //$form = new XoopsThemeForm(_MD_MODCAT,'','main.php','GET');//TODO: ModuleAdmin.
            $form = new XoopsThemeForm(_MD_MODCAT,'','main.php?op=catMod&fct=modCat','POST');//TODO: Use POST for now..
            //$form->addElement(new XoopsFormHidden('op','catMod'));
            $select = new XoopsFormSelect(_MD_CATID,'lid');
            $count_msg = count($entry);
            for ( $i = 0; $i < $count_msg; $i++ ) {
                $select->addOption($entry[$i]['map_id'],$entry[$i]['name']);
            }
            $form->addElement($select);
            //$form->addElement(new XoopsFormHidden('fct','mypoints'));//??
            //$form->addElement(new XoopsFormHidden('fct','modCat'));
            $form->addElement(new XoopsFormButton('','',_MD_MODIFY,'submit'));
            $form->render();
            echo $form->display();

            xoops_cp_footer();
        }else{
            redirect_header("index.php",1,_MD_NOCATS);
        }
    }
}

//Modify the selected category

function modCat()
{
    global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig;
    $lid = intval($_POST['lid']);
    $result = $xoopsDB->query("select map_id, lat, lon, zoom, name from ".$xoopsDB->prefix("gmap_category")." where map_id=$lid") or $eh->show("0013");
    list($map_id, $lat, $lon, $zoom, $name) = $xoopsDB->fetchRow($result);
    $name = $myts->htmlSpecialChars($name);
    $lat = $myts->htmlSpecialChars($lat);
    $lon = $myts->htmlSpecialChars($lon);
    $zoom = intval($zoom);
    xoops_cp_header();
    //Display the map
    echo "<script src=\"https://maps.googleapis.com/maps/api/js?key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script>\n";
    insertgscript(array('lng'=>$lon,'lat'=> $lat,'zoom'=>$zoom));
    $form = new XoopsThemeForm(_MD_MODCAT,'form', 'main.php');
    $gmap = new XoopsFormLabel('', "<div id='map' style='height: 400px'></div >");
    $gmap->setNocolspan(true);
    $form->addElement($gmap);
    $form->addElement(new XoopsFormText(_MD_CAT,'name',50,100,$name));
    $form->addElement(new XoopsFormText(_MD_CATLONGITUDE,'lon',50,250));
    $form->addElement(new XoopsFormText(_MD_CATLATITUDE,'lat',50,250));
    $form->addElement(new XoopsFormText(_MD_ZOOM,'zoom',50,250));
    $form->addElement(new XoopsFormHidden('op','modCatS'));
    $form->addElement(new XoopsFormHidden('lid',$lid));
    $modify = new XoopsFormButton('','modify',_MD_MODIFY,'submit');
    $delete = new XoopsFormButton('','delete',_MD_DELETE,'submit');
    $cancel = new XoopsFormButton('','cancel',_MD_CANCEL,'button');//This one will only be a button. no need to POST.
    $cancel->setExtra('onclick="location.href=\'main.php?op=catMod\'"');
    $tray = new XoopsFormElementTray();
    $tray->addElement($modify);
    $tray->addElement($delete);
    $tray->addElement($cancel);
    $form->addElement($tray);
    $form->render();
    echo $form->display();
    xoops_cp_footer();
}

function modCatS(){
    global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig;
    if(isset($_POST['modify'])){
        $name = $myts->htmlSpecialChars($_POST["name"]);
        $lat = $myts->htmlSpecialChars($_POST["lat"]);
        $lon = $myts->htmlSpecialChars($_POST["lon"]);
        $zoom = $myts->htmlSpecialChars($_POST["zoom"]);
        $lid = intval($_POST['lid']);
        $xoopsDB->query("update ".$xoopsDB->prefix("gmap_category")." set name='$name', lat='$lat', lon='$lon',zoom='$zoom'where map_id=".$lid."")  or $eh->show("0013");
        redirect_header("index.php",1,_MD_DBUPDATED);
    }elseif(isset($_POST['delete'])){
        delCat();
    }else{
        //TODO: ERROR MSG
        redirect_header("index.php",1,"error message");
    }
    exit(); 
}

//Add in a new point
function pointAdd()
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig;
    $result2 = $xoopsDB->query("select count(*) from ".$xoopsDB->prefix("gmap_category")."");
    list($numrows2) = $xoopsDB->fetchRow($result2);
    if ( $numrows2 > 0 ) {
        $result1 = $xoopsDB->query("select map_id, name from ".$xoopsDB->prefix("gmap_category")."");//TODO:sort
        xoops_cp_header();
        //Display the map
        echo "<script src=\"https://maps.googleapis.com/maps/api/js?key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script>\n";
        insertgscript(array('lng'=>0,'lat'=>0,'zoom'=>1));
        $form = new XoopsThemeForm(_MD_ADDNEWPOINT,'form', 'main.php');
        $gmap = new XoopsFormLabel('', "<div id='map' style='height: 400px'></div >");
        $gmap->setNocolspan(true);
        $form->addElement($gmap);
        $form->addElement(new XoopsFormText(_MD_LOCATION,'title',50,100));
        $form->addElement(new XoopsFormText(_MD_LONGITUDE,'lon',50,250));
        $form->addElement(new XoopsFormText(_MD_LATITUDE,'lat',50,250));
        $form->addElement(new XoopsFormText(_MD_ZOOM,'zoom',50, 250));     
        $catselect = new XoopsFormSelect(_MD_CATEGORYC,'category');
        while ($array = $xoopsDB->fetchArray($result1)) {
            $catselect->addOption($array['map_id'],$array['name']);
        }
        $form->addElement($catselect);
        $form->addElement(new XoopsFormDhtmlTextArea(_MD_DESCRIPTIONC,'description',null,60,8));
        $form->addElement(new XoopsFormHidden('op','pointInsert'));
        $form->addElement(new XoopsFormButton('', 'post', _MD_ADD, 'submit'));
        //->setExtra('accesskey="s\"');
        $form->render();
        echo $form->display();
        xoops_cp_footer();
    }else{
        redirect_header("main.php?op=catAdd",1,_MD_NOCATSADD);
    }
}

//Insert the new point into the DB
function pointInsert()
{
    global $xoopsConfig, $xoopsDB, $myts, $xoopsUser, $xoopsModule, $eh;
    $title = $myts->htmlSpecialChars($_POST["title"]);
    $lat = $myts->htmlSpecialChars($_POST["lat"]);
    $lon = $myts->htmlSpecialChars($_POST["lon"]);
    $zoom = $myts->htmlSpecialChars($_POST["zoom"]);
    $category = $myts->htmlSpecialChars($_POST["category"]);
    $description = $myts->makeTareaData4Save($_POST["description"]);
    $submitter = $xoopsUser->uid();
    $date = time();
    $errormsg = '';
    // Check if Title exist
    if ( $title == "" ) {
        $errormsg .= "<h4 style='color: #ff0000'>";
        $errormsg .= _MD_ERRORTITLE."</h4>";
        $error =1;
    }

    // Check if Description exist
    if ( $description == "" ) {
        $errormsg .= "<h4 style='color: #ff0000'>";
        $errormsg .= _MD_ERRORDESC."</h4>";
        $error =1;
    }
    if ( $error == 1 ) {
        xoops_cp_header();
        echo $errormsg;
        xoops_cp_footer();
        exit();
    }
    list($order) = $xoopsDB->fetchRow($xoopsDB->query("SELECT MAX(`order`) FROM ".$xoopsDB->prefix("gmap_points")." WHERE `map_id` = ".$category));
    $order++;
    $sql = "INSERT INTO ".$xoopsDB->prefix("gmap_points")." (`map_id` , `lat` , `lon` , `title` , `html` , `zoom` , `submitter` , `status` , `date`, `order`) VALUES ('$category', '$lat', '$lon', '$title', '$description', '$zoom','$submitter', '1', '$date', '$order')";
    $xoopsDB->query($sql) or $eh->show("0013");
    if ( $newid == 0 ) {
        $newid = $xoopsDB->getInsertId();
    }
    redirect_header("main.php?op=pointMod",1,_MD_NEWPOINTADDED);
}

//Modify a point select page to choose which point to mod

function pointMod()
{
        global $xoopsDB;
        $result2 = $xoopsDB->query("select count(*) from ".$xoopsDB->prefix("gmap_points")."");
        list($numrows2) = $xoopsDB->fetchRow($result2);
        if ( $numrows2 > 0 ) {
            $result1 = $xoopsDB->query("select id, title from ".$xoopsDB->prefix("gmap_points")."");
            $i = 0;
            while ($array = $xoopsDB->fetchArray($result1)) {
            $entry[$i]['id']   = $array['id'];
            $entry[$i]['title']   = $array['title'];
            $i++;
        }
        xoops_cp_header();
        /*$form = new XoopsThemeForm(_MD_MODPOINT,'','main.php');
        $select = new XoopsFormSelect(_MD_POINTID,'lid');
        $count_msg = count($entry);
        for ( $i = 0; $i < $count_msg; $i++ ) {
            $select->addOption($entry[$i]['id'],$entry[$i]['title']);
        }
        $form->addElement($select);
        $form->addElement(new XoopsFormHidden('fct','mypoints'));
        $form->addElement(new XoopsFormHidden('op','modPoint'));
        $form->addElement(new XoopsFormButton('','post',_MD_MODIFY,'submit'));*/

        //prefer GET but need to use post.
        $form = new XoopsThemeForm(_MD_MODPOINT,'','main.php?op=pointMod&fct=modPoint');
        $select = new XoopsFormSelect(_MD_POINTID,'lid');
        $count_msg = count($entry);
        for ( $i = 0; $i < $count_msg; $i++ ) {
            $select->addOption($entry[$i]['id'],$entry[$i]['title']);
        }
        $form->addElement($select);
//        $form->addElement(new XoopsFormHidden('fct','modPoint'));
  //      $form->addElement(new XoopsFormHidden('op','pointMod'));
        $form->addElement(new XoopsFormButton('','post',_MD_MODIFY,'submit'));
        $form->render();
        echo $form->display();
        xoops_cp_footer();
    }else{
        redirect_header("index.php",1,_MD_NOPOINTS);
    }
}

//Modify the point

function modPoint()
{
    global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig;
    $lid = intval($_POST['lid']);
    //TODO:ERR
    $result = $xoopsDB->query("select id, lat, lon, zoom, map_id, title, html from ".$xoopsDB->prefix("gmap_points")." where id=$lid") or $eh->show("0013");
    list($id, $lat, $lon, $zoom, $map_id, $title, $html) = $xoopsDB->fetchRow($result);
    $title = $myts->htmlSpecialChars($title);
    $lat = $myts->htmlSpecialChars($lat);
    $lon = $myts->htmlSpecialChars($lon);
    $zoom = intval($zoom);
    $map_id = intval($map_id);
    $GLOBALS['html'] = $myts->htmlSpecialChars($html);//??
    $result1 = $xoopsDB->query("select map_id, name from ".$xoopsDB->prefix("gmap_category")."");//TODO:ERR
    $i = 0;
    while ($array = $xoopsDB->fetchArray($result1)) {
        $entry[$i]['map_id']   = $array['map_id'];
        $entry[$i]['name']   = $array['name'];
        $i++;
    }
    xoops_cp_header();

    //Display Map
    $options = array('lng' => $lon,'lat' => $lat,'zoom' => $zoom);
    $options['title'] = $name;
    echo "<script src=\"https://maps.googleapis.com/maps/api/js?key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script>\n";
    insertgscript($options);
    $form = new XoopsThemeForm(_MD_MODPOINT,'form','main.php?op=pointMod');
    $gmap = new XoopsFormLabel('', "<div id='map' style='height: 400px'></div >");
    $gmap->setNocolspan(true);
    $form->addElement($gmap);
    $form->addElement(new XoopsFormText(_MD_LOCATION,'title',50,100,$title));
    $form->addElement(new XoopsFormText(_MD_LONGITUDE,'lon',50,250));
    $form->addElement(new XoopsFormText(_MD_LATITUDE,'lat',50,250));
    $form->addElement(new XoopsFormText(_MD_ZOOM,'zoom',50, 250));
    $select = new XoopsFormSelect(_MD_CATEGORYC,'map_id');
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
            $select->addOption($entry[$i]['map_id'],$entry[$i]['name']);
        if ( $map_id == $entry[$i]['map_id'] ) {
            $select->setValue($entry[$i]['map_id'],$entry[$i]['name']);
        }
    }
    $form->addElement($select);
    $form->addElement(new XoopsFormHidden('lid',$id));
    $form->addElement(new XoopsFormHidden('fct','modPointS'));
    $tray = new XoopsFormElementTray();
    $tray->addElement(new XoopsFormButton('', 'modify', _MD_MODIFY, 'submit'));
    $tray->addElement(new XoopsFormButton('','delete',_MD_DELETE,'submit'));//delCat
    $cancel = new XoopsFormButton('','cancel',_MD_CANCEL,'button');
    $cancel->setExtra('onclick="location.href=\'main.php?op=pointMod\'"');
    $tray->addElement($cancel);
    $form->addElement($tray);
    $form->render();
    echo $form->display();
    xoops_cp_footer();
}

//Insert the modified point into the DB

function modPointS()
{
    global $xoopsDB, $myts, $eh;
    if(isset($_POST['modify'])){
        $title = $myts->htmlSpecialChars($_POST["title"]);
        $map_id = intval($_POST["map_id"]);
        $lid = $myts->htmlSpecialChars($_POST["lid"]);
        $lat = $myts->htmlSpecialChars($_POST["lat"]);
        $lon = $myts->htmlSpecialChars($_POST["lon"]);
        $zoom = $myts->htmlSpecialChars($_POST["zoom"]);
        $html = $myts->makeTareaData4Save($_POST["html"]);
        $xoopsDB->query("update ".$xoopsDB->prefix("gmap_points")." set map_id='$map_id', title='$title', lat='$lat', lon='$lon',zoom='$zoom',html='$html', status=2, date=".time()." where id=".$lid."")  or $eh->show("0013");
        redirect_header("index.php",1,_MD_DBUPDATED);
    }elseif(isset($_POST['delete'])){
        delPoint();
    }else{        //TODO: ERROR MSG
        redirect_header("index.php",1,"error message");
    }
    exit();
}

//Delete a Category

function delCat()
{
    global $xoopsDB, $eh, $xoopsModule;
    $lid = intval($_POST['lid']);
    $sql = sprintf("DELETE FROM %s WHERE map_id = %u", $xoopsDB->prefix("gmap_category"), $lid);//TODO: orphaned points.
    print($sql);
    $xoopsDB->query($sql) or $eh->show("0013");
    redirect_header("index.php",1,_MD_CATDELETED);
    exit();
}

//Delete a point

function delPoint()
{
    global $xoopsDB, $eh, $xoopsModule;
    $lid = intval($_POST['lid']);
    $sql = sprintf("DELETE FROM %s WHERE id = %u", $xoopsDB->prefix("gmap_points"), $lid);
    $xoopsDB->query($sql) or $eh->show("0013");
    redirect_header("index.php",1,_MD_POINTDELETED);
    exit();
}

//Add a new polyline

function plAdd()
{
    global $xoopsDB;
    $result2 = $xoopsDB->query("select count(*) from ".$xoopsDB->prefix("gmap_points")."");
    list($numrows2) = $xoopsDB->fetchRow($result2);
    if ( $numrows2 > 1 ) {
        $result1 = $xoopsDB->query("select id, title from ".$xoopsDB->prefix("gmap_points")."");
        $i = 0;
        while ($array = $xoopsDB->fetchArray($result1)) {
        $entry[$i]['id']   = $array['id'];
        $entry[$i]['title']   = $array['title'];
        $i++;
    }
    $result3 = $xoopsDB->query("select map_id, name from ".$xoopsDB->prefix("gmap_category")."");
    $i = 0;
    while ($array = $xoopsDB->fetchArray($result3)) {
        $centry[$i]['map_id']   = $array['map_id'];
        $centry[$i]['name']   = $array['name'];
        $i++;
    }
    xoops_cp_header();
    $form = new XoopsThemeForm(_MD_PLADD,'','main.php');
    $selectPolyStart = new XoopsFormSelect(_MD_PLID1, 'lid');//Start point
    //$selectPolyStart->addOption('','------');
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
         $selectPolyStart->addOption($entry[$i]['id'],$entry[$i]['title']);
    }
    $form->addElement($selectPolyStart);
    $selectPolyEnd = new XoopsFormSelect(_MD_PLID2, 'lid2');
    $form->addElement($selectPolyEnd);
    //$selectPolyEnd->addOption('','------');
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        $selectPolyEnd->addOption($entry[$i]['id'],$entry[$i]['title']);
    }
    $selectCategory = new XoopsFormSelect(_MD_CATEGORYC,'map_id');
    $count_msg = count($centry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        $selectCategory->addOption($centry[$i]['map_id'],$centry[$i]['name']);
    }
    $form->addElement($selectCategory);
    $form->addElement(new XoopsFormHidden('fct','mypoints'));
    $form->addElement(new XoopsFormHidden('op','plInsert'));
    $form->addElement(new XoopsFormButton('','post',_MD_ADD,'submit'));
    $form->render();
    echo $form->display();
    xoops_cp_footer();
    }else{//???
         redirect_header("index.php",1,_MD_NOPOINTS);
    }
}

//Insert the polyline into the DB

function plInsert()
{
    global $xoopsConfig, $xoopsDB, $myts, $xoopsUser, $xoopsModule, $eh;
    $point_id1 = $_POST["lid"];
    $point_id2 = $_POST["lid2"];
    $map_id = $_POST["map_id"];
    $errormsg = '';
    // Check if point exists
    if ( $point_id1 == "" | $point_id2 == "") {
        $errormsg .= "<h4 style='color: #ff0000'>";
        $errormsg .= _MD_ERRORPOINT."</h4>";
        $error =1;
    }

    if ( $error == 1 ) {
        xoops_cp_header();
        echo $errormsg;
        xoops_cp_footer();
        exit();
    }
    $sql = "INSERT INTO ".$xoopsDB->prefix("gmap_pl")." (`map_id` , `point_id1` , `point_id2`, `active`) VALUES ('$map_id', '$point_id1', '$point_id2', '1')";
    $xoopsDB->query($sql) or $eh->show("0013");
    redirect_header("main.php?op=plMod",1,_MD_NEWPLADDED);
}

//modify a polyline select page

function plMod()
{
    global $xoopsDB;
    $result2 = $xoopsDB->query("select count(*) from ".$xoopsDB->prefix("gmap_pl")."");
    list($numrows2) = $xoopsDB->fetchRow($result2);
    if ( $numrows2 > 0 ) {
        $result1 = $xoopsDB->query("select * from ".$xoopsDB->prefix("gmap_pl")."");
        $i = 0;
        while ($array = $xoopsDB->fetchArray($result1)) {
        $entry[$i]['id']   = $array['id'];
        $entry[$i]['point_id1']   = $array['point_id1'];
        $result3 = $xoopsDB->query("select title from ".$xoopsDB->prefix("gmap_points")." WHERE id = ".$entry[$i]['point_id1']."");
        list($title) = $xoopsDB->fetchRow($result3);
        $entry[$i]['point_name1'] = $title;
        $entry[$i]['point_id2']   = $array['point_id2'];
        $result4 = $xoopsDB->query("select title from ".$xoopsDB->prefix("gmap_points")." WHERE id = ".$entry[$i]['point_id2']."");
        list($title) = $xoopsDB->fetchRow($result4);
        $entry[$i]['point_name2']= $title;
        $i++;
         }
    xoops_cp_header();
    echo "<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_MODPL."</th></tr>";
    echo "<form method=get action=\"main.php\">\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_PLID."</td>";
    echo "<td class='even'><select size='1' name='lid'>";
    echo "<option value=' '>------</option>";
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        echo "<option value='".$entry[$i]['id']."'>".$entry[$i]['point_name1']." -> ".$entry[$i]['point_name2']."</option>";
    }
    echo "</select></td></tr>\n";
    echo "<input type=hidden name=fct value=mypoints>\n";
    echo "<input type=hidden name=op value=modPl><br /><br />\n";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form></td></tr></table>";
    xoops_cp_footer();
    }else{
         redirect_header("index.php",1,_MD_NOPOINTS);
    }
     
}

// Modify the polyline page

function modPl()
{
    global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig;
    $lid = $_GET['lid'];
    $result = $xoopsDB->query("select * from ".$xoopsDB->prefix("gmap_pl")." where id=$lid") or $eh->show("0013");
    list($id, $map_id, $point_id1, $point_id2) = $xoopsDB->fetchRow($result);
    $result1 = $xoopsDB->query("select map_id, name from ".$xoopsDB->prefix("gmap_category")."");
        $i = 0;
    while ($array = $xoopsDB->fetchArray($result1)) {
        $centry[$i]['map_id']   = $array['map_id'];
        $centry[$i]['name']   = $array['name'];
        $i++;
    }
    $result2 = $xoopsDB->query("select id, title from ".$xoopsDB->prefix("gmap_points")."");
    $i = 0;
        while ($array = $xoopsDB->fetchArray($result2)) {
        $pentry[$i]['id']   = $array['id'];
        $pentry[$i]['title']   = $array['title'];
        $i++;
         }
    xoops_cp_header();
    echo "<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_MODPL."</th></tr>";
    echo "<form method=post action=\"index.php\">\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_PLID1."</td>";
    echo "<td class='even'><select size='1' name='lid'>";
    echo "<option value=' '>------</option>";
    $count_msg = count($pentry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
    if ( $point_id1 == $pentry[$i]['id'] ) {
            $opt_selected = "selected='selected'";
        }else{
            $opt_selected = "";
        }
        echo "<option value='".$pentry[$i]['id']."' $opt_selected>".$pentry[$i]['title']."</option>";
    }
    echo "</select></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_PLID2."</td>";
    echo "<td class='even'><select size='1' name='lid2'>";
    echo "<option value=' '>------</option>";
    $count_msg = count($pentry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
    if ( $point_id2 == $pentry[$i]['id'] ) {
            $opt_selected = "selected='selected'";
        }else{
            $opt_selected = "";
        }
        echo "<option value='".$pentry[$i]['id']."' $opt_selected>".$pentry[$i]['title']."</option>";
    }
    echo "</select></td></tr>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_CATEGORYC."</td>";
    echo "<td class='even'><select size='1' name='map_id'>";
    $count_msg = count($centry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
    if ( $map_id == $centry[$i]['map_id'] ) {
            $opt_selected = "selected='selected'";
        }else{
            $opt_selected = "";
        }
        echo "<option value='".$centry[$i]['map_id']."' $opt_selected>".$centry[$i]['name']."</option>";
    }
    echo "</select></td></tr>\n";
    echo "<input type=hidden name=fct value=mypoints>\n";
    echo "<input type=hidden name=id value=$lid></input>\n";
    echo "<input type=hidden name=op value=modPlS><br /><br />\n";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><table><tr><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form></td>";
    echo "<td class='even'>".myTextForm("main.php?op=delPl&lid=".$lid , _MD_DELETE)."</td>";
    echo "<td class='even'>".myTextForm("main.php?op=plMod", _MD_CANCEL)."</td>";
    echo "</tr></table></td></tr></table>";
    xoops_cp_footer();
}

//submit the polyline to the DB

function modPlS()
{
    global $xoopsDB, $myts, $eh;
    $point_id1 = $_POST["lid"];
    $point_id2 = $_POST["lid2"];
    $map_id = $_POST["map_id"];
    $xoopsDB->query("update ".$xoopsDB->prefix("gmap_pl")." set map_id='$map_id', point_id1='$point_id1', point_id2='$point_id2', active=2 where id=".$_POST_['id']."")  or $eh->show("0013");
    redirect_header("index.php",1,_MD_DBUPDATED);
    exit();
}

//Delete a polyline

function delPl()
{
    global $xoopsDB, $eh, $xoopsModule;
    $sql = sprintf("DELETE FROM %s WHERE id = %u", $xoopsDB->prefix("gmap_pl"), $_GET['lid']);
    $xoopsDB->query($sql) or $eh->show("0013");
    redirect_header("index.php",1,_MD_POINTDELETED);
    exit();
}

//Order a Category

function catOrder()
{
    global $xoopsDB,$myts, $eh;
    $entry=array();
    $result = $xoopsDB->query("select `map_id`, `name`, `order` from ".$xoopsDB->prefix("gmap_category")." ORDER BY `order` ASC") or $eh->show("0013");
    $i = 0;
    while ($array = $xoopsDB->fetchArray($result)) {
        $entry[$i]['id']   = $array['map_id'];
        $entry[$i]['name']   = $array['name'];
        $entry[$i]['order']   = $array['order'];
        $i++;
    }

    if($i == 0){
        redirect_header("index.php",1,_MD_NOCATS);
    }else{
        xoops_cp_header();
        $form = new XoopsThemeForm(_MD_CATORDER,'form','main.php');
        $count_msg = count($entry);
        $form->addElement(new XoopsFormHidden('count',$count_msg));
        $form->addElement(new XoopsFormHidden('op','catOrderS'));
        for ( $i = 0; $i < $count_msg; $i++ ) {
            //TODO: length
            $form->addElement(new XoopsFormText($entry[$i]['name'],'order'.$i,25,25,$entry[$i]['order']));
            $form->addElement(new XoopsFormHidden('id'.$i,$entry[$i]['id']));
        }
        $form->addElement(new XoopsFormButton('','post',_MD_MODIFY,'submit'));
        $form->render();
        echo $form->display();
        xoops_cp_footer();
    }
}

//Order a Category into the DB

function catOrderS()
{
    global $xoopsDB, $myts, $eh;
    $count_msg = intval($_POST["count"]);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        $id = intval($_POST["id".$i]);
        $order = intval($_POST["order".$i]);
        $xoopsDB->query("update ".$xoopsDB->prefix("gmap_category")." set `order`='$order' where map_id='$id'")  or $eh->show("0013");
    }
    redirect_header("index.php",1,_MD_DBUPDATED);
    exit();
}

//Order a Point

function pointOrder($fct)
{
    global $xoopsDB,$myts, $eh;
    if($fct == 'pointOrderS' & isset($_POST['modify']) & ($_POST['map_id'] != -1)){
        pointOrderS();
    }else{
        $result='';
        $entry=array();
        $centry=array();
        if (isset($_POST['map_id'])){
            $map_id = $_POST['map_id'];
            $result = $xoopsDB->query("select `id`, `title`, `order` from ".$xoopsDB->prefix("gmap_points")." WHERE `map_id` = ".$map_id." ORDER BY `order` ASC") or $eh->show("0013");
        }
        $i = 0;
            while ($array = $xoopsDB->fetchArray($result)) {
            $entry[$i]['id']   = $array['id'];
            $entry[$i]['title']   = $array['title'];
            $entry[$i]['order']   = $array['order'];
            $i++;
            }
        $result1 = $xoopsDB->query("select map_id, name from ".$xoopsDB->prefix("gmap_category")."");
            $i = 0;
        while ($array = $xoopsDB->fetchArray($result1)) {
            $centry[$i]['map_id']   = $array['map_id'];
            $centry[$i]['name']   = $array['name'];
            $i++;
        }
        if(count($centry) == 0){
            redirect_header("index.php",1,_MD_NOPOINTS);
        }else{
            $count_msg = count($entry);
            xoops_cp_header();
            $form = new XoopsThemeForm(_MD_POINTORDER,'','main.php?op=pointOrder');//
            $select = new XoopsFormSelect(_MD_CATEGORYC,'map_id');
            $select->setExtra('onchange="JavaScript:submit()"');
            $count_msg2 = count($centry);
            $select->addOption('-1','------');
            for ( $i = 0; $i < $count_msg2; $i++ ) {
                $select->addOption($centry[$i]['map_id'],$centry[$i]['name']);
                if ($map_id == $centry[$i]['map_id'] ) {
                    $select->setValue($centry[$i]['map_id'],$centry[$i]['name']);
                }
            }
            $form->addElement($select);
            $form->addElement(new XoopsFormHidden('count',$count_msg));
            $form->addElement(new XoopsFormHidden('fct','pointOrderS'));//TODO:

            for ( $i = 0; $i < $count_msg; $i++ ) {
                $form->addElement(new XoopsFormText($entry[$i]['title'],'order'.$i,50,250,$entry[$i]['order']));
                $form->addElement(new XoopsFormHidden('id'.$i,$entry[$i]['id']));
            }
            $form->addElement(new XoopsFormButton('', 'modify', _MD_MODIFY, 'submit'));
            $form->render();
            echo $form->display();
            xoops_cp_footer();
        }
    }
}

//Submit the ordered point to the DB

function pointOrderS()
{
   global $xoopsDB, $myts, $eh;

    $count_msg = $_POST["count"];
    for ( $i = 0; $i < $count_msg; $i++ ) {
        $id = $_POST["id".$i];
        $order = $_POST["order".$i];
        $xoopsDB->query("update ".$xoopsDB->prefix("gmap_points")." set `order`='$order' where id='$id'")  or $eh->show("0013");
    }
    redirect_header("index.php",1,_MD_DBUPDATED);
    exit();
}

if(!isset($_POST['op'])) {
    $op = isset($_GET['op']) ? $_GET['op'] : 'main';
} else {
    $op = $_POST['op'];
}
if(!isset($_POST['fct'])) {
    $fct = isset($_GET['fct']) ? $_GET['fct'] : 'main';
} else {
    $fct = $_POST['fct'];
}
switch ($op) {
case "catAdd":
    catAdd();
    break;
case "catInsert":
    catInsert();
    break;
case "pointAdd":
    pointAdd();
    break;
case "pointInsert":
    pointInsert();
    break;
case "plAdd":
    plAdd();
    break;
case "plInsert":
    plInsert();
    break;
case "plMod":
    plMod();
    break;
case "modPl":
    modPl();
    break;
case "modPlS":
    modPlS();
    break;
case "pointMod":
    switch($fct){
    case "modPoint":
        modPoint();
        break;
    case "modPointS":
        modPointS();
        break;
    default:
        pointMod();
    break;
    }
    break;
case "catMod":
    catMod($fct);
    break;
//case "modCat":
    //modCat();
    //break;
case "modCatS":
    modCatS();
    break;
//case "delCat":
  //  delCat();
    //break;
case "delPoint":
    delPoint();
    break;
case "delPl":
    delPl();
    break;
case "pointDel":
    pointDel();
    break;
case "pointOrder":
    pointOrder($fct);
    break;
//case "pointOrderS":
//    pointOrderS();
//    break;
case "catOrder":
    catOrder();
    break;
case "catOrderS":
    catOrderS();
    break;
case 'main':
default:
    $op = 'main';
    main();
    break;
}
