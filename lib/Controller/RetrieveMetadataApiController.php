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


use OCA\MediaMetadata\Services\RetrieveMetadata;
use OCP\AppFramework\ApiController;

/**
 * Class RetrieveMetadataApiController
 *
 * @package OCA\MediaMetadata\Controller
 */
class RetrieveMetadataApiController extends ApiController {

	/**
	 * @var RetrieveMetadata
	 */
	protected $retrievalService;

	/**
	 * @param RetrieveMetadata $retrieveMetadata
	 */
	public function __construct(RetrieveMetadata $retrieveMetadata) {
		$this->retrievalService = $retrieveMetadata;
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param $fileList
	 * @return array
	 */
	public function getMetadata($fileList) {
		$metadata = $this->retrievalService->retrieve($fileList);

		return $metadata;
	}
}
