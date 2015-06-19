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
 * @fileoverview Yui Shoutbox - Websocket/Ajax Shoutbox for Mybb
 * @author Martec
 * @requires jQuery, Nodejs, Socket.io, Express, MongoDB, mongoose, debug and Mybb
 * @credits some part of code based in https://github.com/scotch-io/easy-node-authentication/tree/local
 * @credits sound file by http://community.mybb.com/user-70405.html
 */

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

define('YSB_PLUGIN_VER', '0.1.4');

function yuishoutbox_info()
{
	global $lang;

	$lang->load('config_yuishoutbox');

	return array(
		"name"			=> "Yui Shoutbox",
		"description"	=> $lang->yuishoutbox_plug_desc,
		"author"		=> "martec",
		"authorsite"	=> "",
		"version"		=> YSB_PLUGIN_VER,
		"compatibility" => "18*"
	);
}

function yuishoutbox_install()
{
	global $db, $lang;

	$lang->load('config_yuishoutbox');

	$query	= $db->simple_select("settinggroups", "COUNT(*) as rows");
	$dorder = $db->fetch_field($query, 'rows') + 1;

	$groupid = $db->insert_query('settinggroups', array(
		'name'		=> 'yuishoutbox',
		'title'		=> 'Yui Shoutbox',
		'description'	=> $lang->yuishoutbox_sett_desc,
		'disporder'	=> $dorder,
		'isdefault'	=> '0'
	));

	$yuishout_setting[] = array(
		'name' => 'yuishout_online',
		'title' => $lang->yuishoutbox_onoff_title,
		'description' => $lang->yuishoutbox_onoff_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 1,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_height',
		'title' => $lang->yuishoutbox_heigh_title,
		'description' => $lang->yuishoutbox_heigh_desc,
		'optionscode' => 'numeric',
		'value' => '220',
		'disporder' => 2,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_num_shouts',
		'title' => $lang->yuishoutbox_shoutlimit_title,
		'description' => $lang->yuishoutbox_shoutlimit_desc,
		'optionscode' => 'numeric',
		'value' => '25',
		'disporder' => 3,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_lognum_shouts',
		'title' => $lang->yuishoutbox_logshoutlimit_title,
		'description' => $lang->yuishoutbox_logshoutlimit_desc,
		'optionscode' => 'numeric',
		'value' => '50',
		'disporder' => 4,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_grups_acc',
		'title' => $lang->yuishoutbox_nogrp_title,
		'description' => $lang->yuishoutbox_nogrp_desc,
		'optionscode' => 'groupselect',
		'value' => '7',
		'disporder' => 5,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_mod_grups',
		'title' => $lang->yuishoutbox_mod_title,
		'description' => $lang->yuishoutbox_mod_desc,
		'optionscode' => 'groupselect',
		'value' => '3,4,6',
		'disporder' => 6,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_guest',
		'title' => $lang->yuishoutbox_guest_title,
		'description' => $lang->yuishoutbox_guest_desc,
		'optionscode' => 'yesno',
		'value' => '0',
		'disporder' => 7,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_title',
		'title' => $lang->yuishoutbox_shout_title,
		'description' => $lang->yuishoutbox_shout_desc,
		'optionscode' => 'text',
		'value' => 'Yui Shoutbox',
		'disporder' => 8,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_server',
		'title' => $lang->yuishoutbox_server_title,
		'description' => $lang->yuishoutbox_server_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 9,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_socketio',
		'title' => $lang->yuishoutbox_socketio_title,
		'description' => $lang->yuishoutbox_socketio_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 10,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_server_username',
		'title' => $lang->yuishoutbox_serusr_title,
		'description' => $lang->yuishoutbox_serusr_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 11,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_server_password',
		'title' => $lang->yuishoutbox_serpass_title,
		'description' => $lang->yuishoutbox_serpass_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 12,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_imgurapi',
		'title' => $lang->yuishoutbox_imgur_title,
		'description' => $lang->yuishoutbox_imgur_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 13,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_dataf',
		'title' => $lang->yuishoutbox_dataf_title,
		'description' => $lang->yuishoutbox_dataf_desc,
		'optionscode' => 'text',
		'value' => 'DD/MM hh:mm A',
		'disporder' => 14,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_antiflood',
		'title' => $lang->yuishoutbox_antiflood_title,
		'description' => $lang->yuishoutbox_antiflood_desc,
		'optionscode' => 'numeric',
		'value' => '0',
		'disporder' => 15,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_newpost',
		'title' => $lang->yuishoutbox_newpost_title,
		'description' => $lang->yuishoutbox_newpost_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 16,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_newthread',
		'title' => $lang->yuishoutbox_newthread_title,
		'description' => $lang->yuishoutbox_newthread_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 17,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_folder_acc',
		'title' => $lang->yuishoutbox_foldacc_title,
		'description' => $lang->yuishoutbox_foldacc_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 18,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_newpt_color',
		'title' => $lang->yuishoutbox_newptcolor_title,
		'description' => $lang->yuishoutbox_newptcolor_desc,
		'optionscode' => 'text',
		'value' => '#727272',
		'disporder' => 19,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_ment_style',
		'title' => $lang->yuishoutbox_mentstyle_title,
		'description' => $lang->yuishoutbox_mentstyle_desc,
		'optionscode' => 'text',
		'value' => '5px solid #cd0e0a',
		'disporder' => 20,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_zone',
		'title' => $lang->yuishoutbox_zone_title,
		'description' => $lang->yuishoutbox_zone_desc,
		'optionscode' => 'text',
		'value' => '-3',
		'disporder' => 21,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_shouts_start',
		'title' => $lang->yuishoutbox_shoutstart_title,
		'description' => $lang->yuishoutbox_shoutstart_desc,
		'optionscode' => 'radio
'.$lang->yuishoutbox_shoutstart_opt.'',
		'value' => 'bottom',
		'disporder' => 22,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_lim_character',
		'title' => $lang->yuishoutbox_limcharact_title,
		'description' => $lang->yuishoutbox_limcharact_desc,
		'optionscode' => 'numeric',
		'value' => 0,
		'disporder' => 23,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_act_avatar',
		'title' => $lang->yuishoutbox_aavatar_title,
		'description' => $lang->yuishoutbox_aavatar_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 24,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_act_color',
		'title' => $lang->yuishoutbox_acolor_title,
		'description' => $lang->yuishoutbox_acolor_desc,
		'optionscode' => 'yesno',
		'value' => 1,
		'disporder' => 25,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_des_index',
		'title' => $lang->yuishoutbox_destindx_title,
		'description' => $lang->yuishoutbox_destindx_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 26,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_act_port',
		'title' => $lang->yuishoutbox_actport_title,
		'description' => $lang->yuishoutbox_actport_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 27,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_ban_usr',
		'title' => $lang->yuishoutbox_banusr_title,
		'description' => $lang->yuishoutbox_banusr_desc,
		'optionscode' => 'text',
		'value' => '',
		'disporder' => 28,
		'gid'		=> $groupid
	);
	$yuishout_setting[] = array(
		'name' => 'yuishout_bad_word',
		'title' => $lang->yuishoutbox_bword_title,
		'description' => $lang->yuishoutbox_bword_desc,
		'optionscode' => 'yesno',
		'value' => 0,
		'disporder' => 29,
		'gid'		=> $groupid
	);	

	$db->insert_query_multiple("settings", $yuishout_setting);
	rebuild_settings();

}

function yuishoutbox_uninstall()
{

	global $db;

	//Delete Settings
	$db->write_query("DELETE FROM ".TABLE_PREFIX."settings WHERE name IN(
		'yuishout_online',
		'yuishout_height',
		'yuishout_num_shouts',
		'yuishout_grups_acc',
		'yuishout_mod_grups',
		'yuishout_guest',
		'yuishout_title',
		'yuishout_server',
		'yuishout_socketio',
		'yuishout_server_username',
		'yuishout_server_password',
		'yuishout_imgurapi',
		'yuishout_dataf',
		'yuishout_antiflood',
		'yuishout_newpost',
		'yuishout_newthread',
		'yuishout_folder_acc',
		'yuishout_newpt_style',
		'yuishout_newpt_color',
		'yuishout_ment_style',
		'yuishout_zone',
		'yuishout_shouts_start',
		'yuishout_lim_character',
		'yuishout_act_avatar',
		'yuishout_act_color',
		'yuishout_des_index',
		'yuishout_act_port',
		'yuishout_ban_usr',
		'yuishout_bad_word'
	)");

	$db->delete_query("settinggroups", "name = 'yuishoutbox'");
	rebuild_settings();

}

function yuishoutbox_is_installed()
{
	global $db;

	$query = $db->simple_select("settinggroups", "COUNT(*) as rows", "name = 'yuishoutbox'");
	$rows  = $db->fetch_field($query, 'rows');

	return ($rows > 0);
}

function yuishoutbox_activate()
{

	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';

	$new_template_global['codebutyui'] = "<link href=\"{\$mybb->asset_url}/jscripts/yui/shoutbox/style.css?ver=".YSB_PLUGIN_VER."\" rel='stylesheet' type='text/css'>
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.5/socket.io.min.js\"></script>
<link rel=\"stylesheet\" href=\"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css\">
<script type=\"text/javascript\" src=\"https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js\"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js'></script>
<link rel=\"stylesheet\" href=\"{\$mybb->asset_url}/jscripts/sceditor/editor_themes/{\$theme['editortheme']}\" type=\"text/css\" media=\"all\" />
<link rel=\"stylesheet\" href=\"{\$mybb->asset_url}/jscripts/sceditor/editor_themes/ysb.css\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/sceditor/jquery.sceditor.bbcode.min.js?ver=".YSB_PLUGIN_VER."\"></script>
<script type=\"text/javascript\">
<!--
	var ysbvar = {mybbuid:'{\$mybb->user['uid']}', mybbusername:'{\$ysbusrname}', mybbusergroup:'{\$mybb->user['usergroup']}', yuimodgroups:'{\$mybb->settings['yuishout_mod_grups']}', ysblc:'{\$mybb->settings['yuishout_lim_character']}', floodtime:'{\$mybb->settings['yuishout_antiflood']}', mpp: '{\$mybb->settings['yuishout_lognum_shouts']}'},
	shout_lang = '{\$lang->yuishoutbox_shout}',
	add_spolang = '{\$lang->yuishoutbox_add_spoiler}',
	spo_lan = '{\$lang->yuishoutbox_spoiler}',
	show_lan = '{\$lang->yuishoutbox_show}',
	hide_lan = '{\$lang->yuishoutbox_hide}',
	upimgurlang = '{\$lang->yuishoutbox_up_imgur}',
	loadlang = '{\$lang->yuishoutbox_load_msg}',
	mes_emptylan = '{\$lang->yuishoutbox_mes_empty}',
	usr_banlang = '{\$lang->yuishoutbox_user_banned}',
	flood_msglan = '{\$lang->yuishoutbox_flood_msg}',
	secounds_msglan = '{\$lang->yuishoutbox_flood_scds}',
	log_msglan = '{\$lang->yuishoutbox_log_msg}',
	log_shoutlan = '{\$lang->yuishoutbox_log_shout}',
	log_nextlan = '{\$lang->yuishoutbox_log_next}',
	log_backlan = '{\$lang->yuishoutbox_log_back}',
	prune_shoutlan = '{\$lang->yuishoutbox_prune_shout}',
	ban_msglan = '{\$lang->yuishoutbox_ban_sys}',
	not_msglan = '{\$lang->yuishoutbox_notice_msg}',
	prune_msglan = '{\$lang->yuishoutbox_prune_msg}',
	del_msglan = '{\$lang->yuishoutbox_del_mesg}',
	banlist_modmsglan = '{\$lang->yuishoutbox_banlist_mod}',
	not_modmsglan = '{\$lang->yuishoutbox_notice_mod}',
	shout_prunedmsglan = '{\$lang->yuishoutbox_pruned}',
	conf_questlan = '{\$lang->yuishoutbox_conf_quest}',
	shout_yeslan = '{\$lang->yuishoutbox_yes}',
	shout_nolan = '{\$lang->yuishoutbox_no}',
	shout_savelan = '{\$lang->yuishoutbox_save}',
	shout_delan = '{\$lang->yuishoutbox_del_msg}',
	cancel_editlan = '{\$lang->yuishoutbox_cancel_edt}',
	sound_lan = '{\$lang->yuishoutbox_sound_msg}',
	volume_lan = '{\$lang->yuishoutbox_volume_msg}',
	min_lan = '{\$lang->yuishoutbox_vmin_msg}',
	max_lan = '{\$lang->yuishoutbox_vmax_msg}',
	perm_msglan = '{\$lang->yuishoutbox_user_permission}',
	err_credlan = '{\$lang->yuishoutbox_error_cred}',
	err_fldlan = '{\$lang->yuishoutbox_error_flood}',
	numshouts = '{\$mybb->settings['yuishout_num_shouts']}',
	direction = '{\$mybb->settings['yuishout_shouts_start']}',
	zoneset = '{\$mybb->settings['yuishout_zone']}',
	zoneformt = '{\$mybb->settings['yuishout_dataf']}',
	shout_height = '{\$mybb->settings['yuishout_height']}',
	theme_borderwidth = '{\$theme['borderwidth']}',
	theme_tablespace = '{\$theme['tablespace']}',
	imgurapi = '{\$mybb->settings['yuishout_imgurapi']}',
	orgtit = document.title,
	ment_borderstyle = '{\$mybb->settings['yuishout_ment_style']}',
	actavat = '{\$mybb->settings['yuishout_act_avatar']}',
	actcolor = '{\$mybb->settings['yuishout_act_color']}',
	ysbaddress = '{\$mybb->settings['yuishout_server']}',
	socketaddress = '{\$mybb->settings['yuishout_socketio']}';
	Object.defineProperty(ysbvar, 'mybbuid', { writable: false });
	Object.defineProperty(ysbvar, 'mybbusername', { writable: false });
	Object.defineProperty(ysbvar, 'mpp', { writable: false });
	Object.defineProperty(ysbvar, 'mybbusergroup', { writable: false });
	Object.defineProperty(ysbvar, 'yuimodgroups', { writable: false });
	Object.defineProperty(ysbvar, 'ysblc', { writable: false });
	Object.defineProperty(ysbvar, 'floodtime', { writable: false });
// -->
</script>
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/yui/shoutbox/yuishout.helper.js?ver=".YSB_PLUGIN_VER."\"></script>
<script type=\"text/javascript\">
yui_smilies = {
	{\$smilies_json}
},
opt_editor = {
	plugins: \"bbcode\",
	style: \"{\$mybb->asset_url}/jscripts/sceditor/textarea_styles/jquery.sceditor.{\$theme['editortheme']}\",
	rtl: {\$lang->settings['rtl']},
	locale: \"mybblang\",
	enablePasteFiltering: true,
	emoticonsEnabled: {\$emoticons_enabled},
	emoticons: {
		// Emoticons to be included in the dropdown
		dropdown: {
			{\$dropdownsmilies}
		},
		// Emoticons to be included in the more section
		more: {
			{\$moresmilies}
		},
		// Emoticons that are not shown in the dropdown but will still be converted. Can be used for things like aliases
		hidden: {
			{\$hiddensmilies}
		}
	},
	emoticonsCompat: true,
	toolbar: \"spoiler,emoticon,imgur\",
};
{\$editor_language}

\$(document).ready(function() {
	\$('#shout_text').height('70px');
	\$('#shout_text').sceditor(opt_editor);
	\$('#shout_text').next().css(\"z-index\", \"1\");
	\$('#shout_text').sceditor('instance').sourceMode(true);
	yuishout_connect();
});

</script>";

	$new_template_global['ysb_template'] = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" class=\"tborder tShout\">
	<thead>
		<tr>
			<td class=\"thead theadShout\" colspan=\"1\">
				<div class=\"expcolimage\"><img src=\"{\$theme['imgdir']}/collapse{\$collapsedimg['yshout']}.png\" id=\"yshout_img\" class=\"expander\" alt=\"[-]\" title=\"[-]\" /></div>
				<div><strong>{\$mybb->settings['yuishout_title']}</strong></div>
			</td>
		</tr>
	</thead>
	<tbody style=\"{\$collapsed['yshout_e']}\" id=\"yshout_e\">
		<tr>
			<td class=\"trow2\">
				<div class=\"contentShout\">
					<div class=\"shoutarea wrapShout\" style=\"height:{\$mybb->settings['yuishout_height']}px;\"></div>
					<form id=\"yuishoutbox-form\">
						<input type=\"text\" name=\"shout_text\" class=\"editorShout\" id=\"shout_text\" data-type=\"shout\" autocomplete=\"off\">{\$codebutyui}
					</form>
				</div>
			</td>
		</tr>
	</tbody>
</table>";

	$new_template_global['ysb_guest_template'] = "<table border=\"0\" cellspacing=\"0\" cellpadding=\"4\" class=\"tborder tShout\">
	<thead>
		<tr>
			<td class=\"thead theadShout\" colspan=\"1\">
				<div class=\"expcolimage\"><img src=\"{\$theme['imgdir']}/collapse{\$collapsedimg['yshout']}.png\" id=\"yshout_img\" class=\"expander\" alt=\"[-]\" title=\"[-]\" /></div>
				<div><strong>{\$mybb->settings['yuishout_title']}</strong></div>
			</td>
		</tr>
	</thead>
	<tbody style=\"{\$collapsed['yshout_e']}\" id=\"yshout_e\">
		<tr>
			<td class=\"trow2\">
				<div class=\"contentShout\">
					<div class=\"shoutarea wrapShout\" style=\"height:{\$mybb->settings['yuishout_height']}px;\"></div>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<link href=\"{\$mybb->asset_url}/jscripts/yui/shoutbox/style.css?ver=".YSB_PLUGIN_VER."\" rel='stylesheet' type='text/css'>
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.3.5/socket.io.min.js\"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js'></script>
<script type=\"text/javascript\">
<!--
	var ysbvar = {mybbuid:'{\$mybb->user['uid']}', mybbusername:'{\$lang->guest}', mybbavatar:'{\$mybb->user['avatar']}', mybbusergroup:'{\$mybb->user['usergroup']}', Yuimodgroups:'{\$mybb->settings['yuishout_mod_grups']}', ysblc:'{\$mybb->settings['yuishout_lim_character']}', floodtime:'{\$mybb->settings['yuishout_antiflood']}'},
	spo_lan = '{\$lang->yuishoutbox_spoiler}',
	show_lan = '{\$lang->yuishoutbox_show}',
	hide_lan = '{\$lang->yuishoutbox_hide}',
	loadlang = '{\$lang->yuishoutbox_load_msg}',
	prune_shoutlan = '{\$lang->yuishoutbox_prune_shout}',
	not_msglan = '{\$lang->yuishoutbox_notice_msg}',
	banlist_modmsglan = '{\$lang->yuishoutbox_banlist_mod}',
	not_modmsglan = '{\$lang->yuishoutbox_notice_mod}',
	numshouts = '{\$mybb->settings['yuishout_num_shouts']}',
	direction = '{\$mybb->settings['yuishout_shouts_start']}',
	zoneset = '{\$mybb->settings['yuishout_zone']}',
	zoneformt = '{\$mybb->settings['yuishout_dataf']}',
	shout_height = '{\$mybb->settings['yuishout_height']}',
	theme_borderwidth = '{\$theme['borderwidth']}',
	theme_tablespace = '{\$theme['tablespace']}',
	imgurapi = '{\$mybb->settings['yuishout_imgurapi']}',
	actavat = '{\$mybb->settings['yuishout_act_avatar']}',
	actcolor = '{\$mybb->settings['yuishout_act_color']}',
	socketaddress = '{\$mybb->settings['yuishout_socketio']}';
// -->
</script>
<script type=\"text/javascript\" src=\"{\$mybb->asset_url}/jscripts/yui/shoutbox/yuishout.helper.guest.js?ver=".YSB_PLUGIN_VER."\"></script>
<script type=\"text/javascript\">
yui_smilies = {
	{\$smilies_json}
};
\$(document).ready(function() {
	yuishout_connect();
});
</script>";

	foreach($new_template_global as $title => $template)
	{
		$new_template_global = array('title' => $db->escape_string($title), 'template' => $db->escape_string($template), 'sid' => '-1', 'version' => '1801', 'dateline' => TIME_NOW);
		$db->insert_query('templates', $new_template_global);
	}

	find_replace_templatesets("index", '#{\$forums}#', "{\$yuishout}\n{\$forums}");
	find_replace_templatesets("portal", '#{\$announcements}#', "{\$yuishout}\n{\$announcements}");
}

function yuishoutbox_deactivate()
{

	global $db;
	require MYBB_ROOT.'/inc/adminfunctions_templates.php';

	$db->delete_query("templates", "title IN('codebutyui','ysb_template','ysb_guest_template')");

	//Exclui templates para as posições da shoutbox
	find_replace_templatesets("index", '#'.preg_quote('{$yuishout}').'#', '',0);
	find_replace_templatesets("portal", '#'.preg_quote('{$yuishout}').'#', '',0);
}

global $settings;
if ($settings['yuishout_online']) {
	$plugins->add_hook('global_start', 'Yui_cache_template');
}
function Yui_cache_template()
{
	global $templatelist, $mybb;

	if (isset($templatelist)) {
		$templatelist .= ',';
	}

	if (THIS_SCRIPT == 'index.php' && !$mybb->settings['yuishout_des_index']) {
		$templatelist .= 'codebutyui,ysb_template,ysb_guest_template';
	}
	if (THIS_SCRIPT == 'portal.php' && $mybb->settings['yuishout_act_port']) {
		$templatelist .= 'codebutyui,ysb_template,ysb_guest_template';
	}
}

function yui_bbcode_func($smilies = true)
{
	global $db, $mybb, $theme, $templates, $lang, $smiliecache, $cache;

	if (!$lang->yuishoutbox) {
		$lang->load('yuishoutbox');
	}

	$editor_lang_strings = array(
		"editor_bold" => "Bold",
		"editor_italic" => "Italic",
		"editor_underline" => "Underline",
		"editor_strikethrough" => "Strikethrough",
		"editor_subscript" => "Subscript",
		"editor_superscript" => "Superscript",
		"editor_alignleft" => "Align left",
		"editor_center" => "Center",
		"editor_alignright" => "Align right",
		"editor_justify" => "Justify",
		"editor_fontname" => "Font Name",
		"editor_fontsize" => "Font Size",
		"editor_fontcolor" => "Font Color",
		"editor_removeformatting" => "Remove Formatting",
		"editor_cut" => "Cut",
		"editor_cutnosupport" => "Your browser does not allow the cut command. Please use the keyboard shortcut Ctrl/Cmd-X",
		"editor_copy" => "Copy",
		"editor_copynosupport" => "Your browser does not allow the copy command. Please use the keyboard shortcut Ctrl/Cmd-C",
		"editor_paste" => "Paste",
		"editor_pastenosupport" => "Your browser does not allow the paste command. Please use the keyboard shortcut Ctrl/Cmd-V",
		"editor_pasteentertext" => "Paste your text inside the following box:",
		"editor_pastetext" => "PasteText",
		"editor_numlist" => "Numbered list",
		"editor_bullist" => "Bullet list",
		"editor_undo" => "Undo",
		"editor_redo" => "Redo",
		"editor_rows" => "Rows:",
		"editor_cols" => "Cols:",
		"editor_inserttable" => "Insert a table",
		"editor_inserthr" => "Insert a horizontal rule",
		"editor_code" => "Code",
		"editor_width" => "Width (optional):",
		"editor_height" => "Height (optional):",
		"editor_insertimg" => "Insert an image",
		"editor_email" => "E-mail:",
		"editor_insertemail" => "Insert an email",
		"editor_url" => "URL:",
		"editor_insertlink" => "Insert a link",
		"editor_unlink" => "Unlink",
		"editor_more" => "More",
		"editor_insertemoticon" => "Insert an emoticon",
		"editor_videourl" => "Video URL:",
		"editor_videotype" => "Video Type:",
		"editor_insert" => "Insert",
		"editor_insertyoutubevideo" => "Insert a YouTube video",
		"editor_currentdate" => "Insert current date",
		"editor_currenttime" => "Insert current time",
		"editor_print" => "Print",
		"editor_viewsource" => "View source",
		"editor_description" => "Description (optional):",
		"editor_enterimgurl" => "Enter the image URL:",
		"editor_enteremail" => "Enter the e-mail address:",
		"editor_enterdisplayedtext" => "Enter the displayed text:",
		"editor_enterurl" => "Enter URL:",
		"editor_enteryoutubeurl" => "Enter the YouTube video URL or ID:",
		"editor_insertquote" => "Insert a Quote",
		"editor_invalidyoutube" => "Invalid YouTube video",
		"editor_dailymotion" => "Dailymotion",
		"editor_metacafe" => "MetaCafe",
		"editor_veoh" => "Veoh",
		"editor_vimeo" => "Vimeo",
		"editor_youtube" => "Youtube",
		"editor_facebook" => "Facebook",
		"editor_liveleak" => "LiveLeak",
		"editor_insertvideo" => "Insert a video",
		"editor_php" => "PHP",
		"editor_maximize" => "Maximize"
	);
	$editor_language = "(function ($) {\n$.sceditor.locale[\"mybblang\"] = {\n";

	$editor_languages_count = count($editor_lang_strings);
	$i = 0;
	foreach($editor_lang_strings as $lang_string => $key)
	{
		$i++;
		$js_lang_string = str_replace("\"", "\\\"", $key);
		$string = str_replace("\"", "\\\"", $lang->$lang_string);
		$editor_language .= "\t\"{$js_lang_string}\": \"{$string}\"";

		if($i < $editor_languages_count)
		{
			$editor_language .= ",";
		}

		$editor_language .= "\n";
	}

	$editor_language .= "}})(jQuery);";

	if(defined("IN_ADMINCP"))
	{
		global $page;
		$yuibbcode = $page->build_codebuttons_editor($editor_language, $smilies);
	}
	else
	{
		// Smilies
		$emoticon = "";
		$emoticons_enabled = "false";
		if($smilies && $mybb->settings['smilieinserter'] != 0 && $mybb->settings['smilieinsertercols'] && $mybb->settings['smilieinsertertot'])
		{
			$emoticon = ",emoticon";
			$emoticons_enabled = "true";

			if(!$smiliecache)
			{
				if(!is_array($smilie_cache))
				{
					$smilie_cache = $cache->read("smilies");
				}
				foreach($smilie_cache as $smilie)
				{
					if($smilie['showclickable'] != 0)
					{
						$smilie['image'] = str_replace("{theme}", $theme['imgdir'], $smilie['image']);
						$smiliecache[$smilie['sid']] = $smilie;
					}
				}
			}

			unset($smilie);

			if(is_array($smiliecache))
			{
				reset($smiliecache);

				$smilies_json = $dropdownsmilies = $moresmilies = $hiddensmilies = "";
				$i = 0;

				foreach($smiliecache as $smilie)
				{
					$finds = explode("\n", $smilie['find']);
					$finds_count = count($finds);

					// Only show the first text to replace in the box
					$smilie['find'] = $finds[0];

					$find = htmlspecialchars_uni($smilie['find']);
					$image = htmlspecialchars_uni($smilie['image']);
					$findfirstquote = preg_quote($find);
					$findsecoundquote = preg_quote($findfirstquote);
					$smilies_json .= '"'.$findsecoundquote.'": "<img src=\"'.$mybb->asset_url.'/'.$image.'\" />",';
					if($i < $mybb->settings['smilieinsertertot'])
					{
						$dropdownsmilies .= '"'.$find.'": "'.$mybb->asset_url.'/'.$image.'",';
					}
					else
					{
						$moresmilies .= '"'.$find.'": "'.$mybb->asset_url.'/'.$image.'",';
					}

					for($j = 1; $j < $finds_count; ++$j)
					{
						$find2 = htmlspecialchars_uni($finds[$j]);
						$hiddensmilies .= '"'.$find.'": "'.$mybb->asset_url.'/'.$image.'",';
					}
					++$i;
				}
			}
		}
		$ysbusrname = addslashes($mybb->user['username']);
		eval("\$yuibbcode = \"".$templates->get("codebutyui")."\";");
	}

	return $yuibbcode;
}

if ($settings['yuishout_online'] && !$settings['yuishout_des_index']) {
	$plugins->add_hook('index_start', 'yuishout');
}
if ($settings['yuishout_online'] && $settings['yuishout_act_port']) {
	$plugins->add_hook('portal_start', 'yuishout');
}
function yuishout() {

	global $settings, $mybb, $theme, $templates, $yuishout, $codebutyui, $lang, $collapsed;

	$codebutyui = yui_bbcode_func();

	if (!$lang->yuishoutbox) {
		$lang->load('yuishoutbox');
	}

	if(!in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['yuishout_grups_acc'])) && $mybb->user['uid']!=0) {
		eval("\$yuishout = \"".$templates->get("ysb_template")."\";");
	}
	elseif ($mybb->user['uid']==0 && $settings['yuishout_guest']==1) {
		eval("\$yuishout = \"".$templates->get("ysb_guest_template")."\";");
	}
}

function sendPostDataYSB($type, $data) {

	global $mybb, $settings;

	$baseurl = $settings['yuishout_server'];
	$emiturl = $baseurl."/".$type."";
	$ch = curl_init($emiturl);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Origin: http://'.$_SERVER['HTTP_HOST'].'', 'Content-Type: application/json'));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_USERPWD, "".$settings['yuishout_server_username'].":".$settings['yuishout_server_password']."");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

if ($settings['yuishout_online'] && $settings['yuishout_newthread']) {
	$plugins->add_hook('newthread_do_newthread_end', 'ysb_newthread');
}
function ysb_newthread()
{
	global $mybb, $tid, $settings, $lang, $forum;

	if(!in_array((int)$forum['fid'],explode(',',$mybb->settings['yuishout_folder_acc']))) {
		$lang->load('admin/config_yuishoutbox');

		$name = format_name($mybb->user['username'], $mybb->user['usergroup'], $mybb->user['displaygroup']);
		$link = '[url=' . $settings['bburl'] . '/' . get_thread_link($tid) . ']' . $mybb->input['subject'] . '[/url]';
		$linklang = $lang->sprintf($lang->yuishoutbox_newthread_lang, $link);

		$baseurl = $settings['yuishout_server'];

		$data = array(
			"nick" => $name,
			"msg" => $linklang,
			"uid" => $mybb->user['uid'],
			"colorsht" => $mybb->settings['yuishout_newpt_color'],
			"avatar" => $mybb->user['avatar'],
			"type" => "system", 
			"fltime" => 0
		);

		sendPostDataYSB('message', $data);
	}
}

if ($settings['yuishout_online'] && $settings['yuishout_newpost']) {
	$plugins->add_hook('newreply_do_newreply_end', 'ysb_newpost');
}
function ysb_newpost()
{
	global $mybb, $tid, $settings, $lang, $url, $thread, $forum, $db;

	if(!in_array((int)$forum['fid'],explode(',',$mybb->settings['yuishout_folder_acc']))) {
		$lang->load('admin/config_yuishoutbox');

		$name = format_name($mybb->user['username'], $mybb->user['usergroup'], $mybb->user['displaygroup']);
		$ysb_url = htmlspecialchars_decode($url);
		$link = '[url=' . $settings['bburl'] . '/' . $ysb_url . ']' . $thread['subject'] . '[/url]';
		$linklang = $lang->sprintf($lang->yuishoutbox_newpost_lang, $link);

		$data = array(
			"nick" => $name,
			"msg" => $linklang,
			"uid" => $mybb->user['uid'],
			"colorsht" => $mybb->settings['yuishout_newpt_color'],
			"avatar" => $mybb->user['avatar'],
			"type" => "system", 
			"fltime" => 0
		);

		sendPostDataYSB('message', $data);
	}
}

$plugins->add_hook('xmlhttp', 'ysb_listen');
function ysb_listen()
{
	global $mybb, $lang, $parser, $settings;
	
	if (!is_object($parser))
	{
		require_once MYBB_ROOT.'inc/class_parser.php';
		$parser = new postParser;
	}
	
	$lang->load('admin/config_yuishoutbox');
	
	switch ($mybb->input['action']) {

		case 'message':

			if ($mybb->input['action'] != "message" || $mybb->request_method != "post"){return false;exit;}

			if (!verify_post_check($mybb->input['my_post_key'], true)) {
				xmlhttp_error($lang->invalid_post_code);
			}

			if (!in_array((int)$mybb->user['uid'],explode(',',$mybb->settings['yuishout_ban_usr'])) || in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['yuishout_mod_grups']))){
					$name = format_name($mybb->user['username'], $mybb->user['usergroup'], $mybb->user['displaygroup']);
					
					if (!$settings['yuishout_lim_character']) {
						$msg = htmlspecialchars_uni($_POST['msg']);
					}
					else {
						$msg = substr(htmlspecialchars_uni($_POST['msg']), 0, $settings['yuishout_lim_character']);		
					}
					
					if ((int)$settings['yuishout_bad_word']) {
						$options = [
							'allow_mycode'    => 0,
							'allow_smilies'   => 0,
							'allow_imgcode'   => 0,
							'filter_badwords' => 1
						];
						$msg = $parser->parse_message($msg, $options);
					}

					$data = array(
						"nick" => $name,
						"uid" => $mybb->user['uid'],
						"colorsht" => htmlspecialchars_decode($_POST['colorsht']),
						"avatar" => htmlspecialchars_decode($mybb->user['avatar']),
						"msg" => $msg,
						"type" => htmlspecialchars_decode($_POST['type']), 
						"fltime" => $settings['yuishout_antiflood']
					);

					echo sendPostDataYSB('message', $data);
					exit;
			}
			xmlhttp_error($lang->yuishoutbox_without_permission);			
			
		break;

		case 'updmsg':

			if ($mybb->input['action'] != "updmsg" || $mybb->request_method != "post"){return false;exit;}

			if (!verify_post_check($mybb->input['my_post_key'], true)) {
				xmlhttp_error($lang->invalid_post_code);
			}

			if (!$settings['yuishout_lim_character']) {
				$msg = htmlspecialchars_uni($_POST['newmsg']);
			}
			else {
				$msg = substr(htmlspecialchars_uni($_POST['newmsg']), 0, $settings['yuishout_lim_character']);		
			}

			if ((int)$settings['yuishout_bad_word']) {
				$options = [
					'allow_mycode'    => 0,
					'allow_smilies'   => 0,
					'allow_imgcode'   => 0,
					'filter_badwords' => 1
				];
				$msg = $parser->parse_message($msg, $options);
			}

			$data2 = array(
				"id" => htmlspecialchars_decode($_POST['id']),
				"newmsg" => $msg
			);
			if(!in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['yuishout_mod_grups']))) {
				$data1 = array(
					"id" => htmlspecialchars_decode($_POST['id'])
				);
				
				$checkuid = json_decode(sendPostDataYSB('ckuid', $data1));
				
				if ((int)$checkuid->{'error'}==130) {
					xmlhttp_error($lang->yuishoutbox_without_permission);
				}

				if ((int)$checkuid->{'sucess'}==(int)$mybb->user['uid']) {
					echo sendPostDataYSB('updmsg', $data2);
					exit;
				}
			}
			else {
				echo sendPostDataYSB('updmsg', $data2);
				exit;
			}
			xmlhttp_error($lang->yuishoutbox_without_permission);

		break;

		case 'updbanl':

			if ($mybb->input['action'] != "updbanl" || $mybb->request_method != "post"){return false;exit;}

			if (!verify_post_check($mybb->input['my_post_key'], true))
			{
				xmlhttp_error($lang->invalid_post_code);
			}

			if (in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['yuishout_mod_grups']))){
				$data = array(
					"ban" => htmlspecialchars_uni($_POST['ban'])
				);	
				$db->update_query("settings", ['value' => $db->escape_string(htmlspecialchars_uni($_POST['ban']))], "name='yuishout_ban_usr'");
				rebuild_settings();
				echo sendPostDataYSB('updbanl', $data);
				exit;
			}
			xmlhttp_error($lang->yuishoutbox_without_permission);

		break;

		case 'purge':
		
			if ($mybb->input['action'] != "purge" || $mybb->request_method != "post"){return false;exit;}

			if (!verify_post_check($mybb->input['my_post_key'], true))
			{
				xmlhttp_error($lang->invalid_post_code);
			}

			if (in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['yuishout_mod_grups']))){
				echo sendPostDataYSB('purge', []);
				exit;
			}
			xmlhttp_error($lang->yuishoutbox_without_permission);
			
		break;
		
		case 'rmvmsg':

			if ($mybb->input['action'] != "rmvmsg" || $mybb->request_method != "post"){return false;exit;}

			if (!verify_post_check($mybb->input['my_post_key'], true))
			{
				xmlhttp_error($lang->invalid_post_code);
			}

			$data = array(
				"id" => htmlspecialchars_decode($_POST['id'])
			);
			if(!in_array((int)$mybb->user['usergroup'],explode(',',$mybb->settings['yuishout_mod_grups']))) {

				$checkuid = json_decode(sendPostDataYSB('ckuid', $data));

				if ((int)$checkuid->{'error'}==130) {
					xmlhttp_error($lang->yuishoutbox_without_permission);
				}

				if ((int)$checkuid->{'sucess'}==(int)$mybb->user['uid']) {
					echo sendPostDataYSB('rmvmsg', $data);
					exit;
				}
			}
			else {
				echo sendPostDataYSB('rmvmsg', $data);
				exit;
			}
			xmlhttp_error($lang->yuishoutbox_without_permission);

		break;
	}
}
?>