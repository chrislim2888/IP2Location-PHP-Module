<?php

declare(strict_types=1);

namespace IP2Location\Test\RegionTest;

use PHPUnit\Framework\TestCase;

class RegionTest extends TestCase
{
	public function testRegionCodeField()
	{
		$region = new \IP2Location\Region('./data/IP2LOCATION-ISO3166-2.CSV');

		$this->assertEquals(
			'US-CA',
			$region->getRegionCode('US', 'California'),
		);
	}
}
