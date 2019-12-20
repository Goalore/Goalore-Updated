;(function ($) {
	$(document).ready(function () {
		var ChatStored = [];
		var ChatIdStored = [];
		var NotifyUserID = [];
		var ChatCached = "";
		var RequestState1 = true;
		var RequestState2 = true;
		var notifyInterval, showNotifyInterval, friendListRefresh;
		var winNo = [], oldMsgsNo = {}, newMsgsNo = {}, newMessageInterval, blinkImgColor;
				
		var AjaxChat = {
			
			bpchatInit: function () {
				var self = $(this);
				this.LoadFriendsWindow();
				this.loadSmileys();
				this.initSmileys();
				this.eventHandler();
				this.loadChatRow();
				this.submitMessage();
				//this.initializeActiveChats();
				this.searchFriends();
				this.notificationAtTitle();	
				this.resizewindow();	
			},
			
			loadFriends: function () {
				if(RequestState2 == true) {
					RequestState2 = false;
					$.ajax({
						url: bpchat_conf.ajaxURL,
						type: "POST",
						dataType: "JSON",
						data:{
							action : bpchat_conf.ajaxActions.load_friends.action,
							nonce : bpchat_conf.ajaxNonce
						},
						success: function(data) {
							$(".bpchatFriendsBody").html(data.FriendsRow);
							if(parseInt(bpchat_conf.memberCount)){
								$(".bpchatFriendsCount").html('('+data.friendsCount+')');
							}
						},
						complete: function() {
							friendListRefresh = setTimeout(AjaxChat.loadFriends, bpchat_conf.friendListRate);
							RequestState2 = true;
						}
					});
				}
			},
						
			refreshFriendsList: function () {
				$(".bpchatFriendsBody").prepend('<div class="bpchatFriendsBodyLoading"></div>');
				clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: bpchat_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : bpchat_conf.ajaxActions.refresh_friends.action,
						nonce : bpchat_conf.ajaxNonce
					},
					success: function(data) {
						//$(".bpchatFriendsBodyLoading").remove();
						$(".bpchatFriendsBody").html(data.FriendsRow);
					},
					complete: function() {
						friendListRefresh = setTimeout(AjaxChat.loadFriends, bpchat_conf.friendListRate);
						RequestState2 = true;
					}
				});
			},
			
			loadFriendsOnline: function () {
				$(".bpchatFriendsBody").prepend('<div class="bpchatFriendsBodyLoading"></div>');
				clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: bpchat_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : bpchat_conf.ajaxActions.online_friends.action,
						nonce : bpchat_conf.ajaxNonce
					},
					success: function(data) {
						//$(".bpchatFriendsBodyLoading").remove();
						$(".bpchatFriendsBody").html(data.FriendsRow);
					},
					complete: function() {
						friendListRefresh = setTimeout(AjaxChat.loadFriends, bpchat_conf.friendListRate);
						RequestState2 = true;
					}
				});
			},
			
			loadbpFriendsOnline: function () {
				$(".bpchatFriendsBody").prepend('<div class="bpchatFriendsBodyLoading"></div>');
				clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: bpchat_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : bpchat_conf.ajaxActions.bp_online_friends.action,
						nonce : bpchat_conf.ajaxNonce
					},
					success: function(data) {
						//$(".bpchatFriendsBodyLoading").remove();
						$(".bpchatFriendsBody").html(data.FriendsRow);
					},
					complete: function() {
						friendListRefresh = setTimeout(AjaxChat.loadFriends, bpchat_conf.friendListRate);
						RequestState2 = true;
					}
				});
			},
			
			loadbpGroupList: function () {
				$(".bpchatFriendsBody").prepend('<div class="bpchatFriendsBodyLoading"></div>');
				clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: bpchat_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						action : bpchat_conf.ajaxActions.bp_group_list.action,
						nonce : bpchat_conf.ajaxNonce
					},
					success: function(data) {
						//$(".bpchatFriendsBodyLoading").remove();
						$(".bpchatFriendsBody").html(data.FriendsRow);
					},
					complete: function() {
						friendListRefresh = setTimeout(AjaxChat.loadFriends, bpchat_conf.friendListRate);
						RequestState2 = true;
					}
				});
			},
			
			loadFriendsByGroupID: function (groupid) {
				$(".bpchatFriendsBody").prepend('<div class="bpchatFriendsBodyLoading"></div>');
				clearTimeout(friendListRefresh);
				RequestState2 = false;
				$.ajax({
					url: bpchat_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data:{
						GroupID : groupid,
						action : bpchat_conf.ajaxActions.bp_group_friend_list.action,
						nonce : bpchat_conf.ajaxNonce
					},
					success: function(data) {
						//$(".bpchatFriendsBodyLoading").remove();
						$(".bpchatFriendsBody").html(data.FriendsRow);
					},
					complete: function() {
						friendListRefresh = setTimeout(AjaxChat.loadFriends, bpchat_conf.friendListRate);
						RequestState2 = true;
					}
				});
			},
			
			searchFriends: function () {
				$("body").on("keyup", "#bpchatSearchFriends", function(e) {
					if(e.keyCode == 13) {
						var searchValue = $(this).val();
						clearTimeout(friendListRefresh);
						RequestState2 = false;
						$.ajax({
							url: bpchat_conf.ajaxURL,
							type: "POST",
							dataType: "JSON",
							data: { 
								searchData: searchValue,
								action : bpchat_conf.ajaxActions.search_friends.action,
								nonce : bpchat_conf.ajaxNonce
							 },
							success: function(data) {
								$(".bpchatFriendsBody").html(data.FriendsRow);
							},
							complete: function() {
								friendListRefresh = setTimeout(AjaxChat.loadFriends, bpchat_conf.friendListRate);
								RequestState2 = true;
							}
						});
						$(this).val("");
					}
				});
			},

			createChatWindow: function (userID, userName, WindowState, userImage){
				
				var chatWindow ='';
					chatWindow += '<div id="chat-window-id-'+userID+'" data-minimize="0" class="bpchatWindow" data-parameter-window-id="'+userID+'" data-identifier="'+userID+'" data-window-state="'+WindowState+'">';
					chatWindow += '<div class="bpchatHeader" data-event="minimize-window" data-parameter-window-id="'+userID+'">';
						chatWindow += '<div class="bpchatUser">'+userName+'</div>';
						chatWindow += '<div class="bpchatEvents">';
						chatWindow += '<i data-event="minimize-chat-window" data-parameter-window-id="'+userID+'" data-location="bpchat-event-minimize-'+userID+'" class="bpc_minimize">&minus;</i>';
							chatWindow += '<i data-event="close-chat-window" data-parameter-window-id="'+userID+'" data-location="bpchat-event-close-'+userID+'" class="close">&times;</i>';
						chatWindow += '</div>';
					chatWindow += '</div>';
					chatWindow += '<div class="bpchatBody" data-location="bpchat-body-'+userID+'">';
					chatWindow += '</div>';
					chatWindow += '<div class="bpchatFooter">';
					chatWindow += '<span class="bpchatSmiley" data-event="smiley_open" data-parameter-window-id="'+userID+'"></span>';
					chatWindow += '<input type="text" data-event="submit-chat" placeholder="'+bpchat_conf.start_chat+'" data-parameter-window-id="'+userID+'" />';
					chatWindow += '</div>';
				chatWindow += '</div>';
				chatWindow += '<img id="bpc_userimg_'+userID+'" class="bpc_userimg bpc_userimg_grey" data-parameter-window-id="'+userID+'" data-event="open-chat-window" src="'+userImage+'" />';
				if(!$("#chat-window-id-"+userID+"").length){
					$("#bpchatChatsWindow").append(chatWindow);
					
					if($.inArray(userID, ChatStored) == -1) {
						ChatStored.push(userID);
						AjaxChat.setActiveChat(userID, userName, userImage);
					}
				}
				if(bpchat_conf.fullHeight && $(window).width() < 768){
					$('.bpchatWindow').css('height',$(window).height()+'px');
					$('.bpchatBody').css('height',$(window).height()-64+'px');
				}
			},
			
			LoadFriendsWindow: function () {
				$.ajax({
					url: bpchat_conf.ajaxURL,
					type: "POST",
					dataType: "JSON",
					data: { 
						action : bpchat_conf.ajaxActions.load_bpc_window.action,
						nonce : bpchat_conf.ajaxNonce
					 },
					success: function(data) {
						var data = data.bpc_window;
						$("body").append(data);
						$("#loadingBar").attr("src", bpchat_conf.pluginsURL + "/bpchat/images/loading.gif");
					},
					complete: function() {
						AjaxChat.loadFriends();
					}
				});
				
			},
			
			loadSmileys: function () {
				$.get(bpchat_conf.templateURL + "smiley.html", function(data) {
					$("body").append(data);
				});
			},
			
			eventHandler: function () {
				$("body").on("click", "[data-event]", function (){					
					var Event = $(this).attr("data-event");
					switch(Event) {						
						case "close-chat-window":
							var WindowId = $(this).attr("data-parameter-window-id");
							$("#chat-window-id-"+WindowId).remove();	
							$("#bpc_userimg_"+WindowId).remove();
							//ChatStored.remove(WindowId);
							var index = ChatStored.indexOf(WindowId);
							if (index >= 0) {
							  ChatStored.splice( index, 1 );
							}						
							$.ajax({
								url: bpchat_conf.ajaxURL,						
								type: "POST",
								dataType: "JSON",
								data: { 
									windowId: WindowId, 
									action : bpchat_conf.ajaxActions.remove_active_chat.action,
									nonce : bpchat_conf.ajaxNonce
								 },
								success: function(data) { }
							});
						break;
						
						case "close-friends-window":
							var WindowId = $(this).attr("data-parameter-window-id");
							$("[data-identifier=\"" + WindowId + "\"]").css("display","none");
							$(".listOpenIcon").css("display","inline-block");
							$("[data-identifier=\"" + WindowId + "\"]").attr("data-window-state", "0");
						break;
						
						case "open-friends-window":
							var holder = $(".bpchatFriendsHolder").attr("data-window-state");
							if(holder=="0"){
								$(".bpchatFriendsHolder").css("display","block");
								$(".listOpenIcon").html("&or;");
								$(".bpchatFriendsHolder").attr("data-window-state", "1");
							}else{
								$(".bpchatFriendsHolder").css("display","none");
								$(".listOpenIcon").html("&and;");
								$(".bpchatFriendsHolder").attr("data-window-state", "0");
							}
							
						break;
						
						case "refresh_friends":
							AjaxChat.refreshFriendsList();
							
						break;
						
						case "online_friends":
							AjaxChat.loadFriendsOnline();
							
						break;
						
						case "bp_online_friends":
							AjaxChat.loadbpFriendsOnline();
							
						break;
						
						case "bp_group_list":
							AjaxChat.loadbpGroupList();
							
						break;
						
						case "bp_group_friend_list":
							var GroupID = $(this).attr("data-parameter-group-id");
							var GroupName = $(this).attr("data-parameter-group-name");
							AjaxChat.loadFriendsByGroupID(GroupID);
						break;
						
						case "initialize-chat":
							var WindowId = $(this).attr("data-parameter-user-id");
							var userImage = $(this).find('img').attr('src');
							var WindowUserName = $(this).attr("data-parameter-user-name");
							var fholder = $(".bpchatFriendsHolder").attr("data-window-state");
							if(fholder=="1"){
								$(".bpchatFriendsHolder").css("display","none");
								$(".listOpenIcon").html("&and;");
								$(".bpchatFriendsHolder").attr("data-window-state", "0");
							}						
							if($("#chat-window-id-"+WindowId).length > 0){
								$("#chat-window-id-"+WindowId).fadeIn(function () {
									$(this).css("display","block");
								});
							}else{
								AjaxChat.createChatWindow(WindowId, WindowUserName, 1, userImage);
								$.ajax({
									url: bpchat_conf.ajaxURL,						
									type: "POST",
									dataType: "JSON",
									data: { 
										senderID: WindowId,
										action : bpchat_conf.ajaxActions.load_allchat.action,
										nonce : bpchat_conf.ajaxNonce
									 },
									success: function(data) {
										data = data.allmessages;
										$.each(data, function(i, object) {
											var WindowId = i;
											var WindowContent = object.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
											var Container = $("[data-location=\"bpchat-body-" + i + "\"]");
											
											var ScrollTop = $(Container).scrollTop();
											var CurrentHeight = $(Container).prop("scrollHeight") - 266;
											
											$(Container).prepend(WindowContent);
											var NewHeight = $(Container).prop("scrollHeight") - 266;
											var Difference = NewHeight - CurrentHeight;
											$(Container).scrollTop($(Container).prop("scrollHeight"));
											
											var LastMessageId = $(".bpchatContent").last().attr("data-parameter");
											if(LastMessageId !== ChatIdStored[i]) {
												ChatIdStored[i] = LastMessageId;
											}
											
										});
									}
								});
							}
							
						break;
						
						case "minimize-chat-window":
							var miniWindowId = $(this).attr('data-parameter-window-id');
							$("#chat-window-id-"+miniWindowId).attr('data-minimize',1);
							$("#chat-window-id-"+miniWindowId).slideUp("slow");
							$("#bpc_userimg_"+miniWindowId).slideDown("slow");
						break;
						case "open-chat-window":
							var openWindowId = $(this).attr('data-parameter-window-id');
							if($(this).hasClass('bpc_userimg_green')){
								$(this).removeClass('bpc_userimg_green').addClass('bpc_userimg_grey');
							}
							$("#chat-window-id-"+openWindowId).attr('data-minimize',0);
							$("#bpc_userimg_"+openWindowId).slideUp("slow");
							$("#chat-window-id-"+openWindowId).slideDown("slow");
						break;
					}
				});
			},
			
			setActiveChat: function(WindowId, WindowUserName, UserImage){
				$.ajax({
					url: bpchat_conf.ajaxURL,						
					type: "POST",
					dataType: "JSON",
					data: { 
						windowId: WindowId,
						UserImage: UserImage,
						WindowUserName: WindowUserName,
						windowState: 1,
						action : bpchat_conf.ajaxActions.set_active_chat.action,
						nonce : bpchat_conf.ajaxNonce
					 },
					success: function(data) { },
					complete: function() {
						RequestState5 = true;
					}
				});
			},
			loadChatRow: function () {
				if(RequestState1 == true) {
					RequestState1 = false;
					$.ajax({
						url: bpchat_conf.ajaxURL,						
						type: "POST",
						dataType: "JSON",
						data: { 
							action : bpchat_conf.ajaxActions.load_chat.action,
							nonce : bpchat_conf.ajaxNonce
						 },
						success: function (data){
							var senderdata = data.bpc_senderinfo;
							var chatdata = data.bpc_chatinfo;
							
							jQuery.each(senderdata, function(i, object) {
								var WindowId = i;
								var WindowUserName = object.SenderName;
								var avatar = object.avatar;
								if($("#chat-window-id-"+WindowId).length > 0){
									// Do nothing
								}else{
									AjaxChat.createChatWindow(WindowId, WindowUserName, 1, avatar);
								}
							});
							jQuery.each(chatdata, function(i, object) {
								var chatID = i;
								var senderID = object.senderid;
								var receiverID = object.receiverid;
								var pmessage = object.message;
								var message = pmessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
								var chatTime = object.chat_time;
								var avatar = object.avatar;
								
								var Container = $("[data-location=\"bpchat-body-" + senderID + "\"]");
								
								var WindowContent = '<div class="bpchatMessageRow bpc_clear"><div class="bpchatMessageUserImage leftImage"><img src="'+avatar+'" /></div><div class="bpchatMessage leftMessage"><div data-parameter="'+chatID+'" class="bpchatContent bpchatMessageLocation-'+senderID+'">'+message+'</div></div></div>';
								$(Container).append(WindowContent);
								$(Container).scrollTop($(Container).prop("scrollHeight"));
							});
						},
						complete: function() {
							setTimeout(AjaxChat.loadChatRow, bpchat_conf.chatRate);
							RequestState1 = true;
						}
					});
				}
			},

			submitMessage: function () {
				$("body").on("keyup", "[data-event=\"submit-chat\"]", function (e) {
					if(e.keyCode == 13) {
						var userImage = bpchat_conf.avatar;
						var d = new Date();
						var n = d.getTime();
						var pMessage = AjaxChat.escapeTag($.trim($(this).val()));
						var Message = pMessage.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
						var WindowId = $(this).attr("data-parameter-window-id");
						
						var Container = $("[data-location=\"bpchat-body-" + WindowId + "\"]");
						var msg = '<div class="bpchatMessageRow bpc_clear"><div class="bpchatMessageUserImage rightImage"><img src="'+userImage+'" /></div><div class="bpchatMessage rightMessage"><div data-parameter="'+n+'" class="bpchatContent bpchatMessageLocation-'+WindowId+'">'+Message+'</div></div></div>';
						if(pMessage != '') {
							$(Container).append(msg);
							$(Container).scrollTop($(Container).prop("scrollHeight"));
							$.ajax({
								url: bpchat_conf.ajaxURL,						
								type: "POST",
								dataType: "JSON",
								data: { 
									messageContent: pMessage, 
									receiverUserId: WindowId,
									action : bpchat_conf.ajaxActions.submit_message.action,
									nonce : bpchat_conf.ajaxNonce
								 },
								success: function(data) {
									//AjaxChat.loadChatRow();
								}
							});
						}
						$(this).val("");
					}
				});
			},
			escapeTag: function(string){
				var map = {
					'&': '&amp;',
					'<': '&lt;',
					'>': '&gt;',
					'"': '&quot;',
					"'": '&#039;'
				  };
				
				return string.replace(/[&<>"']/g, function(m) { return map[m]; });
			},
			initializeActiveChats: function () {
				$.ajax({
					url: bpchat_conf.ajaxURL,						
					type: "POST",
					dataType: "JSON",
					data: { 
						action : bpchat_conf.ajaxActions.load_active_chat.action,
						nonce : bpchat_conf.ajaxNonce
					 },
					success: function(data) {
						data = data.ChatStored;
						$.each(data, function(i, object) {
							var WindowId = data[i].WINDOWID;
							var UserName = data[i].USERNAME;
							var UserImage = data[i].USERIMAGE;
							var WindowState = data[i].STATE;
							AjaxChat.createChatWindow(WindowId,UserName, WindowState, UserImage);
							
							$.ajax({
								url: bpchat_conf.ajaxURL,						
								type: "POST",
								dataType: "JSON",
								data: { 
									senderID: WindowId,
									action : bpchat_conf.ajaxActions.load_allchat.action,
									nonce : bpchat_conf.ajaxNonce
								 },
								success: function(data) {
									data = data.allmessages;
									$.each(data, function(i, object) {
										var WindowId = i;
										var WindowContent = object.replace(/(smiley[0-9]{1,3})/g,'<span class="bpcSmiley bpc-$1"></span>');
										var Container = $("[data-location=\"bpchat-body-" + i + "\"]");
										
										var ScrollTop = $(Container).scrollTop();
										var CurrentHeight = $(Container).prop("scrollHeight") - 266;
										
										$(Container).prepend(WindowContent);
										var NewHeight = $(Container).prop("scrollHeight") - 266;
										var Difference = NewHeight - CurrentHeight;
										$(Container).scrollTop($(Container).prop("scrollHeight"));
									});
								}
								
							});
						});
					}
				});
			},
			initSmileys : function(){
				
				var slideCount = $('#bpchat-smiley ul li').length;
				var slideWidth = $('#bpchat-smiley ul li').width();
				var slideHeight = $('#bpchat-smiley ul li').height();
				var sliderUlWidth = slideCount * slideWidth;
				$('#bpchat-smiley').css({ width: slideWidth, height: slideHeight });
				$('#bpchat-smiley ul').css({ width: sliderUlWidth, marginLeft: - slideWidth });
				$('#bpchat-smiley ul li:last-child').prependTo('#bpchat-smiley ul');
					
				$("body").on("click", "[data-event]", function (){
					var Event = $(this).attr("data-event");
					switch(Event) {						
						case "smiley_prev":
							AjaxChat.smileyMoveLeft();
						break;
						case "smiley_next":
							AjaxChat.smileyMoveRight();
						break;
						case "smiley_open":
							var wID = $(this).attr("data-parameter-window-id");
							var posX = $(this).offset().left,
								posY = $(this).offset().top - $(window).scrollTop();    
							 $('#bpchat-smiley ul li span').each(function(index, element) {
                                $(this).attr("data-parameter-window-id", wID);
                            });
							
							if($("#chatroom-smiley").is(":visible")){
								$("#chatroom-smiley").css({"display": "none"});
							}else{
								$("#bpchat-smiley").css({"top":posY-285+"px","left":posX-240+"px", "display": "block"});
							}
							
						break;
						case "smiley_close":
							$('#bpchat-smiley').css("display", "none");
						break;
						case "insert_smiley":
							var sID = $(this).attr("data-parameter-window-id");
							var sClass = $(this).attr("class");
							var sName = sClass.substr(sClass.indexOf("-")+1);
							var iElement = $("input[data-parameter-window-id='"+sID+"']");
							var pValue = iElement.val();
							iElement.val(pValue+' '+sName+' ');
							$('#bpchat-smiley').css("display", "none");
							iElement.focus();
						break;
					}
				})
				
			},
			
			smileyMoveLeft : function() {
				$('#bpchat-smiley ul').animate({
					left: + $('#bpchat-smiley ul li').width()
				}, 200, function () {
					$('#bpchat-smiley ul li:last-child').prependTo('#bpchat-smiley ul');
					$('#bpchat-smiley ul').css('left', '');
				});
			},
		
			smileyMoveRight : function() {
				$('#bpchat-smiley ul').animate({
					left: - $('#bpchat-smiley ul li').width()
				}, 200, function () {
					$('#bpchat-smiley ul li:first-child').appendTo('#bpchat-smiley ul');
					$('#bpchat-smiley ul').css('left', '');
				});
			},
			notificationAtTitle : function(){
				var timer=0, newtitle = [], oldtitle = document.title;
				newtitle.push(oldtitle);
				var vis = (function(){
					var stateKey, eventKey, keys = {
						hidden: "visibilitychange",
						webkitHidden: "webkitvisibilitychange",
						mozHidden: "mozvisibilitychange",
						msHidden: "msvisibilitychange"
					};
					for (stateKey in keys) {
						if (stateKey in document) {
							eventKey = keys[stateKey];
							break;
						}
					}
					return function(c) {
						if (c) document.addEventListener(eventKey, c);
						return !document[stateKey];
					}
				})();
				vis(function(){
					var boxname = [], chatno = {};
					var audioplayer = document.getElementById("bpchat_alert");
					$(".bpchatWindow").each(function(i, element) {
						var id = $(this).attr('id');
						chatno[id] = $(this).find('.bpchatMessageRow').length;
					});
					
					if(!vis()){
						notifyInterval = setInterval(function(){ 
							var nchatno = {}, ntitle;							
							$(".bpchatWindow").each(function(i, element) {
								var nid = $(this).attr('id'),
									ntitle =  $(this).find('.bpchatUser').html()+' sent you new message';
									
								nchatno[nid] = $(this).find('.bpchatMessageRow').length;
								
								if(nchatno[nid] > chatno[nid] && $.inArray(ntitle, newtitle) == -1){
									newtitle.push(ntitle);
								}
							});
						}, 2000);
						
						showNotifyInterval = setInterval(function(){ 
							if(newtitle.length > 1){
								document.title = newtitle[timer];
								timer++
								if (timer >= newtitle.length){
									timer=0;
								}
								audioplayer.play();
							}
						}, 3000);
						
					}else{
						clearInterval(notifyInterval);
						clearInterval(showNotifyInterval);
						document.title = oldtitle;
						newtitle = [];
						newtitle.push(oldtitle);
						audioplayer.pause();
					}
					
				});
				
				newMessageInterval  = setInterval(function(){ 
					$(".bpchatWindow").each(function(){
						var wid = $(this).attr('data-parameter-window-id');
						newMsgsNo[wid] = $(this).find('.bpchatBody').children().length;
						
						if($(this).attr('data-minimize') == 1 && (newMsgsNo[wid] > oldMsgsNo[wid])){
							if($("#bpc_userimg_"+wid).hasClass('bpc_userimg_grey')){
								$("#bpc_userimg_"+wid).removeClass('bpc_userimg_grey').addClass('bpc_userimg_green');
							}else if($("#bpc_userimg_"+wid).hasClass('bpc_userimg_green')){
								$("#bpc_userimg_"+wid).removeClass('bpc_userimg_green').addClass('bpc_userimg_grey');
							}
							
						}
						oldMsgsNo[wid] = $(this).find('.bpchatBody').children().length;
					})
					
				}, 3500);
				
				blinkImgColor  = setInterval(function(){ 
					if($(".bpc_userimg_green").length > 0){
						$(".bpc_userimg_green").each(function(){
							var randColor = '#'+(Math.random()*0xFFFFFF<<0).toString(16);
							$(this).css('border-color', randColor)
						})
					}
					
				}, 1000);
			},
			
			resizewindow: function(){
				if(bpchat_conf.fullHeight && $(window).width() < 768){
					$(window).resize(function(e) {
                        $('.bpchatWindow').css('height',$(window).height()+'px');
						$('.bpchatBody').css('height',$(window).height()-64+'px');
                    });
				}
			}
		}
		
		AjaxChat.bpchatInit();
		
	});
}(jQuery));
