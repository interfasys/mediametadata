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

function getimagesize($filename = null, array &$imageinfo = null) {
	return ExtractMetadataTest::$dimensions;
}

function exif_read_data($filename, $sections = null, $arrays = false, $thumbnail = false) {
	return ExtractMetadataTest::$exif;
}

use OCA\MediaMetadata\AppInfo\Application;
use OCA\MediaMetadata\Tests\TestCase;

/**
 * Class ExtractMetadataTest
 *
 * @package OCA\MediaMetadata\Services
 */
class ExtractMetadataTest extends TestCase {
	/**
	 * @var \OCA\MediaMetadata\Services\ExtractMetadata
	 */
	protected $extractMetadata;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ExtractMetadata
	 */
	protected $extractor;
	/**
	 * @var $container
	 */
	private $container;
	/**
	 * @var $dimensions
	 */
	public static $dimensions = [100, 100];
	/**
	 * @var $exif
	 */
	public static $exif = false;

	protected function setUp() {
		parent::setUp();

		$app = new Application();
		$this->container = $app->getContainer();

		$this->extractor = $this->getMockBuilder(
			'OCA\MediaMetadata\Services\ExtractMetadata')
			->disableOriginalConstructor()
			->getMock();

		$this->extractMetadata = new ExtractMetadata();
	}

	public function testExtract() {
		$location = '/testFolder/test.jpg';

		$absolutePath = $this->container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory').$location;

		$width = 100;
		$height = 100;

		$metadata = array();
		$metadata['imageWidth'] = $width;
		$metadata['imageHeight'] = $height;
		$metadata['EXIFData'] = false;

		$this->assertEquals($this->extractMetadata->extract($absolutePath), $metadata);
	}
}
