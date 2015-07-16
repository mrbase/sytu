<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../templates');
$app['twig.options'] = array('cache' => __DIR__.'/../var/cache/twig');

$app['spotify.options'] = [
	'callback'  => 'http://sytu.un:9999/spotify/auth/callback',
	'client_id' => '05da6a3361a04d6fa24b852327d3b667',
	'secret'    => 'cd130cc914f948df839851a1f7f08b2c',
];
