(function(){var a=tinymce.DOM;tinymce.create("tinymce.plugins.WordPress",{mceTout:0,init:function(c,d){var e=this,h=c.getParam("wordpress_adv_toolbar","toolbar2"),g=0,f,b;f='<img src="'+d+'/img/trans.gif" class="mceWPmore mceItemNoResize" title="'+c.getLang("wordpress.wp_more_alt")+'" />';b='<img src="'+d+'/img/trans.gif" class="mceWPnextpage mceItemNoResize" title="'+c.getLang("wordpress.wp_page_alt")+'" />';if(getUserSetting("hidetb","0")=="1"){c.settings.wordpress_adv_hidden=0}c.onPostRender.add(function(){var i=c.controlManager.get(h);if(c.getParam("wordpress_adv_hidden",1)&&i){a.hide(i.id);e._resizeIframe(c,h,28)}});c.addCommand("WP_More",function(){c.execCommand("mceInsertContent",0,f)});c.addCommand("WP_Page",function(){c.execCommand("mceInsertContent",0,b)});c.addCommand("WP_Help",function(){c.windowManager.open({url:tinymce.baseURL+"/wp-mce-help.php",width:450,height:420,inline:1})});c.addCommand("WP_Adv",function(){var i=c.controlManager,j=i.get(h).id;if("undefined"==j){return}if(a.isHidden(j)){i.setActive("wp_adv",1);a.show(j);e._resizeIframe(c,h,-28);c.settings.wordpress_adv_hidden=0;setUserSetting("hidetb","1")}else{i.setActive("wp_adv",0);a.hide(j);e._resizeIframe(c,h,28);c.settings.wordpress_adv_hidden=1;setUserSetting("hidetb","0")}});c.addButton("wp_more",{title:"wordpress.wp_more_desc",image:d+"/img/more.gif",cmd:"WP_More"});c.addButton("wp_page",{title:"wordpress.wp_page_desc",image:d+"/img/page.gif",cmd:"WP_Page"});c.addButton("wp_help",{title:"wordpress.wp_help_desc",image:d+"/img/help.gif",cmd:"WP_Help"});c.addButton("wp_adv",{title:"wordpress.wp_adv_desc",image:d+"/img/toolbars.gif",cmd:"WP_Adv"});c.addButton("add_media",{title:"wordpress.add_media",image:d+"/img/media.gif",onclick:function(){tb_show("",tinymce.DOM.get("add_media").href);tinymce.DOM.setStyle(["TB_overlay","TB_window","TB_load"],"z-index","999999")}});c.addButton("add_image",{title:"wordpress.add_image",image:d+"/img/image.gif",onclick:function(){tb_show("",tinymce.DOM.get("add_image").href);tinymce.DOM.setStyle(["TB_overlay","TB_window","TB_load"],"z-index","999999")}});c.addButton("add_video",{title:"wordpress.add_video",image:d+"/img/video.gif",onclick:function(){tb_show("",tinymce.DOM.get("add_video").href);tinymce.DOM.setStyle(["TB_overlay","TB_window","TB_load"],"z-index","999999")}});c.addButton("add_audio",{title:"wordpress.add_audio",image:d+"/img/audio.gif",onclick:function(){tb_show("",tinymce.DOM.get("add_audio").href);tinymce.DOM.setStyle(["TB_overlay","TB_window","TB_load"],"z-index","999999")}});c.onBeforeExecCommand.add(function(i,l,k,m){var j=tinymce.DOM;if("mceFullScreen"!=l){return}if("mce_fullscreen"!=i.id&&j.get("add_audio")&&j.get("add_video")&&j.get("add_image")&&j.get("add_media")){i.settings.theme_advanced_buttons1+=",|,add_image,add_video,add_audio,add_media"}});c.addCommand("JustifyLeft",function(){var i=c.selection.getNode();if(i.nodeName!="IMG"){c.editorCommands.mceJustify("JustifyLeft","left")}else{c.plugins.wordpress.do_align(i,"alignleft")}});c.addCommand("JustifyRight",function(){var i=c.selection.getNode();if(i.nodeName!="IMG"){c.editorCommands.mceJustify("JustifyRight","right")}else{c.plugins.wordpress.do_align(i,"alignright")}});c.addCommand("JustifyCenter",function(){var k=c.selection.getNode(),j=c.dom.getParent(k,"p"),i=c.dom.getParent(k,"dl");if(k.nodeName=="IMG"&&(j||i)){c.plugins.wordpress.do_align(k,"aligncenter")}else{c.editorCommands.mceJustify("JustifyCenter","center")}});if("undefined"!=typeof wpWordCount){c.onKeyUp.add(function(i,j){if(j.keyCode==g){return}if(13==j.keyCode||8==g||46==g){wpWordCount.wc(i.getContent({format:"raw"}))}g=j.keyCode})}c.onSaveContent.add(function(i,j){if(typeof(switchEditors)=="object"){if(i.isHidden()){j.content=j.element.value}else{j.content=switchEditors.pre_wpautop(j.content)}}});e._handleMoreBreak(c,d);c.addShortcut("alt+shift+c",c.getLang("justifycenter_desc"),"JustifyCenter");c.addShortcut("alt+shift+r",c.getLang("justifyright_desc"),"JustifyRight");c.addShortcut("alt+shift+l",c.getLang("justifyleft_desc"),"JustifyLeft");c.addShortcut("alt+shift+j",c.getLang("justifyfull_desc"),"JustifyFull");c.addShortcut("alt+shift+q",c.getLang("blockquote_desc"),"mceBlockQuote");c.addShortcut("alt+shift+u",c.getLang("bullist_desc"),"InsertUnorderedList");c.addShortcut("alt+shift+o",c.getLang("numlist_desc"),"InsertOrderedList");c.addShortcut("alt+shift+d",c.getLang("striketrough_desc"),"Strikethrough");c.addShortcut("alt+shift+n",c.getLang("spellchecker.desc"),"mceSpellCheck");c.addShortcut("alt+shift+a",c.getLang("link_desc"),"mceLink");c.addShortcut("alt+shift+s",c.getLang("unlink_desc"),"unlink");c.addShortcut("alt+shift+m",c.getLang("image_desc"),"mceImage");c.addShortcut("alt+shift+g",c.getLang("fullscreen.desc"),"mceFullScreen");c.addShortcut("alt+shift+z",c.getLang("wp_adv_desc"),"WP_Adv");c.addShortcut("alt+shift+h",c.getLang("help_desc"),"WP_Help");c.addShortcut("alt+shift+t",c.getLang("wp_more_desc"),"WP_More");c.addShortcut("alt+shift+p",c.getLang("wp_page_desc"),"WP_Page");c.addShortcut("ctrl+s",c.getLang("save_desc"),function(){if("function"==typeof autosave){autosave()}});if(tinymce.isWebKit){c.addShortcut("alt+shift+b",c.getLang("bold_desc"),"Bold");c.addShortcut("alt+shift+i",c.getLang("italic_desc"),"Italic")}c.onInit.add(function(i){tinymce.dom.Event.add(i.getWin(),"scroll",function(j){i.plugins.wordpress._hideButtons()});tinymce.dom.Event.add(i.getBody(),"dragstart",function(j){i.plugins.wordpress._hideButtons()})});c.onBeforeExecCommand.add(function(i,k,j,l){i.plugins.wordpress._hideButtons()});c.onSaveContent.add(function(i,j){i.plugins.wordpress._hideButtons()});c.onMouseDown.add(function(i,j){if(j.target.nodeName!="IMG"){i.plugins.wordpress._hideButtons()}})},getInfo:function(){return{longname:"WordPress Plugin",author:"WordPress",authorurl:"http://wordpress.org",infourl:"http://wordpress.org",version:"3.0"}},_setEmbed:function(b){return b.replace(/\[embed\]([\s\S]+?)\[\/embed\][\s\u00a0]*/g,function(d,c){return'<img width="300" height="200" src="'+tinymce.baseURL+'/plugins/wordpress/img/trans.gif" class="wp-oembed mceItemNoResize" alt="'+c+'" title="'+c+'" />'})},_getEmbed:function(b){return b.replace(/<img[^>]+>/g,function(c){if(c.indexOf('class="wp-oembed')!=-1){var d=c.match(/alt="([^\"]+)"/);if(d[1]){c="[embed]"+d[1]+"[/embed]"}}return c})},_showButtons:function(f,d){var g=tinyMCE.activeEditor,i,h,b,j=tinymce.DOM,e,c;b=g.dom.getViewPort(g.getWin());i=j.getPos(g.getContentAreaContainer());h=g.dom.getPos(f);e=Math.max(h.x-b.x,0)+i.x;c=Math.max(h.y-b.y,0)+i.y;j.setStyles(d,{top:c+5+"px",left:e+5+"px",display:"block"});if(this.mceTout){clearTimeout(this.mceTout)}this.mceTout=setTimeout(function(){g.plugins.wordpress._hideButtons()},5000)},_hideButtons:function(){if(!this.mceTout){return}if(document.getElementById("wp_editbtns")){tinymce.DOM.hide("wp_editbtns")}if(document.getElementById("wp_gallerybtns")){tinymce.DOM.hide("wp_gallerybtns")}clearTimeout(this.mceTout);this.mceTout=0},do_align:function(j,d){var h,f,g,b,i,e=tinyMCE.activeEditor;if(/^(mceItemFlash|mceItemShockWave|mceItemWindowsMedia|mceItemQuickTime|mceItemRealMedia)$/.test(j.className)){return}h=e.dom.getParent(j,"p");f=e.dom.getParent(j,"dl");g=e.dom.getParent(j,"div");if(f&&g){b=e.dom.hasClass(f,d)?"alignnone":d;f.className=f.className.replace(/align[^ '"]+\s?/g,"");e.dom.addClass(f,b);i=(b=="aligncenter")?e.dom.addClass(g,"mceIEcenter"):e.dom.removeClass(g,"mceIEcenter")}else{if(h){b=e.dom.hasClass(j,d)?"alignnone":d;j.className=j.className.replace(/align[^ '"]+\s?/g,"");e.dom.addClass(j,b);if(b=="aligncenter"){e.dom.setStyle(h,"textAlign","center")}else{if(h.style&&h.style.textAlign=="center"){e.dom.setStyle(h,"textAlign","")}}}}e.execCommand("mceRepaint")},_resizeIframe:function(c,e,b){var d=c.getContentAreaContainer().firstChild;a.setStyle(d,"height",d.clientHeight+b);c.theme.deltaHeight+=b},_handleMoreBreak:function(c,d){var e,b;e='<img src="'+d+'/img/trans.gif" alt="$1" class="mceWPmore mceItemNoResize" title="'+c.getLang("wordpress.wp_more_alt")+'" />';b='<img src="'+d+'/img/trans.gif" class="mceWPnextpage mceItemNoResize" title="'+c.getLang("wordpress.wp_page_alt")+'" />';c.onInit.add(function(){c.dom.loadCSS(d+"/css/content.css")});c.onPostRender.add(function(){if(c.theme.onResolveName){c.theme.onResolveName.add(function(f,g){if(g.node.nodeName=="IMG"){if(c.dom.hasClass(g.node,"mceWPmore")){g.name="wpmore"}if(c.dom.hasClass(g.node,"mceWPnextpage")){g.name="wppage"}}})}});c.onBeforeSetContent.add(function(f,g){g.content=g.content.replace(/<!--more(.*?)-->/g,e);g.content=g.content.replace(/<!--nextpage-->/g,b)});c.onPostProcess.add(function(f,g){if(g.get){g.content=g.content.replace(/<img[^>]+>/g,function(i){if(i.indexOf('class="mceWPmore')!==-1){var h,j=(h=i.match(/alt="(.*?)"/))?h[1]:"";i="<!--more"+j+"-->"}if(i.indexOf('class="mceWPnextpage')!==-1){i="<!--nextpage-->"}return i})}});c.onNodeChange.add(function(g,f,h){f.setActive("wp_page",h.nodeName==="IMG"&&g.dom.hasClass(h,"mceWPnextpage"));f.setActive("wp_more",h.nodeName==="IMG"&&g.dom.hasClass(h,"mceWPmore"))})}});tinymce.PluginManager.add("wordpress",tinymce.plugins.WordPress)})();