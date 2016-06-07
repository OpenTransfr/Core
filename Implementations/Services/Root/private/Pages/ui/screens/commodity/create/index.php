<div id='commodity.create.0'>

<h1>Create a new commodity</h1><br>
Commodities are things that we can trade. They're anything from currencies or shares to land and votes. Here you can create a new one such as your own currency. You'll need:
<br>
<br>
<li>Any entity with the type 'root', 'bank' or 'issuer'. If you haven't got one yet <a href='javascript:change("entity.create");' style='text-decoration:underline;'>create one here</a>.</li>
<li>The Issuer API available at your entities domain.</li>
<br>
<br>
There's also parent commodities. For example, 'currency' is the parent of 'currency.gbp'. All commodities can act as a parent and how sub-commodities get issued (if at all) is entirely chosen by the parent commodities issuer.
<br>
<br>
<div class='bigButton' onmousedown='change(1)'>Let's get started</div>
<br>
<br>
<span style='font-size:0.8em;'>Note: This uses the API.</span>
</div>

<div id='commodity.create.1'>

<form id='uiform'>
<center>
Give your commodity a name
<br>
<br>
<input name='name' class='inputBox' type='text' placeholder='Imperial Credits'>
<br>
<br>
And a quick description of what it is (optional)
<br>
<br>
<input name='description' class='inputBox' type='text' placeholder='A dashing currency suitable only for Galactic Empires.'>
<br>
<br>
The tag of your new commodity
<br>
<br>
<input name='tag' class='inputBox' type='text' placeholder='virt.currency.ic' onblur='Page.getTag(this)'>
<div id='tag_registrar'></div>
<br>
<br>
The divisor, if one is needed
<br>
<span style='font-size:0.7em;'>E.g. the smallest USD amount is 0.01 (1 cent). Balances are in cents and get divided by 100.</span>
<br>
<br>
<input name='divisor' class='inputBox' type='text' placeholder='10'>
<br>
<br>
Will you issue sub-commodities?
<br>
<span style='font-size:0.7em;'>For example, 'currency.gbp' is a sub of 'currency'</span>
<br>
<br>
<select class='inputBox' name='policy'><option value='closed' selected>No</option><option value='public'>Yes - open to all</option><option value='reviewed'>Yes - with a review</option><select>
<br>
<br>
<br>
<br>
<div class='bigButton' onmousedown='Page.send(this)'>Send</div>
<div id='commodity.create.1_load'></div>
</form>
</div>
<div id='commodity.create.2'>
<center>
<h1>Commodity created!</h1><br>
Congratulations - your commodity has been created!
<br>
<br>
You can now go ahead and start issuing it using the commodity/issue API.
<br>
<br>
PHP Issuer API users: it's available as the function issue('your.commodity.tag',amountToIssue)
</center>
</div>
<div id='commodity.create.3'>
<center>
<h1>Commodity submitted for review!</h1><br>
Your commodity has been submitted for review. This means whoever issues the parent tag is considering your application and you'll receive a response through the Issuer API.
</center>
</div>
<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	tagClear:false,
	tagParent:null,
	
	tagError:function(msg){
		
		var f=form["tag"];
		
		if(msg){
			f.style.borderColor='#cc3300';
			Page.tagMessage('<center>'+msg+'</center>','#cc3300');
		}else{
			f.style.borderColor='#339900';
		}
		
	},
	
	tagMessage:function(msg,col){
		
		if(col){
			msg='<div style="width:378px;font-size:0.8em;border:1px solid '+col+';padding:10px;text-align:left;">'+msg+'</div>';
		}
		
		document.getElementById('tag_registrar').innerHTML="<br><br>"+msg;
		
	},
	
	getTag:function(f){
		var tag=f.value;
		
		Page.tagClear=false;
		
		if(!tag){
			return;
		}
		
		Commodities.Info(tag,function(info){
			
			var parent=Commodities.Parent(tag);
			
			if(info!=null){
				Page.tagError('That tag is taken.');
				return;
			}
			
			if(parent==null){
				// Can't define a new top-level tag.
				Page.tagError('Can\'t define a new top-level tag.');
				return;
			}
			
			if(parent.Policy=="1" || parent.Policy=="2"){
				
				// You can register it!
				Page.tagError('');
				
				var info='<b>Registering sub-tag of</b>: '+parent.Name_en;

				info+='<br><br><b>Issued by</b>: '+parent.IssuerName;
								
				info+='<br><br><b>It\'s policy</b>: ';
				
				if(parent.Policy=="1"){
					info+='<span style="color:#339900;">Open to all (Instant)</span>';
				}else{
					info+='Reviewed (there is a delay and possibly a small fee)';
				}
				
				Page.tagMessage(info,'#339900');
				Page.tagClear=true;
				Page.tagParent=parent;
				
			}else{
				Page.tagError('This tag is closed - you can\'t register a sub-tag.');
			}
			
		});
		
	},
	
	send:function(btn){
		
		var result=document.getElementById('commodity.create.1_load');
		
		if(!Entity || !Entity.key){
			
			// No entity loaded!
			result.innerHTML="<br>"+Error.display({type:"error/entity/required"});
			
			return;
		}
		
		btn.style.display='none';
		
		result.innerHTML=API.Loading;
		
		// Make sure there's a divisor:
		if(form['divisor'].value==''){
			form['divisor'].value='1';
		}
		
		// Convert the form to JSON:
		var json=formToFields(form);
		
		// Wrap name/description in objects with an 'en' property:
		json.name={en:json.name};
		json.description={en:json.description};
		
		// Build the JWS:
		json=jws(Entity,'',json);
		
		// NOTE: This is to avoid cross-origin problems.
		// You could send directly to this URL instead:
		// 'https://'+Page.tagParent.Issuer+'/v1/commodity/request'
		
		// Submit it to the sub-create
		requestJson('/ui/commodity/subcreate',function(d){
			
			if(d.status){
				
				if(d.status=='OK'){
					// Success!
					change(2);
				}else{
					// Review
					change(3);
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