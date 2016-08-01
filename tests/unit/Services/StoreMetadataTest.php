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


use OCA\MediaMetadata\AppInfo\Application;
use OCA\MediaMetadata\Tests\TestCase;

/**
 * Class StoreMetadataTest
 *
 * @package OCA\MediaMetadata\Services
 */
class StoreMetadataTest extends TestCase {
	/**
	 * @var \OCA\MediaMetadata\Services\StoreMetadata
	 */
	protected $storeMetadata;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ImageDimensionMapper
	 */
	protected $mapper;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\StoreMetadata
	 */
	protected $dbManager;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCP\IDb
	 */
	protected $db;
	/**
	 * @var $container
	 */
	private $container;

	protected function setUp() {
		parent::setUp();

		$app = new Application();
		$this->container = $app->getContainer();

		$this->db = $this->getMockBuilder(
			'\OCP\IDBConnection')
			->disableOriginalConstructor()
			->getMock();
		$this->mapper = $this->getMockBuilder(
			'OCA\MediaMetadata\Services\ImageDimensionMapper')
			->setConstructorArgs([
				$this->db
			])
			->getMock();
		$this->dbManager = $this->getMockBuilder(
			'OCA\MediaMetadata\Services\StoreMetadata')
			->setConstructorArgs([
				$this->mapper
			])
			->getMock();
		$this->storeMetadata = new StoreMetadata(
			$this->mapper
		);
	}

	public function testStore() {
		$location = '/testFolder/test.jpg';
		$fileId = 260495;
		$jpgFile = $this->mockJpgFile($location, $fileId);

		$absolutePath = $this->container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory').$location;
		
	}

	/**
	 * @param $fileId
	 * @return object|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function mockFile($fileId) {
		$file = $this->getMockBuilder('OCP\Files\File')
			->disableOriginalConstructor()
			->getMock();

		$file->method('getId')
			->willReturn($fileId);

		return $file;
	}

	/**
	 * @param $path
	 * @param $fileId
	 * @return object|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function mockJpgFile($path, $fileId) {
		$file = $this->mockFile($fileId);

		$this->mockJpgFileMethods($file, $path);

		return $file;
	}

	/**
	 * @param $node
	 * @param $path
	 */
	protected function mockJpgFileMethods($node, $path) {
		$node->method('getPath')
			->willReturn($path);
	}
}