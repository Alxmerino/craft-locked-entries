<?php

namespace alxmerino\lockedentries\models;

use Craft;
use craft\base\Model;

/**
 * Locked Entries settings
 */
class Settings extends Model
{
    public bool $limitAdmins = false;
    public bool $hideLockedEntries = false;
    public string $userGroup = '';
}
