<?php
namespace OCA\Deck\Service;

use OCA\Deck\Activity\ActivityManager;
use OCA\Deck\Activity\ChangeSet;
use OCA\Deck\AppInfo\Application;
use OCA\Deck\BadRequestException;
use OCA\Deck\Db\Acl;
use OCA\Deck\Db\Board;
use OCA\Deck\Db\Directory;
use OCA\Deck\Db\DirectoryMapper;
use OCA\Deck\Db\IPermissionMapper;
use OCA\Deck\Db\Label;
use OCA\Deck\Db\LabelMapper;
use OCA\Deck\Db\Session;
use OCA\Deck\Db\SessionMapper;
use OCA\Deck\Db\Stack;
use OCA\Deck\Db\StackMapper;
use OCA\Deck\Event\AclCreatedEvent;
use OCA\Deck\Event\AclDeletedEvent;
use OCA\Deck\Event\AclUpdatedEvent;
use OCA\Deck\Event\BoardUpdatedEvent;
use OCA\Deck\NoPermissionException;
use OCA\Deck\Notification\NotificationHelper;
use OCA\Deck\Validators\BoardServiceValidator;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\DB\Exception as DbException;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\IUserManager;
use OCP\Server;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class DirectoryService {

	public function __construct(
		private DirectoryMapper $directoryMapper,
		private BoardService $boardService,
		private ?string $userId
	) {

	}

	/**
	 * @param string $userId
	 */
	public function setUserId(string $userId): void {
		$this->userId = $userId;
	}

	/**
	 * @return Directory[]
	 */
	public function findAll(): array
	{
		$directories = $this->directoryMapper->findAll();
		$directoriesBoards = $this->directoryMapper->getAllBoardsId();
		$this->boardService->setUserId($this->userId);

		$userBoards = $this->boardService->getUserBoards();
		foreach($directories as $key => $directory) {
			$boards = [];

			foreach($userBoards as $board) {
				if (in_array($board->getId(), $directoriesBoards[$directory->getId()])) {
					$boards[] = $board;
				}
			}

			$directories[$key] = $directory->setBoards($boards);
		}

		return array_filter($directories, function($directory) {
			return !empty($directory->getBoards());
		});
	}
}
