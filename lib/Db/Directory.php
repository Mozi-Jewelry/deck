<?php

namespace OCA\Deck\Db;

/**
 * @method int getId()
 * @method string getName()
 */
class Directory extends RelationalEntity {
	protected $name;
	protected $boards = [];

	public function __construct() {
		$this->addType('id', 'integer');
		$this->addRelation('boards');
		$this->shared = -1;
	}

	public function jsonSerialize(): array {
		$json = parent::jsonSerialize();
		$json['boards'] = $this->boards ?? [];
		return $json;
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
}
