<?php

namespace OCA\Deck\Db;

/**
 * @method int getId()
 * @method string getName()
 */
class Directory extends RelationalEntity {
	protected $name;
	protected $directoryId;
	protected $boards = [];
	protected $stacks = [];

	public function __construct() {
		$this->addType('id', 'integer');
		$this->addType('directoryId', 'integer');
		$this->addRelation('boards');
		$this->shared = -1;
	}

	public function jsonSerialize(): array {
		$json = parent::jsonSerialize();
		$json['boards'] = $this->boards ?? [];
		return $json;
	}

	public function setStacks($stacks)
	{
		$this->stacks = $stacks;
	}

	public function getStacks()
	{
		return $this->stacks;
	}

	/**
	 * @param Board[] $boards
	 */
	public function setBoards($boards) {
		$this->boards = $boards;
	}

	/** @returns Board[]|null */
	public function getBoards(): ?array {
		return $this->boards;
	}

	public function setDirectoryId($directoryId)
	{
		$this->directoryId = $directoryId;
	}

	/** @returns int|null */
	public function getDirectoryId(): ?int
	{
		return $this->directoryId;
	}
}
