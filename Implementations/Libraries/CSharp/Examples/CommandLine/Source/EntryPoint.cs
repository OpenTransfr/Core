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
using System.IO;
using System.Threading;
using System.Collections.Generic;
using System.Text;
using OpenTransfr;
using Wrench;


/// <summary>
/// This is where the fun starts! This is the entry point for the standalone.
/// It checks the command line args and sets everything up.
/// </summary>

public static class EntryPoint
{	
	
	public static void Main(string[] args)
    {
		
		// Jump to the exe path:
		// - This is so all paths are relative to wherever the exe is.
		Directory.SetCurrentDirectory(
			Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location)
		);
		
		// Setup args:
		Arguments.Load(args);
		
		// Which location are we going to send the request to?
		string endpoint=Arguments.Require("endpoint","It's, for example, 'bank.opentrans.fr'");
		
		// Which method are we going to use?
		string method=Arguments.Require("function","It's, for example, 'commodity/list'");
		
		// Get the JSON payload if there is one (optional):
		string request=Arguments.Get("request");
		
		// Parse the request (it's simply null if the request is):
		JSObject json=JSON.Parse(request);
		
		// Next, the signer - that's who's going to sign our request (if one is needed).
		Signee signer=null;
		
		if(json!=null){
			
			// Signer is only needed if there's a payload to sign.
			
			// We're sending a payload. We'll need someone to sign the request - who?
			// It essentially authenticates the requester.
			string signeeFile=Arguments.Require("signee","It's the path to a JSON file containing auth info");
		
			// Load the signer:
			signer=Signee.LoadFromFile(signeeFile);
			
		}
		
		// Create the API object and grab our endpoint as a location:
		Api api=new Api();
		
		// Get the location:
		Location location=api.GetAt(endpoint);
		
		// Send the request:
		JSObject result=location.Run(method,json,signer);
		
		// Write the JSON response to the console:
		Console.WriteLine( JSON.Stringify(result) );
		
	}
}