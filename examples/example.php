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

// Default file I/O lookup
$db = new \IP2Location\Database('./data/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);

$records = $db->lookup('8.8.8.8', \IP2Location\Database::ALL);

echo $db->getDate();

echo '<pre>';
echo 'IP Number             : ' . $records['ipNumber'] . "\n";
echo 'IP Version            : ' . $records['ipVersion'] . "\n";
echo 'IP Address            : ' . $records['ipAddress'] . "\n";
echo 'Country Code          : ' . $records['countryCode'] . "\n";
echo 'Country Name          : ' . $records['countryName'] . "\n";
echo 'Region Name           : ' . $records['regionName'] . "\n";
echo 'City Name             : ' . $records['cityName'] . "\n";
echo 'Latitude              : ' . $records['latitude'] . "\n";
echo 'Longitude             : ' . $records['longitude'] . "\n";
echo 'Area Code             : ' . $records['areaCode'] . "\n";
echo 'IDD Code              : ' . $records['iddCode'] . "\n";
echo 'Weather Station Code  : ' . $records['weatherStationCode'] . "\n";
echo 'Weather Station Name  : ' . $records['weatherStationName'] . "\n";
echo 'MCC                   : ' . $records['mcc'] . "\n";
echo 'MNC                   : ' . $records['mnc'] . "\n";
echo 'Mobile Carrier        : ' . $records['mobileCarrierName'] . "\n";
echo 'Usage Type            : ' . $records['usageType'] . "\n";
echo 'Elevation             : ' . $records['elevation'] . "\n";
echo 'Net Speed             : ' . $records['netSpeed'] . "\n";
echo 'Time Zone             : ' . $records['timeZone'] . "\n";
echo 'ZIP Code              : ' . $records['zipCode'] . "\n";
echo 'Domain Name           : ' . $records['domainName'] . "\n";
echo 'ISP Name              : ' . $records['isp'] . "\n";
echo 'Address Type          : ' . $records['addressType'] . "\n";
echo 'Category              : ' . $records['category'] . "\n";
echo 'District              : ' . $records['district'] . "\n";
echo 'ASN                   : ' . $records['asn'] . "\n";
echo 'AS                    : ' . $records['as'] . "\n";
echo 'AS Domain             : ' . $records['asDomain'] . "\n";
echo 'AS Usage Type         : ' . $records['asUsageType'] . "\n";
echo 'AS CIDR               : ' . $records['asCidr'] . "\n";
echo '</pre>';

echo '<pre>
CIDR: ' . implode(', ', $db->getCidr('8.8.8.8')) . '
</pre>';

// Web Service
/*$ws = new \IP2Location\WebService('YOUR_API_KEY', 'WS25', true);
$records = $ws->lookup('8.8.8.8', [
	'continent', 'country', 'region', 'city', 'geotargeting', 'country_groupings', 'time_zone_info',
], 'en');

echo '<pre>';
print_r($records);

echo 'Credit Remaining: ' . $ws->getCredit() . "\n";
echo '</pre>';*/

$ipTools = new \IP2Location\IpTools();

// Validate IPv4 address
var_dump($ipTools->isIpv4('8.8.8.8'));

echo '<br>';

// Validate IPv6 address
var_dump($ipTools->isIpv6('2001:4860:4860::8888'));

echo '<br>';

// Convert IPv4 into decimal
echo $ipTools->ipv4ToDecimal('8.8.8.8');

echo '<br>';

// Convert IPv6 into decimal
echo $ipTools->ipv6ToDecimal('2001:4860:4860::8888');

echo '<br>';

// Convert decimal into IPv4 address
echo $ipTools->decimalToIpv4(134744072);

// Convert IPv4 range into CIDR
echo '<pre>';
print_r($ipTools->ipv4ToCidr('8.0.0.0', '8.255.255.255'));
echo '</pre>';

// Convert CIDR to IPv4 range
echo '<pre>';
print_r($ipTools->cidrToIpv4('8.0.0.0/8'));
echo '</pre>';

// Convert CIDR into IPv6 range
echo '<pre>';
print_r($ipTools->cidrToIpv6('2002::1234:abcd:ffff:c0a8:101/64'));
echo '</pre>';

// Convert IPv6 range into CIDR
echo '<pre>';
print_r($ipTools->ipv6ToCidr('2002:0000:0000:1234:abcd:ffff:c0a8:0000', '2002:0000:0000:1234:ffff:ffff:ffff:ffff'));
echo '</pre>';

// Compress IPv6
echo '<pre>';
print_r($ipTools->compressIpv6('2002:0000:0000:1234:FFFF:FFFF:FFFF:FFFF'));
echo '</pre>';

// Expand IPv6
echo '<pre>';
print_r($ipTools->expandIpv6('2002::1234:FFFF:FFFF:FFFF:FFFF'));
echo '</pre>';

echo '<br>';

// Convert decimal into IPv6 address
echo $ipTools->decimalToIpv6('42541956123769884636017138956568135816');

// List country information for US
$country = new \IP2Location\Country('./data/IP2LOCATION-COUNTRY-INFORMATION-BASIC.CSV');

echo '<pre>';
print_r($country->getCountryInfo('US'));
echo '</pre>';

// Get region code by country code and region
$region = new \IP2Location\Region('./data/IP2LOCATION-ISO3166-2.CSV');

echo $region->getRegionCode('US', 'California');
