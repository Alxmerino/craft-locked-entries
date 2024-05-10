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

    public function getUserGroupOptions(): array
    {
        // Default 'Select a user group' option
        $defaultOption = [['label' => 'Select a user group', 'value' => '', 'disabled' => true]];

        // Get all user groups and transform them into 'value' and 'label'
        $groupOptions = array_map(
            function ($group) {
                return ['value' => $group->id, 'label' => $group->name];
            },
            Craft::$app->getUserGroups()->getAllGroups()
        );

        return array_merge($defaultOption, $groupOptions);
    }
}
