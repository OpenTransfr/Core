<div id='account.create.0'>

<form id='uiform' autocomplete='off'>
<center>
What's your full name?
<br>
<br>
<input name='name' class='inputBox' type='text' placeholder='Johnny Callum Starling' onkeyup='Page.buildUsername(this)' onblur='Page.buildUsername(this)'>
<br>
<br>
Pick a username.
<br>
<span style='font-size:0.7em;'>If you enter your name first, you can probably leave this as-is.</span>
<br>
<br>
<input name='username' class='inputBox' type='text' placeholder='johnny.c.starling' onblur='Page.checkUsername()'>
<div id='username_registrar'></div>
<br>
<br>
What's your email address?
<br>
<br>
<input name='email' class='inputBox' type='text' placeholder='johnny@example.com'>
<br>
<br>
</form>
This device's pin
<br>
<span style='font-size:0.7em;'>You'll need to use this pin when you bank on this device.<br>Write it down if you need to - we can't recover it if it's lost.</span>
<br>
<input id='pin_holder' class='inputBox' style='width:120px;' type='password' disabled> <div class='bigButton' onmousedown='Page.cyclePin(this)'>Show it</div>
<br>
<br>
<div class='bigButton' onmousedown='Page.send(this)'>Create Account</div>
<div id='account.create.0_load'></div>
</div>
<div id='account.create.1'>
<center>
<h1>Account created!</h1><br>
Congratulations - you've got a bank account!
<br>
<br>
You can now go ahead and start sending or receiving money and <a href='#screen=account.balances'>check your balances</a>.
</center>
</div>
<div id='account.create.2'>
<center>
<h1>Account sent for review!</h1><br>
The bank is now checking to see if you qualify for an account. They'll let you know by email with the results.
<br>
<br>
</center>
</div>
<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	builtUsername:'',
	nameClear:false,
	
	onChangeMode:function(mode){
		
		// Called when screens are changed.
		if(mode==0){
			// Generate a new pin.
			var pin=randomPin();
			document.getElementById('pin_holder').value=pin;
			
			// Register this device if needed:
			if(Device && currentKey){
				
				// Store the private key encrypted:
				saveKeyEncrypted(pin);
				
			}else{
				
				Device=null;
				currentKey=null;
				
				allocateDevice('',function(){
					
					// Store the private key, encrypted with the pin:
					saveKeyEncrypted(pin);
					
				});
				
			}
			
		}
		
	},
	
	cyclePin:function(btn){
		var pk=document.getElementById("pin_holder");
		
		if(pk.type=='text'){
			pk.type='password';
			btn.innerHTML='Show it';
		}else{
			pk.type='text';
			btn.innerHTML='Hide it';
		}
	},
	
	nameError:function(msg){
		
		var f=form["username"];
		
		if(msg){
			f.style.borderColor='#cc3300';
			Page.nameMessage('<center>'+msg+'</center>','#cc3300');
		}else{
			f.style.borderColor='#339900';
		}
		
	},
	
	buildUsername:function(f){
		
		if(form.username.value!=Page.builtUsername){
			// They changed the username. Don't overwrite it.
			return;
		}
		
		// Get the name:
		var name=f.value.trim().toLowerCase();
		
		// Split it up:
		var pieces=name.split(' ');
		
		var result='';
		
		for(var i=0;i<pieces.length;i++){
			
			var piece=pieces[i];
			
			if(piece==''){
				continue;
			}
			
			if(result){
				result+='.';
			}
			
			if(i==0 || i==(pieces.length-1)){
				
				result+=piece;
				
			}else{
				
				// First character only.
				result+=piece[0];
				
			}
			
		}
		
		form.username.value=result;
		Page.builtUsername=result;
	},
	
	nameMessage:function(msg,col){
		
		if(col){
			msg='<div style="width:378px;font-size:0.8em;border:1px solid '+col+';padding:10px;text-align:left;">'+msg+'</div>';
		}
		
		document.getElementById('username_registrar').innerHTML="<br><br>"+msg;
		
	},
	
	checkUsername:function(onChecked){
		
		var f=form.username;
		var name=f.value;
		
		Page.usernameTested=true;
		Page.nameClear=false;
		
		if(!name){
			if(onChecked){
				onChecked();
			}
			return;
		}
		
		request('/ui/username?name='+name,function(info){
			
			if(info && info.entity){
				Page.nameError('That username is taken.');
				return;
			}
			
			// Available!
			Page.nameError('');
			Page.nameClear=true;
			
			if(onChecked){
				onChecked();
			}
		});
		
	},
	
	send:function(btn){
		
		var result=document.getElementById('account.create.0_load');
		
		if(!Device){
			// Generate a new device:
			generateDevice();
		}
		
		btn.style.display='none';
		
		result.innerHTML=API.Loading;
		
		// Convert the form to JSON:
		var json=formToJson(form);
		
		// Create the account now!
		request('account/create',function(d){
			
			if(d.ID){
				
				if(d.status && d.status=='REVIEW'){
					// Submitted for review.
					change(2);
				}else{
					// Ok!
					change(1);
				}
				
			}else{
				
				// It errored!
				result.innerHTML="<br>"+Error.display(d);
				
			}
			
			btn.style.display='';
		},json);
	}
	
};
</script>