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

namespace OCA\MediaMetadata\Hooks;


use OCA\MediaMetadata\AppInfo\Application;
use OCA\MediaMetadata\Tests\TestCase;

/**
 * Class ImageHooksTest
 *
 * @package OCA\MediaMetadata\Hooks
 */
class ImageHooksTest extends TestCase {
	/**
	 * @var \OCA\MediaMetadata\Hooks\ImageHooks
	 */
	protected $imageHooks;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OC\Files\Node\Root
	 */
	protected $root;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ImageDimensionMapper
	 */
	protected $mapper;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCP\IDb
	 */
	protected $db;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ExtractMetadata
	 */
	protected $extractor;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\StoreMetadata
	 */
	protected $dbManager;
	/**
	 * @var $container
	 */
	private $container;

	protected function setUp() {
		parent::setUp();

		$app = new Application();
		$this->container = $app->getContainer();

		$this->root = $this->getMockBuilder('OC\Files\Node\Root')
			->disableOriginalConstructor()
			->getMock();
		$this->db = $this->getMockBuilder(
			'\OCP\IDBConnection')
			->disableOriginalConstructor()
			->getMock();
		$this->mapper = $this->getMockBuilder('OCA\MediaMetadata\Services\ImageDimensionMapper')
			->setConstructorArgs([
				$this->db,
			])
			->getMock();
		$this->extractor = $this->getMockBuilder(
			'OCA\MediaMetadata\Services\ExtractMetadata')
			->disableOriginalConstructor()
			->getMock();
		$this->dbManager = $this->getMockBuilder(
			'\OCA\MediaMetadata\Services\StoreMetadata')
			->setConstructorArgs([
				$this->mapper
			])
			->getMock();
		$this->imageHooks = new ImageHooks(
			$this->root,
			$this->mapper,
			$this->extractor,
			$this->dbManager,
			$this->container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory')
		);
	}

	public function testPostCreate() {
		$location = '/testFolder/test.jpg';
		$fileId = 260495;
		$jpgFile = $this->mockJpgFile($location, $fileId);

		$absolutePath = $this->container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory').$location;

		$this->assertEquals($jpgFile->getPath(), $location);

		$metadata = array(
			'imageWidth' => 100,
			'imageHeight' => 100
		);

		$this->extractor->expects($this->once())
			->method('extract')
			->with($absolutePath)
			->willReturn($metadata);

		$this->dbManager->expects($this->once())
			->method('store')
			->with(
				$metadata,
				$jpgFile
			)
			->willReturn(true);

		$result = $this->imageHooks->postCreate($jpgFile);

		$this->assertEquals($result, true);
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
