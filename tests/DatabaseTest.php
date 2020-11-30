<?php

declare(strict_types=1);

namespace IP2Location\Test\DatabaseTest;

use IP2Location\Database;
use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
	public function testInvalidDatabase() {
		try {
			$db = new \IP2Location\Database('./databases/NULL.BIN', \IP2Location\Database::FILE_IO);
		} catch (\Exception $e) {
			$this->assertStringContainsString('does not seem to exist.', $e->getMessage());
		}
	}

	public function testCountryCode() {
		$db = new \IP2Location\Database('./databases/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('8.8.8.8', \IP2Location\Database::ALL);

		$this->assertEquals(
			'US',
			$records['countryCode'],
		);
	}

	public function testCountryName() {
		$db = new \IP2Location\Database('./databases/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('8.8.8.8', \IP2Location\Database::ALL);

		$this->assertEquals(
			'United States',
			$records['countryName'],
		);
	}

	public function testUnsupportedField() {
		$db = new \IP2Location\Database('./databases/IP2LOCATION-LITE-DB1.BIN', \IP2Location\Database::FILE_IO);

		$records = $db->lookup('8.8.8.8', \IP2Location\Database::ALL);

		$this->assertStringContainsString('unavailable', $records['cityName']);
	}
}
