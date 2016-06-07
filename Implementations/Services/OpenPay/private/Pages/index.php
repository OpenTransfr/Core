<?php// This response is ordinary HTML:header('Content-Type: text/html');?><!doctype html><html><head><style>.inputBox{	padding:10px;	font-family: "SourceSansProLight","lucida grande",tahoma,verdana,arial,sans-serif;	border: 1px solid #ffffff;    border-radius: 4px;	width: 300px;	color: #000000;}body{	background:#0194E1;	font-family:"SourceSansProLight","lucida grande",tahoma,verdana,arial,sans-serif;	color:#ffffff;	font-size:1.4em;	margin:0;	border:0;	padding:0;}html{	overflow:auto;	margin:0;	border:0;	padding:0;}@font-face{    font-family:"SourceSansProLight";    src:url("/Font/SourceSansProLight.woff") format('woff');}a:link,a:visited{	color:#ffffff;	text-decoration:none;}.bigButton:hover,.outlineButton:hover,.menu:hover{	cursor:pointer;}.outlineButton,.bigButton{	display:inline-block;	padding:10px;	border:1px solid #ffffff;	border-radius:4px;}.bigButton{	margin-top:10px;}.progressCircle{	width:20px;	height:20px;	border:2px solid #ffffff;	display:inline-block;	border-radius:11px;}.progressLine{	height:0px;	width:100px;	margin-bottom:11px;	display:inline-block;	border:1px solid #ffffff;}</style><!--[If IE]><style>@font-face{    font-family:"SourceSansProLight";    src:url("/Font/SourceSansProLight.eot");}</style><![endif]--></head><body><div style='padding:4%'><div id='progress_bar'></div><div style='padding:2%;'><div id='home'><h1>Welcome to OpenTransfr Merchant services!</h1><br>In here is tools that you can use to easily accept payments via OpenTransfr either physically, over the phone or on the internet. All you need is an OpenTransfr capable bank account and you're set.<br><br>Explore what's available:<br><br><li> <a href='javascript:change("button.pay")'>Embed a pay now button on your website</a></li><li> <a href='javascript:change("button.subscribe")'>Embed a subscribe button on your website</a></li></div><div id='api_screen'></div><div id='keypad' style='display:none;'><center>	<style type='text/css'>.keypad_button:hover,.keypad_number:hover{	cursor:pointer;}.noselect{  -webkit-touch-callout:none;  -webkit-user-select:none;  -khtml-user-select:none;   -moz-user-select:none;   -ms-user-select:none;  user-select:none;}.keypad_button,.keypad_number{	width:100px;	height:100px;	background:#1776a8;	color:#ffffff;	text-align:center;	vertical-align:middle;	border:1px solid #ffffff;	font-weight:bold;	font-size:1.4em;}.keypad_number:active{	background:#53baf0;}#keypad_screen{	width:100%;	height:100px;	color:#000000;	background:#ffffff;	text-align:center;	font-size:3em;	border:1px solid #ffffff;}</style>		<table style='width:500px;' cellpadding=0 cellspacing=5>				<tr style='width:100%;'>			<td id='keypad_screen' colspan=3>						</td>		</tr>		<tr style='width:100%;'>			<td class='keypad_number noselect' onmousedown='Keypad.add(1)'>1</td>			<td class='keypad_number noselect' onmousedown='Keypad.add(2)'>2</td>			<td class='keypad_number noselect' onmousedown='Keypad.add(3)'>3</td>		</tr>		<tr style='width:100%;'>			<td class='keypad_number noselect' onmousedown='Keypad.add(4)'>4</td>			<td class='keypad_number noselect' onmousedown='Keypad.add(5)'>5</td>			<td class='keypad_number noselect' onmousedown='Keypad.add(6)'>6</td>		</tr>		<tr style='width:100%;'>			<td class='keypad_number noselect' onmousedown='Keypad.add(7)'>7</td>			<td class='keypad_number noselect' onmousedown='Keypad.add(8)'>8</td>			<td class='keypad_number noselect' onmousedown='Keypad.add(9)'>9</td>		</tr>		<tr style='width:100%;'>			<td class='keypad_button' style='background:#A81740 url(/Images/Keypad-Cancel.svg) no-repeat center;' onmousedown='Keypad.clear()'></td>			<td class='keypad_number noselect' onmousedown='Keypad.add(0)'>0</td>			<td class='keypad_button' style='background:#81fd74 url(/Images/Keypad-Ok.svg) no-repeat center;' onmousedown='Keypad.accept()'></td>		</tr>	</table></center></div></div></div><script type='text/javascript' src='/js/util.js'></script><script type='text/javascript' src='/js/qrcode.min.js'></script><script type='text/javascript'>var Keypad={		max:4,	currentInput:'',	onAccept:null,		clear:function(){		this.currentInput='';		this.updateScreen();	},		updateScreen:function(){				var val='';				for(var i=0;i<this.currentInput.length;i++){			val+='*';		}				document.getElementById('keypad_screen').innerHTML=val;	},		goBack:function(){		var c=this.currentInput;		if(c){			this.currentInput=c.substring(0,c.length-1);		}		this.updateScreen();	},		display:function(visible,onAccept){				this.clear();		this.onAccept=onAccept;				var box=document.getElementById('keypad').style;				if(visible){			box.display='';		}else{			box.display='none';		}			},		add:function(n){				if(this.currentInput.length==this.max){			return;		}				this.currentInput+=n.toString();		this.updateScreen();			},		accept:function(){		this.onAccept(this.currentInput);		this.display(false,null);	}	};var API={	Version:1,	Endpoint:'/',	Loading:'Loading - Just a moment..',	AvailableObjects:{}};window.hashchange=window.onhashchange=function(){		// Load the hash vars:	urlVariables=loadQueryString(window.location.hash.substring(1));		var s=urlVariables['screen'];		if(!s){s='';}		if(screen!=s){				change(s);			}		var m=urlVariables['section'];		if(m){m=parseInt(m);}else{m=0;}		if(currentMode!=m){				change(m);			}	};// Load the hash variables:var urlVariables={};// The current API screen:var screen='';// The elements:var api_screen=document.getElementById('api_screen');var home=document.getElementById('home');var currentMode=0;// JS and vars for the current page:var Page=null;function change(to){	if(typeof to==='string'){		if(to==screen){			return;		}				screen=to;				// Homepage?		if(screen=='home' || screen==''){			// Yep:			api_screen.innerHTML='';			home.style.display='';		}else{			// Nope - load api now!			home.style.display='none';						// Clear the progress indicator:			Progress.showIndicator();						api_screen.innerHTML='<h1>Loading - We\'ll just be a moment..</h1>';						// Load the content:			Ajax.request('/ui/screens/'+to.replace('.','/'),function(r){								// Write it to api_screen:				api_screen.innerHTML=r;								// Grab any scripts and eval them now:				var s=api_screen.getElementsByTagName('script');								for(var i=0;i<s.length;i++){					var script=s[i];					eval(script.innerHTML);				}								// Hide/show the modes:				changeMode(0);							});					}			}else{		changeMode(to);	}}function changeMode(mode){		window.location.hash="screen="+screen+'&section='+mode;		currentMode=mode;	var i=0;		if(Page && Page.onChangeMode){				Page.onChangeMode(mode);			}		while(true){		var box=document.getElementById(screen+'.'+i);				if(!box){			return;		}				if(i==mode){			box.style.display='';		}else{			box.style.display='none';		}				i++;			}	}var Progress={	bar:document.getElementById('progress_bar'),		showIndicator:function(stages,stage){				var progLine='';				if(stages){						progLine='<center>';						for(var i=0;i<stages.length;i++){								if(i!=0){					progLine+='<div class="progressLine"';										progLine+='></div>';				}								progLine+='<div title="'+stages[i]+'" class="progressCircle"';								if(i<stage){					progLine+=' style="background:#21BE68;"';				}else if(i==stage){					progLine+=' style="background:#ffffff;"';				}								progLine+='></div>';							}						progLine+='</center>';				}				Progress.bar.innerHTML=progLine;			}};/** Is the given object an API error?*/function isError(d){		if(d.type){		return true;	}else{		return false;	}	}var Error={	display:function(d){				return "API Errored:<br><br>"+JSON.stringify(d);			}};setupDevice();// Load the hash now:window.hashchange();</script></body></html>