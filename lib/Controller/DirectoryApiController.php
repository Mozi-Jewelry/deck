<?php
namespace OCA\Deck\Controller;

use OCA\Deck\Db\Board;
use OCA\Deck\Service\BoardService;
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
class DirectoryApiController extends ApiController
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
	}

	/**
	 * @NoAdminRequired
	 * @CORS
	 * @NoCSRFRequired
	 *
	 * Return all of the directories that the current user has access to.
	 *
	 * @throws StatusException
	 */
	public function index() {
		$directories = $this->directoryService->setUserId($this->userId)->findAll();
		$response = new DataResponse($directories, HTTP::STATUS_OK);
		return $response;
	}
}
