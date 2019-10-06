
var express = require('express');
var app = express();
var server = require('http').createServer(app);
var io = require('socket.io')(server);
io.set('transports', [ 'websockets', 'polling' ]);
var util = require("util");

io.on('connection', function(socket){
	
	socket.on('product_infos_request', function(data) {
		console.log('product infos request received');
		io.sockets.emit('process_product_request_from_off', {'user_login': data.user_login,'element_id': data.element_id,'file_name': data.file_name});
	});
	
	socket.on('product_from_off_response', function(data) {
		console.log('product_from_off_response received. Data : ' + data);
		io.sockets.emit('product_infos_response',data);
	});

	//io.sockets.emit('ask_for_new_ordering', { status: lowLevel, quantity: leftQuantity});
});

 
//Pour servir la page static
app.use(express.static(__dirname));

server.listen(9000);
