//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;
using System.IO;
using System.Threading;
using System.Collections.Generic;
using System.Text;
using OpenTransfr;

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
		Directory.SetCurrentDirectory(Path.GetDirectoryName(System.Reflection.Assembly.GetExecutingAssembly().Location));
		
		// Setup args:
		Arguments.Load(args);
		
		// Which type of node, if any, are we running as?
		// 'archive', 'broadcast' or 'root'. Default is 'broadcast'.
		string nodeType=Arguments.Get("node");
		
		if(nodeType!=null){
			
			// Make sure it's lowercase:
			nodeType=nodeType.ToLower().Trim();
		
		}
		
		switch(nodeType){
			
			case "root":
			
				// Operate as a root node:
				Node.Current=new RootNode();
				
			break;
			
			case "archive":
				
				// Operate as an archive node:
				Node.Current=new ArchiveNode();
				
			break;
			
			default:
			case "broadcast":
				
				// Operate as a broadcast node:
				Node.Current=new BroadcastNode();
				
			break;
			
		}
		
		// Log a message:
		Log.Add("Operating as a '"+Node.Current.Name+"' node.");
		
		// Start it!
		Node.Current.Start();
		
        // Nighty night
		Thread.Sleep(Timeout.Infinite);
	}
}