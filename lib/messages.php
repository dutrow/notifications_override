<?php
/**
 * Library of notification message creators 
 */

/**
 * Create wire notification message
 */
function notify_thewire_message($hook, $type, $result, $params) {
	
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'thewire')) {
		$text = $entity->description;
		$thread_id = $entity->getGUID();
		if ($entity->reply) {
			// this is ugly because the code for saving wire posts does not
			// set parameters we need to after the entity is saved
			$reply = true;
			$parent_post = get_entity(get_input('parent_guid'));
			if ($parent_post) {
				$reply_owner = $parent_post->getOwnerEntity();
				$thread_id = $parent_post->wire_thread;
			}
		}

		$poster = $entity->getOwnerEntity();
		if ($reply) {
			$subject = elgg_echo('thewire:notify:subject:reply', array($poster->name, $reply_owner->name));
			$body = elgg_echo('thewire:notify:body:reply', array(
				$poster->name,
				$reply_owner->name,
				$text,
			));
		} else {
			$subject = elgg_echo('thewire:notify:subject:post', array($poster->name));
			$body = elgg_echo('thewire:notify:body:post', array($poster->name, $text));
		}
		$body .= elgg_echo('thewire:notify:body:footer', array(
			elgg_normalize_url("thewire/thread/$thread_id"),
			elgg_normalize_url('thewire'),
		));
		
		return array(
			'subject' => $subject,
			'body' => $body,
			'params' => array(
				'url' => elgg_normalize_url("thewire")
			),
		);
	}
}

/**
 * Create blog notification message
 */
function notify_blog_message($hook, $type, $result, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'blog')) {
		$poster = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		if (elgg_instanceof($container, 'group')) {
			$filter = $container->name;
		} else {
			$filter = 'Blog';
		}
		
		$subject = elgg_echo('blog:notify:subject', array($filter, $entity->title, $poster->name));
		$body = elgg_echo('blog:notify:body', array($poster->name, $entity->title, $entity->description, $entity->getURL()));
		return array(
			'subject' => $subject,
			'body' => $body,
			'params' => array('url' => $entity->getURL()),
		);
	}
}

/**
 * Create bookmark notification message
 */
function notify_bookmarks_message($hook, $type, $result, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'bookmarks')) {
		$poster = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		if (elgg_instanceof($container, 'group')) {
			$filter = $container->name;
		} else {
			$filter = 'Bookmark';
		}
		
		$subject = elgg_echo('bookmarks:notify:subject', array($filter, $entity->title, $poster->name));
		$body = elgg_echo('bookmarks:notify:body', array($poster->name, $entity->title, $entity->description, $entity->address, $entity->getURL()));
		return array(
			'subject' => $subject,
			'body' => $body,
			'params' => array('url' => $entity->getURL()),
		);
	}
}

/**
 * Create file notification message
 */
function notify_file_message($hook, $type, $result, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'file')) {
		$poster = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		if (elgg_instanceof($container, 'group')) {
			$filter = $container->name;
		} else {
			$filter = 'File';
		}
		
		$subject = elgg_echo('file:notify:subject', array($filter, $entity->title, $poster->name));
		$body = elgg_echo('file:notify:body', array($poster->name, $entity->title, $entity->description, $entity->getURL()));
		return array(
			'subject' => $subject,
			'body' => $body,
			'params' => array('url' => $entity->getURL()),
		);
	}
}

/**
 * Create page notification message
 */
function notify_page_message($hook, $type, $result, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'page') || elgg_instanceof($entity, 'object', 'page_top')) {
		$poster = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		if (elgg_instanceof($container, 'group')) {
			$filter = $container->name;
		} else {
			$filter = 'Page';
		}
		
		$subject = elgg_echo('pages:notify:subject', array($filter, $entity->title, $poster->name));
		$body = elgg_echo('pages:notify:body', array($poster->name, $entity->title, $entity->description, $entity->getURL()));
		return array(
			'subject' => $subject,
			'body' => $body,
			'params' => array('url' => $entity->getURL()),
		);
	}
}

function notify_answers_message($hook, $type, $result, $params) {
	$entity = $params['entity'];

	if (elgg_instanceof($entity, 'object')) {
		$subtype = $entity->getSubtype();
		if ($subtype == 'question' || $subtype == 'answer') {

			$descr = $entity->description;
			$owner = $entity->getOwnerEntity();
			
			
			$container = $entity->getContainerEntity();
			if (elgg_instanceof($container, 'group')) {
				$source = $container->name;
			} else {
				$source = 'Questions';
			}

			$title = $entity->title;
			if ($subtype == 'answer') {
				$question = answers_get_question_for_answer($entity);
				$title = $question->title;
				$subject = elgg_echo('answers:notify:subject:answer', array(
					$source,
					$title,
				));
			} else {
				
				$subject = elgg_echo('answers:notify:subject:question', array(
					$source, 
					$title,
					$owner->name
				));
			}
;
			$msg_type = 'answers:notify:body:' . $subtype;
			$body = elgg_echo($msg_type, array(
						$owner->name,
						$title,
						$descr,
						$entity->getURL()
					));

			return array(
			'subject' => $subject,
			'body' => $body,
			'params' => array('url' => $entity->getURL()),
			);
		}
	}
}

/**
 * Create forum topic notification message
 */
function notify_forum_topic_message($hook, $type, $result, $params) {
	$entity = $params['entity'];
	if (elgg_instanceof($entity, 'object', 'groupforumtopic')) {
		$poster = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		$filter = $container->name;
		
		$subject = elgg_echo('forum:notify:topic:subject', array($filter, $entity->title, $poster->name));
		$body = elgg_echo('forum:notify:topic:body', array($poster->name, $container->name, $entity->title, $entity->description, $entity->getURL()));
		return array(
			'subject' => $subject,
			'body' => $body,
			'params' => array('url' => $entity->getURL()),
		);
	}
}

/**
 * Replace groups forum reply notification sender
 */
function notify_send_forum_reply_notifications($event, $type, $annotation) {
	global $CONFIG, $NOTIFICATION_HANDLERS;

	if ($annotation->name !== 'group_topic_post') {
		return;
	}

	// Have we registered notifications for this type of entity?
	$object_type = 'object';
	$object_subtype = 'groupforumtopic';

	$topic = $annotation->getEntity();
	if (!$topic) {
		return;
	}

	$poster = $annotation->getOwnerEntity();
	if (!$poster) {
		return;
	}

	if (isset($CONFIG->register_objects[$object_type][$object_subtype])) {
		$subject = $CONFIG->register_objects[$object_type][$object_subtype];
		$string = $subject . ": " . $topic->getURL();

		// Get users interested in content from this person and notify them
		// (Person defined by container_guid so we can also subscribe to groups if we want)
		foreach ($NOTIFICATION_HANDLERS as $method => $foo) {
			$interested_users = elgg_get_entities_from_relationship(array(
				'relationship' => 'notify' . $method,
				'relationship_guid' => $topic->getContainerGUID(),
				'inverse_relationship' => true,
				'types' => 'user',
				'limit' => 0,
			));

			if ($interested_users && is_array($interested_users)) {
				foreach ($interested_users as $user) {
					if ($user instanceof ElggUser && !$user->isBanned()) {
						if (($user->guid != $poster->guid) && has_access_to_entity($topic, $user) && $topic->access_id != ACCESS_PRIVATE) {
							$results = elgg_trigger_plugin_hook('notify:annotation:message', $annotation->getSubtype(), array(
								'annotation' => $annotation,
								'to_entity' => $user,
								'method' => $method), $string);
							if ($results !== false) {
								notify_user($user->guid, $topic->getContainerGUID(), $results['subject'], $results['body'], $results['params'], array($method));
							}
						}
					}
				}
			}
		}
	}
}

/**
 * Create forum reply notification message
 */
function notify_forum_reply_message($hook, $type, $message, $params) {
	$reply = $params['annotation'];
	$topic = $reply->getEntity();
	$poster = $reply->getOwnerEntity();
	$group = $topic->getContainerEntity();

	$filter = $group->name;
	
	$subject = elgg_echo('forum:notify:reply:subject', array($filter, $topic->title, $poster->name));
	$body = elgg_echo('forum:notify:reply:body', array($poster->name, $group->name, $topic->title, $reply->value, $topic->getURL()));
	return array(
		'subject' => $subject,
		'body' => $body,
		'params' => array('url' => $topic->getURL()),
	);
}

/**
 * Create photo notification message
 * @todo
 */
function notify_photos_message($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	
	$display_name = 'Photo Album';
	if($page_owner instanceof ElggGroup){
		$group = get_entity($page_owner->guid);
		$display_name = $group->name;
	}
	
	if (($entity instanceof ElggEntity) && ($entity->getSubtype() == 'album')) {
		// block notification message when the album doesn't have any photos
		if ($entity->new_album == TP_NEW_ALBUM)
			return false;

		$descr = $entity->description;
		$title = $entity->title;

		if ($method == 'rss' ) {
			$owner = $entity->getOwnerEntity();
			$subject = "Photo album created by {$owner->name}: " . $title;
			$body = $descr;

			$ret = array('subject' => $subject,
					'body' => $body,
					'params' => array('url' => $entity->getURL()),
			);

			return $ret;

		} else {
			$owner = $entity->getOwnerEntity();
			$subject = "({$display_name}) {$title} - photo album created by {$owner->name}";
			$body = $owner->name . ' created a new photo album: ' . $title . "\n\n" . $descr . "\n\n\n";
			$body .= "Link to album:\n" . $entity->getURL();

			$ret = array('subject' => $subject,
					'body' => $body,
					'user' => 'notify',);

			return $ret;
		}
	}
	return null;
}

/**
 * Send new friend relationship message
 */
function notify_new_friend($event, $type, $object) {

	if ($object instanceof ElggRelationship) {
		// send notification upon creation of friend relationship
		$user_one = get_entity($object->guid_one);
		$user_two = get_entity($object->guid_two);

		// does the reciprocal relationship exist?
		$reciprocate_text = '';
		if (check_entity_relationship($user_two->guid, 'friend', $user_one->guid) === false) {
			// this requires the Cooler plugin be activated
			$reciprocate_url = "cooler/follow/" . $user_one->guid;
			$reciprocate_url = elgg_normalize_url($reciprocate_url);
			$reciprocate_text = elgg_echo('friend:newfriend:reciprocate', array($user_one->name, $reciprocate_url));
		}

		$profile_url = $user_one->getURL();
		$subject = elgg_echo('friend:newfriend:subject', array($user_one->name));
		$body = elgg_echo("friend:newfriend:body", array($user_one->name, $reciprocate_text, $profile_url));

		return notify_user($object->guid_two, $object->guid_one, $subject, $body);
	}
}

/**
 * Send messageboard notification
 */
function notify_messageboard_post($event, $type, $object) {

	if (($object instanceof ElggAnnotation) && $object->name === "messageboard") {
		$poster = get_entity($object->owner_guid);
		$postee = get_user($object->entity_guid);

		if ($poster->guid == $postee->guid) {
			return;
		}

		$subject = elgg_echo('messageboard:notify:subject', array($poster->name));
		$body = elgg_echo('messageboard:notify:body', array(
			$poster->name,
			$object->value,
			$poster->name,
			$poster->getURL(),
		));

		return notify_user($postee->guid, $poster->guid, $subject, $body);
	}
}

/**
 * Send comment notification
 */
function notify_comment_message($event, $type, $object) {

	if (($object instanceof ElggAnnotation) && $object->name === "generic_comment") {

		$poster = $object->getOwnerEntity();
		$entity = get_entity($object->entity_guid);		
		$owner = $entity->getOwnerEntity();
		$container = $entity->getContainerEntity();
		
		if ($poster->guid == $owner->guid) {
			return;
		}
		
		$text = $object->value;
		$title = $entity->title;
		
		if (elgg_instanceof($container, 'group')) {
			$filter = $container->name;
		} else {
			$subtype = $entity->getSubtype();
			$filter = elgg_echo("$subtype:email:filter");
		}

		$subject = elgg_echo('comment:notify:subject', array($filter, $title, $poster->name));
		if (!empty($title)){
			$body = elgg_echo('comment:notify:body', array($title, $poster->name, $text, $entity->getURL()));
		} else {
			$body = elgg_echo('comment:notify:body:notitle', array($poster->name, $text, $entity->getURL()));
		}
		$params = array('url' => $entity->getURL());

		notify_user($owner->guid, $poster->guid, $subject, $body, $params);
	}
}
