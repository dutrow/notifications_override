<?php
/**
 * Add notification objects for all users
 */

// this may take a while so turn off time limit
set_time_limit(0);

$options = array(
	'type' => 'user',
	'limit' => 0,
);
$batch = new ElggBatch('elgg_get_entities', $options);

$num_rss_objects = 0;
$num_digest_objects = 0;	
foreach ($batch as $user) {
	// check for digest
	$digest = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'email_digest',
		'owner_guid' => $user->guid,
	));
	if (!$digest) {
		$digest_object = new ElggObject;
		$digest_object->subtype = "email_digest";
		$digest_object->owner_guid = $user->guid;
		$digest_object->container_guid = $user->guid;
		$digest_object->access_id = ACCESS_PUBLIC;
		if ($digest_object->save()) {
			$num_digest_objects++;
		}
	}

	// check for digest
	$rss = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'rss_notify',
		'owner_guid' => $user->guid,
	));
	if (!$rss) {
		$rss_object = new ElggObject;
		$rss_object->subtype = "rss_notify";
		$rss_object->owner_guid = $user->guid;
		$rss_object->container_guid = $user->guid;
		$rss_object->access_id = ACCESS_PUBLIC;
		if ($rss_object->save()) {
			$num_rss_objects++;
		}
	}
}

system_message("Created $num_digest_objects digest and $num_rss_objects rss objects");
