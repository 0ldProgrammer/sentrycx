require('dotenv').config()
const axios = require('axios');
const  cors = require('cors');
var app = require('express')();

const apiURL     = process.env.API_URL;
const corsHost   = process.env.CORS_HOST;
const socketPath = process.env.ADMIN_LAPS_SOCKET_PATH;
const port       = process.env.ADMIN_LAPS_PORT;
const socketChannel = process.env.ADMIN_LAPS_SOCKET_CHANNEL;

app.use(cors());

app.use(function (req, res, next) {

    // Website you wish to allow to connect
    res.setHeader('Access-Control-Allow-Origin', corsHost );

    // Request methods you wish to allow
    res.setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS, PUT, PATCH, DELETE');

    // Request headers you wish to allow
    res.setHeader('Access-Control-Allow-Headers', 'X-Requested-With,content-type');

    // Set to true if you need the website to include cookies in the requests sent
    // to the API (e.g. in case you use sessions)
    res.setHeader('Access-Control-Allow-Credentials', true);

    // Pass to next layer of middleware
    next();
});
var http = require('http').Server(app);

var io = require('socket.io')(http,  {
	origins:'*:*',
	path: socketPath
});
var Redis = require('ioredis');
var redis = new Redis();

console.log("LISTENING TO : ", socketChannel);
redis.subscribe(socketChannel, function(err, count) {
	
});
redis.on('message', function(channel, message) {
    const eventRef = {
        "App\\Modules\\WorkstationModule\\Events\\AdminLapsRequestBroadcast" : 'admin-laps:request',
    };
    console.log('channel: ' + channel);
    console.log('Message Recieved: ' + message);
    message = JSON.parse(message);

    const eventName = eventRef[ message.event ];
    console.log("EVENT NAME", eventName);
    io.emit( eventName, message.data);
});


io.sockets.on('connection', function (socket) {
    console.log("CONNECTION ID : " + socket.id );
    console.log("INFO : Admin Laps App connected");
    socket.on('disconnect', function () {
        console.log("CONNECTION ID : " + socket.id );
        console.log("WARNING : No Admin Lapse Running ");
    })
});

http.listen(port, function(){
    console.log('Listening on Port ' + port);
});
