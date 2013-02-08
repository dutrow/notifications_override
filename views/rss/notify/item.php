<?php
/**
 * Render a single notification item for RSS 
 * 
 * @uses $vars['item']
 */

$item = $vars['item'];

$title = $item['title'];
$url = $item['url'];
$content = autop($item['descr']);
$time = $item['time'];
$author = $item['author'];
if (isset($item['guid'])) {
	$guid = "<guid isPermaLink=\"false\">{$item['guid']}</guid>";
} else {
	$encoded_url = urlencode($url);
	$guid = "<guid isPermaLink=\"true\">$encoded_url</guid>";
}

echo <<<RSS
<item>
	<title><![CDATA[$title]]></title>
	<link>$url</link>
	<description><![CDATA[$content]]></description>
	<pubDate>$time</pubDate>
	$guid
	<author>$author</author>
</item>
RSS;
