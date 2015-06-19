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
 */
function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function revescapeHtml(text) {
  var map = {
	'&amp;': '&',
	'&lt;': '<',
	'&gt;': '>',
	'&quot;': '"',
	'&#039;': "'"
  };

  return text.replace(/(&amp;|&lt;|&gt;|&quot;|&#039;)/g, function(m) { return map[m]; });
}

function regexyui(message) {
	format_search =	 [
		/\[url=(.*?)\](.*?)\[\/url\]/ig,
		/\[spoiler\](.*?)\[\/spoiler\]/ig,
		/(^|[^"=\]])(https?:\/\/[a-zA-Z0-9\.\-\_\-\/]+(?:\?[a-zA-Z0-9=\+\_\;\-\&]+)?(?:#[\w]+)?)/gim,
		/(^|[^"=\]\>\/])(www\.[\S]+(\b|$))/gim,
		/(^|[^"=\]])(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim
	],
	// The matching array of strings to replace matches with
	format_replace = [
		'<a href="$1" target="_blank">$2</a>',
		"<tag><div style=\"margin: 5px\"><div style=\"font-size:11px; border-radius: 3px 3px 0 0 ; padding: 4px; background: #f5f5f5;border:1px solid #ccc;font-weight:bold;color:#000;text-shadow:none; \">"+spo_lan+":&nbsp;&nbsp;<input type=\"button\" onclick=\"if (this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display != '') { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = '';this.innerText = ''; this.value = '"+hide_lan+"'; } else { this.parentNode.parentNode.getElementsByTagName('div')[1].getElementsByTagName('div')[0].style.display = 'none'; this.innerText = ''; this.value = '"+show_lan+"'; }\" style=\"font-size: 9px;\" value=\""+show_lan+"\"></div><div><div style=\"border:1px solid #ccc; border-radius: 0 0 3px 3px; border-top: none; padding: 4px;display: none;\">$1</div></div></div></tag>",
		'$1<a href="$2" target="_blank">$2</a>',
		'$1<a href="http://$2" target="_blank">$2</a>',
		'<a href="mailto:$1">$1</a>'
	];
	// Perform the actual conversion
	for (var i =0;i<format_search.length;i++) {
		message = message.replace(format_search[i], format_replace[i]);
	}

	for (var val in yui_smilies) {
		message = message.replace(new RegExp(''+val+'(?!\\S)', "gi"), yui_smilies[val]);
	}
	return message;
}

function scrollyui(key,area,ckold,imarea) {
	if ((($(""+area+"").scrollTop() + $(""+area+"").outerHeight()) > ($(""+area+"")[0].scrollHeight - 90)) || ckold=='old') {
		imgarea = key;
		if (ckold=='old') {
			imgarea = imarea;
		}
		$(""+area+"").animate({scrollTop: ($(""+area+"")[0].scrollHeight)}, 10);
	}
}

function autocleaner(area,count,numshouts,direction) {
	if($(""+area+"").children("div."+count+"").length>(parseInt(numshouts) - 1)) {
		dif = $(""+area+"").children("div."+count+"").length - (parseInt(numshouts) - 1);
		if(direction=='top'){
			$(""+area+"").children("div."+count+"").slice(-dif).remove();
		}
		else {
			$(""+area+"").children("div."+count+"").slice(0, dif).remove();
		}
	}
}

function shoutgenerator(reqtype,key,colorsht,avatar,hour,username,message,type,ckold,direction,numshouts,cur) {
	var preapp = area = scrollarea = count = usravatar = shoutcolor = '';
	if(direction=='top'){
		preapp = 'prepend';
		if (reqtype == 'logback') {
			preapp = 'append';
		}
	}
	else {
		preapp = 'append';
		if (reqtype == 'logback') {
			preapp = 'prepend';
		}
	}
	if (reqtype=="shout") {
		area = scrollarea = ".shoutarea";
		count = "msgstcount";
		autocleaner(area,count,numshouts,direction);
	}
	if (parseInt(actavat)) {
		if (avatar.trim()) {
			usravatar = "<span class='ysb_tvatar'><img src="+escapeHtml(avatar)+" /></span>";
		}
		else {
			usravatar = "<span class='ysb_tvatar'><img src='"+imagepath+"/default_avatar.png' /></span>";
		}
	}
	if (parseInt(actcolor)) {
		if (colorsht) {
			if (/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(colorsht)) {
				shoutcolor = 'style="color:'+colorsht+'"';
			}
		}
	}
	if(type == 'shout') {
		$(""+area+"")[preapp]("<div class='msgShout "+count+" "+escapeHtml(key)+"' data-ided="+escapeHtml(key)+">"+usravatar+"<span class='time_msgShout'><span>[</span>"+hour+"<span>]</span></span><span class='username_msgShout'>"+username+"</span>:<span class='content_msgShout' "+shoutcolor+">"+message+"</span></div>");
	}
	if(type == 'system') {
		$(""+area+"")[preapp]("<div class='msgShout "+count+" "+escapeHtml(key)+"' data-ided="+escapeHtml(key)+">"+usravatar+"*<span class='username_msgShout'>"+username+"</span><span class='content_msgShout' "+shoutcolor+">"+message+"</span>*</div>");
	}
	if(cur==0) {
		if(direction!='top') {
			scrollyui(key,scrollarea,ckold,count);
		}
	}
}

function yuishout_connect() {
	if(!$('#auto_lod').length) {
		$('<div/>', { id: 'auto_lod', class: 'top-right' }).appendTo('body');
	}
	setTimeout(function() {
		$('#auto_lod').jGrowl(spinner+loadlang, { sticky: true });
	},200);
	socket = io.connect(socketaddress+'/member', { 'forceNew': true });
	yuishout(socket);
}

function yuishout(socket) {

	socket.emit('getoldmsg', {ns:numshouts});

	function displayMsg(reqtype, message, username, colorsht, avatar,type, key, created, ckold, cur){
		var hour = moment(created).utcOffset(parseInt(zoneset)).format(zoneformt);
		message = regexyui(escapeHtml(revescapeHtml(message))),
		nums = numshouts;
		shoutgenerator(reqtype,key,colorsht,avatar,hour,username,message,type,ckold,direction,nums,cur);
	};

	function checkMsg(req, msg, nick, colorsht, avatar, type, _id, created, ckold, cur) {
		var mtype = 'shout';
		displayMsg(mtype, msg, nick, colorsht, avatar, type, _id, created, ckold, cur);
	};

	socket.once('load old msgs', function(docs){
		if ($("#auto_lod").length) { $("#auto_lod .jGrowl-notification:last-child").remove(); }
		for (var i = docs.length-1; i >= 0; i--) {
			checkMsg("msg", docs[i].msg, docs[i].nick, docs[i].colorsht, docs[i].avatar, docs[i].type, docs[i]._id, docs[i].created, 'old', i);
		}
	});

	socket.on('message', function(data){
		checkMsg("msg", data.msg, data.nick, data.colorsht, data.avatar, data.type, data._id, data.created, 'new', 0);
	});
}