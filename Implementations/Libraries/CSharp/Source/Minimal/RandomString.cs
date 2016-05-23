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
using System.Text;


namespace OpenTransfr{

	/// <summary>
	/// Used to generate random alphanumeric, lowercase strings.
	/// </summary>

	public static class RandomString{
		
		/// <summary>The patten from which random characters are selected.</summary>
		private const string Pattern="0123456789abcdefghijklmnopqrstuvwxyz";
		/// <summary>The patten from which random characters are selected when generating random hex.</summary>
		private const string PatternHex="0123456789abcdef";
		/// <summary>The random generator which is used to build the strings.</summary>
		private static Random Generator=new Random();
		
		
		/// <summary>Generates a random string of the given length.</summary>
		/// <param name='length'>The length of the random string.</param>
		public static string Get(int length){
			
			// Use the non-hex pattern here:
			return GetPattern(length,Pattern);
			
		}
		
		/// <summary>Generates a random hex string of the given length.</summary>
		/// <param name='length'>The length of the random string.</param>
		public static string Hex(int length){
			
			// Use the non-hex pattern here:
			return GetPattern(length,PatternHex);
			
		}
		
		/// <summary>Generates a random string of the given length.</summary>
		/// <param name='length'>The length of the random string.</param>
		/// <param name='pattern'>The pattern to select chars from.</param>
		public static string GetPattern(int length,string pattern){
			
			// Start a builder:
			StringBuilder builder=new StringBuilder(length);

			// For each character..
			for(int i=0;i<length;i++){
				
				// Get a random character from the pattern:
				char ch=pattern[Generator.Next(0,pattern.Length)];                 
				
				// Add it to the string in progress:
				builder.Append(ch);
			}
			
			// Done!
			return builder.ToString();
			
		}
		
	}

}