IP2Location (PHP Module)
========================
[![Latest Stable Version](https://img.shields.io/packagist/v/ip2location/ip2location-php.svg)](https://packagist.org/packages/ip2location/ip2location-php)
[![Total Downloads](https://img.shields.io/packagist/dt/ip2location/ip2location-php.svg?style=flat-square)](https://packagist.org/packages/ip2location/ip2location-php)  

*This is the official release maintained by IP2Location.com*

This PHP module provides fast lookup of country, region, city, latitude, longitude, ZIP code, time zone, ISP, domain name, connection speed, IDD code, area code, weather station code, weather station name, MNC, MCC, mobile brand, elevation, and usage type from IP address by using IP2Location database. This module uses a file based database available at IP2Location.com. 

This module can be used in many types of projects such as:

1. select the geographically closest mirror
2. analyze your web server logs to determine the countries of your visitors
3. credit card fraud detection
4. software export controls
5. display native language and currency 
6. prevent password sharing and abuse of service 
7. geotargeting in advertisement

The database will be updated in monthly basis for the greater accuracy. Free sample database is available at /samples directory or download it from http://www.ip2location.com/developers.

The complete database is available at http://www.ip2location.com under Premium subscription package. This module also support IP2Location IPv6 version.


## INSTALLATION

To install this module, unzip the package and copy the following files to your web folder.
* IP2Location.php
* example.php
* databases/IP-COUNTRY-SAMPLE.BIN
* databases/IPV6-COUNTRY-SAMPLE.BIN
 
To test this installation, please browse example.php using web browser.

## USAGE

Below is the description of the functions available in this class

| Function Name | Descripton |
|---|---|
|Constructor|Expect 2 input parameters:<ol><li>Full path of IP2Location BIN data file.</li><li>File Open Mode<ul><li>	SHARED_MEMORY</li><li>MEMORY_CACHE</li><li>FILE_IO</li></ul></li></ol>For SHARED_MEMORY and MEMORY_CACHE, it will require your server to have sufficient memory to hold the BIN data, otherwise it will raise the errors during the object initialization.|
|getDate|Return the database's compilation date as a string of the form 'YYYY-MM-DD'|
|getType|Return the database's type, 1 to 24 respectively for DB1 to DB24. Please visit https://www.ip2location.com/databases for details.|
|getModuleVersion|Return the version of module|
|getDatabaseVersion|Return the version of database|
|lookup|Return the IP information in array. Below is the information returned:<ul><li>ipNumber</li><li>ipVersion</li><li>ipAddress</li><li>countryCode</li><li>countryName</li><li>regionName</li><li>cityName</li><li>latitude</li><li>longitude</li><li>areaCode</li><li>iddCode</li><li>weatherStationCode</li><li>weatherStationName</li><li>mcc</li><li>mnc</li><li>mobileCarrierName</li><li>usageType</li><li>elevation</li><li>netSpeed</li><li>timeZone</li><li>zipCode</li><li>domainName</li><li>isp</li></ul>You can visit [IP2Location](https://www.ip2location.com/databases/db24-ip-country-region-city-latitude-longitude-zipcode-timezone-isp-domain-netspeed-areacode-weather-mobile-elevation-usagetype) for the description of each field. Note: although the above names are not exactly matched with the names given in this link, but they are self-described.|

## DEPENDENCIES (IP2LOCATION BIN DATA FILE)

This library requires IP2Location BIN data file to function. You may download the BIN data file at
* IP2Location LITE BIN Data (Free): http://lite.ip2location.com
* IP2Location Commercial BIN Data (Comprehensive): http://www.ip2location.com

An outdated BIN database was provided in the databases folder for your testing. You are recommended to visit the above links to download the latest BIN database.

## COPYRIGHT AND LICENCE

Copyright (C) 2005-2015 by IP2Location.com

License under LGPLv3
