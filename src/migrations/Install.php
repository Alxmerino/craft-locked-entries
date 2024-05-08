<?php

namespace alxmerino\lockedentries\migrations;

use alxmerino\lockedentries\Constants;
use Craft;
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    private string $tableName = Constants::PLUGIN_TABLE_NAME;
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Check if the table already exists
        if ($this->db->tableExists($this->tableName)) {
            Craft::info(
                "Table {$this->tableName} already exists. Skipping table creation.",
                __METHOD__
            );
            return true;
        }

        // Create the table
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'entry_id' => $this->integer(),
            'user_id' => $this->integer(),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
            'uid' => $this->uid(),
        ]);

        $this->createIndex(
            $this->db->getIndexName() . '_entry_id',
            $this->tableName,
            'entry_id'
        );

        $this->createIndex(
            $this->db->getIndexName() . '_user_id',
            $this->tableName,
            'user_id'
        );

        // Add foreign key constraints
        $this->addForeignKey(
            $this->db->getForeignKeyName() . '_entry_id',
            $this->tableName,
            'entry_id',
            '{{%entries}}', // Reference Craft's entries table
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            $this->db->getForeignKeyName() . '_user_id',
            $this->tableName,
            'user_id',
            '{{%users}}',
            'id',
            'SET NULL',
            'CASCADE'
        );

        Craft::info("Created table {$this->tableName}.", __METHOD__);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        // Check if the table exists
        if ($this->db->tableExists($this->tableName)) {
            // Drop the table if it exists
            $this->dropTableIfExists($this->tableName);
            Craft::info("Dropped table {$this->tableName}.", __METHOD__);
        }

        return true;
    }
}
