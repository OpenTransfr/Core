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


namespace OpenTransfr{
	
	/// <summary>
	/// Represents a callable API Function.
	/// For more information see ApiObject.
	/// </summary>
	
	public class ApiFunction:ApiObject{
		
		public ApiFunction(JSObject data,Api api,string path):base(data,api,path){}
		
		
		/// <summary>Runs this API with the given JSON payload and a signee.</summary>
		public override JSObject Run(JSObject payload,Signee signer,Location at){
			
			// First we need to build a JWS if we have a payload.
			if(payload!=null){
				
				// Build the JWS.
				// It has no header and no protected header because:
				// - The header originates from the signer
				//  (i.e. it adds the 'entity', 'pk' or 'device' field to the header)
				// - Protected header is only ever used by root nodes forwarding to other root nodes.
				payload=JWS.Build(null,null,payload,signer);
				
			}
			
			// Now perform the HTTP request (get/post depending on if we have a payload or not).
			HttpResponse req;
			JSObject result=Http.Request(FullPath(at),payload,out req);
			
			if(!req.Ok){
				
				// It errored! Result is either null or an error description.
				throw new Exception("API Errored! "+(result!=null));
				
			}
			
			// Has it got a Sequence header?
			string seq=req.Sequence;
			
			if(seq!=null && signer!=null){
				// Yes - update the signer:
				signer.Sequence=seq;
			}
			
			return result;
			
		}
		
	}
	
}