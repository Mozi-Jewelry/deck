<?php
namespace OCA\Deck\Controller;

use OCA\Deck\Service\DirectoryService;
use OCA\Deck\StatusException;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;

use OCP\IRequest;
use Sabre\HTTP\Util;

/**
 * Class DirectoryApiController
 *
 * @package OCA\Deck\Controller
 */
class DirectoryController extends ApiController
{
	/**
	 * @param string $appName
	 */
	public function __construct(
		$appName,
		IRequest $request,
		private DirectoryService $directoryService,
		private $userId,
	) {
		parent::__construct($appName, $request);
		$this->directoryService->setUserId($this->userId);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @param int $directoryId
	 * @return \OCP\AppFramework\Db\Entity
	 */
	public function read(int $directoryId)
	{
		return $this->directoryService->getDirectoryById($directoryId);
	}
}
