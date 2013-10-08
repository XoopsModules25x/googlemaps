
-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
-- Usage:
--	Access like following url
--		http://__PREFIX__url/modules/gmap/admin/sqlupdate.php
-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
--
--  Add indexes

ALTER TABLE __PREFIX__gmap_points ADD INDEX(`map_id`);  
ALTER TABLE __PREFIX__gmap_points ADD INDEX(`submitter`);  
ALTER TABLE __PREFIX__gmap_points ADD INDEX(`status`);
ALTER TABLE __PREFIX__gmap_pl ADD INDEX(`point_id1`);
ALTER TABLE __PREFIX__gmap_pl ADD INDEX(`point_id2`);

