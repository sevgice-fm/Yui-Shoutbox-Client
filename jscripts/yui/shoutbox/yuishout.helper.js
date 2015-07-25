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

function regexment(text,nick) {
	text = text.replace(/(<([^>]+)>)/ig,"");
	var mentregex = text.match(/(?:^|\s)@&quot;([^<]+?)&quot;|(?:^|\s)@&#039;([^<]+?)&#039;|(?:^|\s)@[`´]([^<]+?)[`´]|(?:^|\s)@(?:([^"<>\.,;!?()\[\]{}&\'\s\\]{3,}))/gmi);
	if (mentregex) {
		var patt = new RegExp(nick, "gi");
		for (var i =0;i<mentregex.length;i++) {
			mentregex[i] = mentregex[i].replace(/(&quot;|&#039;|`|´)/g, '');
			if(nick.length == (String(mentregex[i]).trim().length - 1)) {
				res = patt.exec(mentregex[i]);
				if (nick.toUpperCase() == String(res).toUpperCase()) {
					return 1;
				}
				return 0;
			}
			return 0;
		}
		return 0;
	}
	return 0;
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

function scrollyuilog() {
	$(".logstyle").animate({scrollTop: ($(".logstyle")[0].scrollHeight)}, 10);
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
	setTimeout(function() {
		if ($('.shoutarea').children("[data-ment=yes]").length) {
			document.title = '('+$('.shoutarea').children("[data-ment=yes]").length+') '+orgtit+'';
		}
		else {
			document.title = orgtit;
		}
	},200);
}

function shoutgenerator(reqtype,key,colorsht,font,size,bold,avatar,hour,username,message,type,ckold,direction,numshouts,cur) {
	var preapp = area = scrollarea = count = usravatar = shoutstyle = '';
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
	else {
		area = ".loglist";
		scrollarea = ".logstyle";
		count = "msglog";
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
				shoutstyle += 'color:'+colorsht+';';
			}
		}
	}
	if (parseInt(actbold)) {
		if (parseInt(bold)===1) {
			shoutstyle += 'font-weight:bold;';
		}
	}
	if (!parseInt(destyl)) {
		if (font.trim()) {
			font_rls = ysbfontype.split(',');
			if (typeof font_rls[parseInt(font)] !== 'undefined') {
				shoutstyle += "font-family:"+font_rls[parseInt(font)].trim()+";";
			}
		}
		if (size.trim()) {
			size_rls = ysbfontsize.split(',');
			if (typeof size_rls[parseInt(size)] !== 'undefined') {
				shoutstyle += 'font-size:'+size_rls[parseInt(size)].trim()+'px;';
			}
		}
	}
	if(type == 'shout') {
		$(""+area+"")[preapp]("<div class='msgShout "+count+" "+escapeHtml(key)+"' data-ided="+escapeHtml(key)+">"+usravatar+"<span class='time_msgShout'><span>[</span>"+hour+"<span>]</span></span><span class='username_msgShout'>"+username+"</span>:<span class='content_msgShout' style='"+shoutstyle+"'>"+message+"</span></div>");
	}
	if(type == 'system') {
		$(""+area+"")[preapp]("<div class='msgShout "+count+" "+escapeHtml(key)+"' data-ided="+escapeHtml(key)+">"+usravatar+"*<span class='username_msgShout'>"+username+"</span><span class='content_msgShout' style='"+shoutstyle+"'>"+message+"</span>*</div>");
	}
	if(cur==0) {
		if (reqtype == 'lognext' || reqtype == 'logback') {
			if(direction!='top') {
				scrollyuilog();
			}
		}
		else {
			if(direction!='top') {
				scrollyui(key,scrollarea,ckold,count);
			}
		}
	}
}

function emitajax(type, data) {
	$.ajax({
		type: 'POST',
		data: data,
		url: 'xmlhttp.php?action='+type+'&my_post_key='+my_post_key
	}).done(function (result) {
		var IS_JSON = true;
		try {
			var json = $.parseJSON(result);
		}
		catch(err) {
			IS_JSON = false;
		}
		if (IS_JSON) {
			if (JSON.parse(result).error) {
				if (JSON.parse(result).error=='260') {
					if(!$('#incadm_cred').length) {
						$('<div/>', { id: 'incadm_cred', class: 'top-right' }).appendTo('body');
					}
					setTimeout(function() {
						$('#incadm_cred').jGrowl(err_credlan, { life: 1500 });
					},200);
				}
				if (JSON.parse(result).error=='220') {
					if(!$('#incadm_cred').length) {
						$('<div/>', { id: 'incadm_cred', class: 'top-right' }).appendTo('body');
					}
					setTimeout(function() {
						$('#incadm_cred').jGrowl(err_credlan, { life: 1500 });
					},200);
				}
				if (JSON.parse(result).error=='110') {
					if(!$('#er_flood').length) {
						$('<div/>', { id: 'er_flood', class: 'top-right' }).appendTo('body');
					}
					setTimeout(function() {
						$('#er_flood').jGrowl(err_fldlan, { life: 1500 });
					},200);
				}
			}
		}
		else {
			if(typeof result == 'object')
			{
				if(result.hasOwnProperty("errors"))
				{
					$.each(result.errors, function(i, message)
					{
						if(!$('#er_others').length) {
							$('<div/>', { id: 'er_others', class: 'top-right' }).appendTo('body');
						}
						setTimeout(function() {
							$('#er_others').jGrowl(message, { life: 1500 });
						},200);
					});
				}
			}
			else {
				return result;
			}
		}
	});
};

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
	var notban = '1',
	mentsound = 0;

	if (parseInt(numshouts)>100) {
		numshouts = '100';
	}	

	var shoutbut = '<button id="sbut" style="margin: 2px; float: right;">'+shout_lang+'</button>';
	$(shoutbut).appendTo('.yuieditor-toolbar');

	if (parseInt(actcolor)) {
		sb_sty = JSON.parse(localStorage.getItem('sb_col_ft'));
		if (sb_sty) {
			if (/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(sb_sty['color'])) {
				colorshout = sb_sty['color'];
			}
		}
	}

	if (!parseInt(destyl)) {
		sb_sty = JSON.parse(localStorage.getItem('sb_col_ft'));
		if (sb_sty) {
			fontype = sb_sty['font'];
			fontsize = sb_sty['size'];
		}
	}

	if (parseInt(actbold)) {
		sb_sty = JSON.parse(localStorage.getItem('sb_col_ft'));
		if (sb_sty) {
			fontbold = sb_sty['bold'];
		}
	}	

	sb_sty = JSON.parse(localStorage.getItem('sb_col_ft'));
	if (sb_sty) {
		shoutvol = sb_sty['sound'];
		mentsound = sb_sty['mentsound'];
	}

	socket.emit('getoldmsg', {ns:numshouts});

	socket.emit('getbanl', function (data) {});
	socket.once('getbanl', function (data) {
		if (data) {
			var listban = data.ban;
			if ($.inArray(parseInt(ysbvar.mybbuid), listban.split(',').map(function(listban){return Number(listban);}))!=-1 && $.inArray(parseInt(ysbvar.mybbusergroup), ysbvar.yuimodgroups.split(',').map(function(modgrup){return Number(modgrup);}))==-1) {
				notban = 0;
				socket.disconnect();
				if(!$('#usr_ban').length) {
					$('<div/>', { id: 'usr_ban', class: 'top-right' }).appendTo('body');
				}
				setTimeout(function() {
					$('#usr_ban').jGrowl(usr_banlang, { life: 1500 });
				},200);
			}
		}
	});
	
	socket.on('updbanl', function (data) {
		if (data) {
			var listban = data.ban;
			if ($.inArray(parseInt(ysbvar.mybbuid), listban.split(',').map(function(listban){return Number(listban);}))!=-1 && $.inArray(parseInt(ysbvar.mybbusergroup), ysbvar.yuimodgroups.split(',').map(function(modgrup){return Number(modgrup);}))==-1) {
				notban = 0;
				socket.disconnect();
				if(!$('#usr_ban').length) {
					$('<div/>', { id: 'usr_ban', class: 'top-right' }).appendTo('body');
				}
				setTimeout(function() {
					$('#usr_ban').jGrowl(usr_banlang, { life: 1500 });
				},200);
			}
			else {
				notban = 1;
			}
		}
		else {
			notban = 1;
		}
	});

	var last_check = Date.now()/1000;

	$('#shout_text').keypress(function(e) {
		if(e.which == 13) {
			e.preventDefault();
			onshout(e);
		}
	});

	($.fn.on || $.fn.live).call($(document), 'click', '#sbut', function (e) {
		e.preventDefault();
		onshout(e);
	});

	function onshout(e) {
		current = Date.now()/1000;
		time_passed = current - last_check;
		if (parseInt(time_passed) >= ysbvar.floodtime) {
			last_check = current;
			if (notban) {
				if ($('#shout_text').attr('data-type')=='shout') {

					var msg = $('#shout_text').val();

					if (parseInt(ysbvar.ysblc) > 0) {
						msg = msg.slice(0, parseInt(ysbvar.ysblc));
					}

					if(msg == '' || msg == null) {
						$('#shout_text').val('').focus();
						return false;
					}
					else {
						$('#shout_text').val('').focus();
						if ( /^\/me[\s]+(.*)$/.test(msg) ) {
							emitajax('message', {msg:msg.slice(4), colorsht: colorshout, font: fontype, size: fontsize, bold: fontbold, type: 'system'});
						}
						else {
							emitajax('message', {msg:msg, colorsht: colorshout, font: fontype, size: fontsize, bold: fontbold, type: 'shout'});
						}
						return false;
					}
				}
				else if ($('#shout_text').attr('data-type')=='edit') {
					var msg = $('#shout_text').val();

					if (parseInt(ysbvar.ysblc) > 0) {
						msg = msg.slice(0, parseInt(ysbvar.ysblc));
					}

					if(msg == '' || msg == null){
						if(!$('#upd_alert').length) {
							$('<div/>', { id: 'upd_alert', class: 'bottom-right' }).appendTo('body');
						}
						setTimeout(function() {
							$('#upd_alert').jGrowl(mes_emptylan, { life: 500 });
						},200);
						$('#shout_text').val('').focus();
						return false;
					}
					else {
						$('#shout_text').val('').focus();
						$('#shout_text').attr("data-type", "shout");
						$('#cancel_edit').remove();
						$('#del_shout').remove();
						var id = $('#shout_text').attr('data-id');
						emitajax('updmsg', {id:id, newmsg:msg});
						return false;
					}
				}
			}
			else {
				$('#shout_text').val('').focus();
				if(!$('#upd_alert').length) {
					$('<div/>', { id: 'upd_alert', class: 'top-right' }).appendTo('body');
				}
				setTimeout(function() {
					$('#upd_alert').jGrowl(usr_banlang, { life: 1500 });
				},200);
				e.preventDefault();
				return;
			}
		}
		else {
			if(!$('#upd_alert').length) {
				$('<div/>', { id: 'upd_alert', class: 'bottom-right' }).appendTo('body');
			}
			setTimeout(function() {
				time_after = ysbvar.floodtime - parseInt(time_passed);
				$('#upd_alert').jGrowl(flood_msglan+time_after+secounds_msglan, { life: 500 });
			},50);
			e.preventDefault();
			return;
		}
	}

	function displayMsg(reqtype, message, username, colorsht, font, size, bold, avatar,type, key, created, ckold, cur){
		var hour = moment(created).utcOffset(parseInt(zoneset)).format(zoneformt);
		message = regexyui(message),
		nums = numshouts;
		if (reqtype=='lognext' || reqtype=='logback') {
			if (parseInt(ysbvar.mpp)>200) {
				nums = '200';
			}
			else {
				nums = ysbvar.mpp;
			}
		}

		shoutgenerator(reqtype,key,colorsht,font,size,bold,avatar,hour,username,message,type,ckold,direction,nums,cur);
		if (regexment(message,ysbvar.mybbusername)) {
			if(parseFloat(shoutvol) && parseInt(mentsound) && ckold=="new") {
				var sound = new Audio(rootpath + '/jscripts/yui/shoutbox/ysb_sound.mp3');
				sound.volume = parseFloat(shoutvol);
				sound.play();
			}
			$("div."+key+"").css("border-left",ment_borderstyle).attr( "data-ment", "yes" );
			setTimeout(function() {
				if ($('.shoutarea').children("[data-ment=yes]").length) {
					document.title = '('+$('.shoutarea').children("[data-ment=yes]").length+') '+orgtit+'';
				}
			},200);
		}
	};

	function checkMsg(req, msg, nick, colorsht, font, size, bold, avatar, type, _id, created, ckold, cur) {
		var mtype = 'shout';

		if (req=='lognext' || req=='logback') {
			mtype = req;
		}
		displayMsg(mtype, msg, nick, colorsht, font, size, bold, avatar, type, _id, created, ckold, cur);
	};

	socket.once('load old msgs', function(docs){
		if ($("#auto_lod").length) { $("#auto_lod .jGrowl-notification:last-child").remove(); }
		for (var i = docs.length-1; i >= 0; i--) {
			checkMsg("msg", docs[i].msg, docs[i].nick, docs[i].colorsht, docs[i].font, docs[i].size, docs[i].bold, docs[i].avatar, docs[i].type, docs[i]._id, docs[i].created, 'old', i);
		}
	});

	socket.on('message', function(data){
		if(parseFloat(shoutvol) && !parseInt(mentsound)) {
			var sound = new Audio(rootpath + '/jscripts/yui/shoutbox/ysb_sound.mp3');
			sound.volume = parseFloat(shoutvol);
			sound.play();
		}
		checkMsg("msg", data.msg, data.nick, data.colorsht, data.font, data.size, data.bold, data.avatar, data.type, data._id, data.created, 'new', 0);
	});

	function updmsg(message, key){
		
		message = regexyui(message);
		setTimeout(function() {
			if ($('.shoutarea').children().hasClass(key)) {
				if(direction!='top') {
					scrollyui(key,'.shoutarea','new','msgstcount');
				}
			}
		},50);
		var menttest = regexment(message,ysbvar.mybbusername);
		if ($("div."+key+"").attr('data-ment') == "yes") {
			if(!menttest) {
				$("div."+key+"").css("border-left","").attr( "data-ment", "no" );
				setTimeout(function() {
					if ($('.shoutarea').children("[data-ment=yes]").length) {
						document.title = '('+$('.shoutarea').children("[data-ment=yes]").length+') '+orgtit+'';
					}
					else {
						document.title = orgtit;
					}
				},200);
			}
		}
		if (menttest) {
			if(parseInt(mentsound)) {
				var sound = new Audio(rootpath + '/jscripts/yui/shoutbox/ysb_sound.mp3');
				sound.volume = parseFloat(shoutvol);
				sound.play();
			}
			$("div."+key+"").css("border-left",ment_borderstyle).attr( "data-ment", "yes" );
			setTimeout(function() {
				document.title = '('+$('.shoutarea').children("[data-ment=yes]").length+') '+orgtit+'';
			},200);
		}
		$('div.'+key+'').children('.content_msgShout').html(message);
	}

	socket.on('updmsg', function (data) {
		if (data) {
			updmsg(data.msg, data._id);
		}
	});

	function logfunc() {
		numslogs = '';
		if (parseInt(ysbvar.mpp)>200) {
			numslogs = '200';
		}
		else {
			numslogs = ysbvar.mpp;
		}	
		socket.emit('logfpgmsg', {mpp:numslogs});
		socket.once('logfpgmsg', function(docs){
			for (var i = docs.length-1; i >= 0; i--) {
				checkMsg('lognext', docs[i].msg, docs[i].nick, docs[i].colorsht, docs[i].font, docs[i].size, docs[i].bold, docs[i].avatar, docs[i].type, docs[i]._id, docs[i].created, 'old', i);
			}
		});
	}

	($.fn.on || $.fn.live).call($(document), 'click', '#log', function (e) {
		var heightwin = window.innerHeight*0.8,
		widthwin = window.innerWidth*0.5,
		page = '',
		initpage = '',
		npostbase = '';

		if (window.innerWidth < 650 || (window.innerWidth < window.innerHeight)) {
			 widthwin = document.getElementById("edshout_e").offsetWidth;
		}
		if (window.innerWidth < window.innerHeight) {
			heightwin = widthwin*0.8;
		}

		function displayfpglogMsg(data){
			numslogs = '';
			if (parseInt(ysbvar.mpp)>200) {
				numslogs = '200';
			}
			else {
				numslogs = ysbvar.mpp;
			}		
			npostbase = data;
			pagebase = Math.ceil(npostbase/numslogs);
			npost = npostbase + pagebase;
			page = Math.ceil(npost/numslogs);
			if (page>1) {
				initpage = "1/"+page;
			}
			else {
				initpage = "1/1";
			}

			$('body').append( '<div id="logpop" style="width: '+widthwin+'px;max-width:900px !important"><div style="overflow-y: auto;max-height: '+heightwin+'px !important; "><table cellspacing="'+theme_borderwidth+'" cellpadding="'+theme_tablespace+'" class="tborder"><tr><td class="thead" colspan="2"><div><strong>'+log_shoutlan+'</strong></div></td></tr><tr><td class="trow1" colspan="2"><div class="logstyle" style="overflow-y: auto;width:99%;height: '+heightwin*0.7+'px;word-break:break-all"><div class="loglist"></div></div></td></tr><td class="trow1"><div id="page" style="text-align:center"><button id="page_back" style="margin:4px;">'+log_backlan+'</button> <span id="pagecount" data-pageact="1" data-pagemax="'+page+'">'+initpage+'</span> <button id="page_next" style="margin:4px;">'+log_nextlan+'</button></div></td></table></div></div>' );
			$('#logpop').modal({ zIndex: 7 });
			logfunc();
		}

		socket.emit('countmsg', function (data) {});
		socket.once('countmsg', function (data) {
			displayfpglogMsg(data);
		});
	});

	($.fn.on || $.fn.live).call($(document), 'click', '#page_next', function (e) {
		e.preventDefault();
		var actpage = $('#pagecount').attr('data-pageact'),
		maxpage = $('#pagecount').attr('data-pagemax');

		if (parseInt(actpage)==parseInt(maxpage)) {
			return;
		}
		else {
			var newactpage = parseInt(actpage) + 1,
			newpagelist = newactpage+"/"+maxpage,
			prevpagefirstid = '';
			if(direction=='top'){
				prevpagefirstid = $(".msglog:last").attr('data-ided');
			}
			else {
				prevpagefirstid = $(".msglog:first").attr('data-ided');
			}
			$('#pagecount').text(newpagelist);
			$('.loglist').remove();
			$(".logstyle").append('<div class="loglist"></div>');
			$('#pagecount').attr('data-pageact', newactpage);
			$('#pagecount').val(newpagelist);

			numslogs = '';
			if (parseInt(ysbvar.mpp)>200) {
				numslogs = '200';
			}
			else {
				numslogs = ysbvar.mpp;
			}
			socket.emit('logmsgnext', {id:prevpagefirstid, mpp:numslogs});
			socket.once('logmsgnext', function (docs) {
				for (var i = docs.length-1; i >= 0; i--) {
					checkMsg('lognext', docs[i].msg, docs[i].nick, docs[i].colorsht, docs[i].font, docs[i].size, docs[i].bold, docs[i].avatar, docs[i].type, docs[i]._id, docs[i].created, 'old', i);
				}
			});
		}
	});

	($.fn.on || $.fn.live).call($(document), 'click', '#page_back', function (e) {
		e.preventDefault();
		var actpage = $('#pagecount').attr('data-pageact'),
		maxpage = $('#pagecount').attr('data-pagemax');

		if (parseInt(actpage)==1) {
			return;
		}
		else {
			var newactpage = parseInt(actpage) - 1,
			newpagelist = newactpage+"/"+maxpage,
			prevpagelastid = '';
			if(direction=='top'){
				prevpagelastid = $(".msglog:first").attr('data-ided');
			}
			else {
				prevpagelastid = $(".msglog:last").attr('data-ided');
			}
			$('#pagecount').text(newpagelist);
			$('.loglist').remove();
			$(".logstyle").append('<div class="loglist"></div>');
			$('#pagecount').attr('data-pageact', newactpage);
			$('#pagecount').val(newpagelist);

			numslogs = '';
			if (parseInt(ysbvar.mpp)>200) {
				numslogs = '200';
			}
			else {
				numslogs = ysbvar.mpp;
			}
			socket.emit('logmsgback', {id:prevpagelastid, mpp:numslogs});
			socket.once('logmsgback', function (docs) {
				for (var i = docs.length-1; i >= 0; i--) {
					checkMsg('logback', docs[i].msg, docs[i].nick, docs[i].colorsht, docs[i].font, docs[i].size, docs[i].bold, docs[i].avatar, docs[i].type, docs[i]._id, docs[i].created, 'old', i);
				}
			});
		}
	});

	if ($.inArray(parseInt(ysbvar.mybbusergroup), ysbvar.yuimodgroups.split(',').map(function(modgrup){return Number(modgrup);}))!=-1) {
		function prunefunc() {
			heightwin = 120;
			$('body').append( '<div class="prune"><div style="overflow-y: auto;max-height: '+heightwin+'px !important; "><table cellspacing="'+theme_borderwidth+'" cellpadding="'+theme_tablespace+'" class="tborder"><tr><td class="thead" colspan="2"><div><strong>'+prune_shoutlan+':</strong></div></td></tr><td class="trow1">'+conf_questlan+'</td></table></div><td><button id="prune_yes" style="margin:4px;">'+shout_yeslan+'</button><button id="del_no" style="margin:4px;">'+shout_nolan+'</button></td></div>' );
			$('.prune').modal({ zIndex: 7 });
		}

		function banusr(listban) {
			heightwin = 120;
			$('body').append( '<div class="banlist"><div style="overflow-y: auto;max-height: '+heightwin+'px !important; "><table cellspacing="'+theme_borderwidth+'" cellpadding="'+theme_tablespace+'" class="tborder"><tr><td class="thead" colspan="2"><div><strong>'+ban_msglan+':</strong></div></td></tr><td class="trow1"><textarea id="ban_list" style="width:97%;height: '+heightwin*0.3+'px;" >'+listban+'</textarea></td></table></div><td><button id="sv_banlist" style="margin:4px;">'+shout_savelan+'</button></td></div>' );
			$('.banlist').modal({ zIndex: 7 });
		}

		banbut = '<a class="yuieditor-button" id="banusr" title="'+ban_msglan+'"><div style="background-image: url('+rootpath+'/images/buddy_delete.png); opacity: 1; cursor: pointer;">'+ban_msglan+'</div></a>';
		$(banbut).appendTo('.yuieditor-group_shout_text:last');		

		($.fn.on || $.fn.live).call($(document), 'click', '#banusr', function (e) {
			socket.emit('getbanl', function (data) {});
			socket.once('getbanl', function (data) {
				var listban = '';
				if (data) {
					var listban = data.ban;
				}
				banusr(listban);
			});

		});
		
		($.fn.on || $.fn.live).call($(document), 'click', '#sv_banlist', function (e) {
			e.preventDefault();
			emitajax('message', {msg:banlist_modmsglan, colorsht: colorshout, font: fontype, size: fontsize, bold: fontbold, type: 'system'});
			var newlist = escapeHtml($('#ban_list').val());
			emitajax('updbanl', {ban:newlist});
			$.modal.close();
		});

		prune = '<a class="yuieditor-button" id="prune" title="'+prune_msglan+'"><div style="background-image: url('+rootpath+'/images/invalid.png); opacity: 1; cursor: pointer;">'+prune_msglan+'</div></a>';
		$(prune).appendTo('.yuieditor-group_shout_text:last');		

		($.fn.on || $.fn.live).call($(document), 'click', '#prune', function (e) {
			prunefunc();
		});

		($.fn.on || $.fn.live).call($(document), 'click', '#prune_yes', function (e) {
			e.preventDefault();
			emitajax('purge', function () {});
			socket.once('purge', function () {
				$('.msgShout').remove();
				setTimeout(function() {
					emitajax('message', {msg:shout_prunedmsglan, colorsht: colorshout, font: fontype, size: fontsize, bold: fontbold, type: 'system'});
				},50);
			});
			$.modal.close();
		});
	}

	($.fn.on || $.fn.live).call($(document), 'click', '#del_shout', function (e) {
		e.preventDefault();
		var id = $(this).attr('data-delid'),
		heightwin = 120;
		$('body').append( '<div class="del"><div style="overflow-y: auto;max-height: '+heightwin+'px !important; "><table cellspacing="'+theme_borderwidth+'" cellpadding="'+theme_tablespace+'" class="tborder"><tr><td class="thead" colspan="2"><div><strong>'+del_msglan+':</strong></div></td></tr><td class="trow1">'+conf_questlan+'</td></table></div><td><button id="del_yes" style="margin:4px;" ided="'+id+'">'+shout_yeslan+'</button><button id="del_no" style="margin:4px;">'+shout_nolan+'</button></td></div>' );
		$('.del').modal({ zIndex: 7 });
	});

	($.fn.on || $.fn.live).call($(document), 'click', '#del_yes', function (e) {
		e.preventDefault();
		var id = $(this).attr('ided');
		emitajax('rmvmsg', {id:id});
		$('#shout_text').val('').focus();
		$('#shout_text').attr("data-type", "shout");
		$('#cancel_edit').remove();
		$('#del_shout').remove();
		$.modal.close();
	});

	socket.on('rmvmsg', function (data) {
		if (data) {
			$('div.wrapShout').children('div.'+data._id+'').remove();
			setTimeout(function() {
				if ($('.shoutarea').children("[data-ment=yes]").length) {
					document.title = '('+$('.shoutarea').children("[data-ment=yes]").length+') '+orgtit+'';
				}
				else {
					document.title = orgtit;
				}
			},200);
		}
	});

	($.fn.on || $.fn.live).call($(document), 'click', '#del_no', function (e) {
		e.preventDefault();
		$('#shout_text').val('').focus();
		$('#shout_text').attr("data-type", "shout");
		$('#cancel_edit').remove();
		$('#del_shout').remove();
		$.modal.close();
	});

	function soundfunc() {
		heightwin = 140;
		checked = '';
		if (mentsound) {
			checked = 'checked';
		}
		$('body').append( '<div class="sound"><div style="overflow-y: auto;max-height: '+heightwin+'px !important; "><table cellspacing="'+theme_borderwidth+'" cellpadding="'+theme_tablespace+'" class="tborder"><tr><td class="thead" colspan="2"><div><strong>'+sound_lan+'</strong></div></td></tr><tr><td class="tcat">'+volume_lan+':</td></tr><tr><td class="trow1" style="text-align:center;">'+min_lan+'<input id="s_volume" type="range" min="0" max="1" step="0.05" value="'+parseFloat(shoutvol)+'"/>'+max_lan+'</td></tr><tr><td class="trow1"><input type="checkbox" id="mentsound" '+checked+'>'+ment_sound+'</td></tr></table></div><td></div>' );
		var soundinput = document.getElementById("s_volume");
		soundinput.addEventListener("input", function() {
			var sb_sty = JSON.parse(localStorage.getItem('sb_col_ft'));
			if (!sb_sty) {
				sb_sty = {};
			}
			sb_sty['sound'] = soundinput.value;
			localStorage.setItem('sb_col_ft', JSON.stringify(sb_sty));
			shoutvol = parseFloat(soundinput.value);
		}, false);
		var mentsoundinput = document.getElementById("mentsound");
		mentsoundinput.addEventListener("change", function() {
			var sb_sty = JSON.parse(localStorage.getItem('sb_col_ft'));
			if (!sb_sty) {
				sb_sty = {};
			}
			if (mentsoundinput.checked) {
				sb_sty['mentsound'] = 1;
				mentsound = 1;
			} 
			else {
				sb_sty['mentsound'] = 0;
				mentsound = 0;
			}
			localStorage.setItem('sb_col_ft', JSON.stringify(sb_sty));
		}, false);
		$('.sound').modal({ zIndex: 7 });
	}

	sound = '<a class="yuieditor-button" id="sound" title="'+sound_lan+'"><div style="background-image: url('+rootpath+'/images/sound.png); opacity: 1; cursor: pointer;">'+sound_lan+'</div></a>';
	$(sound).appendTo('.yuieditor-group_shout_text:last');

	($.fn.on || $.fn.live).call($(document), 'click', '#sound', function (e) {
		soundfunc();
	});

	log = '<a class="yuieditor-button" id="log" title="'+log_msglan+'"><div style="background-image: url('+rootpath+'/images/log.png); opacity: 1; cursor: pointer;">'+log_msglan+'</div></a>';
	$(log).appendTo('.yuieditor-group_shout_text:last');

	($.fn.on || $.fn.live).call($(document), 'dblclick', '.msgShout', function (e) {
		var id = $(this).attr('data-ided');
		function edtfunc(msg, uid){
			if (uid == ysbvar.mybbuid || $.inArray(parseInt(ysbvar.mybbusergroup), ysbvar.yuimodgroups.split(',').map(function(modgrup){return Number(modgrup);}))!=-1) {
				$('#shout_text').attr( {"data-type": "edit", "data-id": id} );
				$('#shout_text').val(revescapeHtml(msg).replace(/(<([^>]+)>)/ig,""));
				if(!$('#cancel_edit').length) {
					$('#yuishoutbox-form').append('<button id="cancel_edit" style="margin:4px;">'+cancel_editlan+'</button><button id="del_shout" style="margin:4px;" data-delid='+id+'>'+shout_delan+'</button>');
				}
			}
			else {
				if(!$('#upd_alert').length) {
					$('<div/>', { id: 'upd_alert', class: 'bottom-right' }).appendTo('body');
				}
				setTimeout(function() {
					$('#upd_alert').jGrowl(perm_msglan, { life: 500 });
				},200);
			}
		}
		socket.emit('readonemsg', {id:id});
		socket.once('readonemsg', function (docs) {
			edtfunc(docs.msg, parseInt(docs.uid));
		});
	});

	($.fn.on || $.fn.live).call($(document), 'click', '#cancel_edit', function (e) {
		e.preventDefault();
		$('#shout_text').val('').focus();
		$('#shout_text').attr("data-type", "shout");
		$('#cancel_edit').remove();
		$('#del_shout').remove();
	});
}
