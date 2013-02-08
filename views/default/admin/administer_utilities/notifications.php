<?php
/**
 * Notifications admin settings 
 */

echo elgg_view_form('notifications/reset');

echo '<form class="elgg-form mtl ptl"><fieldset>';
echo "<div>When enabling on a site with users, we need to 
	create the notification objects for the current users as
	a bootstrapping step.</div>";

echo '<div>';
echo elgg_view('output/url', array(
	'href' => 'action/notifications/install',
	'text' => 'Install',
	'is_action' => true,
	'class' => 'elgg-button elgg-button-submit',
));
echo '</div>';
echo '</fieldset></form>';
