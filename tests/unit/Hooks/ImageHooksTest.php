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

function getimagesize($filename = null, array &$imageinfo = null) {
	return ImageHooksTest::$dimensions;
}

use OCA\MediaMetadata\AppInfo\Application;
use OCA\MediaMetadata\Services\ImageDimension;
use OCA\MediaMetadata\Services\ImageDimensionMapper;
use OCA\MediaMetadata\Tests\TestCase;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\Mapper;
use OCP\IDBConnection;
use OCP\ILogger;
use OCP\IDb;
use OCP\Files\File;
use OCP\Files\Folder;

class testImageDimension extends Entity {
	protected $imageId;
	protected $imageHeight;
	protected $imageWidth;
}

class testImageDimensionMapper extends Mapper {
	/**
	 * @param IDBConnection $database
	 */
	public function __construct(IDBConnection $database) {
		parent::__construct($database, 'mediametadata_image_size', '\OCA\MediaMetadata\Hooks\testImageDimension');
	}
}

/**
 * Class ImageHooksTest
 *
 * @package OCA\MediaMetadata\Hooks
 */
class ImageHooksTest extends TestCase {
	/**
	 * @var \OCA\MediaMetadata\Hooks\ImageHooks
	 */
	public $imagehooks;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Hooks\ImageHooks
	 */
	protected $mockImageHooks;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OC\Files\Node\Root
	 */
	protected $root;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ImageDimensionMapper
	 */
	protected $mapper;
	/**
	 * @var \OCA\MediaMetadata\Hooks\testImageDimensionMapper
	 */
	protected $testMapper;
	/**
	 * @var ILogger
	 */
	protected $logger;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCP\IDb
	 */
	protected $db;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ImageDimension
	 */
	protected $imageDimension;

	/**
	 * @var array $dimensions that will be returned by getimagesize()
	 */
	public static $dimensions;

	protected function setUp() {
		parent::setUp();

		$app = new Application();
		$container = $app->getContainer();

		$this->root = $this->getMockBuilder('OC\Files\Node\Root')
			->disableOriginalConstructor()
			->getMock();
		$this->db = $this->getMockBuilder(
			'\OCP\IDBConnection')
			->disableOriginalConstructor()
			->getMock();
		$this->mapper = $this->mockImageDimensionMapper();
		$this->testMapper = new testImageDimensionMapper($this->db);
		$this->logger = $this->getMockBuilder('\OCP\ILogger')
			->disableOriginalConstructor()
			->getMock();
		$this->imageDimension = $this->mockImageDimension();
		$this->imagehooks = new ImageHooks(
			$this->root,
			$this->mapper,
			$container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory')
		);
	}

	public function testPostCreate() {
		$location = 'testFolder/test.jpg';
		$fileId = 260495;
		$jpgFile = $this->mockJpgFile($location, $fileId);

		$imageWidth = 100;
		$imageHeight = 100;
		$dimensions = array($imageWidth, $imageHeight);

		$this->logger->log('debug', 'Test Image Path: '.$location, array('app' => 'MediaMetadata'));
		$this->logger->log('debug', 'Test Image Height: '.$imageHeight, array('app' => 'MediaMetadata'));
		$this->logger->log('debug', 'Test Image Width: '.$imageWidth, array('app' => 'MediaMetadata'));

		$this->db->expects($this->once())
			->method('lastInsertId')
				->with($this->equalTo('*PREFIX*mediametadata_image_size'));

		$this->imageDimension->setId($jpgFile->getId());
		$this->assertContains('id', $this->imageDimension->getUpdatedFields());
		$this->imageDimension->setImageHeight($imageHeight);
		$this->assertContains('image_height', $this->imageDimension->getUpdatedFields());
		$this->imageDimension->setImageWidth($imageWidth);
		$this->assertContains('image_width', $this->imageDimension->getUpdatedFields());

		$this->testMapper->insert($this->imageDimension);

		$this->imagehooks->postCreate($jpgFile);
	}

	/**
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

	/**
	 * @param $mockClass
	 * @param $location
	 * @param $dimensions
	 */
	/*private function mockGetImageSize($mockClass, $location, $dimensions) {
		$mockClass->expects($this->once())
			->method('getimagesize')
			->with($location)
			->willReturn($dimensions);
	}*/

	/**
	 * @return object|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockImageDimensionMapper() {
		/*return new ImageDimensionMapper(
			$this->db
		);*/

		return $this->getMockBuilder('OCA\MediaMetadata\Services\ImageDimensionMapper')
			->setConstructorArgs([
				$this->db,
			])
			->getMock();

		/*
		 * Another Method
		 * $app = new \OCA\MediaMetadata\AppInfo\Application();
		 * $container = $app->getContainer();
		 *
		 * return new ImageDimensionMapper(
		 *     $container->query('ServerContainer')->getDb()
		 * );
		 */
	}

	/**
	 * @return object|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockImageDimension() {
		return new testImageDimension();

		/*return $this->getMockBuilder('OCA\MediaMetadata\Services\ImageDimension')
			->disableOriginalConstructor()
			->getMock();*/
	}

	/**
	 * @param array $mockedMethods
	 * @return object|ImageHooks|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockPostCreate(array $mockedMethods = []) {
		$app = new Application();
		$container = $app->getContainer();

		return $this->getMockBuilder('OCA\MediaMetadata\Hooks\ImageHooks')
			->setConstructorArgs([
				$this->root,
				$this->mapper,
				$container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory')
			])
			->setMethods($mockedMethods)
			->getMock();
	}

}