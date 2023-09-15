# IP2Location PHP API

## Database Class

```{py:class} Database($file, $mode)
Initiate the IP2Location class and load the IP2Location BIN database.

:param str $file: (Required) The file path links to IP2Location BIN databases.
:param str $mode: (Optional) The file mode used to open the IP2Location BIN database. Options available are FILE_IO, SHARED_MEMORY and MEMORY_CACHE. Default is FILE_IO.
```

```{py:function} lookup($ip)
Retrieve geolocation information for an IP address.

:param str $ip: (Required) The IP address (IPv4 or IPv6).
:return: Returns the geolocation information in dict. Refer below table for the fields avaliable in the dict
:rtype: array

**RETURN FIELDS**

| Field Name       | Description                                                  |
| ---------------- | ------------------------------------------------------------ |
| countryCode    |     Two-character country code based on ISO 3166. |
| countryName     |     Country name based on ISO 3166. |
| regionName           |     Region or state name. |
| cityName             |     City name. |
| isp              |     Internet Service Provider or company\'s name. |
| latitude         |     City latitude. Defaults to capital city latitude if city is unknown. |
| longitude        |     City longitude. Defaults to capital city longitude if city is unknown. |
| domainName           |     Internet domain name associated with IP address range. |
| zipCode          |     ZIP code or Postal code. [172 countries supported](https://www.ip2location.com/zip-code-coverage). |
| timeZone         |     UTC time zone (with DST supported). |
| netSpeed         |     Internet connection type. |
| iddCode         |     The IDD prefix to call the city from another country. |
| areaCode        |     A varying length number assigned to geographic areas for calls between cities. [223 countries supported](https://www.ip2location.com/area-code-coverage). |
| weatherStationCode     |     The special code to identify the nearest weather observation station. |
| weatherStationName     |     The name of the nearest weather observation station. |
| mcc              |     Mobile Country Codes (MCC) as defined in ITU E.212 for use in identifying mobile stations in wireless telephone networks, particularly GSM and UMTS networks. |
| mnc              |     Mobile Network Code (MNC) is used in combination with a Mobile Country Code(MCC) to uniquely identify a mobile phone operator or carrier. |
| mobileCarrierName     |     Commercial brand associated with the mobile carrier. You may click [mobile carrier coverage](https://www.ip2location.com/mobile-carrier-coverage) to view the coverage report. |
| elevation        |     Average height of city above sea level in meters (m). |
| usageType       |     Usage type classification of ISP or company. |
| addressType     |     IP address types as defined in Internet Protocol version 4 (IPv4) and Internet Protocol version 6 (IPv6). |
| category         |     The domain category based on [IAB Tech Lab Content Taxonomy](https://www.ip2location.com/free/iab-categories). |
| district         |     District or county name. |
| asn              |     Autonomous system number (ASN). BIN databases. |
| as               |     Autonomous system (AS) name. |
```

## IpTools Class

```{py:class} IpTools()
Initiate IpTools class.
```

```{py:function} isIpv4($ip)
Verify if a string is a valid IPv4 address.

:param str $ip: (Required) IP address.
:return: Return True if the IP address is a valid IPv4 address or False if it isn't a valid IPv4 address.
:rtype: boolean
```

```{py:function} isIpv6($ip)
Verify if a string is a valid IPv6 address

:param str $ip: (Required) IP address.
:return: Return True if the IP address is a valid IPv6 address or False if it isn't a valid IPv6 address.
:rtype: boolean
```

```{py:function} ipv4ToDecimal($ip)
Translate IPv4 address from dotted-decimal address to decimal format.

:param str $ip: (Required) IPv4 address.
:return: Return the decimal format of the IPv4 address.
:rtype: int
```

```{py:function} decimalToIpv4($number)
Translate IPv4 address from decimal number to dotted-decimal address.

:param str $number: (Required) Decimal format of the IPv4 address.
:return: Returns the dotted-decimal format of the IPv4 address.
:rtype: string
```

```{py:function} ipv6ToDecimal($ip)
Translate IPv6 address from hexadecimal address to decimal format.

:param str $ip: (Required) IPv6 address.
:return: Return the decimal format of the IPv6 address.
:rtype: int
```

```{py:function} decimalToIpv6($number)
Translate IPv6 address from decimal number into hexadecimal address.

:param str $number: (Required) Decimal format of the IPv6 address.
:return: Returns the hexadecimal format of the IPv6 address.
:rtype: string
```

```{py:function} ipv4ToCidr($ipFrom, $ipTo)
Convert IPv4 range into a list of IPv4 CIDR notation.

:param str $ipFrom: (Required) The starting IPv4 address in the range.
:param str $ipTo: (Required) The ending IPv4 address in the range.
:return: Returns the list of IPv4 CIDR notation.
:rtype: list
```

```{py:function} cidrToIpv4($cidr)
Convert IPv4 CIDR notation into a list of IPv4 addresses.

:param str $cidr: (Required) IPv4 CIDR notation.
:return: Returns an array of IPv4 addresses.
:rtype: dict
```

```{py:function} ipv6ToCidr($ipFrom, $ipTo)
Convert IPv6 range into a list of IPv6 CIDR notation.

:param str $ipFrom: (Required) The starting IPv6 address in the range.
:param str $ipTo: (Required) The ending IPv6 address in the range.
:return: Returns the list of IPv6 CIDR notation.
:rtype: list
```

```{py:function} cidrToIpv6($cidr)
Convert IPv6 CIDR notation into a list of IPv6 addresses.

:param str $cidr: (Required) IPv6 CIDR notation.
:return: Returns an array of IPv6 addresses.
:rtype: dict
```


```{py:function} compressIpv6($ip)
Compress a IPv6 to shorten the length.

:param str $ip: (Required) IPv6 address.
:return: Returns the compressed version of IPv6 address.
:rtype: str
```

```{py:function} expandIpv6($ip)
Expand a shorten IPv6 to full length.

:param str $ip: (Required) IPv6 address.
:return: Returns the extended version of IPv6 address.
:rtype: str
```

```{py:function} getVisitorIp($ipData)
Return the real IP address of the visitor. If an array of $ipData is supplied, it will return the list of IP address data found.

:param array $ipData: (Optional) List of IP addresses.
:return: Returns the extended version of IPv6 address.
:rtype: str
```

## Country Class

```{py:class} Country($csv)
Initiate Country class and load the IP2Location Country Information CSV file. This database is free for download at <https://www.ip2location.com/free/country-information>.

:param str $csv: (Required) The file path links to IP2Location Country Information CSV file.
```

```{py:function} getCountryInfo($countryCode)
Provide a ISO 3166 country code to get the country information in array. Will return a full list of countries information if country code not provided. 

:param str $countryCode: (Required) The ISO 3166 country code of a country.
:return: Returns the country information in dict. Refer below table for the fields avaliable in the dict.
:rtype: dict

**RETURN FIELDS**

| Field Name       | Description                                                  |
| ---------------- | ------------------------------------------------------------ |
| country_code     | Two-character country code based on ISO 3166.                |
| country_alpha3_code | Three-character country code based on ISO 3166.           |
| country_numeric_code | Three-character country code based on ISO 3166.          |
| capital          | Capital of the country.                                      |
| country_demonym  | Demonym of the country.                                      |
| total_area       | Total area in km{sup}`2`.                                    |
| population       | Population of year 2014.                                     |
| idd_code         | The IDD prefix to call the city from another country.        |
| currency_code    | Currency code based on ISO 4217.                             |
| currency_name    | Currency name.                                               |
| currency_symbol  | Currency symbol.                                             |
| lang_code        | Language code based on ISO 639.                              |
| lang_name        | Language name.                                               |
| cctld            | Country-Code Top-Level Domain.                               |
```

## Region Class

```{py:class} Region($csv)
Initiate Region class and load the IP2Location ISO 3166-2 Subdivision Code CSV file. This database is free for download at <https://www.ip2location.com/free/iso3166-2>

:param str $csv: (Required) The file path links to IP2Location ISO 3166-2 Subdivision Code CSV file.
```

```{py:function} getRegionCode($countryCode, $regionName)
Provide a ISO 3166 country code and the region name to get ISO 3166-2 subdivision code for the region.

:param str $countryCode: (Required) Two-character country code based on ISO 3166.
:param str $regionName: (Required) Region or state name.
:return: Returns the ISO 3166-2 subdivision code of the region.
:rtype: str
```
