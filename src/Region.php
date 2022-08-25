<?php

namespace IP2Location;

/**
 * Region class.
 */
class Region
{
	/**
	 * Unable to locate CSV file.
	 *
	 * @var int
	 */
	public const EXCEPTION_FILE_NOT_EXISTS = 10000;

	/**
	 * Invalid CSV file.
	 *
	 * @var int
	 */
	public const EXCEPTION_INVALID_CSV = 10001;

	/**
	 * Unable to read the CSV file.
	 *
	 * @var int
	 */
	public const EXCEPTION_UNABLE_TO_OPEN_CSV = 10002;

	/**
	 * No record found.
	 *
	 * @var int
	 */
	public const EXCEPTION_NO_RECORD = 10003;

	/**
	 * Fields from CSV.
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Records from CSV.
	 *
	 * @var array
	 */
	protected $records = [];

	/**
	 * Constructor.
	 *
	 * @param string $csv Path to CSV file
	 *
	 * @throws \Exception
	 */
	public function __construct($csv)
	{
		if (!file_exists($csv)) {
			throw new \Exception(__CLASS__ . ': The CSV file "' . $csv . '" is not found.', self::EXCEPTION_FILE_NOT_EXISTS);
		}

		$file = fopen($csv, 'r');

		if (!$file) {
			throw new \Exception(__CLASS__ . ': Unable to read "' . $csv . '".', self::EXCEPTION_UNABLE_TO_OPEN_CSV);
		}

		$line = 1;

		while (!feof($file)) {
			$data = fgetcsv($file);

			if (!$data) {
				++$line;
				continue;
			}

			// Parse the CSV fields
			if ($line == 1) {
				if ($data[1] != 'subdivision_name') {
					throw new \Exception(__CLASS__ . ': Invalid region information CSV file.', self::EXCEPTION_INVALID_CSV);
				}

				$this->fields = $data;
			} else {
				$this->records[$data[0]][] = [
					'code' => $data[2],
					'name' => $data[1],
				];
			}

			++$line;
		}
	}

	/**
	 * Get region code by country code and region name.
	 *
	 * @param string $countryCode The country ISO 3166 country code.
	 * @param string $regionName  Region name.
	 *
	 * @throws \Exception
	 *
	 * @return string|null
	 */
	public function getRegionCode($countryCode, $regionName)
	{
		if (empty($this->records)) {
			throw new \Exception(__CLASS__ . ': No record available.', self::EXCEPTION_NO_RECORD);
		}

		if (!isset($this->records[$countryCode])) {
			return;
		}

		foreach ($this->records[$countryCode] as $record) {
			if (strtoupper($regionName) == strtoupper($record['name'])) {
				return $record['code'];
			}
		}
	}
}
