<?php
/**
 * Private message
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code 
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       The XOOPS Project http://sourceforge.net/projects/xoops/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         MRBS
 * @since           1.41
 * @author          jobrazo
 * @version         $Id: admin.php  $
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/include/cp_header.php';
include_once dirname(__FILE__) . '/admin_header.php';

xoops_cp_header();

	$indexAdmin = new ModuleAdmin();

//-----------------------

$result=$xoopsDB->query("select count(*) from ".$xoopsDB->prefix("gmap_points"));
list($numrows) = $xoopsDB->fetchRow($result);

$indexAdmin->addInfoBox(_MD_GMAPS_DASHBOARD);

$indexAdmin->addInfoBoxLine(
    _MD_GMAPS_DASHBOARD, _MD_THEREARE , $numrows, 'Green'
);
//----------------------------


    echo $indexAdmin->addNavigation('index.php');
    echo $indexAdmin->renderIndex();

include "admin_footer.php";