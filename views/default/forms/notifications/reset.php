<?php
/**
 * Notifications reset form 
 */
$select = elgg_view('input/autocomplete', array(
	'name' => 'username',
	'match_on' => 'users',
));
$submit = elgg_view('input/submit', array('value' => elgg_echo('submit')));

echo <<<HTML
<div>Reset a user's notifications</div>
<div><label>Select user based on name: </label>$select</div>
<div>$submit</div>
HTML;
