<?php

$english = array(
	'admin:administer_utilities:notifications' => 'Notifications',

	'notification:method:digest' => "Digest Email",
	'notification:method:rss' => "RSS",
	'item:object:rss_notify' => "RSS notification objects",
	'item:object:email_digest' => "Digest notification objects",

	'blog:email:filter' => "Blog",
	'bookmarks:email:filter' => "Bookmarks",
	'file:email:filter' => "File",
	'page:email:filter' => "Page",
	'page_top:email:filter' => "Page",
	'market:email:filter' => "Market",
	'album:email:filter' => "Photo Album",
	'image:email:filter' => "Photo",
	'answer:email:filter' => "Questions",

	'thewire:notify:subject:post' => "(Wire) post by %s",
	'thewire:notify:subject:reply' => "(Wire) %s responded to %s",
	'thewire:notify:body:post' => "%s on the wire:\n\n%s\n\n\n",
	'thewire:notify:body:reply' => "%s responded to %s on the wire:\n\n%s\n\n\n",
	'thewire:notify:body:footer' => "This thread:\n%s\n\nAll wire posts:\n%s\n",

	'blog:notify:subject' => "(%s) %s - blog post by %s",
	'blog:notify:body' => "%s wrote a new blog post: %s\n\n\n%s\n\n\nLink to blog:\n%s",

	'bookmarks:notify:subject' => "(%s) %s - new bookmark by %s",
	'bookmarks:notify:body' => "%s bookmarked a new page: %s\n\n%s\n\n\nBookmark link:\n%s\n\nLink to bookmark description:\n%s",
	
	'file:notify:subject' => "(%s) %s - file uploaded by %s",
	'file:notify:body' => "%s uploaded a new file: %s\n\n\n%s\n\n\nLink to file description:\n%s",

	'pages:notify:subject' => "(%s) %s - page created by %s",
	'pages:notify:body' => "%s created a new page: %s\n\n\n%s\n\n\nLink to page:\n%s",
    
	'answers:notify:subject:question' => "(%s) %s - asked by %s",
	'answers:notify:subject:answer' => "(%s) %s - answer",
	'answers:notify:body:question' => "%s created a new question: %s\n\n\n%s\n\n\nLink to page:\n%s",
	'answers:notify:body:answer' => "%s answered the question: %s\n\n\n%s\n\n\nLink to page:\n%s",
    
	'forum:notify:topic:subject' => "(%s) %s - discussion",
	'forum:notify:topic:body' => "%s started a new discussion topic in the group %s: %s\n\n\n%s\n\n\nLink to topic:\n%s",
	'forum:notify:reply:subject' => "(%s) %s - discussion",
	'forum:notify:reply:body' => "%s commented in the %s's group discussion on the topic: %s\n\n\n%s\n\n\nLink to topic:\n%s",

	'messageboard:notify:subject' => "(Message Board) comment by %s",	
	'messageboard:notify:body' => "You have a new message board comment from %s. It reads:\n\n\n%s\n\n\nTo view %s's profile, click here:\n\n%s\n",

	'friend:newfriend:subject' => "%s is now following you",
	'friend:newfriend:reciprocate' => "\n\nTo follow %s, click this link: %s\n\n",
	'friend:newfriend:body' => "%s is now following you.\n%s\nTo view this person's profile, click here:\n\n%s\n\nPlease do not reply to this email.\n",

	'comment:notify:subject' => "(%s) %s - comment by %s",	
	'comment:notify:body' => "You have a new comment on your item \"%s\" from %s. It reads:\n\n\n%s\n\n\nTo reply or view the original item, click here:\n%s",
	'comment:notify:body:notitle' => "You have a new comment on your item from %s. It reads:\n\n\n%s\n\n\nTo reply or view the original item, click here:\n%s",
);

add_translation("en", $english);
