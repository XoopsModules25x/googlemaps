-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
-- Usage:
--	Access like following url
--		http://__PREFIX__url/modules/gmap/admin/sqlupdate.php
-- ----8----8----8----8----8----8----8----8----8----8----8----8----8----8----
--
-- Change zoom level to suit google API V2
--
UPDATE __PREFIX__gmap_points SET `zoom` = 17 - `zoom` WHERE 0<1;
UPDATE __PREFIX__gmap_category SET `zoom` = 17 - `zoom` WHERE 0<1;

--
-- End of file
--
