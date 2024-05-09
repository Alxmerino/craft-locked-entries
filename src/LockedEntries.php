<?php

namespace alxmerino\lockedentries;

use Craft;
use alxmerino\lockedentries\models\Settings;
use alxmerino\lockedentries\records\LockedEntry;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\db\ElementQuery;
use craft\elements\Entry;
use craft\events\AuthorizationCheckEvent;
use craft\events\CancelableEvent;
use craft\events\DefineHtmlEvent;
use craft\events\ModelEvent;
use craft\helpers\Cp;
use craft\helpers\Html;
use craft\services\Elements;
use yii\base\Event;

/**
 * Locked Entries plugin
 *
 * @method static LockedEntries getInstance()
 * @method Settings getSettings()
 * @author Rene Merino <rene@amayamedia.com>
 * @copyright Rene Merino
 * @license MIT
 */
class LockedEntries extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => [],
        ];
    }

    public function init(): void
    {
        parent::init();

        // Defer setup tasks until Craft is fully initialized
        Craft::$app->onInit(function() {
            $this->attachEventHandlers();
        });
    }

    /**
     * Listen for events
     * @return void
     */
    private function attachEventHandlers(): void
    {
        // Add lightswitch to toggle an entry as "locked"
        Event::on(
            Element::class,
            Element::EVENT_DEFINE_SIDEBAR_HTML,
            function (DefineHtmlEvent $event) {
                $event->html = $this->getLockedFieldHtml($event->sender) . $event->html;
            }
        );

        // Assign Entry to User marking as "locked"
        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_SAVE,
            function(ModelEvent $e) {
                $entry = $e->sender;
                $request = Craft::$app->getRequest();
                $user = Craft::$app->getUser()->getIdentity();

                // Bail early if no user
                if (!$user) {
                    return;
                }

                // Check if the "locked" field was submitted and set
                $isLocked = $request->getBodyParam(Constants::PLUGIN_FIELD_NAME, false);

                // If locked, lets keep a record in our table
                if ($isLocked) {
                    // Check if this entry is already locked
                    $recordExists = LockedEntry::find()
                        ->where(['entry_id' => $entry->id])
                        ->exists();

                    // If it's locked and doesn't exist in the table, add it
                    if (!$recordExists) {
                        $lockedEntry = new LockedEntry([
                            'entry_id' => $entry->id,
                            'user_id' => $user->id,
                        ]);
                        $lockedEntry->save();
                    }
                } else {
                    // If not locked, remove it from the custom database table
                    LockedEntry::deleteAll(['entry_id' => $entry->id]);
                }
            }
        );

        // Event to handle when an entry is deleted
        Event::on(
            Entry::class,
            Entry::EVENT_BEFORE_DELETE,
            function (ModelEvent $event) {
                $entry = $event->sender;

                // Remove the record from our table
                LockedEntry::deleteAll(['entry_id' => $entry->id]);
            }
        );

        // Check if user is authorized to see this entry
        Event::on(
            Elements::class,
            Elements::EVENT_AUTHORIZE_VIEW,
            function(AuthorizationCheckEvent $event) {
                $entry = $event->element;
                $lockedEntry = LockedEntry::find()
                    ->where(['entry_id' => $entry->id])
                    ->one();

                // Check if this entry is locked and in the CP only
                if (Craft::$app->request->isCpRequest && $lockedEntry) {
                    $user = Craft::$app->getUser()->getIdentity();

                    // Bail early if no user
                    if (!$user) {
                        return;
                    }

                    $limitAdmins = $this->getSettings()->limitAdmins;

                    // Only user's who locked this entry and if admins are allowed
                    // should be able to see this
                    $event->authorized = (
                        $lockedEntry->user_id === $user->id ||
                        ($user->admin && !$limitAdmins)
                    );
                }
            }
        );

        /**
         * Modify query to exclude entries that are 'locked'
         */
        Event::on(
            ElementQuery::class,
            ElementQuery::EVENT_BEFORE_PREPARE,
            function(CancelableEvent $event) {
                // Bail early if setting is off and display locked entries
                if (!$this->getSettings()->hideLockedEntries) {
                    return true;
                }

                $query = $event->sender;
                // Remove locked entries on the entry element only and in a collection of entries,
                // meaning the limit is more than 1
                if ($query->elementType == 'craft\elements\Entry' && $query->limit > 1) {
                    $user = Craft::$app->getUser()->getIdentity();
                    $lockedEntries = LockedEntry::find()
                        ->where(['not', ['user_id' => $user->id]])
                        ->collect()->pluck('entry_id')->toArray();

                    if (!empty($lockedEntries)) {
                        $query->id(['not', ...$lockedEntries]);
                    }
                }
            }
        );
    }

    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('locked-entries/_settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    /**
     * Get the HTML for to display on the entry's sidebar
     * @param object|null $entry
     * @return string
     */
    protected function getLockedFieldHtml(null|object $entry): string
    {
        $lockedEntry = LockedEntry::find()->where(['entry_id' => $entry->id])->exists();
        $lockedField = Cp::lightswitchFieldHtml([
                'id' => Constants::PLUGIN_FIELD_NAME,
                'label' => Craft::t(Constants::PLUGIN_HANDLE, 'Locked'),
                'name' => Constants::PLUGIN_FIELD_NAME,
                'on' => $lockedEntry ?? false,
            ]);

        return Html::beginTag('fieldset') .
            Html::tag('div', $lockedField, ['class' => 'meta']) .
            Html::endTag('fieldset');
    }
}
