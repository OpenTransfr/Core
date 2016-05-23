<div id='entity.create.0'>
<h1>Create a new entity</h1>
An entity is an organisation like a bank or an issuer. You'll need to have:
<br>
<br>
<li>A domain name that you control</li>
<li>A web server running HTTPS at that domain</li>
<br>
It'll take about 5 minutes.
<br><br>
If you want to later be a fully certified entity, the domain name you use must be registered with a recognised financial regulator. For example, if you use 'txroot.example.com', 'example.com' would need to be shown on e.g. <a href='https://register.fca.org.uk/' style='text-decoration:underline;'>the FCA register</a>.
<br>
<br>
<div class='bigButton' onmousedown='change(1)'>Let's get started</div>
<br>
<br>
<span style='font-size:0.8em;'>Note: This uses the API.</span>
</div>

<div id='entity.create.1'>

<form id='uiform'>
<center>
Host name where you'd host the API<br>
<span style='font-size:0.7em;'>It's usually a subdomain called 'txroot'</span><br><br>
<input name='domain' class='inputBox' type='text' placeholder='txroot.example.com' onblur='Page.getExtraInfo(this)'>
<br>
<br>
What type of entity is this? Pick the most general one that applies.
<br>
<br>
<select name='type' class='inputBox'>
<option value='' selected>It's a..</option>
<option value='bank'>Bank</option>
<option value='issuer'>Issuer</option>
<option value='verifier'>Verifier</option>
<option value='merchant'>Merchant</option>
</select>
<br>
<br>
The name of your entity
<br>
<br>
<input name='name' class='inputBox' type='text' placeholder='My Bank Ltd'>
<br>
<br>
The country that it is headquartered in
<br>
<br>
<select name='country' class='inputBox'>
<option value='' selected>Country..</option>
<?php

$countries=$dz->get_list('select iso2, short_name from countries');

foreach($countries as $country){
	
	echo '<option value="'.$country['iso2'].'">'.$country['short_name'].'</option>';
	
}

?>
</select>
<br>
<br>
It's headquarters address
<br>
<br>
<textarea name='address' class='inputBox' placeholder='1 High Street, London, L1N D0N'>
</textarea>
<br>
<br>
<br>
<br>
<div class='bigButton' onmousedown='Page.send(this)'>Send</div>
<div id='entity.create.1_load'></div>
</center>
</form>

</div>

<div id='entity.create.2'>
<center>
<h1>Entity creation in progress!</h1><br>
To complete the creation you'll now need to prove that you own the domain name. To do that, upload the following data to this location:<br><br>
<input id='token_url' class='inputBox' style='width:80%;'>
<br><br>
<textarea style='width:80%;height:200px;' class='inputBox' id='token_data'></textarea>
<br><br>
<div class='bigButton' onmousedown='Page.uploaded(this)'>I've done that</div>
<div id='entity.create.2_load'></div>
<br>
<br>
</center>
</div>

<div id='entity.create.3'>
<center>
<h1>Entity created!</h1><br>
Congratulations - your entity has been created!
<br>
<br>
<input type='password' class='inputBox' style='width:80%;' id='private_key'>
<br><br>
Your browser generated a <b>private key</b> which you'll need to use for all future requests as this entity. Take a copy of this key and keep it safe. If the key might be compromised, use the entity/update API to change it. It's recommended to do this regularly anyway (about once every 2 weeks).
<br>
<br>
<div class='bigButton' onmousedown='Page.cycleKey(this)'>Reveal it</div>
</center>
</div>
<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	key:null,
	onUploaded:null,
	
	onChangeMode:function(mode){
		
		if(mode==0){
			Progress.showIndicator();
		}else{
			
			Progress.showIndicator(['Basic info','Ownership test'],mode-1);
			
		}
		
	},
	
	cycleKey:function(btn){
		var pk=document.getElementById("private_key");
		
		if(pk.type=='text'){
			pk.type='password';
			btn.innerHTML='Reveal it';
		}else{
			pk.type='text';
			btn.innerHTML='Hide it';
		}
	},
	
	uploaded:function(btn){
		
		btn.style.display='none';
		var result=document.getElementById('entity.create.2_load');
		
		result.innerHTML=API.Loading;
		
		request(Page.onUploaded,function(d){
			
			d=JSON.parse(d);
				
			if(isError(d)){
				
				// It errored!
				result.innerHTML="<br>"+Error.display(d);
				
			}else{
				// Success!
				
				// Write out the private key:
				document.getElementById("private_key").value=Page.getKey();
				result.innerHTML='';
				change(3);
			}
			
			btn.style.display='';
		});
		
	},
	
	getKey:function(){
		return currentKey.getHexFormat();
	},
	
	send:function(btn){
		
		var result=document.getElementById('entity.create.1_load');
		
		if(form.domain.value.indexOf('.')==-1){
			
			result.innerHTML="<br>"+Error.display({type:"error/field/invalid",args:["domain"]});
			
			return;
			
		}
		
		btn.style.display='none';
		
		result.innerHTML=API.Loading;
		
		// Generate a new public/private key:
		var key=generateKey();
		Page.key=key;
		
		// Convert the form to JSON:
		var json=formToJson(form);
		
		// Build the JWS:
		json=jws({"pk":key.getPubKeyHex()},'',json);
		
		// Submit it to the entity/create API now!
		requestJson('entity/create',function(d){
			
			if(isError(d)){
				
				// It errored!
				result.innerHTML="<br>"+Error.display(d);
				
			}else{
				// Perfect! We have a challenges set - grab the data itself (always just 1 http challenge at the moment):
				d=d.challenges[0];
				var challenge=d.token;
				
				// Sign it all and wrap into another JSON object:
				challenge='{"token":"'+challenge+'","signature":"'+sign(challenge,key)+'"}';
				
				// Update the values:
				document.getElementById('token_url').value=d.at;
				document.getElementById('token_data').value=challenge;
				
				// Update the URI to load when done:
				Page.onUploaded=d.uri;
				result.innerHTML='';
				
				// Change to mode 2:
				change(2);
			}
			
			btn.style.display='';
		},json);
		
	},
	
	getExtraInfo:function(f){
		var name=f.value;
		
		if(!name){
			return;
		}
		
		findDetails(name,function(res){
			
			if(!form.country.value && res.country){
				form.country.value=res.country;
			}
			
			if(!form.name.value){
				form.name.value=res.name;
			}
			
			if(!form.address.value){
				var addr='';
				
				for(var i=0;i<res.address.length;i++){
					var line=res.address[i];
					
					if(!line){
						continue;
					}
					
					if(addr){
						addr+=', ';
					}
					
					addr+=line;
				}
				
				form.address.value=addr;
			}
			
		});
		
	}

};

</script>