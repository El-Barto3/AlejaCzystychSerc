<?php

require_once 'vendor/autoload.php';

if (!session_id())
{
    session_start();
}

// Call Facebook API

$facebook = new \Facebook\Facebook([
  'app_id'      => '260969055528157',
  'app_secret'     => '98eb03364290ddbe1890812bc295dd5c',
  'default_graph_version'  => 'v10.0'
]);

?>
