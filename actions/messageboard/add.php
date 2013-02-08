<?php
/**
 * Overridding the messageboard add action so that we can control notifications
 */

$message_content = get_input('message_content');
$owner_guid = get_input("owner_guid");
$owner = get_entity($owner_guid);

if ($owner && !empty($message_content)) {
	$result = $owner->annotate('messageboard', $message_content, $owner->access_id, elgg_get_logged_in_user_guid());

	if ($result) {
		add_to_river('river/object/messageboard/create',
					'messageboard',
					elgg_get_logged_in_user_guid(),
					$owner->guid,
					$owner->access_id,
					0,
					$result);
	}

	if ($result) {
		system_message(elgg_echo("messageboard:posted"));

		$options = array(
			'annotations_name' => 'messageboard',
			'guid' => $owner->getGUID(),
			'limit' => $num_display,
			'pagination' => false,
			'reverse_order_by' => true,
			'limit' => 1
		);

		$output = elgg_list_annotations($options);
		echo $output;

	} else {
		register_error(elgg_echo("messageboard:failure"));
	}

} else {
	register_error(elgg_echo("messageboard:blank"));
}

forward(REFERER);
