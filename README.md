<p align="center"><img src="./src/icon.svg" width="100" height="100" alt="Feed Me icon"></p>

<h1 align="center">Locked Entries for Craft CMS</h1>

Gives users the ability to mark entries as 'locked' so only they can edit them

## Key Features:
- **Lock Entries**: Users can mark entries as locked, signaling that they are responsible for editing and maintaining those entries.
- **Editing Permissions**: Once an entry is locked, other users will be restricted from making changes, ensuring a single point of responsibility.
- **Flexible Management**: Administrators have the ability to override locks, ensuring smooth collaboration when necessary.

## Requirements

This plugin requires Craft CMS 4.x or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Locked Entries”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require alxmerino/craft-locked-entries

# tell Craft to install the plugin
./craft plugin/install locked-entries
```
