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

namespace OCA\MediaMetadata\AppInfo;


use OCA\MediaMetadata\Hooks\ImageHooks;
use OCA\MediaMetadata\Services\ExtractMetadata;
use OCA\MediaMetadata\Services\ImageDimensionMapper;
use OCA\MediaMetadata\Services\StoreMetadata;
use OCP\AppFramework\App;
use OCP\IContainer;

class Application extends App {

	public function __construct() {
		parent::__construct('mediametadata');

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('ImageHooks', function(IContainer $Container) {
			$serverContainer = $Container->query('ServerContainer');
			return new ImageHooks(
				$serverContainer->getRootFolder(),
				$Container->query('ImageDimensionMapper'),
				$Container->query('ExtractMetadata'),
				$Container->query('StoreMetadata'),
				$serverContainer->getConfig()->getSystemValue('datadirectory')
			);
		});

		$container->registerService('ImageDimensionMapper', function(IContainer $Container) {
			return new ImageDimensionMapper(
				$Container->query('ServerContainer')->getDb()
			);
		});

		$container->registerService('ExtractMetadata', function(IContainer $Container) {
			return new ExtractMetadata();
		});

		$container->registerService('StoreMetadata', function(IContainer $Container) {
			return new StoreMetadata(
				$Container->query('ImageDimensionMapper')
			);
		});
	}
}
