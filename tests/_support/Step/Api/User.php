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

namespace Step\Api;

/**
 * Class User
 *
 * @package Step\Api
 */
class User extends \ApiTester {
	/**
	 * Adds the authorisation headers
	 *
	 * We use custom methods defined in _support/Helper/Api
	 */
	public function getUserCredentialsAndUseHttpAuthentication() {
		$I = $this;
		list ($userId, $password) = $I->getUserCredentials();
		$I->amHttpAuthenticated($userId, $password);
	}

	public function breakMyConfigFile() {
		$I = $this;
		$I->createBrokenConfig();
	}

	public function createMyConfigFileWithABom() {
		$I = $this;
		$I->createConfigWithBom();
	}

	public function emptyMyConfigFile() {
		$I = $this;
		$I->emptyConfig();
	}

	public function fixMyConfigFile() {
		$I = $this;
		$I->restoreValidConfig();
	}
}
