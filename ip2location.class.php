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

class ip2locationRecord{
	var $countryShort;
	var $countryLong;
	var $region;
	var $city;
	var $isp;
	var $latitude;
	var $longitude;
	var $domain;
	var $zipCode;
	var $timeZone;
	var $netSpeed;
	var $iddCode;
	var $areaCode;
	var $weatherStationCode;
	var $weatherStationName;
	var $mcc;
	var $mnc;
	var $mobileBrand;
	var $elevation;
	var $usageType;
	var $ipAddress;
	var $ipNumber;
}

class ip2location{
	var $version = '5.0.0';
	var $unpackMethod = 'unpack';
	var $handle;
	var $dbType;
	var $dbColumn;
	var $dbYear;
	var $dbMonth;
	var $dbDay;
	var $dbCount;
	var $baseAddress;
	var $ipVersion;

	function ip2location(){
		// Proceed endian test
		list($endianTest) = array_values(unpack('L1L', pack('V', 1)));

		// We use Big Endian Unpack if endian test failed
		if($endianTest != 1) $this->unpackMethod = 'bigEndianUnpack';

		// Call destructor, this method works in both PHP4 and PHP5
		register_shutdown_function(array(&$this, 'destructor'));
	}

	function destructor(){
		if(is_resource($this->handle)) fclose($this->handle);
	}

	function error($message){
		die('IP2Location: ' . $message . "\n");
	}

	function getVersion(){
		return $this->version;
	}

	function open($file){
		if(!file_exists($file)){
			$this->error('Cannot allocate database file at "' . $file . '".');
		}
		$this->handle = fopen($file, 'rb');

		$this->dbType = $this->read8(1);
		$this->dbColumn = $this->read8(2);
		$this->dbYear = $this->read8(3);
		$this->dbMonth = $this->read8(4);
		$this->dbDay = $this->read8(5);
		$this->dbCount = $this->read32(6);
		$this->baseAddress = $this->read32(10);
		$this->ipVersion = $this->read32(14);
	}

	function bigEndianUnpack($format, $data){
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

	function readBinary($format, $data){
		$result = ($this->unpackMethod == 'bigEndianUnpack') ? $this->bigEndianUnpack($format, $data) : unpack($format, $data);
		return $result;
	}

	function read8($position){
		fseek($this->handle, $position-1, SEEK_SET);
		$data = @fread($this->handle, 1);

		$output = $this->readBinary('C', $data);
		return $output[1];
	}

	function read32($position){
		fseek($this->handle, $position-1, SEEK_SET);
		$data = @fread($this->handle, 4);

		$output = $this->readBinary('V', $data);
		if($output[1]<0) $output[1] += 4294967296;

		return (int)$output[1];
	}

	function read128($position){
		fseek($this->handle, $position-1, SEEK_SET);
		$data = @fread($this->handle, 16);
		return $this->bytes2Int($data);
	}

	function readString($position){
		fseek($this->handle, $position, SEEK_SET);
		$size = @fread($this->handle, 1);
		$output = $this->readBinary('C', $size);

		$data = @fread($this->handle, $output[1]);

		return $data;
	}

	function readFloat($position){
		fseek($this->handle, $position-1, SEEK_SET);
		$data = @fread($this->handle, 4);
		$output = $this->readBinary('f', $data);

		return $output[1];
	}

	function bytes2Int($binData){
		$array = preg_split('//', $binData, -1, PREG_SPLIT_NO_EMPTY);

		if(count($array) != 16) return 0;

		$ip96_127 = $this->readBinary('V', $array[0] . $array[1] . $array[2] . $array[3]);
		$ip64_95 = $this->readBinary('V', $array[4] . $array[5] . $array[6] . $array[7]);
		$ip32_63 = $this->readBinary('V', $array[8] . $array[9] . $array[10] . $array[11]);
		$ip1_31 = $this->readBinary('V', $array[12] . $array[13] . $array[14] . $array[15]);

		if($ip96_127[1]<0) $ip96_127[1] += 4294967296;
		if($ip64_95[1]<0) $ip64_95[1] += 4294967296;
		if($ip32_63[1]<0) $ip32_63[1] += 4294967296;
		if($ip1_31[1]<0) $ip1_31[1] += 4294967296;

		$result = bcadd(bcadd(bcmul($ip1_31[1], bcpow(4294967296, 3)), bcmul($ip32_63[1], bcpow(4294967296, 2))), bcadd( bcmul($ip64_95[1], 4294967296), $ip96_127[1]));

		return $result;
	}

	function isIPv4($ip){
		return (long2ip(ip2long($ip)) == $ip) ? true : false;
	}

	function isIPv6($ip){
		$n = substr_count($ip, ':');

		if($n<1 || $n>7) return false;

		$k = 0;
		foreach(preg_split('/:/', $ip) as $ipSub){
			$k++;

			if($ipSub == '') continue;
			if(preg_match('/^[a-f\d]{1,4}$/i', $ipSub)) continue;

			if($k == $n+1){
				if($this->isIPv4($ipSub)){
					// here we know it is embeded ipv4, should retrieve data from ipv4 db, pending...
					// the result of this will not be valid, since all characters are treated and calculated
					// in hex based.
					// In addition, embeded ipv4 requires 96 '0' bits. We need to check this too.
					continue;
				}
			}
			return false;
		}

		$m = preg_match_all('/:(?=:)/', $ip, $dummy);
		if($m>1 && $n<7) return false;

		return true;
	}

	function ipv6ToLong($ip){
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

	function notSupported(){
		return 'This field is not supported in DB' . $this->dbType . '. Please upgrade your IP2Location database.';
	}

	function invalidIPAddress(){
		return 'Invalid IP address.';
	}

	function invalidIPv6Address(){
		return 'Invalid IPv6 address.';
	}

	function getRecord($ip, $mode='all'){
		$arrCountry				= array(0, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2);
		$arrRegion				= array(0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3);
		$arrCity				= array(0, 0, 0, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4);
		$arrIsp					= array(0, 0, 3, 0, 5, 0, 7, 5, 7, 0, 8, 0, 9, 0, 9, 0, 9, 0, 9, 7, 9, 0, 9, 7, 9);
		$arrLatitude			= array(0, 0, 0, 0, 0, 5, 5, 0, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5);
		$arrLongitude			= array(0, 0, 0, 0, 0, 6, 6, 0, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6);
		$arrDomain				= array(0, 0, 0, 0, 0, 0, 0, 6, 8, 0, 9, 0, 10,0, 10, 0, 10, 0, 10, 8, 10, 0, 10, 8, 10);
		$arrZipCode				= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 7, 7, 7, 7, 0, 7, 7, 7, 0, 7, 0, 7, 7, 7, 0, 7);
		$arrTimeZone			= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 8, 7, 8, 8, 8, 7, 8, 0, 8, 8, 8, 0, 8);
		$arrNetSpeed			= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 11,0, 11,8, 11, 0, 11, 0, 11, 0, 11);
		$arrIddCode				= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 12, 0, 12, 0, 12, 9, 12, 0, 12);
		$arrAreaCode			= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10 ,13 ,0, 13, 0, 13, 10, 13, 0, 13);
		$arrWeatherStationCode	= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 14, 0, 14, 0, 14, 0, 14);
		$arrWeatherStationName	= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 15, 0, 15, 0, 15, 0, 15);
		$arrMcc                 = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 16, 0, 16, 9, 16);
		$arrMnc                 = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10,17, 0, 17, 10, 17);
		$arrMobileBrand         = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11,18, 0, 18, 11, 18);
		$arrElevation           = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11, 19, 0, 19);
		$arrUsageType           = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 20);

		$result = new ip2locationRecord;

		switch($mode){
			case 'countryShort':
				if($arrCountry[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'countryLong':
				if($arrCountry[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'region':
				if($arrRegion[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'city':
				if($arrCity[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'isp':
				if($arrIsp[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'latitude':
				if($arrLatitude[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'longitude':
				if($arrLongitude[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'domain':
				if($arrDomain[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'zipCode':
				if($arrZipCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'timeZone':
				if($arrTimeZone[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'netSpeed':
				if($arrNetSpeed[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'iddCode':
				if($arrIddCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'areaCode':
				if($arrAreaCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'weatherStationCode':
				if($arrWeatherStationCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'weatherStationName':
				if($arrWeatherStationName[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'mcc':
				if($arrMcc[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

   			case 'mnc':
				if($arrMnc[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

            case 'mobileBrand':
				if($arrMobileBrand[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'elevtion':
				if($arrElevation[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'usageType':
				if($arrUsageType[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

		}

		if($ip == '') $this->error('Missing IP address.');

		if(!$this->isIPv4($ip)){
			$result->countryShort = $this->invalidIPAddress();
			$result->countryLong = $this->invalidIPAddress();
			$result->region = $this->invalidIPAddress();
			$result->city = $this->invalidIPAddress();
			$result->isp = $this->invalidIPAddress();
			$result->latitude = $this->invalidIPAddress();
			$result->longitude = $this->invalidIPAddress();
			$result->domain = $this->invalidIPAddress();
			$result->zipCode = $this->invalidIPAddress();
			$result->timeZone = $this->invalidIPAddress();
			$result->netSpeed = $this->invalidIPAddress();
			$result->iddCode = $this->invalidIPAddress();
			$result->areaCode = $this->invalidIPAddress();
			$result->weatherStationCode = $this->invalidIPAddress();
			$result->weatherStationName = $this->invalidIPAddress();
			$result->mcc = $this->invalidIPAddress();
			$result->mnc = $this->invalidIPAddress();
			$result->mobileBrand = $this->invalidIPAddress();
			$result->elevation = $this->invalidIPAddress();
			$result->usageType = $this->invalidIPAddress();
			$result->ipAddress = $this->invalidIPAddress();
			$result->ipNumber = $this->invalidIPAddress();

			return $result;
		}

		$ip = gethostbyname($ip);
		$ipLong = sprintf('%u', ip2long($ip));

		$low = 0;
		$high = $this->dbCount;
		$mid = 0;
		$ipFrom = 0;
		$ipTo = 0;

		$ipLong  = ($ipLong == 4294967295) ? ($ipLong-1) : $ipLong;

		$result->countryShort = $this->notSupported();
		$result->countryLong = $this->notSupported();
		$result->region = $this->notSupported();
		$result->city = $this->notSupported();
		$result->isp = $this->notSupported();
		$result->latitude = $this->notSupported();
		$result->longitude = $this->notSupported();
		$result->domain = $this->notSupported();
		$result->zipCode = $this->notSupported();
		$result->timeZone = $this->notSupported();
		$result->netSpeed = $this->notSupported();
		$result->iddCode = $this->notSupported();
		$result->areaCode = $this->notSupported();
		$result->weatherStationCode = $this->notSupported();
		$result->weatherStationName = $this->notSupported();
		$result->mcc = $this->notSupported();
		$result->mnc = $this->notSupported();
		$result->mobileBrand = $this->notSupported();
		$result->elevation = $this->notSupported();
		$result->usageType = $this->notSupported();
		$result->ipAddress = $ip;
		$result->ipNumber = $ipLong;

		while($low <= $high){
			$mid = (int)(($low + $high)/2);
			$ipFrom = $this->read32($this->baseAddress + $mid * $this->dbColumn * 4);
			$ipTo = $this->read32($this->baseAddress + ($mid + 1) * $this->dbColumn * 4);

			if($ipFrom < 0) $ipFrom += pow(2, 32);
			if($ipTo < 0) $ipTo += pow(2, 32);

			if(($ipLong >= $ipFrom) && ($ipLong  < $ipTo)){
				switch($mode){
					case 'countryShort':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrCountry[$this->dbType]-1)));
					break;

					case 'countryLong':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrCountry[$this->dbType]-1))+3);
					break;

					case 'region':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrRegion[$this->dbType]-1)));
					break;

					case 'city':
						return$this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrCity[$this->dbType]-1)));
					break;

					case 'isp':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrIsp[$this->dbType]-1)));
					break;

					case 'latitude':
						return $this->readFloat($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrLatitude[$this->dbType]-1));
					break;

					case 'longitude':
						return $this->readFloat($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrLongitude[$this->dbType]-1));
					break;

					case 'domain':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrDomain[$this->dbType]-1)));
					break;

					case 'zipCode':
						return $this->readString($handle, $this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrZipCode[$this->dbType]-1)));
					break;

					case 'timeZone':
						return $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4) + 4 * ($arrTimeZone[$this->dbType]-1)));
					break;

					case 'netSpeed':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4) + 4 * ($arrNetSpeed[$this->dbType]-1)));
					break;

					case 'iddCode':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4) + 4 * ($arrIddCode[$this->dbType]-1)));
					break;

					case 'areaCode':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4) + 4 * ($arrAreaCode[$this->dbType]-1)));
					break;

					case 'weatherStationCode':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4) + 4 * ($arrWeatherStationCode[$this->dbType]-1)));
					break;

					case 'weatherStationName':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrWeatherStationName[$this->dbType]-1)));
					break;

					case 'mcc':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrMcc[$this->dbType]-1)));
					break;

					case 'mnc':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrMnc[$this->dbType]-1)));
					break;

					case 'mobileBrand':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrMobileBrand[$this->dbType]-1)));
					break;

					case 'elevation':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrElevation[$this->dbType]-1)));
					break;

					case 'usageType':
						return $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrUsageType[$this->dbType]-1)));
					break;

					default:
						if($arrCountry[$this->dbType] != 0){
							$result->countryShort = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrCountry[$this->dbType]-1)));
							$result->countryLong = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrCountry[$this->dbType]-1))+3);
						}

						if($arrRegion[$this->dbType] != 0){
							$result->region = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrRegion[$this->dbType]-1)));
						}

						if($arrCity[$this->dbType] != 0){
							$result->city = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrCity[$this->dbType]-1)));
						}

						if($arrIsp[$this->dbType] != 0){
							$result->isp = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrIsp[$this->dbType]-1)));
						}

						if($arrLatitude[$this->dbType] != 0){
							$result->latitude = $this->readFloat($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrLatitude[$this->dbType]-1));
						}

						if($arrLongitude[$this->dbType] != 0){
							$result->longitude = $this->readFloat($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrLongitude[$this->dbType]-1));
						}

						if($arrDomain[$this->dbType] != 0){
							$result->domain = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrDomain[$this->dbType]-1)));
						}

						if($arrZipCode[$this->dbType] != 0){
							$result->zipCode = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrZipCode[$this->dbType]-1)));
						}

						if($arrTimeZone[$this->dbType] != 0){
							$result->timeZone = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrTimeZone[$this->dbType]-1)));
						}

						if($arrNetSpeed[$this->dbType] != 0){
							$result->netSpeed = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrNetSpeed[$this->dbType]-1)));
						}

						if($arrIddCode[$this->dbType] != 0){
							$result->iddCode = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrIddCode[$this->dbType]-1)));
						}

						if($arrAreaCode[$this->dbType] != 0){
							$result->areaCode = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrAreaCode[$this->dbType]-1)));
						}

						if($arrWeatherStationCode[$this->dbType] != 0){
							$result->weatherStationCode = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrWeatherStationCode[$this->dbType]-1)));
						}

						if($arrWeatherStationName[$this->dbType] != 0){
							$result->weatherStationName = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrWeatherStationName[$this->dbType]-1)));
						}

						if($arrMcc[$this->dbType] != 0){
							$result->mcc = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrMcc[$this->dbType]-1)));
						}

						if($arrMnc[$this->dbType] != 0){
							$result->mnc = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrMnc[$this->dbType]-1)));
						}

						if($arrMobileBrand[$this->dbType] != 0){
							$result->mobileBrand = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrMobileBrand[$this->dbType]-1)));
						}

						if($arrElevation[$this->dbType] != 0){
							$result->elevation = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrElevation[$this->dbType]-1)));
						}

						if($arrUsageType[$this->dbType] != 0){
							$result->usageType = $this->readString($this->read32($this->baseAddress + ($mid * $this->dbColumn * 4) + 4 * ($arrUsageType[$this->dbType]-1)));
						}

						return $result;
					break;
				}
			}
			else{
				if($ipLong <$ipFrom){
					$high = $mid - 1;
				}
				else{
					$low = $mid + 1;
				}
			}
		}
		return $result;
	}

	function getRecordV6 ($ip, $mode='all'){
		$arrCountry				= array(0, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2);
		$arrRegion				= array(0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3);
		$arrCity				= array(0, 0, 0, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4);
		$arrIsp					= array(0, 0, 3, 0, 5, 0, 7, 5, 7, 0, 8, 0, 9, 0, 9, 0, 9, 0, 9, 7, 9, 0, 9, 7, 9);
		$arrLatitude			= array(0, 0, 0, 0, 0, 5, 5, 0, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5);
		$arrLongitude			= array(0, 0, 0, 0, 0, 6, 6, 0, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6);
		$arrDomain				= array(0, 0, 0, 0, 0, 0, 0, 6, 8, 0, 9, 0, 10,0, 10, 0, 10, 0, 10, 8, 10, 0, 10, 8, 10);
		$arrZipCode				= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 7, 7, 7, 7, 0, 7, 7, 7, 0, 7, 0, 7, 7, 7, 0, 7);
		$arrTimeZone			= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 8, 7, 8, 8, 8, 7, 8, 0, 8, 8, 8, 0, 8);
		$arrNetSpeed			= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 11,0, 11,8, 11, 0, 11, 0, 11, 0, 11);
		$arrIddCode				= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 12, 0, 12, 0, 12, 9, 12, 0, 12);
		$arrAreaCode			= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10 ,13 ,0, 13, 0, 13, 10, 13, 0, 13);
		$arrWeatherStationCode	= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 14, 0, 14, 0, 14, 0, 14);
		$arrWeatherStationName	= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10, 15, 0, 15, 0, 15, 0, 15);
		$arrMcc                 = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 16, 0, 16, 9, 16);
		$arrMnc                 = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10,17, 0, 17, 10, 17);
		$arrMobileBrand         = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11,18, 0, 18, 11, 18);
		$arrElevation           = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 11, 19, 0, 19);
		$arrUsageType           = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 12, 20);

		$result = new ip2locationRecord;

		switch($mode){
			case 'countryShort':
				if($arrCountry[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'countryLong':
				if($arrCountry[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'region':
				if($arrRegion[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'city':
				if($arrCity[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'isp':
				if($arrIsp[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'latitude':
				if($arrLatitude[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'longitude':
				if($arrLongitude[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'domain':
				if($arrDomain[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'zipCode':
				if($arrZipCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'timeZone':
				if($arrTimeZone[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'netSpeed':
				if($arrNetSpeed[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'iddCode':
				if($arrIddCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'areaCode':
				if($arrAreaCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'weatherStationCode':
				if($arrWeatherStationCode[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'weatherStationName':
				if($arrWeatherStationName[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'mcc':
				if($arrMcc[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'mnc':
				if($arrMnc[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'mobileBrand':
				if($arrMobileBrand[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'elevation':
				if($arrElevation[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;

			case 'usageType':
				if($arrUsageType[$this->dbType] == 0){
					return $this->notSupported();
				}
			break;
		}

		if($ip == '') $this->error('Missing IP address.');

		if(!$this->isIPv6($ip)){
			$result->countryShort = $this->invalidIPv6Address();
			$result->countryLong = $this->invalidIPv6Address();
			$result->region = $this->invalidIPv6Address();
			$result->city = $this->invalidIPv6Address();
			$result->isp = $this->invalidIPv6Address();
			$result->latitude = $this->invalidIPv6Address();
			$result->longitude = $this->invalidIPv6Address();
			$result->domain = $this->invalidIPv6Address();
			$result->zipCode = $this->invalidIPv6Address();
			$result->timeZone = $this->invalidIPv6Address();
			$result->netSpeed = $this->invalidIPv6Address();
			$result->iddCode = $this->invalidIPv6Address();
			$result->areaCode = $this->invalidIPv6Address();
			$result->weatherStationCode = $this->invalidIPv6Address();
			$result->weatherStationName = $this->invalidIPv6Address();
			$result->mcc = $this->invalidIPv6Address();
			$result->mnc = $this->invalidIPv6Address();
			$result->mobileBrand = $this->invalidIPv6Address();
			$result->elevation = $this->invalidIPv6Address();
			$result->usageType = $this->invalidIPv6Address();
			$result->ipAddress = $this->invalidIPv6Address();
			$result->ipNumber = $this->invalidIPv6Address();

			return $result;
		}

		$ipLong  = $this->ipv6ToLong($ip);

		$low = 0;
		$high = $this->dbCount;
		$mid = 0;
		$ipFrom = 0;
		$ipTo = 0;

		$ipLong = (bccomp($ipLong , 340282366920938463463374607431768211455) == 0) ? bcsub($ipLong , 1) : $ipLong;

		$result->countryShort = $this->notSupported();
		$result->countryLong = $this->notSupported();
		$result->region = $this->notSupported();
		$result->city = $this->notSupported();
		$result->isp = $this->notSupported();
		$result->latitude = $this->notSupported();
		$result->longitude = $this->notSupported();
		$result->domain = $this->notSupported();
		$result->zipCode = $this->notSupported();
		$result->timeZone = $this->notSupported();
		$result->netSpeed = $this->notSupported();
		$result->iddCode = $this->notSupported();
		$result->areaCode = $this->notSupported();
		$result->weatherStationCode = $this->notSupported();
		$result->weatherStationName = $this->notSupported();
		$result->mcc = $this->notSupported();
		$result->mnc = $this->notSupported();
		$result->mobileBrand = $this->notSupported();
		$result->elevation = $this->notSupported();
		$result->usageType = $this->notSupported();
		$result->ipAddress = $ip;
		$result->ipNumber = $ipLong;

		$count=0;
		while($low <= $high){
			$mid = (int)(($low + $high)/2);
			$ipFrom = $this->read128($this->baseAddress + $mid * ($this->dbColumn * 4 + 12));
			$ipTo = $this->read128($this->baseAddress + ($mid + 1) * ($this->dbColumn * 4 + 12));
			$count++;

			if((bccomp($ipLong , $ipFrom) >= 0 ) && (bccomp($ipLong, $ipTo) < 0)){
				switch($mode){
					case 'countryShort':
						return $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrCountry[$this->dbType]-1)));
					break;

					case 'countryLong':
						return $this->readString($this->read32($this->baseAddress + $mid * ($this->dbColumn * 4 + 12) + 12 + 4 * ($arrCountry[$this->dbType]-1))+3);
					break;

					case 'region':
						return $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrRegion[$this->dbType]-1)));
					break;

					case 'city':
						return $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrCity[$this->dbType]-1)));
					break;

					case 'isp':
						return $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrIsp[$this->dbType]-1)));
					break;

					case 'latitude':
						return $this->readFloat($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrLatitude[$this->dbType]-1));
					break;

					case 'longitude':
						return $this->readFloat($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrLongitude[$this->dbType]-1));
					break;

					case 'domain':
						return $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrDomain[$this->dbType]-1)));
					break;

					case 'zipCode':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrZipCode[$this->dbType]-1)));
					break;

					case 'timeZone':
						return $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrTimeZone[$this->dbType]-1)));
					break;

					case 'netSpeed':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrNetSpeed[$this->dbType]-1)));
					break;

					case 'iddCode':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrIddCode[$this->dbType]-1)));
					break;

					case 'areaCode':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrAreaCode[$this->dbType]-1)));
					break;

					case 'weatherStationCode':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrWeatherStationCode[$this->dbType]-1)));
					break;

					case 'weatherStationName':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrWeatherStationName[$this->dbType]-1)));
					break;

					case 'mcc':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrMcc[$this->dbType]-1)));
					break;

					case 'mnc':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrMnc[$this->dbType]-1)));
					break;

					case 'mobileBrand':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrMobileBrand[$this->dbType]-1)));
					break;

					case 'elevation':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrElevation[$this->dbType]-1)));
					break;

					case 'usageType':
						return $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrUsageType[$this->dbType]-1)));
					break;

					default:
						if($arrCountry[$this->dbType] != 0){
							$result->countryShort = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrCountry[$this->dbType]-1)));
							$result->countryLong = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrCountry[$this->dbType]-1))+3);
						}

						if($arrRegion[$this->dbType] != 0){
							$result->region = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrRegion[$this->dbType]-1)));
						}

						if($arrCity[$this->dbType] != 0){
							$result->city = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrCity[$this->dbType]-1)));
						}

						if($arrIsp[$this->dbType] != 0){
							$result->isp = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrIsp[$this->dbType]-1)));
						}

						if($arrLatitude[$this->dbType] != 0){
							$result->latitude = $this->readFloat($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrLatitude[$this->dbType]-1));
						}

						if($arrLongitude[$this->dbType] != 0){
							$result->longitude = $this->readFloat($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrLongitude[$this->dbType]-1));
						}

						if($arrDomain[$this->dbType] != 0){
							$result->domain = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrDomain[$this->dbType]-1)));
						}

						if($arrZipCode[$this->dbType] != 0){
							$result->zipCode = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrZipCode[$this->dbType]-1)));
						}

						if($arrTimeZone[$this->dbType] != 0){
							$result->timeZone = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrTimeZone[$this->dbType]-1)));
						}

						if($arrNetSpeed[$this->dbType] != 0){
							$result->netSpeed = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrNetSpeed[$this->dbType]-1)));
						}

						if($arrIddCode[$this->dbType] != 0){
							$result->iddCode = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrIddCode[$this->dbType]-1)));
						}

						if($arrAreaCode[$this->dbType] != 0){
							$result->areaCode = $this->readString($handle, $this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrAreaCode[$this->dbType]-1)));
						}

						if($arrWeatherStationCode[$this->dbType] != 0){
							$result->weatherStationCode = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrWeatherStationCode[$this->dbType]-1)));
						}

						if($arrWeatherStationName[$this->dbType] != 0){
							$result->weatherStationName = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrWeatherStationName[$this->dbType]-1)));
						}

						if($arrMcc[$this->dbType] != 0){
							$result->mcc = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrMcc[$this->dbType]-1)));
						}

						if($arrMnc[$this->dbType] != 0){
							$result->mnc = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrMnc[$this->dbType]-1)));
						}

						if($arrMobileBrand[$this->dbType] != 0){
							$result->mobileBrand = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrMobileBrand[$this->dbType]-1)));
						}

						if($arrElevation[$this->dbType] != 0){
							$result->elevation = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrElevation[$this->dbType]-1)));
						}

						if($arrUsageType[$this->dbType] != 0){
							$result->usageType = $this->readString($this->read32($this->baseAddress + $mid *($this->dbColumn * 4 + 12) + 12 + 4 * ($arrUsageType[$this->dbType]-1)));
						}
						return $result;
					break;
				}
			}
			else{
				if(bccomp($ipLong, $ipFrom)<0){
					$high = $mid - 1;
				}
				else{
					$low = $mid + 1;
				}
			}
		}
		return $result;
	}

	function getCountryShort($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'countryShort') : $this->getRecord($ip, 'countryShort');
	}

	function getCountryLong($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'countryLong') : $this->getRecord($ip, 'countryLong');
	}

	function getRegion($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'region') : $this->getRecord($ip, 'region');
	}

	function getCity($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'city') : $this->getRecord($ip, 'city');
	}

	function getIsp($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'isp') : $this->getRecord($ip, 'isp');
	}

	function getLatitude($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'latitude') : $this->getRecord($ip, 'latitude');
	}

	function getLongitude($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'longitude') : $this->getRecord($ip, 'longitude');
	}

	function getZipCode($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'zipCode') : $this->getRecord($ip, 'zipCode');
	}

	function getDomain($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'domain') : $this->getRecord($ip, 'domain');
	}

	function getTimeZone($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'timeZone') : $this->getRecord($ip, 'timeZone');
	}

	function getNetSpeed($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'netSpeed') : $this->getRecord($ip, 'netSpeed');
	}

	function getIddCode($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'iddCode') : $this->getRecord($ip, 'iddCode');
	}

	function getAreaCode($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'areaCode') : $this->getRecord($ip, 'areaCode');
	}

	function getWeatherStationCode($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'weatherStationCode') : $this->getRecord($ip, 'weatherStationCode');
	}

	function getWeatherStationName($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'weatherStationName') : $this->getRecord($ip, 'weatherStationName');
	}

	function getMcc($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'mcc') : $this->getRecord($ip, 'mcc');
	}

	function getMnc($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'mnc') : $this->getRecord($ip, 'mnc');
	}

	function getMobileBrand($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'mobileBrand') : $this->getRecord($ip, 'mobileBrand');
	}

	function getElevation($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'elevation') : $this->getRecord($ip, 'elevation');
	}

	function getUsageType($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'usageType') : $this->getRecord($ip, 'usageType');
	}

	function getAll($ip){
		return ($this->ipVersion==1) ? $this->getRecordV6($ip, 'all') : $this->getRecord($ip, 'all');
	}
}
?>
