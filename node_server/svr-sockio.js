/*var io = require('socket.io').listen(8000);

io.sockets.on('connection', function (socket) {
    console.log('user connected!');

    socket.on('foo', function (data) {
        console.log('here we are in action event and data is: ' + data);
    });
});*/

var server     = require('http').createServer();
var io         = require('socket.io')(server);
var port       = 8000;
var clients = {};
var ipAddress = '192.168.193.172';

server.listen(port, ipAddress, function(){
    var hosth = server.address().address;
    var porth = server.address().port;
    console.log('running at http://' + hosth + ':' + porth)
});


io.on('connection', function (socket){
    console.log('Connected socket ' + socket.id);
	var clientIp = socket.request.connection.remoteAddress;
    console.log('New connection from ' + clientIp);

    socket.on('add-user', function(data){
        clients[data.username] = {
            "socket": socket.id
        };
    });

    socket.on('private-message', function(data){
        console.log("Sending: " + data.content + " to " + data.username);
        if (clients[data.username]){
            io.sockets.connected[clients[data.username].socket].emit("add-message", data);
        } else {
            console.log("User does not exist: " + data.username);
        }
    });

    socket.on('disconnect', function () {
        console.log('Disconnected socket ' + socket.id);
        for(var name in clients) {
            if(clients[name].socket === socket.id) {
                delete clients[name];
                break;
            }
        }
    });

    socket.on('broadcast', function (message) {
        console.log('broadcast ' + JSON.stringify(message));
        socket.broadcast.emit('new-presensi', JSON.stringify(message));
    });
});

//server.listen(port);