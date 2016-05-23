//--------------------------------------
//             OpenTransfr
//
//        For documentation or 
//    if you have any issues, visit
//             opentrans.fr
//
//          Licensed under MIT
//--------------------------------------

using System;
using Wrench;
using OpenTransfr;


namespace OpenTransfr{
	
	/// <summary>
	/// A device authenticates with the Bank API.
	/// They can be anything from a typical mobile device to a self-driving car.
	/// </summary>
	
	public class Device:Signee{
		
		/// <summary>The server assigned device ID.</summary>
		public string ID;
		
		/// <summary>
		/// Creates a new device at the given location. Optionally give the device a name.
		/// </summary>
		public Device(Location location,string name):base(){
			
			// Register this device. The payload contains a name for it:
			JSObject payload=new JSArray();
			payload["name"]=new JSValue(name);
			
			// Register it now:
			JSObject response=location.Run("device/create",payload,this);
			
			// Response contains id and sequence:
			ID=response["id"].ToString();
			Sequence=response["sequence"].ToString();
			
		}
		
		/// <summary>
		/// Loads a device from the given JSON object.
		/// </summary>
		public Device(JSObject json):base(json){
			
			// Load the sequence and ID:
			ID=json["id"].ToString();
			Sequence=json["sequence"].ToString();
			
		}
		
		/// <summary>
		/// The JWS header defines what signed the JWS payload - an entity, a device etc.
		/// This creates the JWS header.</summary>
		public override void GetJwsHeader(JSObject header){
			
			// Devices use the 'device' header along with their server assigned ID.
			
			if(ID==null){
				// ID hasn't been assigned yet. Fall back onto the 'pk' (base) header.
				// This occurs in device/create requests.
				base.GetJwsHeader(header);
				return;
			}
			
			// Create the device header now:
			header["device"]=new JSValue(ID);
			
		}
		
		/// <summary>
		/// Builds a JSON string representation of this signee.
		/// </summary>
		public override void ToJson(JSObject obj){
			
			// Must store the sequence, ID (as well as the keypair, done by base)
			base.ToJson(obj);
			
			// Apply a type:
			obj["type"]=new JSValue("device");
			
			obj["sequence"]=new JSValue(Sequence);
			obj["id"]=new JSValue(ID);
			
		}
		
	}
	
}