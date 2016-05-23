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
using System.Net;
using System.Web;
using System.Text;
using System.Collections;
using System.Collections.Generic;
using Wrench;


namespace OpenTransfr{

	/// <summary>
	/// A class for performing HTTP requests.
	/// </summary>

	public static class Http{
		
		/// <summary>Sends a GET request with no headers.</summary>
		public static string Request(string url,out HttpResponse head){
			
			// No headers:
			Dictionary<string,string> headers=null;
			
			// Make a request:
			return Request(url,headers,out head);
			
		}
		
		/// <summary>Sends a GET request with the given custom headers.</summary>
		public static string Request(string url,Dictionary<string,string> headers,out HttpResponse head){
			string resp="";
			
			try{
				
				// Create the request:
				HttpWebRequest request = (HttpWebRequest)WebRequest.Create(url);
				
				// Apply the headers to the request, handling any standard headers:
				HandleHeaders(request,headers);
				
				// Get the response:
				HttpWebResponse response=(HttpWebResponse)( request.GetResponse() );
				StreamReader reader = new StreamReader(response.GetResponseStream());
				resp=reader.ReadToEnd();
				
				// Create the response object:
				head=new HttpResponse(response);
				
				// Tidy up:
				reader.Close();
				response.Close();
				
			}catch{
				
				// Something went wrong.
				head=new HttpResponse(null);
				
			}
			
			return resp;
		}
		
		/// <summary>Performs a HTTP POST with custom headers.</summary>
		public static string Request(string url,string postData,Dictionary<string,string> headers,out HttpResponse head){
			
			// The post data as bytes:
			byte[] bytes=null;
			
			if(postData!=null){
				// We've got some post data - get it's bytes now:
				bytes=Encoding.UTF8.GetBytes(postData);
			}
			
			// Perform a POST request with the bytes:
			return Request(url,bytes,headers,out head);
		}
		
		/// <summary>Performs a HTTP POST with no custom headers.</summary>
		public static string Request(string url,string postData,out HttpResponse head){
			return Request(url,postData,null,out head);
		}
		
		/// <summary>Applies the given set of headers to the given request.</summary>
		private static void HandleHeaders(HttpWebRequest httpWReq,Dictionary<string,string> headers){
			
			if(headers==null){
				// None to apply - stop there.
				return;
			}
			
			// For each header..
			foreach(KeyValuePair<string,string> kvp in headers){
				
				// Certain headers require special handling.
				if(kvp.Key=="Content-Type"){
					
					// Content type header - special handling:
					httpWReq.ContentType=kvp.Value;
					
					continue;
					
				}else if(kvp.Key=="Accept"){
					
					// Accept header - special handling:
					httpWReq.Accept=kvp.Value;
					
					continue;
					
				}else if(kvp.Key=="Method"){
					
					// Method header - special handling:
					httpWReq.Method=kvp.Value;
					
					continue;
					
				}
				
				// Apply the header to the request's headers set:
				httpWReq.Headers[kvp.Key]=kvp.Value;
				
			}
		}
		
		/// <summary>Sends a GET request and parses the response as JSON.</summary>
		public static JSObject RequestJson(string url,out HttpResponse head){
			
			// Request and parse:
			return Request(url,(JSObject)null,null,out head);
			
		}
		
		/// <summary>Posts the given JSON to the given URL.
		/// Performs a GET if no JSON is provided.
		/// The response is parsed as JSON.</summary>
		public static JSObject Request(string url,JSObject json,out HttpResponse head){
			return Request(url,json,null,out head);
		}
		
		/// <summary>Posts the given JSON to the given URL with optional custom headers.
		/// Performs a GET if no JSON is provided.
		/// The response is parsed as JSON.</summary>
		public static JSObject Request(string url,JSObject json,Dictionary<string,string> headers,out HttpResponse head){
			
			// Got a JSON payload?
			if(json==null){
				// Perform a GET request and parse the response as JSON.
				return JSON.Parse( Request(url,headers,out head) );
			}
			
			// Make sure we use the application/json content type:
			string type="application/json";
			
			if(headers!=null){
				// Apply the content type:
				headers["Content-Type"]=type;
			}
			
			// Get the bytes of the JSON payload:
			byte[] payload=System.Text.Encoding.UTF8.GetBytes( JSON.Stringify(json) );
			
			// Perform the request now, and parse the response:
			return JSON.Parse( Request(url,payload,headers,type,out head) );
			
		}
		
		/// <summary>Performs a POST request with optional custom headers.
		/// If no headers are given, it assumes the content-type is application/x-www-form-urlencoded.</summary>
		public static string Request(string url,byte[] postData,Dictionary<string,string> headers,out HttpResponse head){
			
			// Perform a POST request with the default urlencoded type:
			return Request(url,postData,headers,"application/x-www-form-urlencoded",out head);
			
		}
		
		/// <summary>Performs a POST request with optional custom headers.
		/// If no headers are given, the content-type of the post data is given as cType.</summary>
		public static string Request(string url,byte[] postData,Dictionary<string,string> headers,string cType,out HttpResponse head){
			string response="";
			
			// Do we have any post data?
			if(postData==null){
				postData=new byte[0];
			}
			
			try{
				// Start the request:
				HttpWebRequest httpWReq =(HttpWebRequest)WebRequest.Create(url);
				
				// It's a post request:
				httpWReq.Method="POST";
				
				if(headers==null){
					
					// No headers - apply content-type:
					httpWReq.ContentType=cType;
					
				}else{
					
					// Handle the headers which should contain our Content-Type:
					HandleHeaders(httpWReq,headers);
					
				}
				
				// Apply the content length:
				httpWReq.ContentLength=postData.Length;
				
				// Start writing out the post data:
				using(Stream stream=httpWReq.GetRequestStream()){
					stream.Write(postData,0,postData.Length);
				}
				
				// Get the response:
				HttpWebResponse webResponse=(HttpWebResponse)(httpWReq.GetResponse());
				
				// Create the response object:
				head=new HttpResponse(webResponse);
				
				StreamReader reader=new StreamReader(webResponse.GetResponseStream());
				response=reader.ReadToEnd();
				
			}catch{
				
				// Something went wrong.
				head=new HttpResponse(null);
				
			}
			
			return response;
		}
		
		/// <summary>Encodes the given piece of text so it's suitable to go into a post or get string.</summary>
		/// <param name="text">The text to encode.</param>
		/// <returns>The url encoded text.</returns>
		public static string UrlEncode(string text){
			return System.Uri.EscapeDataString(text);
		}
		
	}
	
}