<?php
/* Copyright (C) 2005-2013 IP2Location.com
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
	public $ipAddress, $ipNumber, $countryCode, $countryName, $regionName, $cityName, $latitude, $longitude, $isp, $domainName, $zipCode, $timeZone, $netSpeed, $iddCode, $areaCode, $weatherStationCode, $weatherStationName, $mcc, $mnc, $mobileCarrierName, $elevation, $usageType;
}

class IP2Location {
	// Current version
	const VERSION = '6.0.0';

	// Database storage method
	const FILE_IO = 0;
	const MEMORY_CACHE = 1;
	const SHARED_MEMORY = 2;

	// Unpack method
	const ENDIAN = 0;
	const BIG_ENDIAN = 1;

	// Record field
	const ALL					= 0;
	const COUNTRY_CODE			= 1;
	const COUNTRY_NAME			= 2;
	const REGION_NAME			= 3;
	const CITY_NAME				= 4;
	const LATITUDE				= 5;
	const LONGITUDE				= 6;
	const ISP					= 7;
	const DOMAIN_NAME			= 8;
	const ZIP_CODE				= 9;
	const TIME_ZONE				= 10;
	const NET_SPEED				= 11;
	const IDD_CODE				= 12;
	const AREA_CODE				= 13;
	const WEATHER_STATION_CODE	= 14;
	const WEATHER_STATION_NAME	= 15;
	const MCC					= 16;
	const MNC					= 17;
	const MOBILE_CARRIER_NAME	= 18;
	const ELEVATION				= 19;
	const USAGE_TYPE			= 20;

	// IP version
	const IPV4 = 0;
	const IPV6 = 1;

	// SHMOP memory address
    const SHM_KEY = 4194500608;

	// Message
	const FIELD_NOT_SUPPORTED = 'This field is not supported in DB%TYPE%. Please upgrade your IP2Location database.';
	const INVALID_IPV4 = 'Invalid IPv4 address.';
	const INVALID_IPV6 = 'Invalid IPv6 address.';

	private $columns = array(
		'COUNTRY_CODE'			=> array(0,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2),
		'COUNTRY_NAME'			=> array(0,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2,	2),
		'REGION_NAME'			=> array(0,	0,	0,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3,	3),
		'CITY_NAME'				=> array(0,	0,	0,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4,	4),
		'LATITUDE'				=> array(0,	0,	0,	0,	0,	5,	5,	0,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5,	5),
		'LONGITUDE'				=> array(0,	0,	0,	0,	0,	6,	6,	0,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6,	6),
		'ISP'					=> array(0,	0,	3,	0,	5,	0,	7,	5,	7,	0,	8,	0,	9,	0,	9,	0,	9,	0,	9,	7,	9,	0,	9,	7,	9),
		'DOMAIN_NAME'			=> array(0,	0,	0,	0,	0,	0,	0,	6,	8,	0,	9,	0,	10,	0,	10,	0,	10,	0,	10,	8,	10,	0,	10,	8,	10),
		'ZIP_CODE'				=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	7,	7,	7,	7,	0,	7,	7,	7,	0,	7,	0,	7,	7,	7,	0,	7),
		'TIME_ZONE'				=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	8,	8,	7,	8,	8,	8,	7,	8,	0,	8,	8,	8,	0,	8),
		'NET_SPEED'				=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	8,	11,	0,	11,	8,	11,	0,	11,	0,	11,	0,	11),
		'IDD_CODE'				=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	9,	12,	0,	12,	0,	12,	9,	12,	0,	12),
		'AREA_CODE'				=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	10,	13,	0,	13,	0,	13,	10,	13,	0,	13),
		'WEATHER_STATION_CODE'	=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	9,	14,	0,	14,	0,	14,	0,	14),
		'WEATHER_STATION_NAME'	=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	10,	15,	0,	15,	0,	15,	0,	15),
		'MCC'					=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	9,	16,	0,	16,	9,	16),
		'MNC'					=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	10,	17,	0,	17,	10,	17),
		'MOBILE_CARRIER_NAME'	=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	11,	18,	0,	18,	11,	18),
		'ELEVATION'				=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	11,	19,	0,	19),
		'USAGE_TYPE'			=> array(0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	0,	12,	20),
	);

	private $shmId = '';
	private $db = array();
	private $unpack;
	private $buffer;
	private $mode;
	private $handle;

	public function __construct($file=NULL, $mode=self::FILE_IO){
		if(!is_file($file)) throw new PEAR_Exception('Unable to open file "' . $file . '".');

		// Define system unpack method
		list($test) = array_values(unpack('L1L', pack('V', 1)));

		// Use Big Endian Unpack if endian test failed
		$this->unpack = (($test != 1)) ? self::BIG_ENDIAN : self::ENDIAN;

		switch($mode){
			case self::SHARED_MEMORY:
				if(!function_exists('shmop_open')) throw new PEAR_Exception('Please make sure your PHP setup has "php_shmop" enabled.');
				$this->mode = self::SHARED_MEMORY;

				$this->shmId = @shmop_open(self::SHM_KEY, 'a', 0, 0);

				/*shmop_delete($this->shmId);
				shmop_close($this->shmId);
				die;*/

				if($this->shmId === FALSE){
					// First execution, load database into memory
					if(($fp = fopen($file, 'rb')) === FALSE) throw new PEAR_Exception('Unable to open file "' . $file . '".');

					$stats = fstat($fp);

					if($shmId = @shmop_open(self::SHM_KEY, 'w', 0, 0)){
						shmop_delete($shmId);
						shmop_close($shmId);
					}

					if($shmId = @shmop_open(self::SHM_KEY, 'c', 0644, $stats['size'])){
						/*$buf = fread($fp, $stats['size']);
						shmop_write($shmId, $buf, 0);*/

						$offset = 0;
						while($offset < $stats['size']){
							$buf = fread($fp, 524288);
							shmop_write($shmId, $buf, $offset);
							$offset += 524288;
						}
						shmop_close($shmId);
					}
					fclose($fp);

					$this->shmId = @shmop_open(self::SHM_KEY, 'a', 0, 0);

					if($this->shmId === FALSE){
						throw new PEAR_Exception('Unable to access shared memory block.');
					}
				}
			break;

			default:
				$this->mode = self::FILE_IO;
				$this->handle = fopen($file, 'rb');

				if($mode == self::MEMORY_CACHE){
					$this->mode = self::MEMORY_CACHE;
					$stats = fstat($this->handle);
					$this->buffer = fread($this->handle, $stats['size']);
				}
		}

		$this->db['type'] = $this->readByte(1, '8');
		$this->db['column'] = $this->readByte(2, '8');
		$this->db['year'] = $this->readByte(3, '8');
		$this->db['month'] = $this->readByte(4, '8');
		$this->db['day'] = $this->readByte(5, '8');
		$this->db['count'] = $this->readByte(6, '32');
		$this->db['base_address'] = $this->readByte(10, '32');
		$this->db['ip_version'] = $this->readByte(14, '32');
	}

	private function readByte($pos, $mode='string', $autoSize = false){
		switch($this->mode){
			case self::SHARED_MEMORY:
				if($mode == 'string'){
					$data = shmop_read($this->shmId, $pos, ($autoSize) ? shmop_size($this->shmId)-$pos : 100);
				}
				else{
					$data = shmop_read($this->shmId, $pos-1, 50);
				}
			break;

			case self::MEMORY_CACHE:
				$data = substr($this->buffer, (($mode == 'string') ? $pos : $pos-1), 100);
			break;

			default:
				if($mode == 'string'){
					fseek($this->handle, $pos, SEEK_SET);
					$data = @fread($this->handle, 1);
				}
				else{
					fseek($this->handle, $pos-1, SEEK_SET);
					$data = @fread($this->handle, 50);
				}
		}

		switch($mode){
			case '8':
				$out = $this->readBinary('C', $data);
				return $out[1];
			break;

			case '32':
				$out = $this->readBinary('V', $data);
				if($out[1]<0) $out[1] += 4294967296;

				return (int)$out[1];
			break;

			case '128':
				$array = preg_split('//', $data, -1, PREG_SPLIT_NO_EMPTY);

				if(count($array) != 16) return 0;

				$ip96_127 = $this->readBinary('V', $array[0] . $array[1] . $array[2] . $array[3]);
				$ip64_95 = $this->readBinary('V', $array[4] . $array[5] . $array[6] . $array[7]);
				$ip32_63 = $this->readBinary('V', $array[8] . $array[9] . $array[10] . $array[11]);
				$ip1_31 = $this->readBinary('V', $array[12] . $array[13] . $array[14] . $array[15]);

				if($ip96_127[1]<0) $ip96_127[1] += 4294967296;
				if($ip64_95[1]<0) $ip64_95[1] += 4294967296;
				if($ip32_63[1]<0) $ip32_63[1] += 4294967296;
				if($ip1_31[1]<0) $ip1_31[1] += 4294967296;

				return bcadd(bcadd(bcmul($ip1_31[1], bcpow(4294967296, 3)), bcmul($ip32_63[1], bcpow(4294967296, 2))), bcadd( bcmul($ip64_95[1], 4294967296), $ip96_127[1]));
			break;

			case 'float':
				$out = $this->readBinary('f', $data);

				return $out[1];
			break;

			default:
				$out = $this->readBinary('C', $data);
				return (in_array($this->mode, array(self::SHARED_MEMORY, self::MEMORY_CACHE))) ? substr($data, 1, $out[1]) : @fread($this->handle, $out[1]);
		}
	}

	private function readBinary($format, $data){
		if($this->unpack == self::BIG_ENDIAN){
			$ar = unpack($format, $data);
			$vals = array_values($ar);
			$f = explode('/', $format);
			$i = 0;

			foreach($f as $fKey=>$fValue){
				$repeater = intval (substr ($fValue, 1));

				if($repeater == 0) $repeater = 1;
				if($fValue{1} == '*') $repeater = count ($ar) - $i;
				if($fValue{0} != 'd') $i += $repeater; continue;

				$j = $i + $repeater;

				for($a=$i; $a<$j; ++$a){
					$p = pack('d', $vals[$i]);
					$p = strrev ($p);
					list($vals[$i]) = array_values(unpack('d1d', $p));
					++$i;
				}
			}

			$a = 0;
			foreach($ar as $arKey=>$arValue){
				$ar[$arKey] = $vals[$a];
				++$a;
			}
			return $ar;
		}
		return unpack($format, $data);
	}

	private function ipv6ToLong($ip){
		$n = substr_count($ip, ':');

		if($n < 7){
			$expanded = '::';

			while($n < 7){
				$expanded .= ':';
				$n++;
			}
			$ip = preg_replace('/::/', $expanded, $ip);
		}

		$subLoc = 8;
		$ipv6No = '0';

		foreach(preg_split('/:/', $ip) as $ipSub){
			$subLoc--;

			if($ipSub == '') continue;
			$ipv6No = bcadd( $ipv6No, bcmul(hexdec($ipSub), bcpow(hexdec('0x10000'), $subLoc)));
		}
		return $ipv6No;
	}

	public function lookup($ip, $field=self::ALL){
		$keys = array_keys($this->columns);

		// Get record by single field name
		if($field != self::ALL){
			if($this->columns[$keys[$field-1]][$this->db['type']] == 0) return str_replace('%TYPE%', $this->db['type'], self::FIELD_NOT_SUPPORTED);
		}

		$result = new IP2LocationRecord();
		$result->ipAddress = $ip;

		// IPv4 database
		if($this->db['ip_version'] == self::IPV4){
			if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
				if($field != self::ALL){
					return self::INVALID_IPV4;
				}
				else{
					$result->countryCode = $result->countryName = $result->regionName = $result->cityName = $result->latitude = $result->longitude = $result->isp = $result->domainName = $result->zipCode = $result->timeZone = $result->netSpeed = $result->iddCode = $result->areaCode = $result->weatherStationCode = $result->weatherStationName = $result->mcc = $result->mnc = $result->mobileCarrierName = $result->elevation = $result->usageType = self::INVALID_IPV4;
					return $result;
				}
			}
			$ipNumber = sprintf('%u', ip2long($ip));
			$ipNumber  = ($ipNumber >= 4294967295) ? ($ipNumber-1) : $ipNumber;
		}
		else{
			if(!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
				if($field != self::ALL){
					return self::INVALID_IPV6;
				}
				else{
					$result->countryCode = $result->countryName = $result->regionName = $result->cityName = $result->latitude = $result->longitude = $result->isp = $result->domainName = $result->zipCode = $result->timeZone = $result->netSpeed = $result->iddCode = $result->areaCode = $result->weatherStationCode = $result->weatherStationName = $result->mcc = $result->mnc = $result->mobileCarrierName = $result->elevation = $result->usageType = self::INVALID_IPV6;
					return $result;
				}
			}
			$ipNumber  = $this->ipv6ToLong($ip);
			$ipNumber = (bccomp($ipNumber , 340282366920938463463374607431768211455) == 0) ? bcsub($ipNumber , 1) : $ipNumber;
		}

		$result->ipNumber = $ipNumber;

		$low = 0;
		$high = $this->db['count'];
		$mid = 0;
		$ipFrom = 0;
		$ipTo = 0;

		while($low <= $high){
			$mid = (int)(($low + $high)/2);
			$ipFrom = $this->readByte($this->db['base_address'] + $mid * $this->db['column'] * 4, '32');
			$ipTo = $this->readByte($this->db['base_address'] + ($mid + 1) * $this->db['column'] * 4, '32');

			if($ipFrom < 0) $ipFrom += pow(2, 32);
			if($ipTo < 0) $ipTo += pow(2, 32);

			if(($ipNumber >= $ipFrom) && ($ipNumber  < $ipTo)){
				$offset = $this->db['base_address'] + ($mid * $this->db['column'] * 4);
				switch($field){
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
						return $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[$field-1]][$this->db['type']]-1), '32'), 'string', true);

					case self::COUNTRY_NAME:
						return $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[$field-1]][$this->db['type']]-1), '32')+3, 'string', true);

					case self::LATITUDE:
					case self::LONGITUDE:
						return $this->readByte($offset + 4 * ($this->columns[$keys[$field-1]][$this->db['type']]-1), 'float', true);

					case self::USAGE_TYPE:
						return $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[$field-1]][$this->db['type']]-1), '32'), 'string', true);

					default:
						$result->regionName = $result->cityName = $result->latitude = $result->longitude = $result->isp = $result->domainName = $result->zipCode = $result->timeZone = $result->netSpeed = $result->iddCode = $result->areaCode = $result->weatherStationCode = $result->weatherStationName = $result->mcc = $result->mnc = $result->mobileCarrierName = $result->elevation = $result->usageType = str_replace('%TYPE%', $this->db['type'], self::FIELD_NOT_SUPPORTED);

						$result->countryCode = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::COUNTRY_CODE-1]][$this->db['type']]-1), '32'), 'string', true);
						$result->countryName = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::COUNTRY_NAME-1]][$this->db['type']]-1), '32')+3, 'string', true);

						if($this->columns[$keys[self::REGION_NAME-1]][$this->db['type']] != 0)
							$result->regionName = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::REGION_NAME-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::CITY_NAME-1]][$this->db['type']] != 0)
							$result->cityName = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::CITY_NAME-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::LATITUDE-1]][$this->db['type']] != 0)
							$result->latitude = $this->readByte($offset + 4 * ($this->columns[$keys[self::LATITUDE-1]][$this->db['type']]-1), 'float', true);

						if($this->columns[$keys[self::LONGITUDE-1]][$this->db['type']] != 0)
							$result->longitude = $this->readByte($offset + 4 * ($this->columns[$keys[self::LONGITUDE-1]][$this->db['type']]-1), 'float', true);

						if($this->columns[$keys[self::ISP-1]][$this->db['type']] != 0)$result->isp = $this->readByte($this->readByte($offset + 4 *
							($this->columns[$keys[self::ISP-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::DOMAIN_NAME-1]][$this->db['type']] != 0)
							$result->domainName = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::DOMAIN_NAME-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::ZIP_CODE-1]][$this->db['type']] != 0)
							$result->zipCode = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::ZIP_CODE-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::TIME_ZONE-1]][$this->db['type']] != 0)
							$result->timeZone = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::TIME_ZONE-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::NET_SPEED-1]][$this->db['type']] != 0)$result->netSpeed = $this->readByte($this->readByte($offset + 4 *
							($this->columns[$keys[self::NET_SPEED-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::IDD_CODE-1]][$this->db['type']] != 0)$result->iddCode = $this->readByte($this->readByte($offset + 4 *
							($this->columns[$keys[self::IDD_CODE-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::AREA_CODE-1]][$this->db['type']] != 0)
							$result->areaCode =	$this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::AREA_CODE-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::WEATHER_STATION_CODE-1]][$this->db['type']] != 0)
							$result->weatherStationCode = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::WEATHER_STATION_CODE-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::WEATHER_STATION_NAME-1]][$this->db['type']] != 0)
							$result->weatherStationName = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::WEATHER_STATION_NAME-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::MCC-1]][$this->db['type']] != 0)
							$result->mcc = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::MCC-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::MNC-1]][$this->db['type']] != 0)
							$result->mnc = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::MNC-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::MOBILE_CARRIER_NAME-1]][$this->db['type']] != 0)
							$result->mobileCarrierName = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::MOBILE_CARRIER_NAME-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::ELEVATION-1]][$this->db['type']] != 0)
							$result->elevation = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::ELEVATION-1]][$this->db['type']]-1), '32'), 'string', true);

						if($this->columns[$keys[self::USAGE_TYPE-1]][$this->db['type']] != 0)
							$result->usageType = $this->readByte($this->readByte($offset + 4 * ($this->columns[$keys[self::USAGE_TYPE-1]][$this->db['type']]-1), '32'), 'string', true);

						return $result;
				}
			}
			else{
				if($ipNumber < $ipFrom){
					$high = $mid - 1;
				}
				else{
					$low = $mid + 1;
				}
			}
		}
	}
}
?>
