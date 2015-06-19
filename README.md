#IP2Location PHP v7
This PHP module provides fast lookup of country, region, city, latitude, longitude, ZIP code, time zone, ISP, domain name, connection speed, IDD code, area code, weather station code, weather station name, MNC, MCC, mobile brand, elevation, and usage type from IP address by using IP2Location database. The library reads the geo location information
from **IP2Location BIN data** file.

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


# Installation
To install this module, unzip the package and copy the following files to your web folder.
 1. IP2Location.php
 2. example.php
 3. databases/IP-COUNTRY-SAMPLE.BIN
 4. databases/IPV6-COUNTRY-SAMPLE.BIN
 
To test this installation, please browse example.php using web browser.


#### Note
This module require php_gmp extension. Please find the below procedures to enable the module.

### For Windows environment:
Uncomment the extension in php.ini by removing the semi-colon
    `;extension=php_gmp.dll`


### For Linux/Mac envionment:
   `install the php5-gmp package`


# Sample BIN Databases
* Download free IP2Location LITE databases at [http://lite.ip2location.com](http://lite.ip2location.com)  
* Download IP2Location sample databases at [http://www.ip2location.com/developers](http://www.ip2location.com/developers)

# Support
Email: support@ip2location.com.  
URL: [http://www.ip2location.com](http://www.ip2location.com)
