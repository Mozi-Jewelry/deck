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

	public function findById($directoryId): array
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('deck_directories')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($directoryId, IQueryBuilder::PARAM_INT)));

		return $this->findEntity($qb);
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

	public function getAllBoardsIdFromDirectory($directoryId): array
	{
		$qb = $this->db->getQueryBuilder();
		$qb->select('directory_id', 'deck_id')
			->from('deck_directory_decks')
			->where($qb->expr()->eq('directory_id', $qb->createNamedParameter($directoryId, IQueryBuilder::PARAM_INT)));;

		$result = $qb->executeQuery();
		return array_map(fn($directory) => $directory['deck_id'], $result);
	}
}
