<div id='issue.0'>

<form id='uiform' autocomplete='off'>
<center>
What's your username?
<br>
<br>
<input name='username' class='inputBox' type='text' placeholder='johnny.c.starling'>
<br>
<br>
How much would you like? Anything up to $10,000.
<br>
<br>
<input name='amount' class='inputBox' type='text' placeholder='100.00'><br>
<br>
<div class='bigButton' onmousedown='Page.send(this)'>Send me some coins!</div>
<div id='issue.0_load'></div>
</div>
<div id='issue.1'>
<center>
<h1>Coins sent!</h1><br>
We've sent you some test coins!
<br>
<br>
You can now spend the test coins in either the test store or by sending them to some other user.
</center>
</div>
<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	send:function(btn){
		
		var result=document.getElementById('issue.0_load');
		
		btn.style.display='none';
		
		result.innerHTML=API.Loading;
		
		// Convert the form to JSON:
		var fields=formToFields(form);
		
		// Tidy up the amount:
		fields.amount=parseInt( fields.amount.replace(/,/gi,'').replace(/\./gi,'') );
		
		var json=JSON.stringify(fields);
		
		// Create the account now!
		Ajax.request('/ui/testcoin/issue',function(d,req){
			
			if(req.status==200){
				
				// Ok!
				change(1);
				
			}else{
				
				// It errored!
				result.innerHTML="<br>"+Error.display(d);
				
			}
			
			btn.style.display='';
		},json);
	}
	
};
</script>