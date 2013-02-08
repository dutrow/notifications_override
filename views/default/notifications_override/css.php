<?php
/**
 * Notifications override CSS
 */

$url = elgg_get_site_url();

?>

#notificationstable td.digesttogglefield {
	width:50px;
	text-align: center;
	vertical-align: middle;
}
#notificationstable td.digesttogglefield input {
	margin-right:36px;
	margin-top:5px;
}
#notificationstable td.digesttogglefield a {
	width:46px;
	height:24px;
	cursor: pointer;
	display: block;
	outline: none;
}
#notificationstable td.digesttogglefield a.digesttoggleOff {
	background: url(<?php echo $url; ?>mod/notifications_override/graphics/icon_notifications_digest.gif) no-repeat right 2px;
}
#notificationstable td.digesttogglefield a.digesttoggleOn {
	background: url(<?php echo $url; ?>mod/notifications_override/graphics/icon_notifications_digest.gif) no-repeat right -36px;
}

#notificationstable td.rsstogglefield {
	width:50px;
	text-align: center;
	vertical-align: middle;
}
#notificationstable td.rsstogglefield input {
	margin-right:36px;
	margin-top:5px;
}
#notificationstable td.rsstogglefield a {
	width:46px;
	height:24px;
	cursor: pointer;
	display: block;
	outline: none;
}
#notificationstable td.rsstogglefield a.rsstoggleOff {
	background: url(<?php echo $url; ?>mod/notifications_override/graphics/icon_notifications_rss.gif) no-repeat right 2px;
}
#notificationstable td.rsstogglefield a.rsstoggleOn {
	background: url(<?php echo $url; ?>mod/notifications_override/graphics/icon_notifications_rss.gif) no-repeat right -36px;
}
