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
use OCA\MediaMetadata\Services\ImageMetadataMapper;
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
				$Container->query('ImageMetadataMapper'),
				$serverContainer->getConfig()->getSystemValue('datadirectory')
			);
		});

		$container->registerService('ImageMetadataMapper', function(IContainer $Container) {
			return new ImageMetadataMapper(
				$Container->query('ServerContainer')->getDb()
			);
		});
	}
}