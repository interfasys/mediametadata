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
	protected $imagehooks;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OC\Files\Node\Root
	 */
	protected $root;
	/**
	 * @var \PHPUnit_Framework_MockObject_MockObject|\OCA\MediaMetadata\Services\ImageDimensionMapper
	 */
	protected $mapper;

	protected function setUp() {
		parent::setUp();

		$this->imagehooks = $this->getImageHooks();

		$this->root = $this->getMockBuilder('OC\Files\Node\Root')
			->disableOriginalConstructor()
			->getMock();
		$this->mapper = $this->getMockBuilder('OCA\MediaMetadata\Services\ImageDimensionMapper')
			->disableOriginalConstructor()
			->getMock();
	}

	/**
	 * @param array $mockedMethods
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


}