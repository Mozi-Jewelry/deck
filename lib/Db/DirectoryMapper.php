<?php

namespace OCA\Deck\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\Cache\CappedMemoryCache;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\IGroupManager;
use OCP\IUserManager;
use Psr\Log\LoggerInterface;

/** @template-extends QBMapper<Board> */
class DirectoryMapper extends QBMapper {

	public function __construct(
		IDBConnection $db
	) {
		parent::__construct($db, 'deck_directories', Directory::class);
	}

	public function findAll(): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('deck_directories');

		return $this->findEntities($qb);
	}

	public function getAllBoardsId(): array
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('directory_id', 'deck_id')
			->from('deck_directory_decks');

		$result = $qb->executeQuery();
		$decks = [];

		foreach($result->fetchAll() as $entry) {
			$decks[$entry['directory_id']][] = $entry['deck_id'];
		}

		return $decks;
	}
}
