<?php
/**
 * Copyright (C) 2005-2014 IP2Location.com
 * All Rights Reserved
 *
 * This library is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; If not, see <http://www.gnu.org/licenses/>.
 */

class IP2LocationRecord {
  public $ipAddress;
  public $ipNumber;
  public $countryCode;
  public $countryName;
  public $regionName;
  public $cityName;
  public $latitude;
  public $longitude;
  public $isp;
  public $domainName;
  public $zipCode;
  public $timeZone;
  public $netSpeed;
  public $iddCode;
  public $areaCode;
  public $weatherStationCode;
  public $weatherStationName;
  public $mcc;
  public $mnc;
  public $mobileCarrierName;
  public $elevation;
  public $usageType;
}

class IP2Location {
  // Current version.
  const VERSION = '7.0.0';

  // Database storage method.
  const FILE_IO = 0;
  const MEMORY_CACHE = 1;
  const SHARED_MEMORY = 2;

  // Unpack method.
  const ENDIAN = 0;
  const BIG_ENDIAN = 1;

  // Record field.
  const ALL = 100;
  const COUNTRY_CODE = 1;
  const COUNTRY_NAME = 2;
  const REGION_NAME = 3;
  const CITY_NAME = 4;
  const LATITUDE = 5;
  const LONGITUDE = 6;
  const ISP = 7;
  const DOMAIN_NAME = 8;
  const ZIP_CODE = 9;
  const TIME_ZONE = 10;
  const NET_SPEED = 11;
  const IDD_CODE = 12;
  const AREA_CODE = 13;
  const WEATHER_STATION_CODE = 14;
  const WEATHER_STATION_NAME = 15;
  const MCC = 16;
  const MNC = 17;
  const MOBILE_CARRIER_NAME = 18;
  const ELEVATION = 19;
  const USAGE_TYPE = 20;

  // IP version.
  const IPV4 = 0;
  const IPV6 = 1;

  // SHMOP memory address.
  const SHM_KEY = 4194500608;

  // Message.
  const FIELD_NOT_SUPPORTED = 'This parameter is unavailable in selected .BIN data file. Please upgrade data file.';
  const INVALID_IP_ADDRESS = 'Invalid IP address.';

  private $columns = array(
    'COUNTRY_CODE' => array(
      0,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,
    ),
    'COUNTRY_NAME' => array(
      0,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,2,
    ),
    'REGION_NAME' => array(
      0,0,0,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,
    ),
    'CITY_NAME' => array(
      0,0,0,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,4,
    ),
    'LATITUDE' => array(
      0,0,0,0,0,5,5,0,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,5,
    ),
    'LONGITUDE' => array(
      0,0,0,0,0,6,6,0,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,6,
    ),
    'ISP' => array(
      0,0,3,0,5,0,7,5,7,0,8,0,9,0,9,0,9,0,9,7,9,0,9,7,9,
    ),
    'DOMAIN_NAME' => array(
      0,0,0,0,0,0,0,6,8,0,9,0,10,0,10,0,10,0,10,8,10,0,10,8,10,
    ),
    'ZIP_CODE' => array(
      0,0,0,0,0,0,0,0,0,7,7,7,7,0,7,7,7,0,7,0,7,7,7,0,7,
    ),
    'TIME_ZONE' => array(
      0,0,0,0,0,0,0,0,0,0,0,8,8,7,8,8,8,7,8,0,8,8,8,0,8,
    ),
    'NET_SPEED' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,8,11,0,11,8,11,0,11,0,11,0,11,
    ),
    'IDD_CODE' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,9,12,0,12,0,12,9,12,0,12,
    ),
    'AREA_CODE' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,10,13,0,13,0,13,10,13,0,13,
    ),
    'WEATHER_STATION_CODE' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,9,14,0,14,0,14,0,14,
    ),
    'WEATHER_STATION_NAME' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,10,15,0,15,0,15,0,15,
    ),
    'MCC' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,9,16,0,16,9,16,
    ),
    'MNC' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,10,17,0,17,10,17,
    ),
    'MOBILE_CARRIER_NAME' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,11,18,0,18,11,18,
    ),
    'ELEVATION' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,11,19,0,19,
    ),
    'USAGE_TYPE' => array(
      0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,12,20,
    ),
  );

  private $shmId = '';
  private $database = array();
  private $unpackMethod;
  private $buffer;
  private $mode;
  private $resource;
  private $result;

  /**
   * Constructor.
   */
  public function __construct($file = NULL, $mode = self::FILE_IO) {
    if (!file_exists($file)) {
      throw new Exception('IP2Location.class.php: Unable to open file "' . $file . '".');
    }

    // Define system unpack method.
    list($test) = array_values(unpack('L1L', pack('V', 1)));

    // Use Big Endian Unpack if endian test failed.
    $this->unpackMethod = (($test != 1)) ? self::BIG_ENDIAN : self::ENDIAN;

    switch ($mode) {
      case self::SHARED_MEMORY:
        if (!function_exists('shmop_open')) {
          throw new Exception('IP2Location.class.php: Please make sure your PHP setup has "php_shmop" enabled.');
        }

        $this->mode = self::SHARED_MEMORY;

        $this->shmId = @shmop_open(self::SHM_KEY, 'a', 0, 0);

        if ($this->shmId === FALSE) {
          // First execution, load database into memory.
          if (($fp = fopen($file, 'rb')) === FALSE) {
            throw new Exception('IP2Location.class.php: Unable to open file "' . $file . '".');
          }

          $stats = fstat($fp);

          if ($shm_id = @shmop_open(self::SHM_KEY, 'w', 0, 0)) {
            shmop_delete($shm_id);
            shmop_close($shm_id);
          }

          if ($shm_id = @shmop_open(self::SHM_KEY, 'c', 0644, $stats['size'])) {
            $pointer = 0;
            while ($pointer < $stats['size']) {
              $buf = fread($fp, 524288);
              shmop_write($shm_id, $buf, $pointer);
              $pointer += 524288;
            }
            shmop_close($shm_id);
          }
          fclose($fp);

          $this->shmId = @shmop_open(self::SHM_KEY, 'a', 0, 0);

          if ($this->shmId === FALSE) {
            throw new Exception('IP2Location.class.php: Unable to access shared memory block.');
          }
        }
        break;

      default:
        $this->mode = self::FILE_IO;
        $this->resource = fopen($file, 'rb');

        if ($mode == self::MEMORY_CACHE) {
          $this->mode = self::MEMORY_CACHE;
          $stats = fstat($this->resource);
          $this->buffer = fread($this->resource, $stats['size']);
        }
    }

    $this->database['type'] = $this->readByte(1, '8');
    $this->database['column'] = $this->readByte(2, '8');
    $this->database['year'] = $this->readByte(3, '8');
    $this->database['month'] = $this->readByte(4, '8');
    $this->database['day'] = $this->readByte(5, '8');
    $this->database['ipv4_count'] = $this->readByte(6, '32');
    $this->database['ipv4_base_address'] = $this->readByte(10, '32');
    $this->database['ipv6_count'] = $this->readByte(14, '32');
    $this->database['ipv6_base_address'] = $this->readByte(18, '32');

    $this->result = new IP2LocationRecord();
  }

  /**
   * Read bytes.
   */
  private function readByte($pos, $mode = 'string', $auto_size = FALSE) {
    $result = '';

    switch ($this->mode) {
      case self::SHARED_MEMORY:
        if ($mode == 'string') {
          $data = shmop_read($this->shmId, $pos, ($auto_size) ? shmop_size($this->shmId) - $pos : 100);
        }
        else {
          $data = shmop_read($this->shmId, $pos - 1, 50);
        }
        break;

      case self::MEMORY_CACHE:
        $data = substr($this->buffer, (($mode == 'string') ? $pos : $pos - 1), 100);
        break;

      default:
        if ($mode == 'string') {
          fseek($this->resource, $pos, SEEK_SET);
          $data = @fread($this->resource, 1);
        }
        else {
          fseek($this->resource, $pos - 1, SEEK_SET);
          $data = @fread($this->resource, 50);
        }
    }

    switch ($mode) {
      case '8':
        $out = $this->readBinary('C', $data);
        $result = $out[1];

        break;

      case '32':
        $out = $this->readBinary('V', $data);
        if ($out[1] < 0) {
          $out[1] += 4294967296;
        }

        $result = (int) $out[1];

        break;

      case '128':
        $array = preg_split('//', $data, -1, PREG_SPLIT_NO_EMPTY);

        if (count($array) != 16) {
          $result = 0;
        }

        $ip96_127 = $this->readBinary('V', $array[0] . $array[1] . $array[2] . $array[3]);
        $ip64_95 = $this->readBinary('V', $array[4] . $array[5] . $array[6] . $array[7]);
        $ip32_63 = $this->readBinary('V', $array[8] . $array[9] . $array[10] . $array[11]);
        $ip1_31 = $this->readBinary('V', $array[12] . $array[13] . $array[14] . $array[15]);

        if ($ip96_127[1] < 0) {
          $ip96_127[1] += 4294967296;
        }
        if ($ip64_95[1] < 0) {
          $ip64_95[1] += 4294967296;
        }
        if ($ip32_63[1] < 0) {
          $ip32_63[1] += 4294967296;
        }
        if ($ip1_31[1] < 0) {
          $ip1_31[1] += 4294967296;
        }

        $result = bcadd(bcadd(bcmul($ip1_31[1], bcpow(4294967296, 3)), bcmul($ip32_63[1], bcpow(4294967296, 2))), bcadd(bcmul($ip64_95[1], 4294967296), $ip96_127[1]));

        break;

      case 'float':
        $out = $this->readBinary('f', $data);

        $result = $out[1];

        break;

      default:
        $out = $this->readBinary('C', $data);
        $result = (in_array($this->mode, array(self::SHARED_MEMORY, self::MEMORY_CACHE))) ? substr($data, 1, $out[1]) : @fread($this->resource, $out[1]);
    }

    return $result;
  }

  /**
   * Read binary.
   */
  private function readBinary($format, $data) {
    if ($this->unpackMethod == self::BIG_ENDIAN) {
      $ar = unpack($format, $data);
      $vals = array_values($ar);
      $f = explode('/', $format);
      $i = 0;

      foreach ($f as $f_value) {
        $repeater = intval(substr($f_value, 1));

        if ($repeater == 0) {
          $repeater = 1;
        }
        if ($f_value{1} == '*') {
          $repeater = count($ar) - $i;
        }
        if ($f_value{0} != 'd') {
          $i += $repeater;
          continue;
        }

        $j = $i + $repeater;

        for ($a = $i; $a < $j; ++$a) {
          $p = pack('d', $vals[$i]);
          $p = strrev($p);
          list($vals[$i]) = array_values(unpack('d1d', $p));
          ++$i;
        }
      }

      $a = 0;
      foreach ($ar as $ar_key => $ar_value) {
        $ar[$ar_key] = $vals[$a];
        ++$a;
      }
      return $ar;
    }
    return unpack($format, $data);
  }

  /**
   * Convert IPv6 into long integer.
   */
  private function ipv6Numeric($ipv6) {
    $ip_n = inet_pton($ipv6);
    $bits = 15;

    // 16 x 8 bit = 128bit
    $ipv6long = 0;

    while ($bits >= 0) {
      $bin = sprintf("%08b", (ord($ip_n[$bits])));

      if ($ipv6long) {
        $ipv6long = $bin . $ipv6long;
      }
      else {
        $ipv6long = $bin;
      }
      $bits--;
    }
    return gmp_strval(gmp_init($ipv6long, 2), 10);
  }

  /**
   * Validate IP address.
   */
  private function validate($ip) {
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
      return 4;
    }

    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
      return 6;
    }

    return FALSE;
  }

  /**
   * Core function to lookup geolocation data.
   */
  public function lookup($ip, $fields = self::ALL) {
    $this->result->ipAddress = $ip;

    if (($version = $this->validate($ip)) === FALSE) {
      foreach ($this->result as &$obj) {
        if ($obj) {
          continue;
        }

        $obj = self::INVALID_IP_ADDRESS;
      }

      return $this->result;
    }

    if ($version == 4) {
      return $this->ipv4Lookup($ip, $fields);
    }

    if ($version == 6) {
      return $this->ipv6Lookup($ip, $fields);
    }
  }

  /**
   * Lookup for IPv4 records.
   */
  private function ipv4Lookup($ip, $fields) {
    $keys = array_keys($this->columns);

    $base_address = $this->database['ipv4_base_address'];
    $high = $this->database['ipv4_count'];
    $ip_number = sprintf('%u', ip2long($ip));
    $ip_number = ($ip_number >= 4294967295) ? ($ip_number - 1) : $ip_number;
    $this->result->ipNumber = $ip_number;

    $low = 0;
    $mid = 0;
    $ip_from = 0;
    $ip_to = 0;

    while ($low <= $high) {
      $mid = (int) (($low + $high) / 2);
      $ip_from = $this->readByte($base_address + $mid * ($this->database['column'] * 4), 32);
      $ip_to = $this->readByte($base_address + ($mid + 1) * ($this->database['column'] * 4), 32);

      if ($ip_from < 0) {
        $ip_from += pow(2, 32);
      }

      if ($ip_to < 0) {
        $ip_to += pow(2, 32);
      }

      if (($ip_number >= $ip_from) && ($ip_number < $ip_to)) {
        $return = '';
        $pointer = $base_address + ($mid * $this->database['column'] * 4);

        switch ($fields) {
          case self::COUNTRY_CODE:
          case self::REGION_NAME:
          case self::CITY_NAME:
          case self::ISP:
          case self::DOMAIN_NAME:
          case self::ZIP_CODE:
          case self::TIME_ZONE:
          case self::NET_SPEED:
          case self::IDD_CODE:
          case self::AREA_CODE:
          case self::WEATHER_STATION_CODE:
          case self::WEATHER_STATION_NAME:
          case self::MCC:
          case self::MNC:
          case self::MOBILE_CARRIER_NAME:
          case self::ELEVATION:
            $return = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);

            break;

          case self::COUNTRY_NAME:
            $return = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), '32') + 3, 'string', TRUE);

            break;

          case self::LATITUDE:
          case self::LONGITUDE:
            $return = $this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), 'float', TRUE);

            break;

          case self::USAGE_TYPE:
            $return = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);

            break;

          default:
            $this->result->regionName = $this->result->cityName = $this->result->latitude = $this->result->longitude = $this->result->isp = $this->result->domainName = $this->result->zipCode = $this->result->timeZone = $this->result->netSpeed = $this->result->iddCode = $this->result->areaCode = $this->result->weatherStationCode = $this->result->weatherStationName = $this->result->mcc = $this->result->mnc = $this->result->mobileCarrierName = $this->result->elevation = $this->result->usageType = self::FIELD_NOT_SUPPORTED;

            $this->result->countryCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);

            $this->result->countryName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_NAME - 1]][$this->database['type']] - 1), '32') + 3, 'string', TRUE);

            if ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']] != 0) {
              $this->result->regionName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']] != 0) {
              $this->result->cityName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']] != 0) {
              $this->result->latitude = $this->readByte($pointer + 4 * ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']] - 1), 'float', TRUE);
            }

            if ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']] != 0) {
              $this->result->longitude = $this->readByte($pointer + 4 * ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']] - 1), 'float', TRUE);
            }

            if ($this->columns[$keys[self::ISP - 1]][$this->database['type']] != 0) {
              $this->result->isp = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::ISP - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']] != 0) {
              $this->result->domainName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']] != 0) {
              $this->result->zipCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']] != 0) {
              $this->result->timeZone = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']] != 0) {
              $this->result->netSpeed = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']] != 0) {
              $this->result->iddCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']] != 0) {
              $this->result->areaCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']] != 0) {
              $this->result->weatherStationCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']] != 0) {
              $this->result->weatherStationName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::MCC - 1]][$this->database['type']] != 0) {
              $this->result->mcc = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::MCC - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::MNC - 1]][$this->database['type']] != 0) {
              $this->result->mnc = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::MNC - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']] != 0) {
              $this->result->mobileCarrierName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']] != 0) {
              $this->result->elevation = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']] != 0) {
              $this->result->usageType = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']] - 1), '32'), 'string', TRUE);
            }

            return $this->result;
        }
        return $return;
      }
      else {
        if ($ip_number < $ip_from) {
          $high = $mid - 1;
        }
        else {
          $low = $mid + 1;
        }
      }
    }
  }

  /**
   * Lookup for IPv6 records.
   */
  private function ipv6Lookup($ip, $fields) {
    $keys = array_keys($this->columns);

    $base_address = $this->database['ipv6_base_address'];
    $ip_number = $this->ipv6Numeric($ip);
    $this->result->ipNumber = $ip_number;

    $low = 0;
    $mid = 0;
    $high = $this->database['ipv6_count'];
    $ip_from = 0;
    $ip_to = 0;

    while ($low <= $high) {
      $mid = (int) (($low + $high) / 2);
      $ip_from = $this->readByte($base_address + $mid * ($this->database['column'] * 4 + 12), 128);
      $ip_to = $this->readByte($base_address + ($mid + 1) * ($this->database['column'] * 4 + 12), 128);

      if ($ip_from < 0) {
        $ip_from += pow(2, 32);
      }

      if ($ip_to < 0) {
        $ip_to += pow(2, 32);
      }

      if (($ip_number >= $ip_from) && ($ip_number < $ip_to)) {
        $return = '';
        $pointer = $base_address + ($mid * ($this->database['column'] * 4 + 12)) + 8;

        switch ($fields) {
          case self::COUNTRY_CODE:
          case self::REGION_NAME:
          case self::CITY_NAME:
          case self::ISP:
          case self::DOMAIN_NAME:
          case self::ZIP_CODE:
          case self::TIME_ZONE:
          case self::NET_SPEED:
          case self::IDD_CODE:
          case self::AREA_CODE:
          case self::WEATHER_STATION_CODE:
          case self::WEATHER_STATION_NAME:
          case self::MCC:
          case self::MNC:
          case self::MOBILE_CARRIER_NAME:
          case self::ELEVATION:
            $return = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), '32'), 'string', TRUE);

            break;

          case self::COUNTRY_NAME:
            $return = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), '32') + 3, 'string', TRUE);

            break;

          case self::LATITUDE:
          case self::LONGITUDE:
            $return = $this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), 'float', TRUE);

            break;

          case self::USAGE_TYPE:
            $return = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[$fields - 1]][$this->database['type']]), '32'), 'string', TRUE);

            break;

          default:
            $this->result->regionName = $this->result->cityName = $this->result->latitude = $this->result->longitude = $this->result->isp = $this->result->domainName = $this->result->zipCode = $this->result->timeZone = $this->result->netSpeed = $this->result->iddCode = $this->result->areaCode = $this->result->weatherStationCode = $this->result->weatherStationName = $this->result->mcc = $this->result->mnc = $this->result->mobileCarrierName = $this->result->elevation = $this->result->usageType = self::FIELD_NOT_SUPPORTED;

            if ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']] != 0) {
              $this->result->countryCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']]), '32'), 'string', TRUE);

              $this->result->countryName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::COUNTRY_CODE - 1]][$this->database['type']]), '32') + 3, 'string', TRUE);
            }

            if ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']] != 0) {
              $this->result->regionName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::REGION_NAME - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']] != 0) {
              $this->result->cityName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::CITY_NAME - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']] != 0) {
              $this->result->latitude = $this->readByte($pointer + 4 * ($this->columns[$keys[self::LATITUDE - 1]][$this->database['type']]), 'float', TRUE);
            }

            if ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']] != 0) {
              $this->result->longitude = $this->readByte($pointer + 4 * ($this->columns[$keys[self::LONGITUDE - 1]][$this->database['type']]), 'float', TRUE);
            }

            if ($this->columns[$keys[self::ISP - 1]][$this->database['type']] != 0) {
              $this->result->isp = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::ISP - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']] != 0) {
              $this->result->domainName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::DOMAIN_NAME - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']] != 0) {
              $this->result->zipCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::ZIP_CODE - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']] != 0) {
              $this->result->timeZone = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::TIME_ZONE - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']] != 0) {
              $this->result->netSpeed = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::NET_SPEED - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']] != 0) {
              $this->result->iddCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::IDD_CODE - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']] != 0) {
              $this->result->areaCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::AREA_CODE - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']] != 0) {
              $this->result->weatherStationCode = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_CODE - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']] != 0) {
              $this->result->weatherStationName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::WEATHER_STATION_NAME - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::MCC - 1]][$this->database['type']] != 0) {
              $this->result->mcc = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::MCC - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::MNC - 1]][$this->database['type']] != 0) {
              $this->result->mnc = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::MNC - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']] != 0) {
              $this->result->mobileCarrierName = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::MOBILE_CARRIER_NAME - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']] != 0) {
              $this->result->elevation = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::ELEVATION - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            if ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']] != 0) {
              $this->result->usageType = $this->readByte($this->readByte($pointer + 4 * ($this->columns[$keys[self::USAGE_TYPE - 1]][$this->database['type']]), '32'), 'string', TRUE);
            }

            return $this->result;
        }
        return $return;
      }
      else {
        if ($ip_number < $ip_from) {
          $high = $mid - 1;
        }
        else {
          $low = $mid + 1;
        }
      }
    }
  }
}
?>