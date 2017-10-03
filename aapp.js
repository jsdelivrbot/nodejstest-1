net = require('net');

// Keep track of the chat clients
var clients = [];
var users = [];//{
    //'ss':'aaa'
//};
var Len=0;

// Start a TCP Server
net.createServer(function (socket) {

  // Identify this client
  socket.name = socket.remoteAddress + ":" + socket.remotePort 

  // Put this new client in the list
  clients.push(socket);

  // Send a nice welcome message and announce
  socket.write("Welcome " + socket.name + "\n");
  broadcast(socket.name + " joined the chat\n", socket);

  // Handle incoming messages from clients.
  
  socket.on('data', function (data) {
    var isReg=false;  
    var index=-1;
    for( var i=0 ; i<users.length ; i++)
    {
        if((users[i][0]==socket)&&(users[i][3]==1))
        {
            isReg=true;
            index=i;
        }
    }
    if(isReg)
    {
        console.log(users[index][1]+" is Reg.");
        return;
    }
    const Bdata=Buffer.from(data);
    var act=0;
      var k=Bdata.indexOf('&&n&');
      if(k!=-1)
          act=1;
      if(k==-1){
        k=Bdata.indexOf('^soc');
        if(k!=-1)   act=2;
      }
      if(k==-1){
        k=Bdata.indexOf('^Call');
        if(k!=-1)   act=3;
      }
      if(k==-1){
        k=Bdata.indexOf('^msg');
        if(k!=-1)   act=4;
      }
      switch(act)
      {
          case  1:
                  const buf1 = Buffer.allocUnsafe( Bdata.length-k-4);
                  Bdata.copy(buf1,0,k+4,Bdata.length);
                  users.push([socket,Buffer.from(buf1).toString(),'  ',0]);
                  console.log("name : "+buf1+"  >k= "+k+"  "+" | "+users.length);
                  break;
          case  2:
                const LL=Bdata.length-k-4;
                var bvb=Buffer.allocUnsafe(LL);
                for(var i=0;i<LL;i++)
                   bvb[i]=Bdata[i+k+4];
                console.log("\n "+bvb+"    --> Len= "+k);
                for( var i=0 ; i<users.length ; i++)
                {
                  if(users[i][0]===socket)
                  {
                    users[i][2]=bvb;
                    users[i][3]=1;
                   }
            
                  }
                  break;
          case  3:
                  
                  break;
          case  4:
                  
                var LL1=Bdata.indexOf('^',k+1);
                if(LL1!=-1)
                {
                    var nl=Bdata.indexOf('^',LL1+1);
                    bvb=Buffer.allocUnsafe(nl-LL1-1);
                    for(var i=0;i<nl-LL1-1;i++)
                      bvb[i]=Bdata[i+LL1+1];
                    const rre=Bdata.length-nl; 
                    var msg=Buffer.allocUnsafe(rre);
                    for(var i=nl+1;i<Bdata.length;i++)
                      msg[i-nl-1]=Bdata[i];
                    console.log("\n"+bvb+"    --> Len= "+rre+" |  "+msg+"\n");
                for( var i=0 ; i<users.length ; i++)
                {
                  console.log("|"+Buffer.from(users[i][1])+"| , |"+bvb+"| , ")  
                  if(Buffer.from(users[i][1]).indexOf(bvb)!=-1)
                  {
                     users[i][0].write("WWW."+msg);
                     console.log("\n ^MSG^ "+msg);
                   }
            
                  }
                    
                }
                  break;
      }
   //-- broadcast(socket.name + "> " + data+"\n", socket);
  });

  // Remove the client from the list when it leaves
  socket.on('end', function () {
    clients.splice(clients.indexOf(socket), 1);
    broadcast(socket.name + " left the chat.\n");
    users.forEach(function (user) {
          console.log(user[1]+"  :  "+user[2]);
        });
    for( var i=0 ; i<users.length ; i++)
    {
        if(users[i][0]==socket)
            users.splice(i,1);
    }
    console.log("\n--------------------------------\n")        ;
    users.forEach(function (user) {
          console.log(user[1]+"  :  "+user[2]);
        });
  });
  
  // Send a message to all clients
  function broadcast(message, sender) {
    clients.forEach(function (client) {
      // Don't want to send it to sender
      if (client === sender) return;
      client.write(message);
    });
    // Log it to the server output too
    process.stdout.write(message)
  }

}).listen(8080);

// Put a friendly message on the terminal of the server.
console.log("Chat server running at port 8080\n");
