IP2Location (PHP Module)
========================
[![Latest Stable Version](https://img.shields.io/packagist/v/ip2location/ip2location-php.svg)](https://packagist.org/packages/ip2location/ip2location-php)
[![Total Downloads](https://img.shields.io/packagist/dt/ip2location/ip2location-php.svg?style=flat-square)](https://packagist.org/packages/ip2location/ip2location-php)  

*This is the official release maintained by IP2Location.com*

This PHP module provides fast lookup of country, region, district, city, latitude, longitude, ZIP code, time zone, ISP, domain name, connection speed, IDD code, area code, weather station code, weather station name, MNC, MCC, mobile brand, elevation, usage type, address type, IAB category and ASN from IP address by using IP2Location database. This module uses a file based database available at IP2Location.com.

This module can be used in many types of projects such as:

1. select the geographically closest mirror
2. analyze your web server logs to determine the countries of your visitors
3. credit card fraud detection
4. software export controls
5. display native language and currency 
6. prevent password sharing and abuse of service 
7. geotargeting in advertisement

Free IP2Location LITE and commerical databases are available for download. 
* LITE database is available at https://lite.ip2location.com (Free with limited accuracy)
* Commercial database is availabe at https://www.ip2location.com (Comprehensive with high accuracy)

Monthly update is available for both IP2Location LITE and commercial database.

## KEY FEATURES

1. **Support both IPv4 and IPv6 with ease.** If you would like to enable IPv6 support, you just need to replace your BIN file with IPv6 version. That's it, and no code modification needed.
2. **Extensible.** If you require different granularity of IP information, you can visit [IP2Location.com](https://www.ip2location.com/databases) to download the relevant BIN file, and the information will made ready for you.
3. **Comprehensive Information.** There are more than 13 types of information that you can retrieve from an IP address. Please visit [IP2Location.com](https://www.ip2location.com/databases) for details.

# Developer Documentation

To learn more about installation, usage, and code examples, please visit the developer documentation at [https://ip2location-php.readthedocs.io/en/latest/index.html](https://ip2location-php.readthedocs.io/en/latest/index.html).


## DEPENDENCIES

This library requires IP2Location BIN data file to function. You may download the BIN data file at
* IP2Location LITE BIN Data (Free): https://lite.ip2location.com
* IP2Location Commercial BIN Data (Comprehensive): https://www.ip2location.com

An outdated BIN database was provided in the databases folder for your testing. You are recommended to visit the above links to download the latest BIN database.

You can also sign up for [IP2Location Web Service](https://www.ip2location.com/web-service/ip2location) to lookup by IP2Location API.

## BIN DOWNLOADER SCRIPT
```
php ip2location_bin_download.php --token DOWNLOAD_TOKEN --file DATABASE_CODE -y
```

The command above will download the DATABASE_CODE BIN file and unzip the file into *data* folder.

| Parameters | Description |
|---|---|
|token|Download token. You can get your token at your [IP2Location Account Area](https://www.ip2location.com/account) at the Download page.|
|file|Database package. (DB1BIN...DB26BIN, DB1BINIPV6...DB26BINIPV6, DB1LITEBIN...DB11LITEBIN or DB1LITEBINIPV6...DB11LITEBINIPV6) You may login to your [IP2Location Account Area](https://www.ip2location.com/account) and get the package code (or download code) at the Download page. |
|y|Auto replace the current BIN file without acknowledgement.|

You can set the **DOWNLOAD_TOKEN** and **DATABASE_CODE** with values in the .env file (same path as *ip2location_bin_download.php*) and run the command `php ip2location_bin_download.php` for the download.

## IPv4 BIN vs IPv6 BIN
* Use the IPv4 BIN file if you just need to query IPv4 addresses.
* Use the IPv6 BIN file if you need to query BOTH IPv4 and IPv6 addresses.

## OTHER FRAMEWORK LIBRARY
Below are the list of other framework library that you can install and use right away.
* [IP2Location Laravel](https://github.com/ip2location/ip2location-laravel)
* [IP2Location CakePHP](https://github.com/ip2location/ip2location-cakephp)
* [IP2Location CodeIgniter](https://github.com/ip2location/codeigniter-ip2location)
* [IP2Location Yii](https://github.com/ip2location/ip2location-yii)
* [Symfony Framework](https://blog.ip2location.com/knowledge-base/geolocation-lookup-using-symfony-4-and-ip2location-bin-database/). Tutorial on the Symfony implementation.

## COPYRIGHT AND LICENSE

Copyright (C) 2005-2024 by IP2Location.com

License under MIT
