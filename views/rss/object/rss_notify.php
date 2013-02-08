<?php 
/**
 * Notifications RSS view 
 */

$rss = $vars['entity'];
if (!$rss || !$rss->description) {
	return true;
}
			
$notification_list = unserialize($rss->description);
if ($notification_list) {
	foreach ($notification_list as $item) {
		echo elgg_view('notify/item', array('item' => $item));
	}
}
