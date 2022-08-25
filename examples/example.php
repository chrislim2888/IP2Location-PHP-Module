<?php

require 'vendor/autoload.php';

/*
   Cache whole database into system memory and share among other scripts & websites
   WARNING: Please make sure your system have sufficient RAM to enable this feature
*/
//$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::SHARED_MEMORY);

/*
   Cache the database into memory to accelerate lookup speed
   WARNING: Please make sure your system have sufficient RAM to enable this feature
*/
// $db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::MEMORY_CACHE);

// List country information for US
$country = new \IP2Location\Country('./data/IP2LOCATION-COUNTRY-INFORMATION-BASIC.CSV');

echo '<pre>';
print_r($country->getCountryInfo('US'));
echo '</pre>';

echo "\n";

// Get region code by country code and region
$region = new \IP2Location\Region('./data/IP2LOCATION-ISO3166-2.CSV');

echo $region->getRegionCode('US', 'California');
