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
if ( file_exists("../language/".$xoopsConfig['language']."/main.php") ) {
    include "../language/".$xoopsConfig['language']."/main.php";
} else {
    include "../language/english/main.php";
}
//include '../include/functions.php';
include_once XOOPS_ROOT_PATH.'/class/xoopstree.php';
include_once XOOPS_ROOT_PATH."/class/xoopslists.php";
include_once XOOPS_ROOT_PATH."/include/xoopscodes.php";
include_once XOOPS_ROOT_PATH.'/class/module.errorhandler.php';
$myts = MyTextSanitizer::getInstance();
$eh = new ErrorHandler;

// Display the main contents

function main()
{
    global $xoopsDB, $xoopsModule;
    xoops_cp_header();

    xoops_cp_footer();
}

// Add in a new category

function catAdd()
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig;
    xoops_cp_header();

    //Display the map
    echo"<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script >\n";
    echo"<script type=\"text/javascript\">\n";
    echo"//&lt;![CDATA[ \n";
    echo"function loadMap(){\n";
    //echo"function GPoint2(x,y) { return new GLatLng(y,x); }\n";
    echo"var map = new GMap2(document.getElementById(\"map\"));\n";
    echo"map.setCenter(new GLatLng(0, 0), 1);\n";
    echo"map.addControl(new GLargeMapControl());\n";
    echo"map.addControl(new GMapTypeControl());\n";
    echo"GEvent.addListener(map, \"click\", function(overlay, point){\n";
    echo"map.clearOverlays();\n";
    //echo"GEvent.addListener(map, 'zoomend', function() {\n";
    echo"document.getElementById(\"zoom\").value = map.getZoom();\n";
    //echo"});\n";
    echo"if (point) {\n";
    echo"map.addOverlay(new GMarker(point,{draggable:true}));\n";
    echo"map.panTo(point);\n";
    echo"lat = point.y;\n";
    echo"lon = point.x;\n";
    echo"document.getElementById(\"lat\").value = lat;\n";
    echo"document.getElementById(\"lon\").value = lon;\n";
    echo"}\n";
    echo"});\n";
    echo"}\n";
    echo"// arrange for our onload handler to 'listen' for onload events\n";
    echo"if (window.attachEvent) {\n";
    echo"window.attachEvent(\"onload\", function() {\n";
    echo"loadMap();	// Internet Explorer\n";
    echo"});\n";
    echo"} else {\n";
    echo"window.addEventListener(\"load\", function() {\n";
    echo"loadMap(); // Firefox and standard browsers\n";
    echo"}, false);\n";
    echo"}\n";
    echo"//]]&gt;\n";
    echo"</script >\n";

    echo"<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_ADDNEWCAT."</th></tr>";
        echo "<form method=post action=main.php>\n";
        echo "<tr valign='top' align='center'><td class='head' colspan='2'><div id='map' style='height: 400px'></div ></td></tr>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_CAT."</td>";
        echo "<td class='even'><input type=text name=title size=50 maxlength=100></td></tr>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_CATLONGITUDE."</td>";
        echo "<td class='even'><input id=lon type=text name=lon size=50 maxlength=250></td></tr>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_CATLATITUDE."</td>";
        echo "<td class='even'><input id=lat type=text name=lat size=50 maxlength=250></td></tr>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_ZOOM."</td>";
        echo "<td class='even'><input id=zoom type=text name=zoom size=50 maxlength=250></td></tr>";
    echo  "<input type=\"hidden\" name=\"op\" value=\"catInsert\"></input>";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_ADD."' accesskey=\"s\" /></form></td></tr></table>";
    xoops_cp_footer();
}

//Insert the category into the DB

function catInsert()
{
    global $xoopsConfig, $xoopsDB, $myts, $xoopsUser, $xoopsModule, $eh;
    $title = $myts->makeTboxData4Save($_POST["title"]);
    $lat = $myts->makeTboxData4Save($_POST["lat"]);
    $lon = $myts->makeTboxData4Save($_POST["lon"]);
    $zoom = $myts->makeTboxData4Save($_POST["zoom"]);
    $errormsg = '';
    // Check if Title exist
    if ( $title == "" ) {
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
    if ( $newid == 0 ) {
        $newid = $xoopsDB->getInsertId();
    }
        redirect_header("main.php?op=catMod",1,_MD_NEWCATADDED);
}

//Modify a category select page to choose which cat to mod

function catMod()
{
    
    global $xoopsDB;
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
    echo"<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_MODCAT."</th></tr>";
        echo "<form method=get action=\"main.php\">\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_CATID."</td>";
    echo "<td class='even'><select size='1' name='lid'>";
    echo "<option value=' '>------</option>";
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        echo "<option value='".$entry[$i]['map_id']."'>".$entry[$i]['name']."</option>";
    }
    echo "</select></td></tr>";
    echo "<input type=hidden name=fct value=mypoints>\n";
    echo "<input type=hidden name=op value=modCat><br /><br />\n";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form></td></tr></table>";
    xoops_cp_footer();
    }else{
         redirect_header("index.php",1,_MD_NOCATS);
    }
     
}

//Modify the selected category

function modCat()
{
    global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig;
    $lid = $_GET['lid'];
    $result = $xoopsDB->query("select map_id, lat, lon, zoom, name from ".$xoopsDB->prefix("gmap_category")." where map_id=$lid") or $eh->show("0013");
    list($map_id, $lat, $lon, $zoom, $name) = $xoopsDB->fetchRow($result);
    $name = $myts->makeTboxData4Edit($name);
    $lat = $myts->makeTboxData4Edit($lat);
    $lon = $myts->makeTboxData4Edit($lon);
    $zoom = $myts->makeTboxData4Edit($zoom);
    xoops_cp_header();

    //Display the map

    echo"<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script >\n";
    echo"<script type=\"text/javascript\">\n";
    echo"//&lt;![CDATA[ \n";
    echo"function loadMap(){\n";
    //echo"function GPoint2(x,y) { return new GLatLng(y,x); }\n";
    echo"var map = new GMap2(document.getElementById(\"map\"));\n";
    echo"map.addControl(new GLargeMapControl());\n";
    echo"map.addControl(new GMapTypeControl());\n";
    echo"map.setCenter(new GLatLng(".$lat.",".$lon."), ".$zoom.");\n";
    echo"map.addOverlay(new GMarker(new GLatLng(".$lat.",".$lon."),{draggable:true}));\n";
    echo"GEvent.addListener(map, \"click\", function(overlay, point){\n";
    echo"map.clearOverlays();\n";
    //echo"GEvent.addListener(map, 'zoomend', function() {\n";
    echo"document.getElementById(\"zoom\").value = map.getZoom();\n";
    //echo"});\n";
    echo"if (point) {\n";
    echo"map.addOverlay(new GMarker(point));\n";
    echo"map.panTo(point);\n";
    echo"lat = point.y;\n";
    echo"lon = point.x;\n";
    echo"document.getElementById(\"lat\").value = lat;\n";
    echo"document.getElementById(\"lon\").value = lon;\n";
    echo"}\n";
    echo"});\n";
    echo"}\n";
    echo"// arrange for our onload handler to 'listen' for onload events\n";
    echo"if (window.attachEvent) {\n";
    echo"window.attachEvent(\"onload\", function() {\n";
    echo"loadMap();	// Internet Explorer\n";
    echo"});\n";
    echo"} else {\n";
    echo"window.addEventListener(\"load\", function() {\n";
    echo"loadMap(); // Firefox and standard browsers\n";
    echo"}, false);\n";
    echo"}\n";
    echo"//]]&gt;\n";
    echo"</script >\n";

    echo"<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_MODCAT."</th></tr>";
        echo "<form method=post action=\"main.php\">\n";
    echo "<tr valign='top' align='center'><td class='head' colspan='2'><div id='map' style='height: 400px'></div ></td></tr>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_CAT."</td>";
    echo "<td class='even'><input type=text name=name value=\"$name\" size=50 maxlength=100></input></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_LONGITUDE."</td>";
    echo "<td class='even'><input type=text id=lon name=lon value=\"$lon\" size=50 maxlength=100></input></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_LATITUDE."</td>";
    echo "<td class='even'><input type=text id=lat name=lat value=\"$lat\" size=50 maxlength=100></input></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_ZOOM."</td>";
    echo "<td class='even'><input type=text name=zoom id=zoom value=\"$zoom\" size=50 maxlength=100></input></td></tr>\n";
    echo "<input type=hidden name=lid value=$map_id></input>\n";
    echo "<input type=hidden name=op value=modCatS>\n";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><table><tr><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form></td>";
    echo "<td class='even'>".myTextForm("main.php?op=delCat&lid=".$lid , _MD_DELETE)."</td>";
    echo "<td class='even'>".myTextForm("main.php?op=catMod", _MD_CANCEL)."</td>";
    echo "</tr></table></td></tr></table>";
        xoops_cp_footer();
}

// Insert the modified category into the DB
function modCatS()
{
    global $xoopsDB, $myts, $eh;
        $name = $myts->makeTboxData4Save($_POST["name"]);
        $lat = $myts->makeTboxData4Save($_POST["lat"]);
        $lon = $myts->makeTboxData4Save($_POST["lon"]);
        $zoom = $myts->makeTboxData4Save($_POST["zoom"]);
        $xoopsDB->query("update ".$xoopsDB->prefix("gmap_category")." set name='$name', lat='$lat', lon='$lon',zoom='$zoom'where map_id=".$_POST['lid']."")  or $eh->show("0013");
        redirect_header("index.php",1,_MD_DBUPDATED);
    exit();
}

//Add in a new point
function pointAdd()
{
    global $xoopsDB, $xoopsModule, $xoopsModuleConfig;
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
        echo"<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script >\n";
        echo"<script type=\"text/javascript\">\n";
        echo"//&lt;![CDATA[ \n";
        echo"function loadMap(){\n";
        //echo"function GPoint2(x,y) { return new GLatLng(y,x); }\n";
        echo"var map = new GMap2(document.getElementById(\"map\"));\n";
        echo"map.addControl(new GLargeMapControl());\n";
        echo"map.addControl(new GMapTypeControl());\n";
        echo"map.setCenter(new GLatLng(0, 0), 1);\n";
        echo"GEvent.addListener(map, \"click\", function(overlay, point){\n";
        echo"map.clearOverlays();\n";
        //echo"GEvent.addListener(map, 'zoomend', function() {\n";
        echo"document.getElementById(\"zoom\").value = map.getZoom();\n";
        //echo"});\n";
        echo"if (point) {\n";
        echo"map.addOverlay(new GMarker(point,{draggable:true}));\n";
        echo"map.panTo(point);\n";
        echo"lat = point.y;\n";
        echo"lon = point.x;\n";
        echo"document.getElementById(\"lat\").value = lat;\n";
        echo"document.getElementById(\"lon\").value = lon;\n";
        echo"}\n";
        echo"});\n";
        echo"}\n";
        echo"// arrange for our onload handler to 'listen' for onload events\n";
        echo"if (window.attachEvent) {\n";
        echo"window.attachEvent(\"onload\", function() {\n";
        echo"loadMap();	// Internet Explorer\n";
        echo"});\n";
        echo"} else {\n";
        echo"window.addEventListener(\"load\", function() {\n";
        echo"loadMap(); // Firefox and standard browsers\n";
        echo"}, false);\n";
        echo"}\n";
        echo"//]]&gt;\n";
        echo"</script >\n";

        echo"<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_ADDNEWPOINT."</th></tr>";
            echo "<form method=post action=\"main.php\">\n";
        echo "<tr valign='top' align='center'><td class='head' colspan='2'><div id='map' style='height: 400px'></div ></td></tr>";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_LOCATION."</td>";
        echo "<td class='even'><input type=text name=title size=50 maxlength=100></td><tr>\n";

        echo "<tr valign='top' align='left'><td class='head'>"._MD_LONGITUDE."</td>";
            echo "<td class='even'><input id=lon type=text name=lon size=50 maxlength=250></td><tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_LATITUDE."</td>";
            echo "<td class='even'><input id=lat type=text name=lat size=50 maxlength=250></td><tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_ZOOM."</td>";
            echo "<td class='even'><input id=zoom type=text name=zoom size=50 maxlength=250></td><tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_CATEGORYC."</td>";
            echo "<td class='even'><select size='1' name='category'>";
        $count_msg = count($entry);
        for ( $i = 0; $i < $count_msg; $i++ ) {
            echo "<option value='".$entry[$i]['map_id']."'>".$entry[$i]['name']."</option>";
        }
        echo "</select></td></tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_DESCRIPTIONC."</td>";
        echo "<td class='even'>";
        xoopsCodeTarea("description",60,8);
        xoopsSmilies("description");
            echo "</td></tr>\n";
        echo "<input type=\"hidden\" name=\"op\" value=\"pointInsert\"></input>\n";
        echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_ADD."' accesskey=\"s\" /></form></td></tr></table>";
        xoops_cp_footer();
    }else{
        redirect_header("main.php?op=catAdd",1,_MD_NOCATSADD);
    }
}

//Insert the new point into the DB
function pointInsert()
{
    global $xoopsConfig, $xoopsDB, $myts, $xoopsUser, $xoopsModule, $eh;
    $title = $myts->makeTboxData4Save($_POST["title"]);
    $lat = $myts->makeTboxData4Save($_POST["lat"]);
    $lon = $myts->makeTboxData4Save($_POST["lon"]);
    $zoom = $myts->makeTboxData4Save($_POST["zoom"]);
    $category = $myts->makeTboxData4Save($_POST["category"]);
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
        echo "<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_MODPOINT."</th></tr>";
        echo "<form method=get action=\"main.php\">\n";
            echo "<tr valign='top' align='left'><td class='head'>"._MD_POINTID."</td>";
        echo "<td class='even'><select size='1' name='lid'>";
        echo "<option value=' '>------</option>";
        $count_msg = count($entry);
        for ( $i = 0; $i < $count_msg; $i++ ) {
            echo "<option value='".$entry[$i]['id']."'>".$entry[$i]['title']."</option>";
        }
        echo "</select></td></tr>\n";
        echo "<input type=hidden name=fct value=mypoints>\n";
        echo "<input type=hidden name=op value=modPoint><br /><br />\n";
        echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form></td></tr></table>";
        xoops_cp_footer();
    }else{
        redirect_header("index.php",1,_MD_NOPOINTS);
         }
     
}

//Modify the point

function modPoint()
{
    global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig;
    $lid = $_GET['lid'];
    $result = $xoopsDB->query("select id, lat, lon, zoom, map_id, title, html from ".$xoopsDB->prefix("gmap_points")." where id=$lid") or $eh->show("0013");
    list($id, $lat, $lon, $zoom, $map_id, $title, $html) = $xoopsDB->fetchRow($result);
    $title = $myts->makeTboxData4Edit($title);
    $lat = $myts->makeTboxData4Edit($lat);
    $lon = $myts->makeTboxData4Edit($lon);
    $zoom = $myts->makeTboxData4Edit($zoom);
    $map_id = $myts->makeTboxData4Edit($map_id);
    $GLOBALS['html'] = $myts->makeTareaData4Edit($html);
    $result1 = $xoopsDB->query("select map_id, name from ".$xoopsDB->prefix("gmap_category")."");
        $i = 0;
    while ($array = $xoopsDB->fetchArray($result1)) {
        $entry[$i]['map_id']   = $array['map_id'];
        $entry[$i]['name']   = $array['name'];
        $i++;
    }
    xoops_cp_header();

    //Display Map

    echo"<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script >\n";
    echo"<script type=\"text/javascript\">\n";
    echo"//&lt;![CDATA[ \n";
    echo"function loadMap(){\n";
    //echo"function GPoint2(x,y) { return new GLatLng(y,x); }\n";
    echo"var map = new GMap2(document.getElementById(\"map\"));\n";
    echo"map.addControl(new GLargeMapControl());\n";
    echo"map.addControl(new GMapTypeControl());\n";
    echo"map.setCenter(new GLatLng(".$lat.",".$lon."), ".$zoom.");\n";
    echo"map.addOverlay(new GMarker(new GLatLng(".$lat.",".$lon."),{draggable:true}));\n";
    echo"GEvent.addListener(map, \"click\", function(overlay, point){\n";
    echo"map.clearOverlays();\n";
    //echo"GEvent.addListener(map, 'zoomend', function() {\n";
    echo"document.getElementById(\"zoom\").value = map.getZoom();\n";
    //echo"});\n";
    echo"if (point) {\n";
    echo"map.addOverlay(new GMarker(point));\n";
    echo"map.panTo(point);\n";
    echo"lat = point.y;\n";
    echo"lon = point.x;\n";
    echo"document.getElementById(\"lat\").value = lat;\n";
    echo"document.getElementById(\"lon\").value = lon;\n";
    echo"}\n";
    echo"});\n";
    echo"}\n";
    echo"// arrange for our onload handler to 'listen' for onload events\n";
    echo"if (window.attachEvent) {\n";
    echo"window.attachEvent(\"onload\", function() {\n";
    echo"loadMap();	// Internet Explorer\n";
    echo"});\n";
    echo"} else {\n";
    echo"window.addEventListener(\"load\", function() {\n";
    echo"loadMap(); // Firefox and standard browsers\n";
    echo"}, false);\n";
    echo"}\n";
    echo"//]]&gt;\n";
    echo"</script >\n";
    
    echo"<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_MODPOINT."</th></tr>";
        echo "<form method=post action=\"main.php\">\n";
    echo "<tr valign='top' align='center'><td class='head' colspan='2'><div id='map' style='height: 400px'></div ></td></tr>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_LOCATION."</td>";
    echo "<td class='even'><input type=text name=title value=\"$title\" size=50 maxlength=100></input></td></tr>\n";

    echo "<tr valign='top' align='left'><td class='head'>"._MD_LONGITUDE."</td>";
    echo "<td class='even'><input type=text id=lon name=lon value=\"$lon\" size=50 maxlength=100></input></td></tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_LATITUDE."</td>";
    echo "<td class='even'><input type=text id=lat name=lat value=\"$lat\" size=50 maxlength=100></input></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_ZOOM."</td>";
    echo "<td class='even'><input type=text name=zoom id=zoom value=\"$zoom\" size=50 maxlength=100></input></td></tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_CATEGORYC."</td>";
    echo "<td class='even'><select size='1' name='map_id'>";
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        if ( $map_id == $entry[$i]['map_id'] ) {
            $opt_selected = "selected='selected'";
        }else{
            $opt_selected = "";
        }
        echo "<option value='".$entry[$i]['map_id']."' $opt_selected >".$entry[$i]['name']."</option>";
    }
    echo "</select></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_DESCRIPTIONC."</td>";
    echo "<td class='even'>";
    xoopsCodeTarea("html",60,8);
    xoopsSmilies("html");
        echo "</td></tr>\n";
    echo "<input type=hidden name=lid value=$id></input>\n";
        echo "<input type=hidden name=op value=modPointS>\n";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><table><tr><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form>";
    echo "<td class='even'>".myTextForm("main.php?op=delPoint&lid=".$lid , _MD_DELETE)."</td>";
    echo "<td class='even'>".myTextForm("main.php?op=pointMod", _MD_CANCEL)."</td>";
    echo "</tr></table></td></tr></table>";
        xoops_cp_footer();
}

//Insert the modified point into the DB

function modPointS()
{
    global $xoopsDB, $myts, $eh;
        $title = $myts->makeTboxData4Save($_POST["title"]);
        $map_id = $_POST["map_id"];
        $lat = $myts->makeTboxData4Save($_POST["lat"]);
        $lon = $myts->makeTboxData4Save($_POST["lon"]);
        $zoom = $myts->makeTboxData4Save($_POST["zoom"]);
        $html = $myts->makeTareaData4Save($_POST["html"]);
        $xoopsDB->query("update ".$xoopsDB->prefix("gmap_points")." set map_id='$map_id', title='$title', lat='$lat', lon='$lon',zoom='$zoom',html='$html', status=2, date=".time()." where id=".$_POST['lid']."")  or $eh->show("0013");
        redirect_header("index.php",1,_MD_DBUPDATED);
    exit();
}

//Delete a Category

function delCat()
{
    global $xoopsDB, $eh, $xoopsModule;
    $sql = sprintf("DELETE FROM %s WHERE map_id = %u", $xoopsDB->prefix("gmap_category"), $_GET['lid']);
    $xoopsDB->query($sql) or $eh->show("0013");
    redirect_header("index.php",1,_MD_CATDELETED);
    exit();
}

//Delete a point

function delPoint()
{
    global $xoopsDB, $eh, $xoopsModule;
    $sql = sprintf("DELETE FROM %s WHERE id = %u", $xoopsDB->prefix("gmap_points"), $_GET['lid']);
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
    echo "<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_PLADD."</th></tr>";
    echo "<form method=post action=\"main.php\">\n";
        echo "<tr valign='top' align='left'><td class='head'>"._MD_PLID1."</td>";
    echo "<td class='even'><select size='1' name='lid'>";
    echo "<option value=' '>------</option>";
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        echo "<option value='".$entry[$i]['id']."'>".$entry[$i]['title']."</option>";
    }
    echo "</select></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_PLID2."</td>";
    echo "<td class='even'><select size='1' name='lid2'>";
    echo "<option value=' '>------</option>";
    $count_msg = count($entry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        echo "<option value='".$entry[$i]['id']."'>".$entry[$i]['title']."</option>";
    }
    echo "</select></td></tr>\n";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_CATEGORYC."</td>";
    echo "<td class='even'><select size='1' name='map_id'>";
    $count_msg = count($centry);
    for ( $i = 0; $i < $count_msg; $i++ ) {
        echo "<option value='".$centry[$i]['map_id']."'>".$centry[$i]['name']."</option>";
    }
    echo "</select></td></tr>\n";
    echo "<input type=hidden name=fct value=mypoints>\n";
    echo "<input type=hidden name=op value=plInsert><br /><br />\n";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_ADD."' accesskey=\"s\" /></form></td></tr></table>";
    xoops_cp_footer();
    }else{
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
xoops_cp_header();
    echo"<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_CATORDER."</th></tr>";
        echo "<form method=post action=main.php>";
    
    $count_msg = count($entry);
    echo "<input type=hidden name=count value='".$count_msg."'>";
    for ( $i = 0; $i < $count_msg; $i++ ) {
        echo "<tr valign='top' align='left'><td class='head'>".$entry[$i]['name']."</td><td class='even'><input type=text name=order".$i." value=".$entry[$i]['order']." size=3 maxlength=100></input></td></tr>\n";
        echo "<input type=hidden name=id".$i." value=".$entry[$i]['id'].">\n";
    }
echo "<input type=hidden name=op value=catOrderS>";
echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form></td></tr></table>";
    xoops_cp_footer();
}

//Order a Category into the DB

function catOrderS()
{
   global $xoopsDB, $myts, $eh;

    $count_msg = $_POST["count"];
    for ( $i = 0; $i < $count_msg; $i++ ) {
        $id = $_POST["id".$i];
        $order = $_POST["order".$i];
        $xoopsDB->query("update ".$xoopsDB->prefix("gmap_category")." set `order`='$order' where map_id='$id'")  or $eh->show("0013");
    }
    redirect_header("index.php",1,_MD_DBUPDATED);
    exit();
}

//Order a Point

function pointOrder()
{
    global $xoopsDB,$myts, $eh;
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
    xoops_cp_header();
    echo"<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>"._MD_POINTORDER."</th></tr>";
        echo "<form method=post action=main.php>";
    echo "<tr valign='top' align='left'><td class='head'>"._MD_CATEGORYC."</td>";
    $count_msg = count($entry);
    echo "<td class='even'><input type=hidden name=op value=pointOrder><select size='1' onchange=\"JavaScript:submit()\" name='map_id'>";
    echo "<option value=' '>------</option>";
    $count_msg2 = count($centry);
    for ( $i = 0; $i < $count_msg2; $i++ ) {
        if ( $map_id == $centry[$i]['map_id'] ) {
            $opt_selected = "selected='selected'";
        }else{
            $opt_selected = "";
        }
        echo "<option value='".$centry[$i]['map_id']."' $opt_selected >".$centry[$i]['name']."</option>";
    }
    echo "</select></td></tr></form>";
    echo "<form method=post action=main.php>";
    echo "<input type=hidden name=count value='".$count_msg."'>";
    for ( $i = 0; $i < $count_msg; $i++ ) {
        echo "<tr valign='top' align='left'><td class='head'>".$entry[$i]['title']."</td><td class='even'><input type=text name=order".$i." value=".$entry[$i]['order']." size=3 maxlength=100></input><input type=hidden name=id".$i." value=".$entry[$i]['id']."></td></tr>\n";
    }
    echo "<input type=hidden name=op value=pointOrderS>";
    echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='"._MD_MODIFY."' accesskey=\"s\" /></form></td></tr></table>";
        xoops_cp_footer();
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
    pointMod();
    break;
case "modPoint":
    modPoint();
    break;
case "modPointS":
    modPointS();
    break;
case "catMod":
    catMod();
    break;
case "modCat":
    modCat();
    break;
case "modCatS":
    modCatS();
    break;
case "delCat":
    delCat();
    break;
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
    pointOrder();
    break;
case "pointOrderS":
    pointOrderS();
    break;
case "catOrder":
    catOrder();
    break;
case "catOrderS":
    catOrderS();
    break;
case 'main':
default:
    main();
    break;
}
