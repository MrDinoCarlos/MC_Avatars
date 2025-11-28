=== Avatars for MC by MrDino ===
Contributors: mrdinocarlos
Donate link: https://buymeacoffee.com/mrdino
Tags: avatar, minecraft, skins, profile, users
Requires at least: 6.5
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 0.0.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Replace WordPress avatars with Minecraft-style heads using Minecraft usernames (and future custom skin features).

== Description ==

MC Avatars by MrDino lets your users connect their Minecraft identity with WordPress.

* Use Minecraft usernames to display player heads.
* Users control their avatar from their profile page.
* Admin can configure how avatars are loaded.

== Installation ==

1. Upload the `avatars-for-mc-by-mrdino` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Go to **Settings → MC Avatars** to configure the plugin.
4. Users can edit their profile and set their **Minecraft Username**.

== External services ==

This plugin is developed and maintained by MrDino (https://mrdino.es).
All live status and avatar features are powered by the external services listed below.
Your WordPress site does not send any data to mrdino.es beyond the normal plugin links and metadata.

This plugin connects to two external services to display live Minecraft data and player avatars.

= mcsrvstat.us API =

This service is used to fetch live status information (online state, players, MOTD, version, ping, etc.) and an optional banner image for the Minecraft server you configure in the plugin settings.
* What data is sent: the server address (host and port) that you enter in the plugin settings.
* When data is sent: whenever the status or player list shortcodes are loaded on a page, or when they refresh via AJAX in the background.
* Service website and API documentation: https://mcsrvstat.us/ (API at https://api.mcsrvstat.us/)
* Legal / privacy: please refer to the legal information and privacy details linked from their website.

= MCHeads (mc-heads.net) =

This service is used to generate square avatar images ("heads") for Minecraft players based on their in-game name.
* What data is sent: the Minecraft player name(s) reported by your server (no WordPress user data is sent).
* When data is sent: when the player list is displayed and an avatar image needs to be shown for an online player.
* Service website: https://mc-heads.net/
* Terms of use: https://minecraft-heads.com/terms-of-use
* Privacy policy: https://minecraft-heads.com/privacy-policy

== Frequently Asked Questions ==

= Does this replace Gravatar? =
Yes, for users who have set a Minecraft username, their Minecraft head will be used as avatar.

= Which skin source do you use? =
Currently Crafatar (and Mojang username-based resolution). More sources and custom skins will be added.

== Changelog ==

= 0.0.2 =

Added full plugin rename support: updated plugin header, text domain, folder name and main file to “avatars-for-mc-by-mrdino”.
Added automatic migration to the new text domain for all translation calls across the plugin.
Added integration auto-loader: BuddyPress, WooCommerce, and Ultimate Member integrations now load only when parent plugins are active.
Added independent integration loading logic (each integration now loads standalone, no more BuddyPress-dependent loading).
Added correct directory resolution for all integration includes under /includes/integrations/.
Added full BuddyPress avatar override integration (bp_core_fetch_avatar / bp_get_member_avatar).
Added BuddyPress REST support for: members, group members, group invites (avatar + avatar_urls injection).
Added unified REST avatar generator (filter_rest_member_like_avatar) for consistent behavior across endpoints.
Added Invite Members support: avatars now taken from MC Avatars (no more random mc-heads lookups).
Added automatic fallback logic for Invite Members: real stored avatar → predefined head → username head.
Added compatibility with BuddyPress Nouveau JS models without modifying core templates.
Added safe detection of user IDs in complex REST payloads (id, user_id, user, member, invited_user).
Added strict use of user’s stored MC Avatar instead of recalculating from display name.
Added improved size-based avatar generation (thumb/full) for consistency across BuddyPress UI.
Improved BuddyPress Invite Members panel rendering: avatars now update instantly without JS hacks.
Improved Group Invites parsing by removing previous JS override and replacing with correct REST injection.
Improved Member Directory consistency so all avatar sizes use the same plugin logic.
Improved reliability of avatar fallback system across BuddyPress and WordPress pages.
Improved performance of avatar resolver by reducing duplicate lookups.
Improved code structure for clarity: removed old group-invites override, merged logic into REST pipeline.
Improved plugin initialization by relocating WooCommerce bootstrap into class-mc-avatars.php for consistency.
Improved internal structure: removed duplicated class instantiation caused by dual WooCommerce bootstrap.
Improved integration reliability by ensuring require paths use the new slug-based folder structure.
Improved admin asset loading on login screen by aligning script handles with new slug.
Improved fallback behavior by unifying integration loading order inside MC_Avatars core class.
Improved maintainability by removing slug-dependent hardcoded references from multiple files.
Improved consistency across all admin pages using the new text domain for labels and messages.
Fixed Invite Members panel showing default BP grey icons instead of plugin avatars.
Fixed issue where avatars inside group invitations used display name instead of real account username.
Fixed missing avatars in Group Admin → Manage Members.
Fixed Group avatar upload interfering with BuddyPress-rendered group member avatars.
Removed obsolete text domain “mc-avatars” from all translation calls.
Removed deprecated WooCommerce bootstrap block from main plugin file.
Removed unnecessary dependency chain tying UM & WooCommerce loading to BuddyPress.
Removed duplicate integration load logic to avoid double hooks execution.