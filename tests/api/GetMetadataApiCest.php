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

use Page\MediaMetadata as MetadataApp;

/**
 * Class GetMetadataApiCest
 */
class GetMetadataApiCest {
	private $apiUrl;
	private $params = [
	];
	private $metadata = [

	];

	/**
	 * @param ApiTester $I
	 */
	public function _before(ApiTester $I) {
		$this->apiUrl = MetadataApp::$URL . '/api/metadata';
	}

	public function _after(ApiTester $I) {
	}

	public function getMetadataAsUser(\Step\Api\User $I) {
		$I->am('an App');
		$I->wantTo('retrieve the metadata stored in the database');

		$data = $I->getFilesDataForFolder('');
		$params = $this->params;

		$id = $data['testimage.jpg']['id'];
		$this->getMetadata($I, $params, $id);
	}

	/**
	 * @param \Step\Api\User $I
	 * @param $params
	 * @param int $id
	 */
	private function getMetadata(\Step\Api\User $I, $params, $id) {
		$I->getUserCredentialsAndUseHttpAuthentication();
		$params['fileList'] = strval($id);
		$I->sendGET($this->apiUrl, $params);
		$I->seeResponseCodeIs(200);
		$I->seeResponseIsJson();
		$metadata = $this->metadata;
		$metadata[strval($id)] = array(
			'imageHeight' => 379,
			'imageWidth'  => 300,
			'dateCreated' => null,
			'gpsLatitude' => null,
			'gpsLongitude' => null
		);
		$I->seeResponseContainsJson($metadata);
	}
}
