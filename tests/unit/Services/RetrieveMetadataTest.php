<?php
/**
 * ownCloud - mediametadata
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Jalpreet Singh Nanda (:imjalpreet) <jalpreetnanda@gmail.com>
 * @copyright Jalpreet Singh Nanda (:imjalpreet) 2016
 */

namespace OCA\MediaMetadata\Services;

use OCA\MediaMetadata\Tests\MapperTestUtility;


/**
 * Class RetrieveMetadataTest
 *
 * @package OCA\MediaMetadata\Services
 */
class RetrieveMetadataTest extends MapperTestUtility {
	/**
	 * @var \OCA\MediaMetadata\Services\RetrieveMetadata
	 */
	protected $retrieveData;

	protected function setUp() {
		parent::setUp();

		$this->retrieveData = new RetrieveMetadata(
			$this->db
		);
	}

	public function testRetrieve() {

		$fileList = array(260495);
		
		$expectedResult = array(
			'260495' => array(
				'imageHeight' => 100,
				'imageWidth'  => 100,
				'dateCreated' => '2016-04-29',
				'gpsLongitude' => 78.21,
				'gpsLatitude' => 27.31
			)
		);

		$imageID = 260495;
		$sql = 'SELECT * FROM *PREFIX*mediametadata WHERE image_id = ?';
		$params = [$imageID];
		$row = [
			'image_height' => 100,
			'image_width' => 100,
			'date_created' => '2016-04-29',
			'gps_latitude' => 27.31,
			'gps_longitude' => 78.21
		];


		$this->setMapperResult($sql, $params, [$row]);

		$this->assertEquals($this->retrieveData->retrieve($fileList), $expectedResult);
	}
}
