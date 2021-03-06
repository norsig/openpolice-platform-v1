#!/usr/bin/php
<?php
// The SQL query to execute.
$sql = '';

// Optional settings.
$options = array('exclude-default' => true);

// Connect to MySQL.
include dirname(__FILE__).'/../../code/configuration.php';
$config = new JConfig();
$mysqli = new mysqli('localhost', $config->user, $config->password);

// Get a list of installed sites.
$result = $mysqli->query('SHOW DATABASES LIKE \'police_%\';');
while($row = $result->fetch_row()) {
    $sites[] = substr($row[0], 7);
}

$result->close();

if($options['exclude-default'])
{
    foreach($sites as $key => $site)
    {
        if($site == 'default') {
            unset($sites[$key]);
        }
    }
}

// Execute the query.
foreach($sites as $site)
{
    $mysqli->select_db('police_'.$site);
    $mysqli->query($sql);
}