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

include "../../mainfile.php";
include "includes/header.php";
include XOOPS_ROOT_PATH . "/header.php";

include_once XOOPS_ROOT_PATH . '/class/xoopstree.php';
include_once XOOPS_ROOT_PATH . "/class/xoopslists.php";
include_once XOOPS_ROOT_PATH . "/include/xoopscodes.php";
include_once XOOPS_ROOT_PATH . '/class/module.errorhandler.php';

$myts = MyTextSanitizer::getInstance();
$eh = new ErrorHandler;

global $xoopsUser;
if (is_object($xoopsUser)) {

// Display the main contents

    function main()
    {
        global $xoopsDB, $xoopsModule, $xoopsUser, $xoopsModuleConfig;
        if (is_object($xoopsUser)) {
            echo "<h4>" . _MD_TITLE . "</h4>";
            echo "<table width='100%' border='0' cellspacing='1' class='outer'>"
                . "<tr class=\"odd\"><td>";
            $uid = $xoopsUser->getVar('uid');
            $controle = $xoopsDB->query("select count(1) from " . $xoopsDB->prefix("gmap_points") . " WHERE `submitter`=" . $uid);
            $rescontrole = $xoopsDB->fetchRow($controle);
            $rescontrole2 = number_format($rescontrole[0]);
            $max = number_format($xoopsModuleConfig['maxpointsusers']);

            if ($rescontrole2 < $max) {
                echo " - <a href=managepoint.php?op=pointAdd>" . _MD_ADDNEWPOINT . "</a>";
                echo "<br/><br/>";} else {
                echo " - " . _MD_ALREADY;
                echo "<br/><br/>";
            }
            echo " - <a href=managepoint.php?op=pointMod>" . _MD_MODPOINT . "</a>";
            echo "<br/><br/>";
            echo " - <a href=index.php>" . _MD_BACKHOME . "</a>";
            $result = $xoopsDB->query("select count(*) from " . $xoopsDB->prefix("gmap_points"));
            list($numrows) = $xoopsDB->fetchRow($result);
            echo "<br /><br />";
            printf(_MD_THEREARE, $numrows);
            //echo "</div>";
            //echo "</div>";
            echo "</td></tr></table>";
        } else {
            echo "" . _MD_ERROR . "";
        }
    }

//Add in a new point
    function pointAdd()
    {
        global $xoopsDB, $xoopsModule, $xoopsModuleConfig, $myts;
        $result2 = $xoopsDB->query("select count(*) from " . $xoopsDB->prefix("gmap_category") . "");
        list($numrows2) = $xoopsDB->fetchRow($result2);
        if ($numrows2 > 0) {
            $result1 = $xoopsDB->query("select map_id, name from " . $xoopsDB->prefix("gmap_category") . "");
            $i = 0;
            while ($array = $xoopsDB->fetchArray($result1)) {
                $entry[$i]['map_id'] = intval($array['map_id']);
                $entry[$i]['name'] = $myts->htmlSpecialChars($array['name']);
                $i++;
            }

            echo "<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=" . $xoopsModuleConfig['api'] . "\" type=\"text/javascript\"></script >\n";
            echo "<script type=\"text/javascript\">\n";
            echo "//&lt;![CDATA[ \n";
            echo "function loadMap(){\n";
            //echo"function GPoint2(x,y) { return new GLatLng(y,x); }\n";
            echo "var map = new GMap2(document.getElementById(\"map\"));\n";
            echo "map.addControl(new GLargeMapControl());\n";
            echo "map.addControl(new GMapTypeControl());\n";
            echo "map.setCenter(new GLatLng(0, 0), 1);\n";
            echo "GEvent.addListener(map, \"click\", function(overlay, point){\n";
            echo "map.clearOverlays();\n";
            //echo"GEvent.addListener(map, \"zoom\", function() {\n";
            echo "document.getElementById(\"zoom\").value = map.getZoom();\n";
            //echo"});\n";
            echo "if (point) {\n";
            echo "map.addOverlay(new GMarker(point,{draggable:true}));\n";
            echo "map.panTo(point);\n";
            echo "lat = point.y;\n";
            echo "lon = point.x;\n";
            echo "document.getElementById(\"lat\").value = lat;\n";
            echo "document.getElementById(\"lon\").value = lon;\n";
            echo "}\n";
            echo "});\n";
            echo "}\n";
            echo "// arrange for our onload handler to 'listen' for onload events\n";
            echo "if (window.attachEvent) {\n";
            echo "window.attachEvent(\"onload\", function() {\n";
            echo "loadMap();	// Internet Explorer\n";
            echo "});\n";
            echo "} else {\n";
            echo "window.addEventListener(\"load\", function() {\n";
            echo "loadMap(); // Firefox and standard browsers\n";
            echo "}, false);\n";
            echo "}\n";
            echo "//]]&gt;\n";
            echo "</script >\n";

            echo "<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>" . _MD_ADDNEWPOINT . "</th></tr>";
            echo "<form method=post action=\"managepoint.php\">\n";
            echo "<tr valign='top' align='center'><td class='head' colspan='2'><div id='map' style='height: 400px'></div ></td></tr>";
            echo "<tr valign='top' align='left'><td class='head'>" . _MD_LOCATION . "</td>";
            echo "<td class='even'><input type=text name=title size=50 maxlength=100></td><tr>\n";

            echo "<tr valign='top' align='left'><td class='head'>" . _MD_LONGITUDE . "</td>";
            echo "<td class='even'><input id=lon type=text name=lon size=50 maxlength=250></td><tr>\n";
            echo "<tr valign='top' align='left'><td class='head'>" . _MD_LATITUDE . "</td>";
            echo "<td class='even'><input id=lat type=text name=lat size=50 maxlength=250></td><tr>\n";
            echo "<tr valign='top' align='left'><td class='head'>" . _MD_ZOOM . "</td>";
            echo "<td class='even'><input id=zoom type=text name=zoom size=50 maxlength=250></td><tr>\n";
            echo "<tr valign='top' align='left'><td class='head'>" . _MD_CATEGORYC . "</td>";
            echo "<td class='even'><select size='1' name='category'>";
            $count_msg = count($entry);
            for ($i = 0; $i < $count_msg; $i++) {
                echo "<option value='" . $entry[$i]['map_id'] . "'>" . $entry[$i]['name'] . "</option>";
            }
            echo "</select></td></tr>\n";
            echo "<tr valign='top' align='left'><td class='head'>" . _MD_DESCRIPTIONC . "</td>";
            echo "<td class='even'>";
            xoopsCodeTarea("description", 60, 8);
            xoopsSmilies("description");
            echo "</td></tr>\n";
            echo "<input type=\"hidden\" name=\"op\" value=\"pointInsert\"></input>\n";
            echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='" . _MD_ADD . "' accesskey=\"s\" /></form></td></tr></table>";
        } else {
            redirect_header("managepoint.php", 1, _MD_NOCATSADD);
        }
    }

//Insert the new point into the DB
    function pointInsert()
    {
        global $xoopsConfig, $xoopsDB, $myts, $xoopsUser, $xoopsModule, $eh;
        $title = $myts->addSlashes($myts->censorString($_POST['title']));
        $lat = floatval($_POST["lat"]);
        $lon = floatval($_POST["lon"]);
        $zoom = floatval($_POST["zoom"]);
        $category = intval($_POST["category"]);
        $description = $myts->addSlashes($myts->censorString($_POST["description"]));
        $submitter = intval($xoopsUser->uid());
        $date = time();
        $errormsg = '';
        // Check if Title exist
        if ($title == "") {
            $errormsg .= "<h4 style='color: #ff0000'>";
            $errormsg .= _MD_ERRORTITLE . "</h4>";
            $error = 1;
        }

        // Check if Description exist
        if ($description == "") {
            $errormsg .= "<h4 style='color: #ff0000'>";
            $errormsg .= _MD_ERRORDESC . "</h4>";
            $error = 1;
        }
        if ($error == 1) {
            //xoops_cp_header();
            redirect_header("managepoint.php?op=pointAdd", 1, $errormsg);
            //echo $errormsg;
            //xoops_cp_footer();
            exit();
        }
        list($order) = $xoopsDB->fetchRow($xoopsDB->query("SELECT MAX(`order`) FROM " . $xoopsDB->prefix("gmap_points") . " WHERE `map_id` = " . $category));
        $order++;
        $sql = "INSERT INTO " . $xoopsDB->prefix("gmap_points") . " ( `map_id` , `lat` , `lon` , `title` , `html` , `zoom` , `submitter` , `status` , `date`, `order`) VALUES ('$category', '$lat', '$lon', '$title', '$description', '$zoom','$submitter', '1', '$date', '$order')";
        $xoopsDB->query($sql) or $eh->show("0013");
        if ($newid == 0) {
            $newid = $xoopsDB->getInsertId();
        }
        redirect_header("managepoint.php?op=linksConfigMenu", 1, _MD_NEWPOINTADDED);
    }

//Modify a point select page to choose which point to mod

    function pointMod()
    {

        global $xoopsDB, $xoopsUser, $myts;

        //if (is_object($xoopsUser)){
        $uid = intval($xoopsUser->getVar('uid'));
        //}
        //else {
        //$groupuser[0] = 3;
        //$uid=XOOPS_GROUP_ANONYMOUS;
        //}
        $result2 = $xoopsDB->query("select count(*) from " . $xoopsDB->prefix("gmap_points") . "");
        list($numrows2) = $xoopsDB->fetchRow($result2);
        if ($numrows2 > 0) {
            $result1 = $xoopsDB->query("select id, title from " . $xoopsDB->prefix("gmap_points") . " where submitter=$uid");
            $i = 0;
            while ($array = $xoopsDB->fetchArray($result1)) {
                $entry[$i]['id'] = intval($array['id']);
                $entry[$i]['title'] = $myts->htmlSpecialChars($array['title']);
                $i++;
            }

            echo "<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>" . _MD_MODPOINT . "</th></tr>";
            echo "<form method=get action=\"managepoint.php\">\n";
            echo "<tr valign='top' align='left'><td class='head'>" . _MD_POINTID . "</td>";
            echo "<td class='even'><select size='1' name='lid'>";
            echo "<option value=' '>------</option>";
            $count_msg = count($entry);
            for ($i = 0; $i < $count_msg; $i++) {
                echo "<option value='" . $entry[$i]['id'] . "'>" . $entry[$i]['title'] . "</option>";
            }
            echo "</select></td></tr>\n";
            echo "<input type=hidden name=fct value=mypoints>\n";
            echo "<input type=hidden name=op value=modPoint><br /><br />\n";
            echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='" . _MD_MODIFY . "' accesskey=\"s\" /></form></td></tr></table>";
            //xoops_cp_footer();
        } else {
            redirect_header("managepoint.php?op=main", 1, _MD_NOPOINTS);
        }

    }

//Modify the point

    function modPoint() //managepoint.php

    {
        global $xoopsDB, $myts, $eh, $mytree, $xoopsConfig, $xoopsModuleConfig, $xoopsModule;
        $lid = intval($_GET['lid']);
        $result = $xoopsDB->query("select id, lat, lon, zoom, map_id, title, html from " . $xoopsDB->prefix("gmap_points") . " where id=$lid") or $eh->show("0013");
        //$uid = $xoopsUser->getVar('uid');
        //$result = $xoopsDB->query("select id, lat, lon, zoom, map_id, title, html from ".$xoopsDB->prefix("gmap_points")." where submitter=$uid") or $eh->show("0013");

        list($id, $lat, $lon, $zoom, $map_id, $title, $html) = $xoopsDB->fetchRow($result);

        $title = $myts->htmlSpecialChars($title);
        $lat = floatval($lat);
        $lon = floatval($lon);
        $zoom = intval($zoom);
        $map_id = intval($map_id);
        $GLOBALS['html'] = $myts->htmlSpecialChars($html);

        $result1 = $xoopsDB->query("select map_id, name from " . $xoopsDB->prefix("gmap_category") . "");
        $i = 0;
        while ($array = $xoopsDB->fetchArray($result1)) {
            $entry[$i]['map_id'] = intval($array['map_id']);
            $entry[$i]['name'] = $myts->htmlSpecialChars($array['name']);
            $i++;
        }

        //Display Map
        //echo"<script src=\"http://maps.google.com/maps?file=api&amp;v=2.x&amp;key=".$xoopsModuleConfig['api']."\" type=\"text/javascript\"></script >\n";
        echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key=' . $xoopsModuleConfig['api'] . '&callback=loadMap" type="text/javascript"></script>\n';
        echo "<script type=\"text/javascript\">\n";
        echo "//&lt;![CDATA[ \n";
        echo "function loadMap(){\n";
        //echo"function GPoint2(x,y) { return new GLatLng(y,x); }\n";
        echo "
        var map = new google.maps.Map(document.getElementById(\"map\"))
        map.setCenter({lat:" . $lat . ", lng:" . $lon . "})
        map.setZoom(" . $zoom . ")
        var marker = new google.maps.Marker({
            position: {lat:" . $lat . ", lng:" . $lon . "},
            map: map
        })
        map.addListener(\"click\", function(event){
            document.getElementById(\"zoom\").value = map.getZoom()
            document.getElementById(\"lat\").value = event.latLng.lat();
            document.getElementById(\"lon\").value = event.latLng.lng();
            marker.setPosition(event.latLng);
            map.setCenter(event.latLng);

        });

        map.addListener(\"zoom_changed\", function(){
            document.getElementById(\"zoom\").value = map.getZoom();
        });
    }
        // arrange for our onload handler to 'listen' for onload events
        if (window.attachEvent) {
            window.attachEvent(\"onload\", function() {
            loadMap();	// Internet Explorer
            });
        } else {
            window.addEventListener(\"load\", function() {
                loadMap(); // Firefox and standard browsers
            }, false);
        }
        //]]&gt";
        echo "</script >";

        echo "<table width='100%' class='outer' cellspacing='1'><tr><th colspan='2'>" . _MD_MODPOINT . "</th></tr>";
        echo "<form method=post action=\"managepoint.php\">\n";
        echo "<tr valign='top' align='center'><td class='head' colspan='2'><div id='map' style='height: 400px'></div ></td></tr>";
        echo "<tr valign='top' align='left'><td class='head'>" . _MD_LOCATION . "</td>";
        echo "<td class='even'><input type=text name=title value=\"$title\" size=50 maxlength=100></input></td></tr>\n";

        echo "<tr valign='top' align='left'><td class='head'>" . _MD_LONGITUDE . "</td>";
        echo "<td class='even'><input type=text id=lon name=lon value=\"$lon\" size=50 maxlength=100></input></td></tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>" . _MD_LATITUDE . "</td>";
        echo "<td class='even'><input type=text id=lat name=lat value=\"$lat\" size=50 maxlength=100></input></td></tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>" . _MD_ZOOM . "</td>";
        echo "<td class='even'><input type=text name=zoom id=zoom value=\"$zoom\" size=50 maxlength=100></input></td></tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>" . _MD_CATEGORYC . "</td>";
        echo "<td class='even'><select size='1' name='map_id'>";
        $count_msg = count($entry);
        for ($i = 0; $i < $count_msg; $i++) {
            if ($map_id == $entry[$i]['map_id']) {
                $opt_selected = "selected='selected'";
            } else {
                $opt_selected = "";
            }
            echo "<option value='" . $entry[$i]['map_id'] . "' $opt_selected >" . $entry[$i]['name'] . "</option>";
        }
        echo "</select></td></tr>\n";
        echo "<tr valign='top' align='left'><td class='head'>" . _MD_DESCRIPTIONC . "</td>";
        echo "<td class='even'>";
        xoopsCodeTarea("html", 60, 8);
        xoopsSmilies("html");
        echo "</td></tr>\n";
        echo "<input type=hidden name=lid value=$id></input>\n";
        echo "<input type=hidden name=op value=modPointS>\n";
        echo "<tr valign='top' align='left'><td class='head'></td><td class='even'><table><tr><td class='even'><input type='submit' class='formButton' name='post'  id='post' value='" . _MD_MODIFY . "' accesskey=\"s\" /></form>";
        //echo "<td class='even'>".myTextForm("managepoint.php?op=delPoint&lid=".$lid , _MD_DELETE)."</td>";
        //echo "<td class='even'>".myTextForm("managepoint.php?op=linksConfigMenu", _MD_CANCEL)."</td>";
        echo "</tr></table></td></tr></table>";

    }

//Insert the modified point into the DB

    function modPointS()
    {
        global $xoopsDB, $myts, $eh;
        $title = $myts->addSlashes($myts->censorString($_POST['title']));

        $map_id = intval($_POST["map_id"]);
        $lat = floatval($_POST["lat"]);
        $lon = floatval($_POST["lon"]);
        $zoom = intval($_POST["zoom"]);
        $html = $myts->addSlashes($myts->censorString($_POST["html"]));
        $xoopsDB->query("update " . $xoopsDB->prefix("gmap_points") . " set map_id='$map_id', title='$title', lat='$lat', lon='$lon',zoom='$zoom',html='$html', status=2, date=" . time() . " where id=" . intval($_POST['lid']) . "") or $eh->show("0013");
        redirect_header("managepoint.php", 1, _MD_DBUPDATED);
        exit();
    }

//Delete a point

    function delPoint()
    {
        global $xoopsDB, $eh, $xoopsModule;
        $sql = sprintf("DELETE FROM %s WHERE id = %u", $xoopsDB->prefix("gmap_points"), intval($_GET['lid']));
        $xoopsDB->query($sql) or $eh->show("0013");
        redirect_header("managepoint.php", 1, _MD_POINTDELETED);
        exit();
    }

    if (!isset($_POST['op'])) {
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

} else {
    echo "<br/><br/>" . _MD_ERROR;
}

include_once XOOPS_ROOT_PATH . '/footer.php';
