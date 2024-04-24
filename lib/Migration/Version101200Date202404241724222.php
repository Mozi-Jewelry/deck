<?php
/**
 * @copyright Copyright (c) 2022, chandi Langecker (git@chandi.it)
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

declare(strict_types=1);


namespace OCA\Deck\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\DB\Types;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version101100Date202404241724222 extends SimpleMigrationStep {
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options): ?ISchemaWrapper {
		$schema = $schemaClosure();

		if (!$schema->hasTable('deck_directories')) {
			$table = $schema->createTable('deck_directories');
			$table->addColumn('id', Types::INTEGER, [
				'autoincrement' => true,
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('name', Types::STRING, [
				'notnull' => true,
				'length' => 191,
			]);
			$table->setPrimaryKey(['id']);
		}

		if (!$schema->hasTable('deck_directory_decks')) {
			$table = $schema->createTable('deck_directory_decks');
			$table->addColumn('directory_id', Types::INTEGER, [
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->addColumn('deck_id', Types::INTEGER, [
				'notnull' => true,
				'unsigned' => true,
			]);
			$table->setPrimaryKey(['directory_id', 'deck_id']);
			$table->addIndex(['directory_id'], 'deck_directory_directory_id_idx');
			$table->addIndex(['deck_id'], 'deck_directory_decks_id_idx');
		}
		return $schema;
	}
}

