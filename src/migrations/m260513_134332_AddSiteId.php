<?php

namespace alxmerino\lockedentries\migrations;

use alxmerino\lockedentries\Constants;
use Craft;
use craft\db\Migration;

/**
 * m260513_134332_AddSiteId migration.
 */
class m260513_134332_AddSiteId extends Migration
{
    private string $tableName = Constants::PLUGIN_TABLE_NAME;
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->addColumn($this->tableName, 'site_id', $this->integer());
        $this->createIndex($this->db->getIndexName() . '_site_id', $this->tableName, 'site_id');
        $this->addForeignKey(
            $this->db->getForeignKeyName() . '_site_id',
            $this->tableName,
            'site_id',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropIndexIfExists($this->tableName, 'site_id');
        if ($this->db->columnExists($this->tableName, 'site_id')) {
            $this->dropColumn($this->tableName, 'site_id');
        }

        return true;
    }
}
