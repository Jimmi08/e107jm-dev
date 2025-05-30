<?php
/*
 * Copyright (C) 2008-2025 e107 Inc (e107.org), Licensed under GNU GPL (http://www.gnu.org/licenses/gpl.txt)
 *
 * Admin Language File
 *
*/

/*
The definitions in this file are for standard "explanatory" messages which might be entered
into any of the system logs. They are in three groups with different prefixes:
	LAN_ADMIN_LOG_nnn - the admin log (records intentional actions by admins)
	LAN_AUDIT_LOG_nnn - the audit log (records actions, generally intentional, by users)
	LAN_ROLL_LOG_nnn - the rolling log (records extraneous events, debugging etc)
*/


// User audit trail events. For messages 11-30, the last 2 digits must match the define for the event type in the admin log class file

return [
    'LAN_AUDIT_LOG_001' => "Access by banned user",
    'LAN_AUDIT_LOG_002' => "Flood protection activated",
    'LAN_AUDIT_LOG_003' => "Access from banned IP Address",
    'LAN_AUDIT_LOG_004' => "",
    'LAN_AUDIT_LOG_005' => "",
    'LAN_AUDIT_LOG_006' => "User changed password",
    'LAN_AUDIT_LOG_007' => "User changed email address",
    'LAN_AUDIT_LOG_008' => "",
    'LAN_AUDIT_LOG_009' => "",
    'LAN_AUDIT_LOG_010' => "User data changed by admin",
    'LAN_AUDIT_LOG_011' => "User signed up",
    'LAN_AUDIT_LOG_012' => "User confirmed registration",
    'LAN_AUDIT_LOG_013' => "User login",
    'LAN_AUDIT_LOG_014' => "User logout",
    'LAN_AUDIT_LOG_015' => "User changed display name",
    'LAN_AUDIT_LOG_016' => "User changed password",
    'LAN_AUDIT_LOG_017' => "User changed email address",
    'LAN_AUDIT_LOG_018' => "User password reset",
    'LAN_AUDIT_LOG_019' => "User changed settings",
    'LAN_AUDIT_LOG_020' => "User added by admin",
    'LAN_AUDIT_LOG_021' => "User email bounce",
    'LAN_AUDIT_LOG_022' => "User banned",
    'LAN_AUDIT_LOG_023' => "User bounce reset",
    'LAN_AUDIT_LOG_024' => "User temporary status",
    'LAN_AUDIT_LOG_025' => "User navigation trail",
    'LAN_AL_ADLOG_01' => "Admin log - prefs updated",
    'LAN_AL_ADLOG_02' => "Admin log - delete old data",
    'LAN_AL_ADLOG_03' => "User Audit log - delete old data",
    'LAN_AL_ADLOG_04' => "User audit options updated",
    'LAN_AL_ADLOG_05' => "",
    'LAN_AL_USET_01' => "Admin edited user data",
    'LAN_AL_USET_02' => "User added by Admin",
    'LAN_AL_USET_03' => "User options updated",
    'LAN_AL_USET_04' => "Users pruned",
    'LAN_AL_USET_05' => "User banned",
    'LAN_AL_USET_06' => "User unbanned",
    'LAN_AL_USET_07' => "User deleted",
    'LAN_AL_USET_08' => "User made admin",
    'LAN_AL_USET_09' => "User admin status revoked",
    'LAN_AL_USET_10' => "User approved",
    'LAN_AL_USET_11' => "Resend validation email",
    'LAN_AL_USET_12' => "Resend all validation emails",
    'LAN_AL_USET_13' => "Bounced emails deleted",
    'LAN_AL_USET_14' => "Class membership updated",
    'LAN_AL_USET_15' => "Signup refused",
    'LAN_AL_UCLASS_00' => "Unknown userclass-related event",
    'LAN_AL_UCLASS_01' => "Userclass created",
    'LAN_AL_UCLASS_02' => "Userclass deleted",
    'LAN_AL_UCLASS_03' => "Userclass edited",
    'LAN_AL_UCLASS_04' => "Class membership updated",
    'LAN_AL_UCLASS_05' => "Initial userclass settings edited",
    'LAN_AL_UCLASS_06' => "Class membership emptied",
    'LAN_AL_BANLIST_00' => "Unknown ban-related event",
    'LAN_AL_BANLIST_01' => "Manual ban added",
    'LAN_AL_BANLIST_02' => "Ban deleted",
    'LAN_AL_BANLIST_03' => "Ban time changed",
    'LAN_AL_BANLIST_04' => "Whitelist entry added",
    'LAN_AL_BANLIST_05' => "Whitelist entry deleted",
    'LAN_AL_BANLIST_06' => "Banlist exported",
    'LAN_AL_BANLIST_07' => "Banlist imported",
    'LAN_AL_BANLIST_08' => "Banlist options updated",
    'LAN_AL_BANLIST_09' => "Banlist entry edited",
    'LAN_AL_BANLIST_10' => "Whitelist entry edited",
    'LAN_AL_BANLIST_11' => "Whitelist hit for ban entry",
    'LAN_AL_BANLIST_12' => "Expired bans cleared",
    'LAN_AL_COMMENT_01' => "Comment(s) deleted",
    'LAN_ROLL_LOG_01' => "Empty username and/or password",
    'LAN_ROLL_LOG_02' => "Incorrect image code entered",
    'LAN_ROLL_LOG_03' => "Invalid username/password combination",
    'LAN_ROLL_LOG_04' => "Invalid username entered",
    'LAN_ROLL_LOG_05' => "Login attempt by user not fully signed up",
    'LAN_ROLL_LOG_06' => "Login blocked by event trigger handler",
    'LAN_ROLL_LOG_07' => "Multiple logins from same address",
    'LAN_ROLL_LOG_08' => "Excessive username length",
    'LAN_ROLL_LOG_09' => "Banned user attempted login",
    'LAN_ROLL_LOG_10' => "Login fail - reason unknown",
    'LAN_ROLL_LOG_11' => "Admin login fail",
    'LAN_AL_PREFS_01' => "Preferences changed",
    'LAN_AL_PREFS_02' => "New Preferences created",
    'LAN_AL_PREFS_03' => "Error saving prefs",
    'LAN_AL_FRONTPG_00' => "Unknown front page-related event",
    'LAN_AL_FRONTPG_01' => "Rules order changed",
    'LAN_AL_FRONTPG_02' => "Rule added",
    'LAN_AL_FRONTPG_03' => "Rule edited",
    'LAN_AL_FRONTPG_04' => "Rule deleted",
    'LAN_AL_FRONTPG_05' => "",
    'LAN_AL_FRONTPG_06' => "",
    'LAN_AL_UTHEME_00' => "Unknown user theme related event",
    'LAN_AL_UTHEME_01' => "User theme settings changed",
    'LAN_AL_UTHEME_02' => "",
    'LAN_AL_UPDATE_00' => "Unknown software update related event",
    'LAN_AL_UPDATE_01' => "Update from 1.0 to 2.0 executed",
    'LAN_AL_UPDATE_02' => "Update from 0.7.x to 0.7.6 executed",
    'LAN_AL_UPDATE_03' => "Missing prefs added",
    'LAN_AL_ADMIN_00' => "Unknown administrator event",
    'LAN_AL_ADMIN_01' => "Update admin permissions",
    'LAN_AL_ADMIN_02' => "Admin rights removed",
    'LAN_AL_ADMIN_03' => "",
    'LAN_AL_MAINT_00' => "Unknown maintenance message",
    'LAN_AL_MAINT_01' => "Maintenance mode set",
    'LAN_AL_MAINT_02' => "Maintenance mode cleared",
    'LAN_AL_SLINKS_00' => "Unknown sitelinks message",
    'LAN_AL_SLINKS_01' => "Sublinks generated",
    'LAN_AL_SLINKS_02' => "Sitelink moved up",
    'LAN_AL_SLINKS_03' => "Sitelink moved down",
    'LAN_AL_SLINKS_04' => "Sitelink order updated",
    'LAN_AL_SLINKS_05' => "Sitelinks options updated",
    'LAN_AL_SLINKS_06' => "Sitelink deleted",
    'LAN_AL_SLINKS_07' => "Sitelink submitted",
    'LAN_AL_SLINKS_08' => "Sitelink updated",
    'LAN_AL_THEME_00' => "Unknown theme-related message",
    'LAN_AL_THEME_01' => "Site theme updated",
    'LAN_AL_THEME_02' => "Admin theme updated",
    'LAN_AL_THEME_03' => "Image preload/site CSS updated",
    'LAN_AL_THEME_04' => "Admin style/CSS updated",
    'LAN_AL_THEME_05' => "",
    'LAN_AL_CACHE_00' => "Unknown cache-control message",
    'LAN_AL_CACHE_01' => "Cache settings updated",
    'LAN_AL_CACHE_02' => "System cache emptied",
    'LAN_AL_CACHE_03' => "Content cache emptied",
    'LAN_AL_CACHE_04' => "",
    'LAN_AL_EMOTE_00' => "Unknown emote-related message",
    'LAN_AL_EMOTE_01' => "Active emote pack changed",
    'LAN_AL_EMOTE_02' => "Emotes activated",
    'LAN_AL_EMOTE_03' => "Emotes deactivated",
    'LAN_AL_WELCOME_00' => "Unknown welcome-related message",
    'LAN_AL_WELCOME_01' => "Welcome message created",
    'LAN_AL_WELCOME_02' => "Welcome message updated",
    'LAN_AL_WELCOME_03' => "Welcome message deleted",
    'LAN_AL_WELCOME_04' => "Welcome message options changed",
    'LAN_AL_WELCOME_05' => "",
    'LAN_AL_ADMINPW_01' => "Admin password changed",
    'LAN_AL_ADMINPW_02' => "Admin password rehashed",
    'LAN_AL_BANNER_00' => "Unknown banner-related message",
    'LAN_AL_BANNER_01' => "Banner menu update",
    'LAN_AL_BANNER_02' => "Banner created",
    'LAN_AL_BANNER_03' => "Banner updated",
    'LAN_AL_BANNER_04' => "Banner deleted",
    'LAN_AL_BANNER_05' => "Banner configuration updated",
    'LAN_AL_BANNER_06' => "",
    'LAN_AL_IMALAN_00' => "Unknown image-related message",
    'LAN_AL_IMALAN_01' => "Avatar deleted",
    'LAN_AL_IMALAN_02' => "All avatars and photos deleted",
    'LAN_AL_IMALAN_03' => "Avatar deleted",
    'LAN_AL_IMALAN_04' => "Settings updated",
    'LAN_AL_IMALAN_05' => "",
    'LAN_AL_IMALAN_06' => "",
    'LAN_AL_LANG_00' => "Unknown language-related message",
    'LAN_AL_LANG_01' => "Language prefs changed",
    'LAN_AL_LANG_02' => "Language tables deleted",
    'LAN_AL_LANG_03' => "Language tables created",
    'LAN_AL_LANG_04' => "Language zip created",
    'LAN_AL_LANG_05' => "",
    'LAN_AL_META_01' => "Meta tags updated",
    'LAN_AL_DOWNL_01' => "Download options changed",
    'LAN_AL_DOWNL_02' => "Download category created",
    'LAN_AL_DOWNL_03' => "Download category updated",
    'LAN_AL_DOWNL_04' => "Download category deleted",
    'LAN_AL_DOWNL_05' => "Download created",
    'LAN_AL_DOWNL_06' => "Download updated",
    'LAN_AL_DOWNL_07' => "Download deleted",
    'LAN_AL_DOWNL_08' => "Download category order updated",
    'LAN_AL_DOWNL_09' => "Download limit added",
    'LAN_AL_DOWNL_10' => "Download limit edited",
    'LAN_AL_DOWNL_11' => "Download limit deleted",
    'LAN_AL_DOWNL_12' => "Download mirror added",
    'LAN_AL_DOWNL_13' => "Download mirror updated",
    'LAN_AL_DOWNL_14' => "Download mirror deleted",
    'LAN_AL_DOWNL_15' => "",
    'LAN_AL_CPAGE_01' => "Custom page/menu added",
    'LAN_AL_CPAGE_02' => "Custom page/menu updated",
    'LAN_AL_CPAGE_03' => "Custom page/menu deleted",
    'LAN_AL_CPAGE_04' => "Custom page/menu settings updated",
    'LAN_AL_EUF_01' => "EUF moved up",
    'LAN_AL_EUF_02' => "EUF moved down",
    'LAN_AL_EUF_03' => "EUF category moved up",
    'LAN_AL_EUF_04' => "EUF category moved down",
    'LAN_AL_EUF_05' => "Extended User Field added",
    'LAN_AL_EUF_06' => "Extended User Field updated",
    'LAN_AL_EUF_07' => "Extended User Field deleted",
    'LAN_AL_EUF_08' => "EUF category added",
    'LAN_AL_EUF_09' => "EUF category updated",
    'LAN_AL_EUF_10' => "EUF category deleted",
    'LAN_AL_EUF_11' => "Extended user fields activated",
    'LAN_AL_EUF_12' => "Extended user fields deactivated",
    'LAN_AL_MENU_01' => "Menu activated",
    'LAN_AL_MENU_02' => "Menu - set visibility",
    'LAN_AL_MENU_03' => "Menu - change area",
    'LAN_AL_MENU_04' => "Menu deactivated",
    'LAN_AL_MENU_05' => "Menu - move to top",
    'LAN_AL_MENU_06' => "Menu - move to bottom",
    'LAN_AL_MENU_07' => "Menu - move up",
    'LAN_AL_MENU_08' => "Menu - move down",
    'LAN_AL_MENU_09' => "",
    'LAN_AL_UPLOAD_01' => "Uploaded file deleted",
    'LAN_AL_UPLOAD_02' => "Upload prefs changed",
    'LAN_AL_SEARCH_01' => "Search settings updated",
    'LAN_AL_SEARCH_02' => "Search prefs updated",
    'LAN_AL_SEARCH_03' => "Search params auto-update",
    'LAN_AL_SEARCH_04' => "Searchable areas updated",
    'LAN_AL_SEARCH_05' => "Search handler settings updated",
    'LAN_AL_SEARCH_06' => "",
    'LAN_AL_NOTIFY_01' => "Notify settings updated",
    'LAN_AL_NEWS_01' => "News item deleted",
    'LAN_AL_NEWS_02' => "News category deleted",
    'LAN_AL_NEWS_03' => "Submitted news deleted",
    'LAN_AL_NEWS_04' => "News category created",
    'LAN_AL_NEWS_05' => "News category updated",
    'LAN_AL_NEWS_06' => "News preferences updated",
    'LAN_AL_NEWS_07' => "Submitted news authorised",
    'LAN_AL_NEWS_08' => "News item added",
    'LAN_AL_NEWS_09' => "News item updated",
    'LAN_AL_NEWS_10' => "News category rewrite changed",
    'LAN_AL_NEWS_11' => "News category rewrite deleted",
    'LAN_AL_NEWS_12' => "News rewrite changed",
    'LAN_AL_NEWS_13' => "News rewrite deleted",
    'LAN_AL_FILEMAN_01' => "File(s) deleted",
    'LAN_AL_FILEMAN_02' => "File(s) moved",
    'LAN_AL_FILEMAN_03' => "File(s) uploaded",
    'LAN_AL_FILEMAN_04' => "",
    'LAN_AL_MAIL_01' => "Test email sent",
    'LAN_AL_MAIL_02' => "Mailshot created",
    'LAN_AL_MAIL_03' => "Mail settings updated",
    'LAN_AL_MAIL_04' => "Mailshot details deleted",
    'LAN_AL_MAIL_05' => "Mail Database tidy",
    'LAN_AL_MAIL_06' => "Mailout activated",
    'LAN_AL_MAIL_07' => "",
    'LAN_AL_PLUGMAN_01' => "Plugin installed",
    'LAN_AL_PLUGMAN_02' => "Plugin updated",
    'LAN_AL_PLUGMAN_03' => "Plugin uninstalled",
    'LAN_AL_PLUGMAN_04' => "Plugin refreshed",
    'LAN_AL_EURL_01' => "Site URL configuration changed",
    'LAN_AL_MISC_01' => "Tree menu settings updated",
    'LAN_AL_MISC_02' => "Online menu settings updated",
    'LAN_AL_MISC_03' => "Login menu settings updated",
    'LAN_AL_MISC_04' => "Comment menu settings updated",
    'LAN_AL_MISC_05' => "Clock menu settings updated",
    'LAN_AL_MISC_06' => "Blog calendar menu settings updated",
    'LAN_AL_PING_01' => "Ping to service",
    'LAN_AL_ADMINUI_01' => "Admin-UI DB Table Insert: [x]",
    'LAN_AL_ADMINUI_02' => "Admin-UI DB Table Update: [x]",
    'LAN_AL_ADMINUI_03' => "Admin-UI DB Table Delete: [x]",
    'LAN_AL_ADMINUI_04' => "Admin-UI DB Error: [x]",
    'LAN_AL_BACKUP' => "Database backup",
    'LAN_AL_MEDIA_01' => "Media Upload",
    'LAN_AL_USET_100' => "Admin logged in as another user",
    'LAN_AL_USET_101' => "Admin logged out as another user",
];
