<?php
###############################################################################
#             Phatblokes adaption of google maps v.1.0 for Xoops 2.x     #
#             Writen by  Jason (jason@jas0r.com) 		              #
#            Improved / rewritten by Marco (mdxprod.com)
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
global $xoopsModuleConfig, $xoopsModule;

if ($xoopsModuleConfig['displaytype'] == 'userprofile') {
    $xoopsOption['template_main'] = 'googlemaps_index_userprofile.html';
} else {
    $xoopsOption['template_main'] = 'googlemaps_index_location.html';
}

include XOOPS_ROOT_PATH . "/header.php";
// If you want to show polylines on your map (like the lines used by Google Maps to show driving directions),
//you need to include the VML namespace and some CSS code in your XHTML document to make everything work properly in IE.
// The beginning of your XHTML document should look something like this:
// see here for details  http://www.google.com/apis/maps/documentation/#XHTML_and_VML
// i will move that part to a better place in near future

//
//include "includes/header.php";

$xoopsTpl->assign("xoops_module_header", '<link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/' . $xoopsModule->getVar('dirname') . '/googlemaps.css" />' .
    '<style type="text/css">
	v\:* {
	  behavior:url(#default#VML);
	}
	</style>');

$xoopsTpl->assign("adminpage", "<a href='" . XOOPS_URL . "/modules/" . $xoopsModule->getVar('dirname') . "/admin/index.php'>" . _MD_GMAPS_GOTOADMIN . "</a>");

$myts = MyTextSanitizer::getInstance();

global $myts, $xoopsUser;
//catch polyline start,end coordinates
$sql3 = $xoopsDB->query("SELECT " . $xoopsDB->prefix("gmap_points") . ".lat, " . $xoopsDB->prefix("gmap_pl") . ".map_id, " . $xoopsDB->prefix("gmap_points") . ".lon FROM " . $xoopsDB->prefix("gmap_points") . ", " . $xoopsDB->prefix("gmap_pl") . " WHERE " . $xoopsDB->prefix("gmap_pl") . ".point_id1 = " . $xoopsDB->prefix("gmap_points") . ".id");
$sql4 = $xoopsDB->query("SELECT " . $xoopsDB->prefix("gmap_points") . ".lat, " . $xoopsDB->prefix("gmap_points") . ".lon FROM " . $xoopsDB->prefix("gmap_points") . ", " . $xoopsDB->prefix("gmap_pl") . " WHERE " . $xoopsDB->prefix("gmap_pl") . ".point_id2 = " . $xoopsDB->prefix("gmap_points") . ".id");

//store polyline start point in an array
$i = 0;
$pentry = array();

while ($array = $xoopsDB->fetchArray($sql3)) {
    $pentry[$i]['map_id'] = intval($array['map_id']);
    $pentry[$i]['lat_id1'] = floatval($array['lat']);
    $pentry[$i]['lon_id1'] = floatval($array['lon']);
    $i++;
}

//store polyline end point in an array
$i = 0;
while ($array = $xoopsDB->fetchArray($sql4)) {
    $pentry[$i]['lat_id2'] = floatval($array['lat']);
    $pentry[$i]['lon_id2'] = floatval($array['lon']);
    $i++;
}
$xoopsTpl->assign('poly', $pentry);

//catch points coordinates,and user's info
$table1 = $xoopsDB->prefix("gmap_points");
$table2 = $xoopsDB->prefix("users");
$sql = $xoopsDB->query("SELECT t1.* , t2.* FROM $table1 AS t1 INNER JOIN $table2 AS t2 ON t1.submitter = t2.uid WHERE t1.status != 0 ORDER BY 'order' ASC");
$i = 0;
while ($array = $xoopsDB->fetchArray($sql)) {
    $mentry[$i]['id'] = intval($array['id']);
    // calcul nombre de points
    $mentry[$i]['lat'] = floatval($array['lat']);
    $mentry[$i]['lon'] = floatval($array['lon']);
    $mentry[$i]['title'] = $myts->htmlSpecialChars($array['title']);
    $mentry[$i]['html'] = str_replace("\"", "\'", $myts->displayTarea($array['html'], 0));
    $mentry[$i]['zoom'] = intval($array['zoom']);
    $mentry[$i]['cat_id'] = intval($array['map_id']); //aka category id
    $mentry[$i]['uid'] = intval($array['submitter']);
    $mentry[$i]['avatar_url'] = $myts->htmlSpecialChars($array['user_avatar']);

    if ($array['name'] == '') {
        $mentry[$i]['nom'] = $myts->htmlSpecialChars($array['uname']);
    } else {
        $mentry[$i]['nom'] = $myts->htmlSpecialChars($array['name']);
    }
    $mentry[$i]['promo'] = $myts->htmlSpecialChars($array['user_occ']);
    $i++;
}
$xoopsTpl->assign('point', $mentry);

//catch categories coordinates
$sql2 = $xoopsDB->query("SELECT * FROM " . $xoopsDB->prefix("gmap_category") . " ORDER BY 'order' ASC ");
$i = 0;
while ($array = $xoopsDB->fetchArray($sql2)) {
    $centry[$i]['cat_id'] = intval($array['map_id']); //aka category id
    $centry[$i]['lat'] = floatval($array['lat']);
    $centry[$i]['lon'] = floatval($array['lon']);
    $centry[$i]['name'] = $myts->htmlSpecialChars($array['name']);
    $centry[$i]['zoom'] = intval($array['zoom']);
    $i++;
}
$xoopsTpl->assign('category', $centry);

// catch some infos for diplay map
if (is_object($xoopsUser)) {
    $xoopsTpl->assign('isuser', true);
} else {
    $xoopsTpl->assign('isuser', false);
}
$xoopsTpl->assign('map_type', $xoopsModuleConfig['map_type']);
$xoopsTpl->assign('api_key', $xoopsModuleConfig['api']);
$xoopsTpl->assign('title_block', $xoopsModuleConfig['title_block']);
$xoopsTpl->assign('map_height', $xoopsModuleConfig['map_height']);
$xoopsTpl->assign('map_width', $xoopsModuleConfig['map_width']);
$xoopsTpl->assign('ovv_left', $xoopsModuleConfig['ovvmap_left']);
$xoopsTpl->assign('ovv_top', $xoopsModuleConfig['ovvmap_top']);
$xoopsTpl->assign('maxlastpoints', $xoopsModuleConfig['maxlastpoints']);
$xoopsTpl->assign('header', $xoopsModuleConfig['header']);

include_once XOOPS_ROOT_PATH . '/footer.php';
