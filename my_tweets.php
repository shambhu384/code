<?php

$username = 'shambhu384';

$twitter_status_url = 'http://twitter.com/statuses/user_timeline/';

$reply = file_get_contents($twitter_status_url.$username.'xml?count=3');

$xml = SimpleXMLElement($reply);

foreach($xml->children() as $status) {
    echo $status->text;
}

