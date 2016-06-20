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

	protected function setUp() {
		parent::setUp();

		$this->imagehooks = $this->getImageHooks();

		$this->root = $this->getMockBuilder('OC\Files\Node\Root')
			->disableOriginalConstructor()
			->getMock();
		$this->mapper = $this->getImageDimensionMapper();
		$this->logger = $this->getMockBuilder('\OCP\ILogger')
			->disableOriginalConstructor()
			->getMock();
		$this->db = $this->getMockBuilder('\OCP\IDb')
			->disableOriginalConstructor()
			->getMock();
		$this->imageDimension = $this->getImageDimension();
	}

	/**
	 * @return object|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getImageDimensionMapper() {
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
	protected function getImageDimension() {
		//return new ImageDimension();

		return $this->getMockBuilder('OCA\MediaMetadata\Services\ImageDimension')
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * @param array $mockedMethods
	 * @return object|ImageHooks|\PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getImageHooks(array $mockedMethods = []) {
		$app = new \OCA\MediaMetadata\AppInfo\Application();
		$container = $app->getContainer();

		if (!empty($mockedMethods)) {
			return $this->getMockBuilder('OCA\MediaMetadata\Hooks\ImageHooks')
				->setConstructorArgs([
					$this->root,
					$this->mapper,
					$container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory')
				])
				->setMethods($mockedMethods)
				->getMock();
		} else {
			return new ImageHooks(
				$this->root,
				$this->mapper,
				$container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory')
			);
		}
	}

	protected function testImageHooks() {
		$imageHooks = $this->getImageHooks([
			'post_create',
		]);

		$location = 'testFolder/test.jpg';
		$fileId = 260495;
		$jpgFile = $this->mockJpgFile($location, $fileId);

		$imageWidth = 100;
		$imageHeight = 100;
		$dimensions = array($imageWidth, $imageHeight);

		$this->mockGetImageSize($imageHooks, $location, $dimensions);

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

		//TODO: Mock Mapper->insert

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
	 * @param $path
	 * @param $dimensions
	 */
	protected function mockGetImageSize($mockClass, $path, $dimensions) {
		$mockClass->expects($this->once())
			->method('getimagesize')
			->with($path)
			->willReturn($dimensions);
	}
}