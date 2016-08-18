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

namespace OCA\MediaMetadata\Controller;

use OCA\MediaMetadata\Tests\TestCase;

class MetadataControllerTest extends TestCase {
	/**
	 * @var string
	 */
	protected $AppName = 'MediaMetadata';
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCP\IRequest
	 */
	protected $request;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\RetrieveMetadata
	 */
	protected $retrieveMetadata;
	/**
	 * @var MetadataController
	 */
	protected $controller;

	public function setUp() {
		parent::setUp();

		$this->request = $this->getMockBuilder('\OCP\IRequest')
			->disableOriginalConstructor()
			->getMock();

		$this->retrieveMetadata = $this->getMockBuilder('\OCA\MediaMetadata\Services\RetrieveMetadata')
			->disableOriginalConstructor()
			->getMock();

		$this->controller = new MetadataController(
			$this->AppName,
			$this->request,
			$this->retrieveMetadata
		);
	}

	public function testGetMetadata() {
		$expectedResult = array(
			'260495' => array(
				'imageHeight' => 100,
				'imageWidth'  => 100,
				'dateCreated' => '2016-04-29',
				'gpsLongitude' => 78.21,
				'gpsLatitude' => 27.31
			)
		);

		$fileList = array(260495);

		$this->retrieveMetadata->expects($this->once())
			->method('retrieve')
			->with($fileList)
			->willReturn($expectedResult);

		$fileIDs = '260495';

		$this->assertEquals($this->controller->getMetadata($fileIDs), $expectedResult);
	}
}
