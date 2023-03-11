require('dotenv').config()
const axios = require('axios');
const  cors = require('cors');
var app = require('express')();

const apiURL     = process.env.API_URL;
const corsHost   = process.env.CORS_HOST;
const socketPath = process.env.SOCKET_PATH;
const port       = process.env.PORT;
const socketChannel = process.env.SOCKET_CHANNEL

app.use(cors());

console.log("ENV", process.env );
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

// const apiURL = 'http://dev-redflag.concentrix.com/api/';
console.log("NOTICE : CURRENTLY RUNNING ON SAFE MODE");
console.log("WIPEOUT AND LOCK IS CURRENTLY DISABLED");
console.log("LISTENING TO : ", socketChannel);
redis.subscribe(socketChannel, function(err, count) {
	
});
redis.on('message', function(channel, message) {
    console.log('channel: ' + channel);
    console.log('Message Recieved: ' + message);
    message = JSON.parse(message);
    let clientTarget = false;
    let eventName = 'agent:connect';
    const eventRef = {
        "App\\Modules\\WorkstationModule\\Events\\AgentLogsRequestBroadcast" : 'agent:logs-request',
        "App\\Modules\\WorkstationModule\\Events\\AgentWorkstationMonitoringRequestBroadcast" : 'agent:monitoring-request',
        "App\\Modules\\WorkstationModule\\Events\\AgentHostfileBroadcast" : 'agent:hostfile-update',
        "App\\Modules\\WorkstationModule\\Events\\AgentAutoMOSBroadcast" : 'agent:auto-mos',
        "App\\Modules\\WorkstationModule\\Events\\AgentMOSRequestBroadcast" : 'agent:mos-request',
        "App\\Modules\\WorkstationModule\\Events\\AgentAppUpdateBroadcast" : 'agent:update-app',
        "App\\Modules\\WorkstationModule\\Events\\AgentLiteModeBroadcast" : 'agent:lite-mode',
        "App\\Modules\\WorkstationModule\\Events\\AgentRemoveScheduledWipeoutBroadcast" : "agent:remove-scheduled-wipeout",
        "App\\Modules\\WorkstationModule\\Events\\DeviceStatusBroadcast" : 'agent:status-check',
        "App\\Modules\\WorkstationModule\\Events\\AgentCommandLineBroadcast" : 'agent:web-cmd',
        "App\\Modules\\WorkstationModule\\Events\\AgentVpnApprovalBroadcast" : 'agent:vpn-approval',
        "App\\Modules\\Maintenance\\Events\\AgentDeploymentApplicationBroadcast" : 'agent:deployment-application',
        "App\\Modules\\Maintenance\\Events\\AgentLoginDeploymentApplicationBroadcast" : 'agent:login-deployment-application',
        "App\\Modules\\Maintenance\\Events\\AgentSoftwareUpdateBroadcast" : 'agent:software-update'
    };


    switch( message.event ){
        // TODO : Refactor by including this into eventRef
        case "App\\Modules\\WorkstationModule\\Events\\AgentDisconnectedBroadcast":
        case "App\\Modules\\WorkstationModule\\Events\\AgentDashboardBroadcast":
            eventName = 'dashboard:agent-list';
            break;
        case "App\\Modules\\Flags\\Events\\DashboardUpdatedBroadcast":
            eventName = 'dashboard:summary-list';
            break;
            
        // TODO : Refactor by including this into eventRef
        case "App\\Modules\\WorkstationModule\\Events\\AgentMTRUpdatedBroadcast":
            eventName = 'dashboard:agent-mtr-updated';
            break;

        // TODO : Refactor by including this into eventRef
        case "App\\Modules\\Flags\\Events\\AgentWorkstationProgressBroadcast":
            eventName  = 'dashboard:agent-workstation-progress';
            break;

        // TODO : Refactor by including this into eventRef
        case "App\\Modules\\Flags\\Events\\AgentWorkstationUpdatedBroadcast":
            eventName  = 'dashboard:agent-workstation-updated';
            break;

        // TODO : Refactor by including this into eventRef
        case "App\\Modules\\Flags\\Events\\DesktopNotificationBroadcast":
            eventName  = 'agent:push-notification';
            clientTarget = message.data['session_id'];

            break;
        
        // TODO : Refactor by including this into eventRef
        case "App\\Modules\\WorkstationModule\\Events\\AgentMTRRequestBroadcast":
            console.log("MESSAGE", message);

            eventName = 'agent:mtr-request';
            clientTarget = message.data['data']['session_id'];
	        console.log("TARGET", clientTarget);
            break;
            
        // TODO : Refactor by including this into eventRef
        case "App\\Modules\\Flags\\Events\\AgentWorkstationRequestBroadcast":
            eventName = 'agent:workstation-request';
            clientTarget = message.data['data']['session_id'];
            break;

        case "App\\Modules\\WorkstationModule\\Events\\DeviceStatusBroadcast":
            eventName = 'agent:status-check';
            clientTarget = message.data['session_id'];
            break;

        case "App\\Modules\\WorkstationModule\\Events\\AgentCommandLineBroadcast":
            eventName = 'agent:web-cmd';
            clientTarget = message.data['session_id'];
        break;

        case "App\\Modules\\WorkstationModule\\Events\\AgentVpnApprovalBroadcast":
            eventName = 'agent:vpn-approval';
            clientTarget = message.data['session_id'];
        break;

        case "App\\Modules\\Maintenance\\Events\\AgentSoftwareUpdateBroadcast":
            eventName = 'agent:software-update';
            clientTarget = message.data['session_id'];
        break;

        case "App\\Modules\\Maintenance\\Events\\AgentDeploymentApplicationBroadcast":
            eventName = 'agent:deployment-application';
            clientTarget = message.data['session_id'];
        break;

        case "App\\Modules\\Maintenance\\Events\\AgentLoginDeploymentApplicationBroadcast":
            eventName = 'agent:login-deployment-application';
            clientTarget = message.data['session_id'];
            console.log("Deployment ", clientTarget);
        break;
            
        default: 
            eventName = eventRef[ message.event ];
            clientTarget = message.data['session_id'];
            break;
        ;
    }
    console.log("EVENTNAME", eventName);
    console.log("EMIT : ", message.data );
    if( clientTarget ){
        io.to( clientTarget ).emit( eventName, message.data );
        return;
    }

    io.emit( eventName, message.data);
});

io.sockets.on('connection', function (socket) {
    console.log("CONNECTION ID : " + socket.id );
    socket.on('disconnect', function () {
        console.log("DISCONNECTED : ", socket.id) ;

          let path = '/workstation/connected-agents/disconnect?session_id=' + socket.id;

          axios.delete( apiURL + path ).then( function(response){
            console.log("TAGGED AS INACTIVE : " , socket.id );
          }, function(error){
            console.log("ERROR ON AGENT DISCONNECTION: ", socket.id);;
          });
    });
});

http.listen(port, function(){
    console.log('Listening on Port ' + port);
});
