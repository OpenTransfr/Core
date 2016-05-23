<div id='button.pay.0'>
<h1>Create a new pay now button</h1>
Pay now buttons go on your website and are used to accept payments from anyone with an OpenTransfr bank account.
<br>
<br>
<div class='bigButton' onmousedown='change(1)'>Let's get started</div>
<br>
<br>
</div>

<div id='button.pay.1'>

<form id='uiform'>
<center>
Your username<br>
<span style='font-size:0.7em;'>It's the username that will receive payments</span><br><br>
<input name='username' class='inputBox' type='text' placeholder='johnny.c.starling'>
<br>
<br>
Merchant Name<br>
<span style='font-size:0.7em;'>Your merchant name</span><br><br>
<input name='merchant' class='inputBox' type='text' placeholder='Starling Shirts Ltd'>
<br>
<br>
Commodities you accept<br>
<span style='font-size:0.7em;'>The currencies you'll accept; default is all. Include vouchers if your store has them.</span><br><br>
<input name='accept' class='inputBox' type='text' placeholder='currency.*,voucher.mystore'>
<br>
<br>
Currency of your prices<br>
<span style='font-size:0.7em;'>What currency are your amounts in?</span><br><br>
<input name='commodity' class='inputBox' type='text' placeholder='currency.usd'>
<br>
<br>
Total amount<br>
<span style='font-size:0.7em;'>The total amount to be paid in cents/ pennies.</span><br><br>
<input name='amount' class='inputBox' type='text' placeholder='400'>
<br>
<br>
Reference<br>
<span style='font-size:0.7em;'>A reference which appears in your and your buyer's transaction history. Usually set dynamically.</span><br><br>
<input name='reference' class='inputBox' type='text' placeholder='#0485'>
<br>
<br>
Transaction title<br>
<span style='font-size:0.7em;'>A title which shows up on the buyer's transaction history.</span><br><br>
<input name='name' class='inputBox' type='text' placeholder='MySite Order'>
<br>
<br>

Products, discounts, notify
<br><br>
<div class='bigButton' onmousedown='Page.build(this)'>Build my button!</div>
<div id='button.buy.1_load'></div>

</center>
</form>
</div>

<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	build:function(f){
		
		var result=document.getElementById('button.buy.1_load');
		
		// Get the form fields:
		var formFields=formToFields(form);
		
		var json=JSON.stringify(formFields);
		
		// Build it now amd write to result:
		var value='<form method="POST" action="https://pay.opentrans.fr/v1/button/pay">\r\n';
		
		value+='\t<input type="hidden" name="info" value="'+json.replace(/\\/g,'\\\\').replace(/"/g,'\\"')+'">\r\n';
		
		value+='\t<img src="https://pay.opentrans.fr/Images/Pay-now.png" style="width:120px;">\r\n';
		
		value+='</form>';
		
		result.innerHTML='<br><br>Copy this HTML and drop it on your website:<br><br><textarea style="width:80%;height:300px;">'+value+'</textarea>';
		
	}
	
};

</script>