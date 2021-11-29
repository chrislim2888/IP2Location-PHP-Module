<?php

declare(strict_types=1);

namespace IP2Location\Test\IpToolsTest;

use PHPUnit\Framework\TestCase;

class IpToolsTest extends TestCase
{
	public function testIpv4()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertTrue(
			$ipTools->isIpv4('8.8.8.8')
		);
	}

	public function testInvalidIpv4()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertFalse(
			$ipTools->isIpv4('8.8.8.555')
		);
	}

	public function testIpv6()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertTrue(
			$ipTools->isIpv6('2001:4860:4860::8888')
		);
	}

	public function testInvalidIpv6()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertFalse(
			$ipTools->isIpv6('2001:4860:4860::ZZZZ')
		);
	}

	public function testIpv4Decimal()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEquals(
			134744072,
			$ipTools->ipv4ToDecimal('8.8.8.8')
		);
	}

	public function testDecimalIpv4()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEquals(
			'8.8.8.8',
			$ipTools->decimalToIpv4('134744072')
		);
	}

	public function testIpv6Decimal()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEquals(
			'42541956123769884636017138956568135816',
			$ipTools->ipv6ToDecimal('2001:4860:4860::8888')
		);
	}

	public function testDecimalIpv6()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEquals(
			'2001:4860:4860::8888',
			$ipTools->decimalToIpv6('42541956123769884636017138956568135816')
		);
	}

	public function testIpv4ToCidr()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEqualsCanonicalizing(
			['8.0.0.0/8'],
			$ipTools->ipv4ToCidr('8.0.0.0', '8.255.255.255')
		);
	}

	public function testCidrToIpv4()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEqualsCanonicalizing(
			[
				'ip_start' => '8.0.0.0',
				'ip_end'   => '8.255.255.255',
			],
			$ipTools->cidrToIpv4('8.0.0.0/8')
		);
	}

	public function testIpv6ToCidr()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEqualsCanonicalizing(
			['2002::1234:abcd:ffff:c0a8:101/64'],
			$ipTools->ipv6ToCidr('2002:0000:0000:1234:abcd:ffff:c0a8:0101', '2002:0000:0000:1234:ffff:ffff:ffff:ffff')
		);
	}

	public function testCidrToIpv6()
	{
		$ipTools = new \IP2Location\IpTools();

		$this->assertEqualsCanonicalizing(
			[
				'ip_start' => '2002:0000:0000:1234:abcd:ffff:c0a8:0101',
				'ip_end'   => '2002:0000:0000:1234:ffff:ffff:ffff:ffff',
			],
			$ipTools->cidrToIpv6('2002::1234:abcd:ffff:c0a8:101/64')
		);
	}
}
