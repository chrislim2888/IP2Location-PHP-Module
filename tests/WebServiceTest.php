<?php

declare(strict_types=1);

namespace IP2Location\Test\WebServiceTest;

use IP2Location\WebService;
use PHPUnit\Framework\TestCase;

class WebServiceTest extends TestCase
{
	public function testCredit() {
		$ws = new \IP2Location\WebService('demo', 'WS24', true);
        if (method_exists($this, 'assertMatchesRegularExpression')) {
            $this->assertMatchesRegularExpression('/^[0-9]+$/', (string) $ws->getCredit());
        }else{
            // Compatible with php 7.2 && phpunit 8.x
            $this->assertRegExp('/^[0-9]+$/', (string) $ws->getCredit());
        }
	}

	public function testCountryCode() {
		$ws = new \IP2Location\WebService('demo', 'WS24', true);

		$records = $ws->lookup('8.8.8.8', [
			'continent', 'country', 'region', 'city', 'geotargeting', 'country_groupings', 'time_zone_info',
		], 'en');

		$this->assertEquals(
			'US',
			$records['country_code']
		);
	}
}
