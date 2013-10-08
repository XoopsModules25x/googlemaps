-- updated : 2007/02/17 --
I've made some quick changes to Phatbloke's Google Map module. Hope you will enjoy them. Next step is to make this module even more Xoops API compliant : this helps me improving my knowledges, so my strategy is "improving step by step so to learn how to"...
Thanks to Phatbloke as well. I hope he will be happy when he will come back from his trip.
This module is from now only compliant with xoops 2.0.x versions, not with 2.2+ 
Enjoy and thanks in advance for your feedbacks, i like them
;-)
Marco

=====

New Installation:

1. Just unpack and upload to modules directory as usual
2. Install your module through module administration panel
3. Modify your theme to add - xmlns:v="urn:schemas-microsoft-com:vml" - into theme.html file (DTD area)

so it looks like
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">

instead of
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<{$xoops_langcode}>" lang="<{$xoops_langcode}>">

4. Configure module in prefs
5. Obtain an api for your site from google. Link is given in the config page.
6. Add your categories and points from the module menu (to make the module work, you will have to create one category at least)

**Warning** : your theme has to be correctly compliant with w3c standards to display well large contents. Combined with the fact that IE asks for strict compliant code (in this case ! lol) , some display errors on IE for large contents have been noticed with some non-compliant themes (no increase of length's infowindow). Example : make use of unique id, as stated in w3c rules. If not, replace them by css classes !

============

Upgrade

Please refer to upgrade.txt file and read it carefully.


=========
Notes:
-The first category will be the default display for the map. This can be changed by re-ordering the categories.
-Images and links can be added in the markers using the xoops text input as usual. 
-Polylines link together the markers