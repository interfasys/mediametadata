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
use OCP\IRequest;

/**
 * Class MetadataApiController
 *
 * @package OCA\MediaMetadata\Controller
 */
class MetadataApiController extends ApiController {
	/**
	 * @var RetrieveMetadata
	 */
	protected $retrievalService;

	/**
	 * @param string $AppName
	 * @param IRequest $request
	 * @param RetrieveMetadata $retrieveMetadata
	 */
	public function __construct(
		$AppName,
		IRequest $request,
		RetrieveMetadata $retrieveMetadata
	) {
		parent::__construct($AppName, $request);

		$this->retrievalService = $retrieveMetadata;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @CORS
	 *
	 * @param string $fileList
	 * @return array
	 */
	public function getMetadata($fileList) {

		$fileList = array_map('intval', explode(';', $fileList));

		$metadata = $this->retrievalService->retrieve($fileList);

		return $metadata;
	}
}
