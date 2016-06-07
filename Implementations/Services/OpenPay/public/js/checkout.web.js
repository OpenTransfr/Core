/*
* OpenTransfr checkout API.
*/

var Checkout={
	
	/*
	* The gateway service. Can be any service that supports the OpenPay API.
	*/
	gateway:'https://pay.opentrans.fr/v1/',
	
	/*
	* Ajax helper functions. You can remove this if your JS already contains ajax function.
	*/
	ajax:{
		
		/*
		* Makes a request to the given endpoint.
		* Optionally posts the given JSON (string or object).
		*/
		request:function(endpoint,done,json){
			
			var request;
			
			if(typeof XDomainRequest != "undefined") {
				
				request=new XDomainRequest();
				
			}else{
				
				request = new XMLHttpRequest();
				
			}
			
			if(!json){
				request.open("GET",endpoint,true);
			}else{
				request.open("POST",endpoint,true);
				request.setRequestHeader("Content-Type", "application/json");
				request.overrideMimeType("text/plain");
			}
			
			request.onload = function(){
				done(request.responseText,request);
			};
			
			if(json && !((typeof json)==='string')){
				// Convert an object into a JSON string:
				json=JSON.stringify(json);
			}
			
			request.send(json);
			
		}

	},
	
	/*
	* Helper functions for getting/ creating a checkout ID cookie.
	*/
	cookie:{
		
		/*
		* Sets the value of the named cookie.
		*/
		set:function(name,v,days){
			var d=new Date();
			d.setTime(d.getTime() + (days*24*3600*1000));
			document.cookie=name+"="+v+"; expires="+ d.toUTCString();
		},
		
		/*
		* Gets the value of the named cookie.
		*/
		get:function(name){
			var c=document.cookie.split(';');
			
			for(var i=0;i<c.length;i++){
				c=c.trim();
				if(!c.indexOf(name+"=")){
					c=c.split('=',2);
					return c[1].trim();
				}
			}
			
			return null;
		}
		
	},
	
	/* Runs an API function at the gateway. */
	run:function(func,done,payload){
		
		Checkout.ajax.request(Checkout.gateway+func,function(res,request){
			
			// Parse the JSON response:
			res=JSON.parse(res);
			
			// Run the done function:
			if(done){
				done(res,(request.status==200));
			}
			
		},payload);
		
	},
	
	/* The ID of the current checkout. Numeric string. */
	current:'',
	
	/* Sets the current checkout ID. This can be used to, for example, move
	* a purchase from one device to another. */
	set:function(id,done,noData){
		
		// Create a cookie lasting for 4 days.
		Checkout.cookie.set('chkid',id,4);
		Checkout.current=id;
		Checkout._info=null;
		
		if(noData){
			if(done){
				done();
			}
		}else{
			Checkout.info(function(){
				if(done){
					done();
				}
			});
		}
	},
	
	/* Creates a new online checkout with the given merchant account ID. */
	setup:function(merchantID,done){
		
		var id=Checkout.current | Checkout.cookie.get('chkid');
		
		if(id){
			Checkout.current=id;
			
			if(done){
				done(id);
			}
			return;
		}
		
		Checkout.run('checkout/create',function(res,ok){
			
			if(ok){
				// Ok! Checkout made.
				Checkout.set(res.id,null,true);
				Checkout._info={products:[],discounts:[]};
				
				if(done){
					done(res.id);
				}
				
			}else if(done){
				// Failed!
				done(null,res);
			}
			
		},
			{
				type:'online',
				site:location.hostname,
				merchant:merchantID
			}
		);
		
	},
	
	/* The obtained checkout info. See info(..) function. */
	_info:null,
	
	/* Gets all the info about the current checkout, such as the current products. */
	info:function(done){
		var c=Checkout.current;
		if(!c){
			throw new Exception('Must call Checkout.setup.');
		}
		
		Checkout.run('checkout/get?id='+c,function(res,ok){
			
			if(ok){
				Checkout._info=res;
			}
			
			done(res);
			
		});
		
	},
	
	/*
	* Builds a HTML list of editable products.
	*/
	list:function(){
		
		var p,c,d;
		
		c=Checkout._info;
		
		if(c){
			p=c.products;
			d=c.discounts;
		}else{
			p=d=[];
		}
		
		var html='';
		
	},
	
	/* Adds products to the checkout. Each product is an object containing:
	* - volume. The quantity or weight. If it's a weight, you must include units ('4.5kg').
	* - total. The total price in e.g. pennies (and in terms of the checkout currency).
	* And Either:
	* - opn. The OPN product ID. This can be, for example, an EAN, ISBN, UPC etc.
	* Or:
	* - id. An ID you use for this product.
	* - category. The nearest GS1 GPC product category.
	* - data. Assorted product info such as the name, description, weight, dimensions etc. Provide as much as you wish.
	* Note that the product gets assigned an opn, but you don't have to use it.
	*/
	add:function(merchantID,products,done){
		Checkout.change(true,merchantID,products,done);
	},
	
	/* Removes a product from the checkout. The same as add. */
	remove:function(merchantID,products,done){
		Checkout.change(false,merchantID,products,done);
	},
	
	/* Finds the index of the given value in the given array. */
	find:function(arr,field,value){
		
		for(var i=0;i<arr.length;i++){
			
			if(arr[i][field]==value){
				return i;
			}
			
		}
		
		return null;
	},
	
	/* See Checkout.add or Checkout.remove instead. */
	change:function(add,merchantID,products,done){
		
		// Add or remove function?
		var addRem=add?'add':'delete';
		
		// Make sure it's setup:
		Checkout.setup(merchantID,function(){
			
			Checkout.run('checkout/product/'+addRem,function(res,ok){
				
				if(ok){
					
					// Remove from/ add to _info, if it's in there.
					for(var i=0;i<products.length;i++){
						
						// Get the product:
						var p=products[i];
						
						// Get the ID:
						var id=p.opn | p.id;
						
						// Find it:
						var ps=Checkout._info.products;
						var index=Checkout.find(ps,p.opn?'opn':'id',id);
						
						if(add){
							
							// Add p.volume.
							if(index==-1){
								ps.push(p);
							}else{
								ps[index].volume+=p.volume;
							}
							
						}else if(index!=-1){
							
							// Remove p.volume.
							ps[index].volume-=p.volume;
							
							// Remove the entry if the volume is zero:
							if(ps[index].volume<=0){
								ps.splice(index,1);
							}
							
						}
						
					}
					
				}
				
				// Call done:
				done(res,ok);
				
			},
				{
					id:Checkout.current,
					products:products
				}
			);
			
		});
		
	}
	
};