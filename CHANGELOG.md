# Release Notes for Locked Entries

## 1.1.1 - 2026-03-07
- Fixed PHP warnings ("Attempt to read property on null") caused by event handlers executing on frontend and console requests where no authenticated user exists
- Added CP request guards to `EVENT_BEFORE_PREPARE` and `EVENT_AUTHORIZE_VIEW` handlers
- Added null user check to `EVENT_DEFINE_SIDEBAR_HTML` handler
- Added console request guard to `EVENT_BEFORE_SAVE` handler
- Improved type safety for `getLockedFieldHtml()` method

## 1.0.1 - 2024-05-10
- Fixed Craft version constrain

## 1.0.0 - 2024-05-10
- Initial release
