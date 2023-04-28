<?php

// Preset PHP settings
error_reporting(E_ALL);
ini_set('display_errors', 0);
date_default_timezone_set('UTC');

define('DS', DIRECTORY_SEPARATOR);

// Define root directory
define('ROOT', __DIR__ . DS);

// Define folders directory
define('DATA', ROOT . 'data' . DS);
define('DBTEMP', ROOT . 'dbtemp' . DS);


$shortOpt = "";
$longOpt = ["token:", "file:"];
$inputs = getopt($shortOpt, $longOpt);

$token = '';
$dbCode = '';
$fileName = '';
$fileSize = 0;

if(isset($inputs["token"])) {
    $token = $inputs["token"];
}

if(isset($inputs["file"])) {
    $dbCode = $inputs["file"];
}

if ($token == '') {
    if (!empty(getenv('DOWNLOAD_TOKEN'))) {
        $token = getenv('DOWNLOAD_TOKEN');
    } else {
        echo "[Error] Missing --token command line switch or parameter.\n";
        exit;
    }
}

if ($dbCode == '') {
    if (!empty(getenv('DATABASE_CODE'))) {
        $dbCode = getenv('DATABASE_CODE');
    } else {
        echo "[Error] Missing --file command line switch or parameter.\n";
        exit;
    }
}

$action = readline('Do you want to proceed with the download as it will replace the existing BIN file if found? (y/n): ');
if (strtolower(trim($action)) == 'y') {
    switch($dbCode) {
        case 'DB1BIN':
            $fileName = "IP-COUNTRY.BIN.ZIP";
        break;

        case 'DB2BIN':
            $fileName = "IP-COUNTRY-ISP.BIN.ZIP";
        break;

        case 'DB3BIN':
            $fileName = "IP-COUNTRY-REGION-CITY.BIN.ZIP";
        break;

        case 'DB4BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-ISP.BIN.ZIP";
        break;

        case 'DB5BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE.BIN.ZIP";
        break;

        case 'DB6BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP.BIN.ZIP";
        break;

        case 'DB7BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB8BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB9BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE.BIN.ZIP";
        break;

        case 'DB10BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB11BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE.BIN.ZIP";
        break;

        case 'DB12BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB13BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-TIMEZONE-NETSPEED.BIN.ZIP";
        break;

        case 'DB14BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED.BIN.ZIP";
        break;

        case 'DB15BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE.BIN.ZIP";
        break;

        case 'DB16BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE.BIN.ZIP";
        break;

        case 'DB17BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-TIMEZONE-NETSPEED-WEATHER.BIN.ZIP";
        break;

        case 'DB18BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER.BIN.ZIP";
        break;

        case 'DB19BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE.BIN.ZIP";
        break;

        case 'DB20BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE.BIN.ZIP";
        break;

        case 'DB21BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE-ELEVATION.BIN.ZIP";
        break;

        case 'DB22BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION.BIN.ZIP";
        break;

        case 'DB23BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE-USAGETYPE.BIN.ZIP";
        break;

        case 'DB24BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE.BIN.ZIP";
        break;

        case 'DB25BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE-ADDRESSTYPE-CATEGORY.BIN.ZIP";
        break;

        case 'DB26BIN':
            $fileName = "IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE-ADDRESSTYPE-CATEGORY-DISTRICT-ASN.BIN.ZIP";
        break;

        case 'DB1BINIPV6':
            $fileName = "IPV6-COUNTRY.BIN.ZIP";
        break;

        case 'DB2BINIPV6':
            $fileName = "IPV6-COUNTRY-ISP.BIN.ZIP";
        break;

        case 'DB3BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY.BIN.ZIP";
        break;

        case 'DB4BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-ISP.BIN.ZIP";
        break;

        case 'DB5BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE.BIN.ZIP";
        break;

        case 'DB6BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP.BIN.ZIP";
        break;

        case 'DB7BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB8BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB9BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE.BIN.ZIP";
        break;

        case 'DB10BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB11BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE.BIN.ZIP";
        break;

        case 'DB12BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN.BIN.ZIP";
        break;

        case 'DB13BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-TIMEZONE-NETSPEED.BIN.ZIP";
        break;

        case 'DB14BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED.BIN.ZIP";
        break;

        case 'DB15BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE.BIN.ZIP";
        break;

        case 'DB16BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE.BIN.ZIP";
        break;

        case 'DB17BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-TIMEZONE-NETSPEED-WEATHER.BIN.ZIP";
        break;

        case 'DB18BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER.BIN.ZIP";
        break;

        case 'DB19BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE.BIN.ZIP";
        break;

        case 'DB20BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE.BIN.ZIP";
        break;

        case 'DB21BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE-ELEVATION.BIN.ZIP";
        break;

        case 'DB22BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION.BIN.ZIP";
        break;

        case 'DB23BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE-USAGETYPE.BIN.ZIP";
        break;

        case 'DB24BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE.BIN.ZIP";
        break;

        case 'DB25BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE-ADDRESSTYPE-CATEGORY.BIN.ZIP";
        break;

        case 'DB26BINIPV6':
            $fileName = "IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE-ADDRESSTYPE-CATEGORY-DISTRICT-ASN.BIN.ZIP";
        break;

        case 'DB1LITEBIN':
            $fileName = "IP2LOCATION-LITE-DB1.BIN.ZIP";
        break;

        case 'DB3LITEBIN':
            $fileName = "IP2LOCATION-LITE-DB3.BIN.ZIP";
        break;

        case 'DB5LITEBIN':
            $fileName = "IP2LOCATION-LITE-DB5.BIN.ZIP";
        break;

        case 'DB9LITEBIN':
            $fileName = "IP2LOCATION-LITE-DB9.BIN.ZIP";
        break;

        case 'DB11LITEBIN':
            $fileName = "IP2LOCATION-LITE-DB11.BIN.ZIP";
        break;

        case 'DB1LITEBINIPV6':
            $fileName = "IP2LOCATION-LITE-DB1-IPV6.BIN.ZIP";
        break;

        case 'DB3LITEBINIPV6':
            $fileName = "IP2LOCATION-LITE-DB3-IPV6.BIN.ZIP";
        break;

        case 'DB5LITEBINIPV6':
            $fileName = "IP2LOCATION-LITE-DB5-IPV6.BIN.ZIP";
        break;

        case 'DB9LITEBINIPV6':
            $fileName = "IP2LOCATION-LITE-DB9-IPV6.BIN.ZIP";
        break;

        case 'DB11LITEBINIPV6':
            $fileName = "IP2LOCATION-LITE-DB11-IPV6.BIN.ZIP";
        break;

        default:
            $fileName = '';
    }

    if ($fileName == '') {
        echo "[Error] Unknown --file command line parameter.\n";
        exit;
    }

    // Check token information
    $queries = [
        'token'   => $token,
        'package' => $dbCode,
    ];
    $response = post('https://www.ip2location.com/download-info', $queries);

    if (!$response) {
        echo "[Error] Error while verifying account.\n";
        exit;
    } else {
        $data = explode(";", $response);
        if ($data[0] == "OK") {
            $fileSize = $data[3];
        } else if ($data[0] == "EXPIRED") {
            echo "[Error] This download account has been expired since $data[1]. Please visit https://www.ip2location.com to renew the subscription.\n";
            exit;
        } else if ($data[0] == "NOPERMISSION") {
            echo "[Error] This download account or token could not download database due to permission issue.\n";
            exit;
        } else {
            echo "[Error] Unknown issue. Please contact support\@ip2location.com.\n";
            exit;
        }
    }

    // Download the BIN ZIP file
    $queries = [
        'token' => $token,
        'file'  => $dbCode,
    ];
    $response = post('https://www.ip2location.com/download', $queries, $fileName);

    if (!$response) {
        echo "[Error] Error while downloading.\n";
        exit;
    } else {
        if ($fileSize != filesize($fileName)) {
            echo "[Error] Incorrect file size of " . $fileName . ".\n";
            exit;
        }
    }

    $zip = new ZipArchive;
    $res = $zip->open($fileName);
    if ($res === TRUE) {
        $zip->extractTo("dbtemp");
    } else {
        echo "[Error] Unzip error of " . $fileName . ".\n";
        exit;
    }
    $zip->close();

    rename(DBTEMP . substr($fileName, 0, -4), DATA . substr($fileName, 0, -4));
    array_map("unlink", glob(DBTEMP . "*"));
    array_map("rmdir", glob(DBTEMP . "*"));
    rmdir(DBTEMP);
    unlink($fileName);
} else {
    exit;
}

function post($url, $fields = [], $file = '') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_USERAGENT, 'IP2Location PHP SDK');

    $queries = (!empty($fields)) ? http_build_query($fields) : '';

    if ($queries) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queries);
    }

    if ($file) {
        $fp = @fopen($file, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
    }

    $response = curl_exec($ch);

    if (empty($response) || curl_error($ch)) {
        curl_close($ch);
        if ($file) { fclose($fp); }
        return false;
    }

    curl_close($ch);
    if ($file) { fclose($fp); }
    return $response;
}
