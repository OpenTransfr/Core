//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.Diagnostics;
using System.Timers;


namespace OpenTransfr{

	/// <summary>
	/// Helps with tracking the current UTC time.
	/// </summary>

	public static class Time{
		
		/// <summary>A clock which tracks the current unix time.</summary>
		private static Timer Clock;
		/// <summary>The number of seconds the system had been on for when the offset was computed.</summary>
		private static int TickStartTime;
		/// <summary>The offset to add to TickCount when the time is requested.</summary>
		private static long TickCountOffset;
		/// <summary>The current unix time in seconds.</summary>
		public static ulong UnixTime;
		/// <summary>The UNIX start date.</summary>
		private static DateTime UnixStartDate=new DateTime(1970,1,1,0,0,0,DateTimeKind.Utc);
		/// <summary>UnixStartdate.Ticks (constant).</summary>
		private const long UnixStartTicks=621355968000000000;
		
		
		/// <summary>True if the time services have started.</summary>
		public static bool Started{
			get{
				return (Clock!=null);
			}
		}
		
		/// <summary>Starts the timer.</summary>
		public static void Start(){
			
			// Create the timer:
			Clock=new Timer();
			Clock.Elapsed+=OnTick;
			Clock.Interval=1000;
			Clock.Enabled=true;
			
			// Get current ticks:
			long ticks=DateTime.UtcNow.Ticks;
			
			// Unix time too:
			UnixTime=(ulong)((ticks-UnixStartTicks) / 10000000);
			
			// Recompute offsets:
			UpdateOffset();
			
		}
		
		/// <summary>Recomputes the TickCountOffset.</summary>
		private static void UpdateOffset(){
			
			// Grab the current UTC ticks as milliseconds:
			long currentTicks=DateTime.UtcNow.Ticks / TimeSpan.TicksPerMillisecond;
			
			// Next, find out how long the system has been up for:
			TickStartTime=Environment.TickCount;
			
			// Figure out the full tick offset. That's the current ticks - how long we've been on for.
			TickCountOffset=currentTicks-TickStartTime;
			
		}
		
		/// <summary>Gets the current number of milliseconds since 0000 as a global value.</summary>
		public static long Ticks{
			get{
				
				// Get the current tick count:
				int ticks=Environment.TickCount;
				
				// Did it wrap?
				if(ticks<TickStartTime){
					// Sure did! Figure out the offset again.
					UpdateOffset();
				}
				
				return ticks+TickCountOffset;
			}
		}
		
		private static void OnTick(object source,ElapsedEventArgs e){
			
			// Get current ticks:
			long ticks=DateTime.UtcNow.Ticks;
			
			// Unix time:
			UnixTime=(ulong)((ticks-UnixStartTicks) / 10000000);
			
		}
		
		/// <summary>Gets the given DateTime as a UNIX timestamp in seconds.</summary>
		public static long AsUnixTime(DateTime dateTime){
			
			return ( (dateTime.Ticks - UnixStartTicks) / 10000000);
			
		}
		
		/// <summary>Gets the given DateTime as a UNIX timestamp in ticks.</summary>
		public static long AsUnixTimeTicks(DateTime dateTime){
			
			return (dateTime.Ticks - UnixStartTicks);
			
		}
		
		/// <summary>The current unix time in ticks.</summary>
		public static ulong UnixTimeTicks{
			get{
				
				return (ulong)(DateTime.UtcNow.Ticks - UnixStartTicks);
				
			}
		}
		
	}

}