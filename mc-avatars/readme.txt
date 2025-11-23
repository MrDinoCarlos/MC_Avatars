=== MC Avatars by MrDino ===
Contributors: mrdinocarlos
Donate link: https://buymeacoffee.com/mrdino
Tags: avatar, minecraft, skins, profile, users
Requires at least: 6.5
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Replace WordPress avatars with Minecraft-style heads using Minecraft usernames (and future custom skin features).

== Description ==

MC Avatars by MrDino lets your users connect their Minecraft identity with WordPress.

* Use Minecraft usernames to display player heads.
* Users control their avatar from their profile page.
* Admin can configure how avatars are loaded.

== Installation ==

1. Upload the `mc-avatars` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to **Settings → MC Avatars** to configure the plugin.
4. Users can edit their profile and set their **Minecraft Username**.

== Frequently Asked Questions ==

= Does this replace Gravatar? =
Yes, for users who have set a Minecraft username, their Minecraft head will be used as avatar.

= Which skin source do you use? =
Currently Crafatar (and Mojang username-based resolution). More sources and custom skins will be added.

== Changelog ==

= 0.0.1 =
* Initial structured version. Basic admin settings page and avatar replacement via Minecraft username.

Added Core plugin structure and file system (mc-avatars.php, classes, assets, includes).
Added Basic Minecraft avatar replacement logic using:
Added Minecraft username → mc-heads avatar service.
Added Fallback to Steve (MHF_Steve).
Added Live avatar preview on: WordPress Profile page, WordPress Registration page, WordPress Login page (AJAX-powered).
Added Predefined Minecraft Heads grid (Steve, Alex, Creeper, Zombie, Skeleton, Enderman).
Added Click-to-select avatar head system (with deselect → fallback to username).
Added Profile fields: Minecraft Username field, Predefined Avatar Head selector.
Added Registration support: Live preview as user types the username, Avatar head selection saved upon account creation.
Added AJAX endpoint for login: User lookup by email or username. If user not found → render Minecraft username head. If invalid → fallback to Steve.
Added Admin Settings page under “Settings → MC Avatars by MrDino”.
Added Option to choose avatar source: mc-heads.net / crafatar.com
Added CSS styling and responsive hover effects for head grid.
Added Unified preview logic between profile, register, and login.
Added script and stylesheet enqueue to use cache-busting (time()).
Improved preset head rendering using HTML buttons instead of <select>.
Improved Reworked translation string in admin footer to remove placeholder usage and comply with WordPress Plugin Check (no more MissingTranslatorsComment error).
Improved Added nonce verification and wp_nonce_field() to user profile fields to satisfy WP security recommendations.
Improved Code cleanup and consistency improvements for better compatibility with WordPress.org standards.
Fixed Preview not updating in real-time while typing.
Fixed Case-sensitive username issues (now converted to lowercase).
Fixed Avatar fallback not applying if no username set.
Fixed Missing preview image on register page.
Fixed AJAX errors on login due to missing ajaxurl.
Fixed Incorrect default avatars appearing if the service (mc-heads / crafatar) failed.
Fixed Broken CSS loading due to caching (now versioned).
Fixed Improper handling of empty username → now always displays Steve.
Fixed Register page grid not loading under certain themes.
Fixed Broken behavior where login page was incorrectly showing head selector (now disabled).
Fixed Sanitization of login preview AJAX parameter using filter_input() to fully remove Plugin Check warnings about unsanitized $_GET usage.
Fixed deprecated or discouraged patterns for internationalization to ensure full compliance with WP 4.6+ automatic textdomain loading.
