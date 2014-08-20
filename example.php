<?php
// Preset PHP settings
error_reporting(E_ALL);
ini_set('display_errors', 1);
set_time_limit(30);

require_once 'IP2Location.php';

// Standard lookup with no cache
$loc = new IP2Location('databases/IP-COUNTRY-SAMPLE.BIN', IP2Location::FILE_IO);

/*
   Cache whole database into system memory and share among other scripts & websites
   WARNING: Please make sure your system have sufficient RAM to enable this feature
*/
//$loc = new IP2Location('databases/IP-COUNTRY-SAMPLE.BIN', IP2Location::SHARED_MEMORY);

/*
   Cache the database into memory to accelerate lookup speed
   WARNING: Please make sure your system have sufficient RAM to enable this feature
*/
//$loc = new IP2Location(ROOT . 'databases/IP-COUNTRY-SAMPLE.BIN', IP2Location::MEMORY_CACHE);

$ip = '80.5.10.7';

// Lookup for single field
echo 'Country Code: ' . $loc->lookup($ip, IP2Location::COUNTRY_CODE) . '<br />';
echo 'Country Name: ' . $loc->lookup($ip, IP2Location::COUNTRY_NAME) . '<br />';

// Lookup for all fields
$record = $loc->lookup($ip, IP2Location::ALL);

echo '<pre>';
print_r($record);
echo '</pre>';
?>
