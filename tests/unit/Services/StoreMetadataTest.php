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
use OCP\AppFramework\Db\Entity;

class ImageDimension extends Entity {
	protected $imageId;
	protected $imageHeight;
	protected $imageWidth;
	protected $dateCreated;
	protected $gpsLatitude;
	protected $gpsLongitude;
}

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
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ImageDimension
	 */
	protected $imageDimensionEntity;
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
		$this->imageDimensionEntity = new ImageDimension();
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
		$metadata = array(
			'imageHeight'=> 100,
			'imageWidth' => 100,
			'EXIFData'   => array('dateCreated' => '26/04/95')
			);

		$absolutePath = $this->container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory').$location;

		$this->assertEquals($jpgFile->getId(), $fileId);
		$this->imageDimensionEntity->setImageId($jpgFile->getId());
		$this->assertContains('id', $this->imageDimensionEntity->getUpdatedFields());
		$this->imageDimensionEntity->setImageWidth($metadata['imageWidth']);
		$this->assertContains('imageWidth', $this->imageDimensionEntity->getUpdatedFields());
		$this->imageDimensionEntity->setImageHeight($metadata['imageHeight']);
		$this->assertContains('imageHeight', $this->imageDimensionEntity->getUpdatedFields());
		$this->imageDimensionEntity->setDateCreated($metadata['EXIFData']['dateCreated']);
		$this->assertContains('dateCreated', $this->imageDimensionEntity->getUpdatedFields());

		$entity = new ImageDimension();
		$entity->setId(1);

		$this->mapper->expects($this->once())
			->method('insert')
			->with($this->imageDimensionEntity)
			->willReturn($entity);

		$this->assertEquals($this->storeMetadata->store($metadata, $jpgFile), true);
	}

	public function testStoreWithError() {
		$location = '/testFolder/test.jpg';
		$fileId = 260495;
		$jpgFile = $this->mockJpgFile($location, $fileId);
		$metadata = array(
			'imageHeight'=> 100,
			'imageWidth' => 100,
			'EXIFData'   => array('dateCreated' => '26/04/95')
		);

		$absolutePath = $this->container->query('ServerContainer')->getConfig()->getSystemValue('datadirectory').$location;

		$this->assertEquals($jpgFile->getId(), $fileId);
		$this->imageDimensionEntity->setImageId($jpgFile->getId());
		$this->assertContains('id', $this->imageDimensionEntity->getUpdatedFields());
		$this->imageDimensionEntity->setImageWidth($metadata['imageWidth']);
		$this->assertContains('imageWidth', $this->imageDimensionEntity->getUpdatedFields());
		$this->imageDimensionEntity->setImageHeight($metadata['imageHeight']);
		$this->assertContains('imageHeight', $this->imageDimensionEntity->getUpdatedFields());
		$this->imageDimensionEntity->setDateCreated($metadata['EXIFData']['dateCreated']);
		$this->assertContains('dateCreated', $this->imageDimensionEntity->getUpdatedFields());

		$entity = new ImageDimension();

		$this->mapper->expects($this->once())
			->method('insert')
			->with($this->imageDimensionEntity)
			->willReturn($entity);

		$this->assertEquals($this->storeMetadata->store($metadata, $jpgFile), false);
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
