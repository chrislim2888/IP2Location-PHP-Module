<?php

namespace IP2Location;

/**
 * IpTools class.
 */
class IpTools
{
	public function isIpv4($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) ? true : false;
	}

	public function isIpv6($ip)
	{
		return (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) ? true : false;
	}

	public function ipv4ToDecimal($ip)
	{
		if (!$this->isIpv4($ip)) {
			return;
		}

		return sprintf('%u', ip2long($ip));
	}

	public function ipv6ToDecimal($ipv6)
	{
		if (!$this->isIpv6($ipv6)) {
			return;
		}

		return (string) gmp_import(inet_pton($ipv6));
	}

	public function decimalToIpv4($number)
	{
		if (!preg_match('/^\d+$/', $number)) {
			return;
		}

		if ($number > 4294967295) {
			return;
		}

		return long2ip($number);
	}

	public function decimalToIpv6($number)
	{
		if (!preg_match('/^\d+$/', $number)) {
			return;
		}

		if ($number <= 4294967295) {
			return;
		}

		return inet_ntop(str_pad(gmp_export($number), 16, "\0", STR_PAD_LEFT));
	}

	public function ipv4ToCidr($ipFrom, $ipTo)
	{
		$s = explode('.', $ipFrom);

		$start = '';
		$dot = '';

		foreach ($s as $val) {
			$start = sprintf('%s%s%d', $start, $dot, $val);
			$dot = '.';
		}

		$end = '';
		$dot = '';

		$e = explode('.', $ipTo);

		foreach ($e as $val) {
			$end = sprintf('%s%s%d', $end, $dot, $val);
			$dot = '.';
		}

		$start = ip2long($start);
		$end = ip2long($end);
		$result = [];

		while ($end >= $start) {
			$maxSize = $this->maxBlock($start, 32);
			$x = log($end - $start + 1) / log(2);
			$maxDiff = floor(32 - floor($x));

			$ip = long2ip($start);

			if ($maxSize < $maxDiff) {
				$maxSize = $maxDiff;
			}

			array_push($result, "$ip/$maxSize");
			$start += pow(2, (32 - $maxSize));
		}

		return $result;
	}

	public function ipv6ToCidr($ipFrom, $ipTo)
	{
		$ipFromBinary = str_pad($this->ip2Bin($ipFrom), 128, '0', STR_PAD_LEFT);
		$ipToBinary = str_pad($this->ip2Bin($ipTo), 128, '0', STR_PAD_LEFT);

		if ($ipFromBinary === $ipToBinary) {
			return [$ipFrom . '/' . 128];
		}

		if (strcmp($ipFromBinary, $ipToBinary) > 0) {
			list($ipFromBinary, $ipToBinary) = [$ipToBinary, $ipFromBinary];
		}

		$networks = [];
		$networkSize = 0;

		do {
			if (substr($ipFromBinary, -1, 1) == '1') {
				$networks[substr($ipFromBinary, $networkSize, 128 - $networkSize) . str_repeat('0', $networkSize)] = 128 - $networkSize;

				$n = strrpos($ipFromBinary, '0');
				$ipFromBinary = (($n == 0) ? '' : substr($ipFromBinary, 0, $n)) . '1' . str_repeat('0', 128 - $n - 1);
			}

			if (substr($ipToBinary, -1, 1) == '0') {
				$networks[substr($ipToBinary, $networkSize, 128 - $networkSize) . str_repeat('0', $networkSize)] = 128 - $networkSize;
				$n = strrpos($ipToBinary, '1');
				$ipToBinary = (($n == 0) ? '' : substr($ipToBinary, 0, $n)) . '0' . str_repeat('1', 128 - $n - 1);
			}

			if (strcmp($ipToBinary, $ipFromBinary) < 0) {
				continue;
			}

			$shift = 128 - max(strrpos($ipFromBinary, '0'), strrpos($ipToBinary, '1'));
			$ipFromBinary = str_repeat('0', $shift) . substr($ipFromBinary, 0, 128 - $shift);
			$ipToBinary = str_repeat('0', $shift) . substr($ipToBinary, 0, 128 - $shift);
			$networkSize += $shift;
			if ($ipFromBinary === $ipToBinary) {
				$networks[substr($ipFromBinary, $networkSize, 128 - $networkSize) . str_repeat('0', $networkSize)] = 128 - $networkSize;
				continue;
			}
		} while (strcmp($ipFromBinary, $ipToBinary) < 0);

		ksort($networks, SORT_STRING);
		$result = [];

		foreach ($networks as $ip => $netmask) {
			$result[] = $this->bin2Ip($ip) . '/' . $netmask;
		}

		return $result;
	}

	public function cidrToIpv4($cidr)
	{
		if (strpos($cidr, '/') === false) {
			return;
		}

		list($ip, $prefix) = explode('/', $cidr);

		$ipStart = long2ip((ip2long($ip)) & ((-1 << (32 - (int) $prefix))));

		$total = 1 << (32 - $prefix);

		$ipStartLong = sprintf('%u', ip2long($ipStart));
		$ipEndLong = $ipStartLong + $total - 1;

		if ($ipEndLong > 4294967295) {
			$ipEndLong = 4294967295;
		}

		$ipLast = long2ip($ipEndLong);

		return [
			'ip_start' => $ipStart,
			'ip_end'   => $ipLast,
		];
	}

	public function cidrToIpv6($cidr)
	{
		if (strpos($cidr, '/') === false) {
			return;
		}

		list($ip, $range) = explode('/', $cidr);

		// Convert the IPv6 into binary
		$binFirstAddress = inet_pton($ip);

		// Convert the binary string to a string with hexadecimal characters
		$hexStartAddress = @reset(@unpack('H*0', $binFirstAddress));

		// Get available bits
		$bits = 128 - $range;

		$hexLastAddress = $hexStartAddress;

		$pos = 31;
		while ($bits > 0) {
			// Convert current character to an integer
			$int = hexdec(substr($hexLastAddress, $pos, 1));

			// Convert it back to a hexadecimal character
			$new = dechex($int | (pow(2, min(4, $bits)) - 1));

			// And put that character back in the string
			$hexLastAddress = substr_replace($hexLastAddress, $new, $pos, 1);

			$bits -= 4;
			--$pos;
		}

		$binLastAddress = pack('H*', $hexLastAddress);

		return [
			'ip_start' => $this->expand(inet_ntop($binFirstAddress)),
			'ip_end'   => $this->expand(inet_ntop($binLastAddress)),
		];
	}

	public function bin2Ip($bin)
	{
		if (\strlen($bin) != 128) {
			return;
		}

		$pad = 128 - \strlen($bin);
		for ($i = 1; $i <= $pad; ++$i) {
			$bin = '0' . $bin;
		}

		$bits = 0;
		$ipv6 = '';

		while ($bits <= 7) {
			$bin_part = substr($bin, ($bits * 16), 16);
			$ipv6 .= dechex(bindec($bin_part)) . ':';
			++$bits;
		}

		return inet_ntop(inet_pton(substr($ipv6, 0, -1)));
	}

	public function compressIpv6($ipv6)
	{
		return inet_ntop(inet_pton($ipv6));
	}

	public function expandIpv6($ipv6)
	{
		$hex = unpack('H*0', inet_pton($ipv6));

		return implode(':', str_split($hex[0], 4));
	}

	public function getVisitorIp(&$ipData = null)
	{
		$ip = $ipRemoteAdd = $ipSucuri = $ipIncap = $ipCf = $ipReal = $ipForwarded = $ipForwardedOri = '::1';

		if (isset($_SERVER['REMOTE_ADDR']) && filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP)) {
			$ipRemoteAdd = $ip = $_SERVER['REMOTE_ADDR'];
		}

		// Get real client IP if they are behind Sucuri firewall.
		if (isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) && filter_var($_SERVER['HTTP_X_SUCURI_CLIENTIP'], FILTER_VALIDATE_IP)) {
			$ipSucuri = $ip = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
		}

		// Get real client IP if they are behind Incapsula firewall.
		if (isset($_SERVER['HTTP_INCAP_CLIENT_IP']) && filter_var($_SERVER['HTTP_INCAP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
			$ipIncap = $ip = $_SERVER['HTTP_INCAP_CLIENT_IP'];
		}

		// Get real client IP if they are behind CloudFlare protection.
		if (isset($_SERVER['HTTP_CF_CONNECTING_IP']) && filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
			$ipCf = $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}

		if (isset($_SERVER['HTTP_X_REAL_IP']) && filter_var($_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
			$ipReal = $ip = $_SERVER['HTTP_X_REAL_IP'];
		}

		// Get real client IP if they are behind proxy server.
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ipForwardedOri = $_SERVER['HTTP_X_FORWARDED_FOR'];
			$xip = trim(current(explode(',', $ipForwardedOri)));
			
			if (filter_var($xip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
				$ipForwarded = $ip = $xip;
			}
		}

		if ((is_array($ipData)) || ($ipData == null)) {
			if ($ipRemoteAdd != '::1') {
				$ipData['REMOTE_ADDR'] = $ipRemoteAdd;
			}

			if ($ipSucuri != '::1') {
				$ipData['HTTP_X_SUCURI_CLIENTIP'] = $ipSucuri;
			}

			if ($ipIncap != '::1') {
				$ipData['HTTP_INCAP_CLIENT_IP'] = $ipIncap;
			}

			if ($ipCf != '::1') {
				$ipData['HTTP_CF_CONNECTING_IP'] = $ipCf;
			}

			if ($ipReal != '::1') {
				$ipData['HTTP_X_REAL_IP'] = $ipReal;
			}

			if ($ipForwardedOri != '::1') {
				$ipData['HTTP_X_FORWARDED_FOR'] = $ipForwardedOri;
			}
		}

		return $ip;
	}

	private function ip2Bin($ip)
	{
		if (($n = inet_pton($ip)) === false) {
			return false;
		}

		$bits = 15;
		$binary = '';
		while ($bits >= 0) {
			$bin = sprintf('%08b', (\ord($n[$bits])));
			$binary = $bin . $binary;
			--$bits;
		}

		return $binary;
	}

	private function maxBlock($base, $bit)
	{
		while ($bit > 0) {
			$decimal = hexdec(base_convert((pow(2, 32) - pow(2, (32 - ($bit - 1)))), 10, 16));

			if (($base & $decimal) != $base) {
				break;
			}

			--$bit;
		}

		return $bit;
	}

	private function expand($ipv6)
	{
		return implode(':', str_split(unpack('H*0', inet_pton($ipv6))[0], 4));
	}
}
