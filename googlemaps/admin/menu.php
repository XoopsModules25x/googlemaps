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


defined("XOOPS_ROOT_PATH") or die("XOOPS root path not defined");

$dirname = basename(dirname(dirname(__FILE__)));
$module_handler = xoops_gethandler('module');
$module = $module_handler->getByDirname($dirname);
$pathIcon32 = $module->getInfo('icons32');

xoops_loadLanguage('admin', $dirname);

$i = 0;

// Index
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU0;
$adminmenu[$i]['link'] = "admin/index.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/home.png';
$i++;

$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU2;
$adminmenu[$i]['link'] = "admin/main.php?op=catAdd";
$adminmenu[$i]["icon"] = $pathIcon32.'/category.png';

$i++;
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU3;
$adminmenu[$i]['link'] = "admin/main.php?op=catMod";
$adminmenu[$i]["icon"] = $pathIcon32.'/manage.png';

$i++;
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU4;
$adminmenu[$i]['link'] = "admin/main.php?op=catOrder";
$adminmenu[$i]["icon"] = './images/icons/32/categorysort.png';

$i++;
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU5;
$adminmenu[$i]['link'] = "admin/main.php?op=pointAdd";
$adminmenu[$i]["icon"] = $pathIcon32.'/add.png';

$i++;
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU6;
$adminmenu[$i]['link'] = "admin/main.php?op=pointMod";
$adminmenu[$i]["icon"] = $pathIcon32.'/manage.png';

$i++;
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU7;
$adminmenu[$i]['link'] = "admin/main.php?op=pointOrder";
$adminmenu[$i]["icon"] = $pathIcon32.'/compfile.png';

$i++;
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU8;
$adminmenu[$i]['link'] = "admin/main.php?op=plAdd";
$adminmenu[$i]["icon"] = $pathIcon32.'/add.png';

$i++;
$adminmenu[$i]['title'] = _MI_GMAPS_ADMENU9;
$adminmenu[$i]['link'] = "admin/main.php?op=plMod";
$adminmenu[$i]["icon"] = $pathIcon32.'/manage.png';

$i++;
$adminmenu[$i]['title'] =   _MI_GMAPS_ABOUT;
$adminmenu[$i]['link'] =  "admin/about.php";
$adminmenu[$i]["icon"] = $pathIcon32.'/about.png';