<?php
/**
 * Yui Shoutbox
 * https://github.com/martec
 *
 * Copyright (C) 2015-2015, Martec
 *
 * Yui Shoutbox is licensed under the GPL Version 3, 29 June 2007 license:
 *	http://www.gnu.org/copyleft/gpl.html
 *
 * @fileoverview Yui Shoutbox - Websocket Shoutbox for Mybb
 * @author Martec
 * @requires jQuery, Nodejs, Socket.io, Express, MongoDB, mongoose, debug and Mybb
 */
$l['yuishoutbox_plug_desc'] = 'Websocket Shoutbox for Mybb.';
$l['yuishoutbox_sett_desc'] = 'Settings for the Yui Shoutbox.';
$l['yuishoutbox_onoff_title'] = 'Enable Yui Shoutbox?';
$l['yuishoutbox_onoff_desc'] = 'Set here if you want enable or disable Yui Shoutbox.';
$l['yuishoutbox_heigh_title'] = 'Shoutbox height';
$l['yuishoutbox_heigh_desc'] = 'Set here height of Shoutbox (value in px).';
$l['yuishoutbox_shoutlimit_title'] = 'Amount of shouts';
$l['yuishoutbox_shoutlimit_desc'] = 'Set here amount of shouts that will appear in shout area. (limit: 100)';
$l['yuishoutbox_logshoutlimit_title'] = 'Amount of shouts in log (archive)';
$l['yuishoutbox_logshoutlimit_desc'] = 'Set here amount of shouts that will appear in log (archive). (limit: 200)';
$l['yuishoutbox_nogrp_title'] = 'Group without permission to use';
$l['yuishoutbox_nogrp_desc'] = 'Set here group that does not has permission to use Yui Shoutbox.';
$l['yuishoutbox_mod_title'] = 'Mod Group';
$l['yuishoutbox_mod_desc'] = 'Set here group with moderation privilege.';
$l['yuishoutbox_guest_title'] = 'Read mode to guest';
$l['yuishoutbox_guest_desc'] = 'Guest not has access to this shout. But you can enable read only mode to guest here.';
$l['yuishoutbox_shout_title'] = 'Title of Yui Shoutbox';
$l['yuishoutbox_shout_desc'] = 'Set here title of shoutbox that will appear.';
$l['yuishoutbox_server_title'] = 'Link to Yui Shoutbox server';
$l['yuishoutbox_server_desc'] = 'Set here your Yui Shoutbox server address.';
$l['yuishoutbox_socketio_title'] = 'Socket.io address';
$l['yuishoutbox_socketio_desc'] = 'Set here adress that yui shoutbox will connect.<br />For openshift users recommended "wss://xxxxxx.rhcloud.com:8443" (replacing xxxxxx with your account).';
$l['yuishoutbox_serusr_title'] = 'Yui Shoutbox Server Username';
$l['yuishoutbox_serusr_desc'] = 'Provide Username of your Yui Shoutbox Server.';
$l['yuishoutbox_serpass_title'] = 'Yui Shoutbox Server Passsword';
$l['yuishoutbox_serpass_desc'] = 'Provide Password of your Yui Shoutbox Server.';
$l['yuishoutbox_imgur_title'] = 'Imgur';
$l['yuishoutbox_imgur_desc'] = 'Set here API of imgur.';
$l['yuishoutbox_dataf_title'] = 'Date Format';
$l['yuishoutbox_dataf_desc'] = 'Set here date format (Options of format you can check in http://momentjs.com/docs/).';
$l['yuishoutbox_antiflood_title'] = 'Anti flood system';
$l['yuishoutbox_antiflood_desc'] = 'Set here time in secound that user need wait before to shout another message. Set 0 to disable this feature.';
$l['yuishoutbox_newpost_title'] = 'Shout new post';
$l['yuishoutbox_newpost_desc'] = 'Shout when someone post in thread.';
$l['yuishoutbox_newthread_title'] = 'Shout new thread';
$l['yuishoutbox_newthread_desc'] = 'Shout when someone post new thread.';
$l['yuishoutbox_foldacc_title'] = 'Folder ignored by Shout new post and Shout new thread';
$l['yuishoutbox_foldacc_desc'] = 'Set here folder that Yui Shoutbox will ignore when someone post in forum (value in id).<br />Separate each forum id with comma.';
$l['yuishoutbox_newptcolor_title'] = 'Color for new thread and new post shout';
$l['yuishoutbox_newptcolor_desc'] = 'Set here color for new thread and new post shout.';
$l['yuishoutbox_mention_title'] = 'Mention Autocomplete';
$l['yuishoutbox_mention_desc'] = 'Set to no if you do not want enable mention autocomplete feature.<br /><strong>Ps:</strong> This may perhaps increase the use of resources.';
$l['yuishoutbox_mentstyle_title'] = 'Mention border style';
$l['yuishoutbox_mentstyle_desc'] = 'Set border style to mention.';
$l['yuishoutbox_zone_title'] = 'Timezone';
$l['yuishoutbox_zone_desc'] = 'Set your Timezone here.';
$l['yuishoutbox_shoutstart_title'] = 'Display direction of shouts';
$l['yuishoutbox_shoutstart_desc'] = 'Choice display direction.';
$l['yuishoutbox_shoutstart_opt'] = 'top=Top
bottom=Bottom';
$l['yuishoutbox_actaimg_title'] = 'Active auto image load?';
$l['yuishoutbox_actaimg_desc'] = 'Set if you want active auto image load. If you select yes, Yui Shoutbox will load image link automatically.';
$l['yuishoutbox_limcharact_title'] = 'Character limit';
$l['yuishoutbox_limcharact_desc'] = 'Set character limit here. Set 0 to disable this feature.';
$l['yuishoutbox_aavatar_title'] = 'Active avatar in Yui Shoutbox?';
$l['yuishoutbox_aavatar_desc'] = 'Set if you want active avatar in Yui Shoutbox. If you select yes, Yui Shoutbox will show avatar.';
$l['yuishoutbox_acolor_title'] = 'Active color in Yui Shoutbox?';
$l['yuishoutbox_acolor_desc'] = 'Set if you want active color in Yui Shoutbox. If you select yes, Yui Shoutbox will give color option to users.';
$l['yuishoutbox_acbold_title'] = 'Active bold style in Miuna Shoutbox?';
$l['yuishoutbox_acbold_desc'] = 'Set if you want active bold style in Miuna Shoutbox. If you select yes, Miuna Shoutbox will give bold style option to users.';
$l['yuishoutbox_stfont_title'] = 'Fonts';
$l['yuishoutbox_stfont_desc'] = 'Set here font-family that users can use.';
$l['yuishoutbox_sizfont_title'] = 'Font sizes';
$l['yuishoutbox_sizfont_desc'] = 'Set here font-size that users can use.';
$l['yuishoutbox_deststyl_title'] = 'Desactive style select?';
$l['yuishoutbox_deststyl_desc'] = 'Set if you want desactive style.';
$l['yuishoutbox_destindx_title'] = 'Hide Yui Shoutbox in Index page?';
$l['yuishoutbox_destindx_desc'] = 'Set here if you want hide shoutbox in index or not.';
$l['yuishoutbox_actport_title'] = 'Show Yui Shoutbox in Portal page?';
$l['yuishoutbox_actport_desc'] = 'Set here if you want show shoutbox in portal or not.';
$l['yuishoutbox_newpost_lang'] = 'posted in thread {1}';
$l['yuishoutbox_newthread_lang'] = 'posted new thread {1}';
$l['yuishoutbox_banusr_title'] = 'Banned users IDs';
$l['yuishoutbox_banusr_desc'] = 'Set here users that banned from shoutbox. Separate ID with comma.';
$l['yuishoutbox_bword_title'] = 'Word Filters';
$l['yuishoutbox_bword_desc'] = 'Set here you want active word filters. If you select yes, Yui Shoutbox will filter bad words.';
$l['yuishoutbox_usefsockopen_title'] = 'Use fsockopen instead of curl?';
$l['yuishoutbox_usefsockopen_desc'] = 'Set here you want active fsockopen instead of curl. If you select yes, Yui Shoutbox will use fsockopen.';
$l['yuishoutbox_without_permission'] = 'You are not allowed to access this action.';
?>