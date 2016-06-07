<div id='account.home.0'>
<center>
<div id='pin_status'></div>
</center>
</div>
<div id='account.home.1'>

<style type='text/css'>

.balanceMenu{
	width:20%;
	height:100px;
	text-align:center;
	vertical-align:center;
	background:#038ad0;
}

#userLine{
	width:100%;
	padding:5px;
}

.tableLine{
	padding:10px;
	background:#13a2ed;
	margin:2px;
}

</style>

<div id='userLine'></div>

<table style='width:100%;' cellspacing=4 cellpadding=10>
	<tr>
		<td class='balanceMenu' onmousedown='Page.change(0)'>Checkouts</td>
		<td class='balanceMenu' onmousedown='Page.change(1)'>Devices</td>
		<td class='balanceMenu' onmousedown='Page.change(2)'>Change settings</td>
	</tr>
</table>

<div id='account.modes.0'></div>
<div id='account.modes.1'></div>

</div>
<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	pageMode:0,
	Modes:null,
	authorisedAccount:null,
	
	pinStatus:function(msg){
		
		document.getElementById("pin_status").innerHTML=msg+"<br>";
		
	},
	
	modeError:function(msg){
		Page.modeBox().innerHTML='<center>'+msg+'</center>';
	},
	
	modeBox:function(){
		return document.getElementById('account.modes.'+Page.pageMode);
	},
	
	modeLoading:function(){
		Page.modeBox().innerHTML='<center>'+API.Loading+'</center>';
	},
	
	change:function(pageMode){
		
		Page.pageMode=pageMode;
		var i=0;
		
		while(true){
			
			var modeBox=document.getElementById('account.modes.'+i);
			
			if(!modeBox){
				break;
			}
			
			if(pageMode==i){
				modeBox.style.display='';
			}else{
				modeBox.style.display='none';
			}
			
			i++;
			
		}
		
		// Start the mode now:
		Page.Modes[pageMode].start();
		
	},
	
	onChangeMode:function(mode){
		
		if(mode==0){
			// has the private key been unlocked yet?
			if(Page.authorisedAccount){
				
				// Go straight to the balances now:
				change(1);
				
			}else{
				
				// Clear status:
				Page.pinStatus('');
				
				// Prompt for pin:
				Keypad.display(true,Page.onPinEntered);
				
			}
			
		}else if(mode==1){
			// Load balances and display them:
			
			document.getElementById('userLine').innerHTML='Hey '+Page.firstName(Page.authorisedAccount.fullName)+"! What would you like to do?";
			Page.change(0);
			
		}
		
	},
	
	firstName:function(full){
		
		return full.split(' ')[0];
		
	},
	
	onPinEntered:function(pin){
		
		// Load the private key (this can't tell if the pin is right or not):
		loadKeyEncrypted(function(){
			
			// Try authing which also obtains account information:
			request('device/authenticate',function(d,req){
				
				if(req.status==200){
					
					// Everything checked out as valid! This response contains some account information.
					Page.authorisedAccount=d.account;
					Page.pinStatus('');
					change(1);
					
				}else{
					
					// Bad pin! Possibly a bad sequence too (depends on the other errors).
					if(d.type=='error/device/locked'){
						// Too many failed attempts; device is locked
						
						Page.pinStatus('<h1>This device is temporarily locked</h1><br>You\'ve used 3 wrong pins - you\'ll need to wait 15 minutes before you can try again.');
						
					}else{
						
						// Assumes error/signature/invalid here.
						Page.pinStatus('That pin wasn\'t right - Try again');
						
						// Prompt for pin:
						Keypad.display(true,Page.onPinEntered);
						
					}
				}
				
			});
			
		},pin,true);
		
	}
};

var coMode={
	
	checkouts:null,
	
	create:function(){
		
		// Creates a checkout
		request('account/checkout/create',function(d,req){
			
			if(req.status==200){
				
				// Ok!
				
			}else{
				
				// Failed!
				
			}
			
		},{type:'physical'});
		
	},
	
	update:function(done){
		
		request('account/checkout/list',function(d,req){
			
			if(req.status==200){
				
				// Load the checkouts - they're a JSON list:
				coMode.checkouts=jsonList.load(d);
				
			}else{
				
				// Failed
				coMode.checkouts=null;
				
			}
			
			done();
			
		});
		
	},
	
	makeSticker:function(checkID,checkoutCode){
		
		new QRCode("sticker_"+checkID, {
			// Note: Scanning this loads the web view of the checkout.
			text: "https://p.opentrans.fr/"+checkoutCode,
			width: 128,
			height: 128,
			colorDark : "#000000",
			colorLight : "#ffffff",
			correctLevel : QRCode.CorrectLevel.H
		});
		
	},
	
	draw:function(){
		
		drawList(coMode.checkouts,'checkouts',function(checkout){
			
			var line=checkout.ID+" ";
			
			if(checkout.Type==3){
				
				line+=" <div class='bigButton' onmousedown='Page.Modes[0].makeSticker("+checkout.ID+",\""+checkout.Code+"\")'>Generate Sticker</div>";
				line+="<div id='sticker_"+checkout.ID+"'></div>";
			}
			
			return line;
		});
		
	},
	
	start:function(){
		
		coMode.update(function(){
			
			coMode.draw();
			
		});
		
	},
};

var deviceMode={
	
	devices:null,
	
	update:function(done){
		
		request('account/device/list',function(d,req){
			
			if(req.status==200){
				
				// Load the devices - they're a JSON list:
				deviceMode.devices=jsonList.load(d);
				
			}else{
				
				// Failed
				deviceMode.devices=null;
				
			}
			
			done();
			
		});
		
	},
	
	draw:function(){
		
		drawList(deviceMode.devices,'devices',function(device){
			
			return device.Name+" {Settings incl. rename} {Block} {Permissions incl. time window}";
			
		});
		
	},
	
	start:function(){
		
		deviceMode.update(function(){
			
			deviceMode.draw();
			
		});
		
	},
};

function drawList(s,name,line){
	
	if(!s){
		Page.modeError("Your "+name+" are currently unavailable - please check later.");
		return;
	}else if(s.length==0){
		Page.modeError("No "+name+" to display.");
		return;
	}
	
	var list='';
	
	for(var i=0;i<s.length;i++){
		
		// Get the row:
		var row=s[i];
		
		list+='<div class="tableLine">'+line(row)+'</div>';
		
	}
	
	Page.modeBox().innerHTML=list;
	
}

Page.Modes=[coMode,deviceMode];

</script>