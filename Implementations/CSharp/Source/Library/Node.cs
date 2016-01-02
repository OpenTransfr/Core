//--------------------------------------
//              OpenTransfr
//    For copyright info etc see the License
//--------------------------------------

using System;


namespace OpenTransfr{
	
	/// <summary>
	/// The base class for running nodes.
	/// See e.g. NodeTypes/Root.cs (the RootNode class).
	/// </summary>
	
	public class Node{
		
		/// <summary>The current running node. E.g. this is a root node, or a broadcast node etc.
		/// Note that no node could be running - you can instead just connect to the network in a readonly way.</summary>
		public static Node Current;
		
		
		/// <summary>The name of this type of node. Always lowercase, e.g. 'root' or 'broadcast'.</summary>
		public virtual string Name{
			get{
				return null;
			}
		}
		
	}
	
}