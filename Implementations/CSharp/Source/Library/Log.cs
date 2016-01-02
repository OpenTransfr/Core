//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// Logs messages.
	/// This allows them to be diverted to some other code.
	/// </summary>
	
	public static class Log{
		
		/// <summary>True if all messages should also log a stack trace 
		/// unless it explicitly states not to with Add(msg,false).</summary>
		public static bool Stacktrace;
		
		/// <summary>Log a message.</summary>
		public static void Add(string message){
			Add(message,Stacktrace);
		}
		
		/// <summary>Log a message, optionally with a stack trace.</summary>
		public static void Add(string message,bool trace){
			
			Console.WriteLine(message);
			
			if(trace){
				Console.WriteLine(Environment.StackTrace);
			}
			
		}
		
	}
	
}