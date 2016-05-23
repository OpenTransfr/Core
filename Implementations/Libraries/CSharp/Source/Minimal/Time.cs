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
using System.Diagnostics;
using System.Timers;


namespace OpenTransfr{

	/// <summary>
	/// Helps with tracking the current UTC time.
	/// </summary>

	public static class Time{
		
		/// <summary>The UNIX start date.</summary>
		// private static DateTime UnixStartDate=new DateTime(1970,1,1,0,0,0,DateTimeKind.Utc);
		/// <summary>UnixStartdate.Ticks (constant).</summary>
		private const long UnixStartTicks=621355968000000000;
		
		
		/// <summary>Gets the given DateTime as a UNIX timestamp in milliseconds.</summary>
		public static long AsUnixTime(DateTime dateTime){
			
			return ( (dateTime.Ticks - UnixStartTicks) / 10000);
			
		}
		
		/// <summary>Gets the given DateTime as a UNIX timestamp in ticks.</summary>
		public static long AsUnixTimeTicks(DateTime dateTime){
			
			return (dateTime.Ticks - UnixStartTicks);
			
		}
		
		/// <summary>The current unix time in seconds.</summary>
		public static ulong UnixTime{
			get{
				return (ulong)(DateTime.UtcNow.Ticks - UnixStartTicks) / 10000000;
			}
		}
		
		/// <summary>The current unix time in milliseconds.</summary>
		public static ulong UnixTimeMs{
			get{
				return (ulong)(DateTime.UtcNow.Ticks - UnixStartTicks) / 10000;
			}
		}
		
		/// <summary>The current unix time in ticks.</summary>
		public static ulong UnixTimeTicks{
			get{
				
				return (ulong)(DateTime.UtcNow.Ticks - UnixStartTicks);
				
			}
		}
		
	}

}