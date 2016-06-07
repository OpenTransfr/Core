<div id='checkout.create.0'>
<h1>Create a checkout (Page unused)</h1>
There's 3 types of checkout - online, over the phone ('verbal') and physical points of sale. A user scans them or enters the code seen on them if their scanning isn't working. The user can then see the products they're purchasing on their mobile device and complete the purcahse from there.
<br>
<br>
<center>
<div id='sticker_zone'>

<div id='qrcode'></div>

</div>
<br><br>
<div class='bigButton' onmousedown='Page.generate()'>Generate another</div>
</center>
<br>
<br>
</div>

<script type='text/javascript'>

var form=document.getElementById('uiform');

Page={
	
	qrCode:null,
	
	generate:function(theCode){
		
		new QRCode("qr_code", {
			text: "https://p.opentrans.fr/"+theCode,
			width: 128,
			height: 128,
			colorDark : "#000000",
			colorLight : "#ffffff",
			correctLevel : QRCode.CorrectLevel.H
		});
		
		// Get the sticker zone element:
		var stickerZone=document.getElementById('sticker_zone');
		
		// Loading:
		stickerZone.innerHTML=API.Loading;
		
	}
	
};

// Generate immediately:
Page.generate();

</script>