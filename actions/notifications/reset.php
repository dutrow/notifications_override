<?php
/**
 * Reset a user's notifications
 */

$username = get_input('username');
$user = get_user_by_username($username);
if (!$user) {
	register_error("Unable to access this user");
	forward();
}

$objects = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'email_digest',
	'owner_guid' => $user->guid,
));
foreach ($objects as $object) {
	$object->delete();
}

$objects = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'rss_notify',
	'owner_guid' => $user->guid,
));
foreach ($objects as $object) {
	$object->delete();
}

notify_setup_objects('', '', $user);

system_message("Notifications reset for $user->name");
