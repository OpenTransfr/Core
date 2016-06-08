<div id='entity.check.0'>
<h1>Check if an entity is regulated</h1>
An entity is an organisation like a bank or an issuer. You'll need to have:
<br>
<br>
<li>This test reference acts like the UK FCA. You'll need your FCA reference number.</li>
<li>An existing entity. <a href='https://txroot.opentrans.fr/#screen=entity.create'>Create one here</a> if you don't have one yet.</li>
<li>Your entity domain name must be known by this regulator.</li>
<br>
It'll take no more than a few minutes.
<br>
<br>
<div class='bigButton' onmousedown='change(1)'>Let's get started</div>
<br>
<br>
<span style='font-size:0.8em;'>Note: This uses the API.</span>
</div>

<div id='entity.check.1'>

<form id='uiform'>
<center>
Your entity's host name<br>
<span style='font-size:0.7em;'>It's usually a subdomain called 'txroot'</span><br><br>
<input name='entity' class='inputBox' type='text' placeholder='txroot.example.com'>
<br>
<br>
FCA reference number<br><br>
<input name='id' class='inputBox' type='text' placeholder='1234567'>
<br>
<br>
<br>
<br>
<div class='bigButton' onmousedown='Page.send(this)'>Send</div>
<div id='entity.check.1_load'></div>
</center>
</form>

</div>

<div id='entity.check.2'>
<center>
<div id='entity_data'>

</div>
<br>
<br>
</center>
</div>

<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	send:function(btn){
		
		var result=document.getElementById('entity.check.1_load');
		
		if(form.entity.value.indexOf('.')==-1){
			
			result.innerHTML="<br>"+Error.display({type:"error/field/invalid",args:["entity"]});
			
			return;
			
		}
		
		btn.style.display='none';
		
		result.innerHTML=API.Loading;
		
		// Convert the form to JSON:
		var json=formToJson(form);
		
		// Submit it to the entity/check API now!
		requestJson('entity/check',function(d){
			
			if(isError(d)){
				
				// It errored!
				result.innerHTML="<br>"+Error.display(d);
				
			}else{
				
				console.log(d);
				
				// Change to mode 2:
				change(2);
			}
			
			btn.style.display='';
		},json);
		
	}
	
};

</script>