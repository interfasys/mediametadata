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
use OCP\AppFramework\Controller;
use OCP\IRequest;

/**
 * Class MetadataController
 *
 * @package OCA\MediaMetadata\Controller
 */
class MetadataController extends Controller {

	/**
	 * @var RetrieveMetadata
	 */
	protected $retrievalService;

	/**
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
	 *
	 * @param $fileList
	 * @return array
	 */
	public function getMetadata($fileList) {
		$metadata = $this->retrievalService->retrieve($fileList);

		return $metadata;
	}
}
