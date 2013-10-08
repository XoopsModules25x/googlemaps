<?php
/************************************************************************/
 # Google Maps                  							# 
 #                                                                  	#
 # Created by  Phatbloke (phatbloke@world-net.co.nz) 	
 # improved and empowered by Marco (mdxprod.com)
/************************************************************************/


$modversion['name'] = _MI_GMAPS_NAME;
$modversion['version'] = 0.91;
$modversion['description'] = _MI_GMAPS_DESC;
$modversion['author'] = "Phatbloke, Marco";
$modversion['credits'] = "Phatbloke,Herve,Philou,Skalpa,Kris,Dugris,Solo,Vivi for inspiration and tips";
$modversion['help']        = 'page=help';
$modversion['license']     = 'GNU GPL 2.0';
$modversion['license_url'] = "www.gnu.org/licenses/gpl-2.0.html/";
$modversion['official'] = 0;
$modversion['image'] = "images/logo.png";
$modversion['dirname'] = "googlemaps";
$modversion['dirmoduleadmin'] = '/Frameworks/moduleclasses/moduleadmin';
$modversion['icons16'] = '../../Frameworks/moduleclasses/icons/16';
$modversion['icons32'] = '../../Frameworks/moduleclasses/icons/32';

//about
$modversion["module_website_url"] = "www.xoops.org/";
$modversion["module_website_name"] = "XOOPS";
$modversion["release_date"] = "2013/01/11";
$modversion["module_status"] = "Beta 1";
$modversion["author_website_url"] = "http://www.xoops.org/";
$modversion["author_website_name"] = "XOOPS";
$modversion['min_php']='5.2';
$modversion['min_xoops']='2.5.5';
$modversion['min_admin']='1.1';
$modversion['min_db']= array('mysql'=>'5.0.7', 'mysqli'=>'5.0.7');

// SQL
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
// Tables created by sql file (without prefix!)
// All tables should not have any prefix!
$modversion['tables'][0] = "gmap_pl";
$modversion['tables'][1] = "gmap_category";
$modversion['tables'][2] = "gmap_points";


// Admin things
$modversion['hasAdmin'] = 1;
$modversion['system_menu'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

// Menu
$modversion['hasMain'] = 1;
//$modversion['sub'][1]['name'] = "Google Maps";
//$modversion['sub'][1]['url'] = "../../modules/googlemaps/";


// Smarty
$modversion['use_smarty'] = 1;

// Templates
$modversion['templates'][1]['file'] = 'googlemaps_index_userprofile.html';
$modversion['templates'][1]['description'] = 'Display within Points User Profile\'s informations';

$modversion['templates'][2]['file'] = 'googlemaps_index_location.html';
$modversion['templates'][2]['description'] = 'Display within Points Location\'s informations';

// Config Settings (only for modules that need config settings generated automatically)

// name of config option for accessing its specified value. i.e. $xoopsModuleConfig['storyhome']
$modversion['config'][1]['name'] = 'title_block';
$modversion['config'][1]['title'] = '_MI_GMAPS_TITLE';
$modversion['config'][1]['description'] = '_MI_GMAPS_TITLEDSC';
$modversion['config'][1]['formtype'] = 'textbox';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = "Map";

// options to be displayed in selection box
// required and valid for 'select' or 'select_multi' formtype option only
// language constants can be used for both array keys and values
//$modversion['config'][1]['options'] = array('5' => 5, '10' => 10, '50' => 50, '100' => 100, '200' => 200, '500' => 500, '1000' => 1000);

$modversion['config'][2]['name'] = 'api';
$modversion['config'][2]['title'] = '_MI_GMAPS_API';
$modversion['config'][2]['description'] = '_MI_GMAPS_APIDSC';
$modversion['config'][2]['formtype'] = 'textbox';
$modversion['config'][2]['valuetype'] = 'text';
$modversion['config'][2]['default'] = "no key";

$modversion['config'][3]['name'] = 'map_type';
$modversion['config'][3]['title'] = '_MI_GMAPS_MAPTYPE';
$modversion['config'][3]['description'] = '_MI_GMAPS_MAPTYPEDSC';
$modversion['config'][3]['formtype'] = 'select';
$modversion['config'][3]['valuetype'] = 'array';
$modversion['config'][3]['default'] = "_MAP_TYPE";
$modversion['config'][3]['options'] = array('Map' => 'G_NORMAL_MAP', 'Hybrid' => 'G_HYBRID_MAP', 'Satellite' => 'G_SATELLITE_MAP');

$modversion['config'][4]['name'] = 'map_width';
$modversion['config'][4]['title'] = '_MI_GMAPS_MAPWIDTH';
$modversion['config'][4]['description'] = '_MI_GMAPS_MAPWIDTHDSC';
$modversion['config'][4]['formtype'] = 'textbox';
$modversion['config'][4]['valuetype'] = 'int';
$modversion['config'][4]['default'] = 500;

$modversion['config'][5]['name'] = 'map_height';
$modversion['config'][5]['title'] = '_MI_GMAPS_MAPHEIGHT';
$modversion['config'][5]['description'] = '_MI_GMAPS_MAPHEIGHTDSC';
$modversion['config'][5]['formtype'] = 'textbox';
$modversion['config'][5]['valuetype'] = 'int';
$modversion['config'][5]['default'] = 500;

$modversion['config'][6]['name'] = 'ovvmap_left';
$modversion['config'][6]['title'] = '_MI_GMAPS_OVVMAPLEFT';
$modversion['config'][6]['description'] = '_MI_GMAPS_OVVMAPLEFTDSC';
$modversion['config'][6]['formtype'] = 'textbox';
$modversion['config'][6]['valuetype'] = 'int';
$modversion['config'][6]['default'] = 380;

$modversion['config'][7]['name'] = 'ovvmap_top';
$modversion['config'][7]['title'] = '_MI_GMAPS_OVVMAPTOP';
$modversion['config'][7]['description'] = '_MI_GMAPS_OVVMAPTOPDSC';
$modversion['config'][7]['formtype'] = 'textbox';
$modversion['config'][7]['valuetype'] = 'int';
$modversion['config'][7]['default'] = 380;

$modversion['config'][8]['name'] = 'maxpointsusers';
$modversion['config'][8]['title'] = '_MI_GMAPS_MAXPOINTUSERS';
$modversion['config'][8]['description'] = '_MI_GMAPS_MAXPOINTUSERSDSC';
$modversion['config'][8]['formtype'] = 'texbox';
$modversion['config'][8]['valuetype'] = 'int';
$modversion['config'][8]['default'] = 1;

$modversion['config'][9]['name'] = 'displaytype';
$modversion['config'][9]['title'] = '_MI_GMAPS_DISTYPE';
$modversion['config'][9]['description'] = '_MI_GMAPS_DISTYPEDSC';
$modversion['config'][9]['formtype'] = 'select';
$modversion['config'][9]['valuetype'] = 'text';
$modversion['config'][9]['options'] = array(_MI_GMAPS_DISPLAYTYPE_USER => 'userprofile', _MI_GMAPS_DISPLAYTYPE_LOCATION => 'location');
$modversion['config'][9]['default'] = 'userprofile';

$modversion['config'][10]['name'] = 'maxlastpoints';
$modversion['config'][10]['title'] = '_MI_GMAPS_MAXLASTPOINTS';
$modversion['config'][10]['description'] = '_MI_GMAPS_MAXLASTPOINTSDSC';
$modversion['config'][10]['formtype'] = 'texbox';
$modversion['config'][10]['valuetype'] = 'int';
$modversion['config'][10]['default'] = 6;

$modversion['config'][11]['name'] = 'header';
$modversion['config'][11]['title'] = '_MI_GMAPS_HEADER';
$modversion['config'][11]['description'] = '_MI_GMAPS_HEADERDSC';
$modversion['config'][11]['formtype'] = 'textarea';
$modversion['config'][11]['valuetype'] = 'text';
$modversion['config'][11]['default'] =  _MI_INSTRUCTIONS ;


?>
