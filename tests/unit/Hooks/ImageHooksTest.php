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
	return ImageHooksTest::$dimensions ?: getimagesize($filename);
}

use OCA\MediaMetadata\Services\ImageDimension;
use OCA\MediaMetadata\Services\ImageDimensionMapper;
use OCA\MediaMetadata\Tests\TestCase;
use OCP\ILogger;
use OCP\IDb;
use OCP\Files\File;
use OCP\Files\Folder;

/**
 * Class ImageHooksTest
 *
 * @package OCA\MediaMetadata\Hooks
 */
class ImageHooksTest extends TestCase {
	/**
	 * @var \OCA\MediaMetadata\Hooks\ImageHooks
	 */
	protected $imagehooks;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OC\Files\Node\Root
	 */
	protected $root;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ImageDimensionMapper
	 */
	protected $mapper;
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

		$this->root = $this->getMockBuilder('OC\Files\Node\Root')
			->disableOriginalConstructor()
			->getMock();
		$this->mapper = $this->mockImageDimensionMapper();
		$this->logger = $this->getMockBuilder('\OCP\ILogger')
			->disableOriginalConstructor()
			->getMock();
		$this->db = $this->getMockBuilder('\OCP\IDb')
			->disableOriginalConstructor()
			->getMock();
		$this->imageDimension = $this->mockImageDimension();

		$app = new \OCA\MediaMetadata\AppInfo\Application();
		$container = $app->getContainer();
		$this->imagehooks = new ImageHooks(
			$this->root,
			$this->mapper,
			$container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory')
		);
	}

	protected function testPostCreate() {
		$location = 'testFolder/test.jpg';
		$fileId = 260495;
		$jpgFile = $this->mockJpgFile($location, $fileId);

		$imageWidth = 100;
		$imageHeight = 100;
		$dimensions = array($imageWidth, $imageHeight);

		$this->mockGetImageSize($location, $dimensions);

		$this->logger->log('debug', 'Test Image Path: '.$location, array('app' => 'MediaMetadata'));
		$this->logger->log('debug', 'Test Image Height: '.$imageHeight, array('app' => 'MediaMetadata'));
		$this->logger->log('debug', 'Test Image Width: '.$imageWidth, array('app' => 'MediaMetadata'));

		$this->imageDimension->expects($this->once())
			->method('setImageId')
			->with($jpgFile->getId());
		$this->imageDimension->expects($this->once())
			->method('setImageHeight')
			->with($imageHeight);
		$this->imageDimension->expects($this->once())
			->method('setImageWidth')
			->with($imageWidth);

		$this->mapper->expects($this->once())
			->method('insert')
			->with($this->imageDimension);

		$this->imagehooks->post_create($jpgFile);
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
	 * @param $location
	 * @param $dimensions
	 */
	private function mockGetImageSize($location, $dimensions) {
		$this->imagehooks->expects($this->once())
			->method('getimagesize')
			->with($location)
			->willReturn($dimensions);
	}

	/**
	 * @return object|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockImageDimensionMapper() {
		/*return new ImageDimensionMapper(
			$this->db
		);*/

		return $this->getMockBuilder('OCA\MediaMetadata\Services\ImageDimensionMapper')
			->setConstructorArgs($this->db)
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
		//return new ImageDimension();

		return $this->getMockBuilder('OCA\MediaMetadata\Services\ImageDimension')
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * @param array $mockedMethods
	 * @return object|ImageHooks|\PHPUnit_Framework_MockObject_MockObject
	 */
	private function mockPostCreate(array $mockedMethods = []) {
		$app = new \OCA\MediaMetadata\AppInfo\Application();
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