<?php
require_once('ip2location.class.php');

$ip = new ip2location;
$ip->open('databases/IP-COUNTRY-SAMPLE.BIN');
$record = $ip->getAll(isset($_GET['ip']) ? $_GET['ip'] : $_SERVER['REMOTE_ADDR']);

echo '<b>IP Address:</b> ' . $record->ipAddress . '<br>';
echo '<b>IP Number:</b> ' . $record->ipNumber . '<br>';
echo '<b>Country Short:</b> ' . $record->countryShort . '<br>';
echo '<b>Country Long:</b> ' . $record->countryLong . '<br>';
echo '<b>Region:</b> ' . $record->region . '<br>';
echo '<b>City:</b> ' . $record->city . '<br>';
echo '<b>ISP/Organisation:</b> ' . $record->isp . '<br>';
echo '<b>Latitude:</b> ' . $record->latitude . '<br>';
echo '<b>Longitude:</b> ' . $record->longitude . '<br>';
echo '<b>Domain:</b> ' . $record->domain . '<br>';
echo '<b>ZIP Code:</b> ' . $record->zipCode . '<br>';
echo '<b>Time Zone:</b> ' . $record->timeZone . '<br>';
echo '<b>Net Speed:</b> ' . $record->netSpeed . '<br>';
echo '<b>IDD Code:</b> ' . $record->iddCode . '<br>';
echo '<b>Area Code:</b> ' . $record->areaCode . '<br>';
echo '<b>Weather Station Code:</b> ' . $record->weatherStationCode . '<br>';
echo '<b>Weather Station Name:</b> ' . $record->areaCode . '<br>';
echo '<b>MCC:</b> ' . $record->mcc . '<br>';
echo '<b>MNC:</b> ' . $record->mnc . '<br>';
echo '<b>Mobile Brand:</b> ' . $record->mobileBrand . '<br>';
echo '<b>Elevation:</b> ' . $record->elevation . '<br>';
echo '<b>Usage Type:</b> ' . $record->usageType . '<br>';
?>
