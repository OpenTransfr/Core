<div id='account.balances.0'>
<center>
<div id='pin_status'></div>
</center>
</div>
<div id='account.balances.1'>

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
		<td class='balanceMenu' onmousedown='Page.change(0)'>Balances</td>
		<td class='balanceMenu' onmousedown='Page.change(1)'>Transactions</td>
		<td class='balanceMenu' onmousedown='Page.change(2)'>Subscriptions</td>
		<td class='balanceMenu' onmousedown='Page.change(4)'>Send money</td>
		<td class='balanceMenu' onmousedown='Page.change(3)'>Devices</td>
		<td class='balanceMenu' onmousedown='Page.change(5)'>Change settings</td>
	</tr>
</table>

<div id='account.modes.0'></div>
<div id='account.modes.1'></div>
<div id='account.modes.2'></div>
<div id='account.modes.3'></div>
<div id='account.modes.4'></div>

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
		
	},
	
	load:function(){
		
		var result=document.getElementById('account.balances.0_load');
		
		btn.style.display='none';
		
		result.innerHTML=API.Loading;
		
		// Get the balances now:
		request('account/balances',function(d){
			
			if(d.ID){
				
			}else{
				
				// It errored!
				result.innerHTML="<br>"+Error.display(d);
				
			}
			
			btn.style.display='';
		});
	}
	
};

var txMode={
	
	transactions:null,
	
	update:function(done){
		
		request('account/transfer/list',function(d,req){
			
			if(req.status==200){
				
				// Load the transactions - they're a JSON list:
				txMode.transactions=jsonList.load(d);
				
			}else{
				
				// Failed
				txMode.transactions=null;
				
			}
			
			done();
			
		});
		
	},
	
	draw:function(){
		
		drawList(txMode.transactions,'transactions',function(tx){
			
			var dp=(tx.Divisor.length-1);
			
			// Let's take the amount and divide by the divisor to give something displayable.
			var rawAmount=(parseInt(tx.Amount)/parseInt(tx.Divisor));
			
			return rawAmount.toFixed(dp)+' '+tx.Name;
			
		});
		
	},
	
	start:function(){
		
		txMode.update(function(){
			
			txMode.draw();
			
		});
		
	},
};

var balanceMode={
	
	balances:null,
	
	update:function(done){
		
		request('account/balance/list',function(d,req){
			
			if(req.status==200){
				
				// Load the balances - they're a JSON list:
				balanceMode.balances=jsonList.load(d);
				
			}else{
				
				// Failed
				balanceMode.balances=null;
				
			}
			
			done();
			
		});
		
	},
	
	draw:function(){
		
		drawList(balanceMode.balances,'balances',function(bal){
			
			var dp=(bal.Divisor.length-1);
			
			// Let's take the amount and divide by the divisor to give something displayable.
			var rawAmount=(parseInt(bal.Amount)/parseInt(bal.Divisor));
			
			return rawAmount.toFixed(dp)+' '+bal.Name;
			
		});
		
	},
	
	start:function(){
		
		balanceMode.update(function(){
			
			balanceMode.draw();
			
		});
		
	},
};

var subMode={
	
	subscriptions:null,
	
	update:function(done){
		
		request('account/subscription/list',function(d,req){
			
			if(req.status==200){
				
				// Load the subscriptions - they're a JSON list:
				subMode.subscriptions=jsonList.load(d);
				
			}else{
				
				// Failed
				subMode.subscriptions=null;
				
			}
			
			done();
			
		});
		
	},
	
	draw:function(){
		
		drawList(subMode.subscriptions,'subscriptions',function(sub){
			
			// 'ID','Amount','Commodity','Reference','Interval','NextTime','Name','Username','ToName','DynamicUrl','ItemInformation'
			var interval=new Interval(sub.Interval);
			
			return sub.Name+" - "+interval.toLanguage();
			
		});
		
	},
	
	start:function(){
		
		subMode.update(function(){
			
			subMode.draw();
			
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

Page.Modes=[balanceMode,txMode,subMode,deviceMode];

</script>