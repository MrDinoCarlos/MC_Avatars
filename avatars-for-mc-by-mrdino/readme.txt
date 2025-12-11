=== Avatars for MC by MrDino ===
Contributors: mrdinocarlos
Donate link: https://buymeacoffee.com/mrdino
Tags: avatar, minecraft, skins, profile, users
Requires at least: 6.5
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 0.0.3
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

= 0.0.3 =

Tested for WordPress Version 6.9