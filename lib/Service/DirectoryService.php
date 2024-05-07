<?php
namespace OCA\Deck\Service;

use OCA\Deck\Db\Acl;
use OCA\Deck\Db\Assignment;
use OCA\Deck\Db\AssignmentMapper;
use OCA\Deck\Db\BoardMapper;
use OCA\Deck\Db\CardMapper;
use OCA\Deck\Db\Directory;
use OCA\Deck\Db\DirectoryMapper;
use OCA\Deck\Db\Stack;
use OCA\Deck\Db\StackMapper;
use OCA\Deck\NoPermissionException;
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
		private BoardMapper $boardMapper,
		private BoardService $boardService,
		private StackMapper $stackMapper,
		private CardMapper $cardMapper,
		private PermissionService $permissionService,
		private AssignmentMapper $assignedUsersMapper,
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
				if (in_array($board->getId(), $directoriesBoards[$directory->getId()] ?? [])) {
					$boards[] = $board;
				}
			}

			$directory->setBoards($boards);
		}

		$directoriesPrepared = [];
		foreach($directories as $directory) {
			if(!empty($directory->getBoards())) {
				$directoriesPrepared[$directory->getId()] = $directory;
			}
		}

		return $directoriesPrepared;
	}

	public function findById(int $directoryId): Directory
	{
		return $this->directoryMapper->findById($directoryId);
	}

	public function getDirectoryCards($directoryId): array
	{
		$directory = $this->directoryMapper->findById($directoryId);
		if (!$directory) {
			return ['error' => 'no directory'];
		}

		$decks = $this->directoryMapper->getAllBoardsIdFromDirectory($directory->getId());
		$stacks = [];
		$cardIds = [];
		foreach($decks as $deck) {
			try {
				$this->permissionService->checkPermission($this->boardMapper, $deck, Acl::PERMISSION_READ);

				$deckStacks = $this->stackMapper->findAll($deck);
				/** @var Stack $stack */
				foreach ($deckStacks as $stack) {
					$board = $this->boardService->find($stack->getBoardId(), false);
					$title = mb_strtolower(trim($stack->getTitle()));
					if (!$stacks[$title]) {
						$stacks[$title] = $stack;
					}

					$cards = $this->cardMapper->findAllByStack($stack->getId());
					$fullCards = $stacks[$title]->getCards();
					foreach ($cards as $card) {
						$cardIds[] = $card->getId();
						$fullCard = $this->cardMapper->find($card->getId());
						$fullCard->setRelatedBoard($board);
						array_push($fullCards, $fullCard);
					}
					$stacks[$title]->setCards($fullCards);
				}
			} catch (NoPermissionException $e) {
				continue;
			}
		}

		$assignedUsers = $this->assignedUsersMapper->findIn($cardIds);
		foreach($stacks as $stack) {
			foreach($stack->getCards() as $card) {
				$cardAssignedUsers = array_values(array_filter($assignedUsers, function (Assignment $assignment) use ($card) {
					return $assignment->getCardId() === $card->getId();
				}));
				$card->setAssignedUsers($cardAssignedUsers);
			}
		}

		$directory->setStacks($stacks);
		return $directory->getStacks();
	}
}
