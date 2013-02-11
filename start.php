<?php
/**
 * JHU notifications override plugin 
 */

elgg_register_event_handler('init', 'system', 'notify_init');

function notify_init() {

	elgg_extend_view('css/elgg', 'notifications_override/css');

	elgg_register_event_handler('create', 'user', 'notify_setup_objects');

	elgg_register_plugin_hook_handler('permissions_check', 'object', 'notify_permission_override');

	unregister_notification_handler("site");
	register_notification_handler("digest", "notify_digest_handler");
	register_notification_handler("rss", "notify_rss_handler");
	register_notification_handler("email", "notify_email_handler");

	elgg_register_plugin_hook_handler('cron', 'daily', 'notify_send_digests');

	// override default notification code for objects
	elgg_unregister_event_handler('create', 'object', 'object_notifications');
	elgg_register_event_handler('create', 'object', 'notify_object_notifications');

	// load notification message library
	$lib_dir = elgg_get_plugins_path() . 'notifications_override/lib';
	elgg_register_library('notify:messages', "$lib_dir/messages.php");
	elgg_load_library('notify:messages');

	// override object notification message for blog, bookmark, file, page, photo, and thewire
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'blog_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_blog_message');
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'bookmarks_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_bookmarks_message');
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'file_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_file_message');
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'page_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_page_message');
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object','tidypics_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_photos_message');
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'thewire_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_thewire_message');
	
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'answers_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_answers_message');

	// override notifications for group forum posts.
	// we need to replace the default topic notification message and the 
	// entire reply notification system
	elgg_unregister_plugin_hook_handler('notify:entity:message', 'object', 'groupforumtopic_notify_message');
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'notify_forum_topic_message');
	elgg_unregister_event_handler('create', 'annotation', 'discussion_reply_notifications');
	elgg_register_event_handler('create', 'annotation', 'notify_send_forum_reply_notifications');
	elgg_unregister_plugin_hook_handler('notify:annotation:message', 'group_topic_post', 'discussion_create_reply_notification');
	elgg_register_plugin_hook_handler('notify:annotation:message', 'group_topic_post', 'notify_forum_reply_message');

	// override friend notifications
	elgg_unregister_event_handler('create', 'friend', 'relationship_notification_hook');
	elgg_register_event_handler('create', 'friend', 'notify_new_friend');

	// messageboard notification
	elgg_register_event_handler('create', 'annotation', 'notify_messageboard_post');

	// comments
	elgg_register_event_handler('create', 'annotation', 'notify_comment_message');

	// admin page for notifications
	elgg_register_admin_menu_item('administer', 'notifications', 'administer_utilities');

	// rss notification page handler
	elgg_register_page_handler('notify', 'notify_page_handler');

	$actions_base = elgg_get_plugins_path() . 'notifications_override/actions';
	elgg_register_action('notifications/install', "$actions_base/notifications/install.php", "admin");
	elgg_register_action('notifications/reset', "$actions_base/notifications/reset.php", "admin");
	elgg_register_action('comments/add', "$actions_base/comments/add.php");
	elgg_register_action('messageboard/add', "$actions_base/messageboard/add.php");
}

/**
 * Create notification storage objects
 *
 * On user creation, setup objects to store digest and rss notifications
 *
 */
function notify_setup_objects($event, $object_type, $user) {
	$digest_object = new ElggObject;
	$digest_object->subtype = "email_digest";
	$digest_object->owner_guid = $user->guid;
	$digest_object->container_guid = $user->guid;
	$digest_object->access_id = ACCESS_PUBLIC;  // so I don't have to login admin in cron job
	$digest_object->save();

	$rss_object = new ElggObject;
	$rss_object->subtype = "rss_notify";
	$rss_object->owner_guid = $user->guid;
	$rss_object->container_guid = $user->guid;
	$rss_object->access_id = ACCESS_PUBLIC; // public so I don't have to login admin
	$rss_object->save();
}


/**
 * Permissions Override
 *
 * We want everyone to have permission to add to the notification objects
 *
 */
function notify_permission_override($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];

	if (elgg_instanceof($entity, 'object', 'email_digest')) {
		return true;
	}

	if (elgg_instanceof($entity, 'object', 'rss_notify')) {
		return true;
	}
}

/**
 * Email notification handler - overrides core email handler
 */
function notify_email_handler(ElggEntity $from, ElggUser $to, $subject, $message, array $params = NULL) {
	
	global $CONFIG;

	if (!$from) {
		$msg = elgg_echo('NotificationException:MissingParameter', array('from'));
		throw new NotificationException($msg);
	}

	if (!$to) {
		$msg = elgg_echo('NotificationException:MissingParameter', array('to'));
		throw new NotificationException($msg);
	}
	
	// skip users with no email address - means they have left the lab
	if ($to->email == "") {
		return true;
	}

	// update stats - can we find a way to do this without this code here - maybe add event
	if (function_exists('stats_notify_update')) {
		stats_notify_update();
	}

	// add notification configuration
	if ($from instanceof ElggGroup) {
		$message .= "\n\nYour group notification settings:\n{$CONFIG->wwwroot}notifications/group/{$to->username}/";
	} else {
		$message .= "\n\nYour user notification settings:\n{$CONFIG->wwwroot}notifications/personal/{$to->username}/";
	}
	// To
	$to = $to->email;

	// From
	$site = get_entity($CONFIG->site_guid);
	// Uncomment this if you would prefer the e-mails come addressed from the group:
	// If there's an email address, use it - but only if its not from a user.
	//if (!($from instanceof ElggUser) && $from->email && $from->name) {
	//	$from = "\"$from->name\" <$from->email>";	
	//} else if (!($from instanceof ElggUser) && $from->name && $site->email) {
	//	$from = "\"$from->name\" <$site->email>";	
	//} else 
	if ($site && $site->email && $site->name) {
		// Use email address of current site if we cannot use sender's email
		$from = "\"$site->name\" <$site->email>";
	} else {
		// If all else fails, use the domain of the site.
		$from = $CONFIG->sitename . ' <noreply@' . get_site_domain($CONFIG->site_guid) . '>';
	}
	
	$from = html_entity_decode($from, ENT_COMPAT, 'UTF-8'); // Decode any html enties
	$subject = html_entity_decode($subject, ENT_COMPAT, 'UTF-8'); // Decode any html enties
	
	return elgg_send_email($from, $to, $subject, $message);
}

/**
 * Email digest notification handler
 *
 * Called by the Elgg notification core and adds data to storage object
 */
function notify_digest_handler(ElggEntity $from, ElggUser $to, $subject, $message, array $params = NULL) {
	if (!$from) {
		$msg = elgg_echo('NotificationException:MissingParameter', array('from'));
		throw new NotificationException($msg);
	}

	if (!$to) {
		$msg = elgg_echo('NotificationException:MissingParameter', array('to'));
		throw new NotificationException($msg);
	}

	// skip users with no email address - means they have left the lab
	if ($to->email == "") {
		//error_log('skipping ' . $to->name);
		return true;
	}

	// get email digest object from the database
	$digest_object = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'email_digest',
		'owner_guid' => $to->guid,
	));
	if (!$digest_object) {
		error_log('unable to find digest storage object for ' . $to->guid);
		return false;
	}

	if (is_array($digest_object)) {
		$digest_object = $digest_object[0];
	}

	if ($digest_object->description) {
		$report_list = unserialize($digest_object->description);
	}

	$report['subject'] = $subject;
	$report['body'] = $message;

	$report_list[] = $report;

	$digest_object->description = serialize($report_list);

	if (!$digest_object->save()) {
		error_log("failed to save digest notification for " . $to->guid);
	}

	return true;
}

/**
 * RSS notification handler
 *
 * Called by the notification core and adds data to storage object
 */
function notify_rss_handler(ElggEntity $from, ElggUser $to, $subject, $message, array $params = NULL) {
	if (!$from) {
		$msg = elgg_echo('NotificationException:MissingParameter', array('from'));
		throw new NotificationException($msg);
	}

	if (!$to) {
		$msg = elgg_echo('NotificationException:MissingParameter', array('to'));
		throw new NotificationException($msg);
	}
	
	// skip users with no email address - means they have left the lab
	if ($to->email == "") {
		//error_log('skipping ' . $to->name);
		return true;
	}

	// get rss object from the database
	$rss_object = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'rss_notify',
		'owner_guid' => $to->guid,
	));
	if (!$rss_object) {
		error_log('failed to find rss storage object for ' . $to->guid);
		return false;
	}

	if (is_array($rss_object)) {
		$rss_object = $rss_object[0];
	}


	if ($rss_object->description) {
		$report_list = unserialize($rss_object->description);
	}

	// keep RSS feed to twenty elements
	if (count($report_list) >= 20) {
		array_pop($report_list);
	}

	// @todo need to replace this as we don't always have logged in user
	$user = elgg_get_logged_in_user_entity();

	$report['author'] = "$user->email ({$user->name})";
	$report['time'] = date("F j, Y, g:i a");
	$report['title'] = $subject;
	$report['descr'] = $message;
	$report['guid'] =  md5(time() . $message);
	if ($params['url']) {
		$report['url'] = $params['url'];
	}

	// new elements go in the front
	if ($report_list) {
		array_unshift($report_list, $report);
	} else {
		$report_list[] = $report;
	}
	
	$rss_object->description = serialize($report_list);

	if (!$rss_object->save()) {
		error_log('save failed in rss notifications object for ' . $to->guid);
	}

	return true;
}

/**
 * Digest Email Cron handler
 *
 * Called once a day to send out emails
 */
function notify_send_digests() {
	// this may take a while so turn off time limit
	set_time_limit(0);

	$options = array(
		'type' => 'user',
		'limit' => 0,
	);
	$batch = new ElggBatch('elgg_get_entities', $options);
	foreach ($batch as $user) {
		// check if user left the lab - skip if so
		if ($user->email == "") {
			continue;
		}

		$digest = elgg_get_entities(array(
			'type' => 'object',
			'subtype' => 'email_digest',
			'owner_guid' => $user->guid,
		));
		if (!$digest) {
			continue;
		}

		if (is_array($digest)) {
			$digest = $digest[0];
		}

		// anything to send out today?
		if (!$digest->description) {
			continue;
		}

		$report_list = unserialize($digest->description);

		$subject = "digest for " . date("F j, Y", strtotime("yesterday"));

		//
		$body = 'Summary of ' . $user->name . "'s notifications for " . date("F j, Y") . "\n";
		foreach ($report_list as $report) {
			$body .= "  * " . $report['subject'] . "\n";
		}

		$body .= "\n\n";

		foreach ($report_list as $report) {
			//$body .= $report['subject'] . "\n";
			$body .= "---------------------------------------------------------------\n";
			$body .= $report['body'] . "\n";
			$body .= "---------------------------------------------------------------\n\n\n";
		}

		notify_user($user->guid, $user->guid, $subject, $body, NULL, "email");

		// clear out notifications to start with clean slate
		$digest->description = "";
		$digest->save();
	}
}


/**
 * Sends notification messages when objects are created
 * This is an event handler for create object
 *
 * @param $event
 * @param $object_type
 * @param $object
 */
function notify_object_notifications($event, $object_type, $object) {
	// We only want to trigger notification events for ElggEntities
	if ($object instanceof ElggEntity) {

		// Get config data
		global $CONFIG, $SESSION, $NOTIFICATION_HANDLERS;

		$hookresult = elgg_trigger_plugin_hook(
				'object:notifications',
				$object_type,
				array(
					'event' => $event,
					'object_type' => $object_type,
					'object' => $object,
				),
				false);
		if ($hookresult === true) {
			return true;
		}

		// Have we registered notifications for this type of entity?
		$object_type = $object->getType();
		if (empty($object_type)) {
			$object_type = '__BLANK__';
		}

		$object_subtype = $object->getSubtype();
		if (empty($object_subtype)) {
			$object_subtype = '__BLANK__';
		}

		if (isset($CONFIG->register_objects[$object_type][$object_subtype])) {
			$subject = $CONFIG->register_objects[$object_type][$object_subtype];
			$string = $subject . ": " . $object->getURL();

			// Get users interested in content from this person and notify them
			// (Person defined by container_guid so we can also subscribe to groups if we want)
			foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
				$interested_users = elgg_get_entities_from_relationship(array(
					'site_guids' => ELGG_ENTITIES_ANY_VALUE,
					'relationship' => 'notify' . $method,
					'relationship_guid' => $object->container_guid,
					'inverse_relationship' => TRUE,
					'types' => 'user',
					'limit' => 99999
				));

				if ($interested_users && is_array($interested_users)) {
					foreach ($interested_users as $user) {
						if ($user instanceof ElggUser && !$user->isBanned()) {
							if (($user->guid != $SESSION['user']->guid) && has_access_to_entity($object, $user)
							&& $object->access_id != ACCESS_PRIVATE) {
								
								$result = elgg_trigger_plugin_hook(
										'notify:entity:message',
										$object->getType(),
										array(
											'entity' => $object,
											'to_entity' => $user,
											'method' => $method,
										),
										$string);

								if ($result === false) {
									// plugin hook says don't send notification
								} else if (empty($result)) {
									// plugin hook did not return a message string so use default
									notify_user($user->guid, $object->container_guid, $string, $string, NULL, array($method));
								} else if (!is_array($result)) {
									// plugin hook returned body of the notification
									notify_user($user->guid, $object->container_guid, $string, $result, NULL, array($method));
								} else {
									// plugin hook returned more than just body
									$subject = $string;
									$body = $string;
									$notify_params = NULL;
									if (isset($result['subject'])) {
										$subject = $result['subject'];
									}
									if (isset($result['body'])) {
										$body = $result['body'];
									}
									if (isset($result['params'])) {
										$notify_params = $result['params'];
									}

									notify_user($user->guid, $object->container_guid, $subject, $body, $notify_params, array($method));
								}
							}
						}
					}
				}
			}
		}
	}
}

/**
 * RSS notification page handler
 * 
 * /notify/username.rss
 *
 * @param array $segments URL segments
 */
function notify_page_handler($segments) {
	if (!isset($segments[0])) {
		return false;
	}

	elgg_set_viewtype('rss');

	$username = array_shift(explode('.', $segments[0]));
	$owner = get_user_by_username($username);
	if (!$owner) {
		return false;
	}
	
	$title = "Notifications for " . $owner->name;
	
	$rss_object = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'rss_notify',
		'owner_guid' => $owner->guid,
	));
	if (is_array($rss_object)) {
		$rss_object = $rss_object[0];
	}
			
	$content = elgg_view_entity($rss_object);

	$body = elgg_view_layout('default', array('content' => $content));
	
	echo elgg_view_page($title, $body);
}
