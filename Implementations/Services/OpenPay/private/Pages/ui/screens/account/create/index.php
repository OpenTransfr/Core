<div id='account.create.0'>

<form id='uiform' autocomplete='off'>
<center>
What's your name of the company?
<br>
<br>
<input name='name' class='inputBox' type='text' placeholder='Starling Shirts Ltd'>
<br>
<br>
What's the username for its bank account?
<br>
<br>
<input name='username' class='inputBox' type='text' placeholder='starling.shirts'>
<br>
<br>
What's your company email address?
<br>
<br>
<input name='email' class='inputBox' type='text' placeholder='starling@example.com'>
<br>
<br>
</form>
This device's pin
<br>
<span style='font-size:0.7em;'>You'll need to use this pin when you use merchant services on this device.<br>Write it down if you need to - we can't recover it if it's lost.</span>
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
Congratulations - you've got a merchant account!
<br>
<br>
You can now go ahead and start creating checkouts and <a href='#screen=account.home'>view your account settings</a>.
</center>
</div>

<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	onChangeMode:function(mode){
		
		// Called when screens are changed.
		if(mode==0){
			var ph=document.getElementById('pin_holder');
			
			if(!ph){
				return;
			}
			
			// Generate a new pin.
			
			var pin=randomPin();
			ph.value=pin;
			Page.generatedPin=pin;
			
			// Register this device if needed:
			if(!Account){
				
				Account=null;
				currentKey=null;
				
				allocateDevice('',function(){});
				
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
	
	send:function(btn){
		
		var result=document.getElementById('account.create.0_load');
		
		btn.style.display='none';
		
		result.innerHTML=API.Loading;
		
		// Convert the form to JSON:
		var fields=formToFields(form);
		var json=JSON.stringify(fields);
		
		// Create the account now!
		request('account/create',function(d){
			
			if(d.ID){
				
				// Update the username:
				Account.username=fields.username;
				
				// Save the account!
				addAccount(Page.generatedPin);
				
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