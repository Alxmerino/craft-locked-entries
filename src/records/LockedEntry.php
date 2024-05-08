<?php

namespace alxmerino\lockedentries\records;

use alxmerino\lockedentries\Constants;
use Craft;
use craft\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * Locked Entry record
 */
class LockedEntry extends ActiveRecord
{
    public static function tableName(): string
    {
        return Constants::PLUGIN_TABLE_NAME;
    }

    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
}
