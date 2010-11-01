#!/usr/bin/php
<?php
/** $Id$ */

$config = array(
	'host'		=> '',
	'port'		=> 0,
	'username'	=> '',
	'password'	=> '',
	'remote_path'	=> '/var/www/private'
);

// Validate the arguments.
if($_SERVER["argc"] < 2 || substr($_SERVER["argv"][1], 0, 7) != '--site=')
{
	echo "Usage: migrator.php --site=1234\n\n";
	exit;
}

$site = trim(substr($_SERVER["argv"][1], 7));

// Connect to the server.
echo 'Connecting...';

if($connection = ssh2_connect($config['host'], $config['port'])) {
	echo "\t\t\tSUCCESS\n";
}
else
{
	echo "\t\t\tFAILED\n\n";
	exit;
}

// Authenticate.
echo 'Authenticating...';

if(@ssh2_auth_password($connection, $config['username'], $config['password'])) {
	echo "\t\tSUCCESS\n";
}
else
{
	echo "\t\tFAILED\n\n";
	exit;
}

// Validate the site.
echo 'Validating site...';

$stream = ssh2_exec($connection, $config['remote_path'].'/remote.php --action=validate --site='.$site);
stream_set_blocking($stream, true);

$response = fread($stream, 4096);
fclose($stream);

if($response == 'SUCCESS') {
	echo "\t\tSUCCESS\n";
}
else
{
	echo "\t\tFAILED\n\n";
	exit;
}

// Compress the site.
echo 'Compressing site...';

$stream = ssh2_exec($connection, $config['remote_path'].'/remote.php --action=compress --site='.$site);
stream_set_blocking($stream, true);

$response = fread($stream, 4096);
fclose($stream);

if($response == 'SUCCESS') {
	echo "\t\tSUCCESS\n";
}
else
{
	echo "\t\tFAILED\n\n";
	exit;
}

// Retrieve the compressed site.
echo 'Retrieving site...';

if(ssh2_scp_recv($connection, '/tmp/'.md5($site).'.tar.gz', '/tmp/'.md5($site).'.tar.gz')) {
	echo "\t\tSUCCESS\n";
}
else
{
	echo "\t\tFAILED\n\n";
	exit;
}