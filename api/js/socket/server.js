var http = require('http');
var connect = require('connect');
var cookie = require('cookie');
var socketio = require('socket.io');
//var sessionSocketio = require('session.socket.io');
var redis = require('redis')

var utils = require('./utils.js');

var PORT = 9001;
var portNumber = process.argv[2] || PORT;

var httpServer = http.createServer(utils.httpHandler);

module.exports.io = socketio.listen(httpServer).configure(function () {
  this.enable('browser client minification');  // send minified client
  this.enable('browser client etag');          // apply etag caching logic based on version number
  this.enable('browser client gzip');          // gzip the file
  this.set('log level', 1);                    // reduce logging
  this.set('transports', [                     // enable all transports (optional if you want flashsocket)
    'websocket',
    'flashsocket',
    'htmlfile',
    'xhr-polling',
    'jsonp-polling'
    ]);
});

// By default configuration, socket.io uses the memory (MemoryStore) to hold open connections. Hence it is not possible to run several socket.io processes, because the processes won't know nothing about the open connections of the other processes. But it's quite easy to use redis as store, which allows to share the open connections between several socket.io processes, which run on different ports.
var redisClient = redis.createClient();
var pub = redis.createClient();
var sub = redis.createClient();
var RedisStore = require('socket.io/lib/stores/redis');
var redisStore = new RedisStore({
  ttl: 1800,
  redisPub: pub,
  redisSub: sub,
  client:redisClient
});
module.exports.io.set('store', redisStore);

httpServer.listen(portNumber, function() {
  console.log('The NodeJS http server is listening...');
});

module.exports.io.set('authorization', function (handshakeData, handler) {
  if (handshakeData.headers.cookie) {
    handshakeData.cookies = cookie.parse(decodeURIComponent(handshakeData.headers.cookie));
    handshakeData.sessionID = handshakeData.cookies['PHPSESSID'];
    handshakeData.socketSessionId = handshakeData.cookies['socketSessionId'];
    console.log("Authorization with sessionID: " + handshakeData.sessionID);
    console.log("Authorization with socketSessionId: " + handshakeData.socketSessionId);
    redisClient.get("PHPREDIS_SESSION:" + handshakeData.sessionID, function (error, reply) {
      if (error) {
        console.log("The redis client had an error: " + error);
        return handler('The connection was refused because the redis client had an error.', false);
      } else if (!reply) {
        return handler('The connection was refused because the redis client did not find the session id.', false);
      } else {
        var redisSocketSessionId = utils.getRedisValue(reply, "socketSessionId");
        if ('undefined' == typeof handshakeData.socketSessionId || redisSocketSessionId != handshakeData.socketSessionId) {
          return handler('The connection was refused because the session id was invalid.', false);
        } else {
          handler(null, true);
        }
      }
    });
  } else {
    return handler('The connection was refused because no cookie was transmitted.', false);
  }
});

// Handle a POST http request sent to the Node.js server
httpHandleServerPostRequest = function(data) {
  console.log("Received posted data: ", data);
  var stringified = JSON.stringify(data);
  var postedData = JSON.parse(stringified);

  // Send a message to the root namespace
  server.io.sockets.emit('myDemoMessage', {'username': "From the NodeJS http server root namespace... firstname: " + postedData.firstname + " lastname: " + postedData.lastname});

  // Send a message to the /demo namespace
  server.io.of('/demo').emit('myNotepadMessage', {'notepad': "Some content for the notepad...", 'firstname': postedData.firstname, 'lastname': postedData.lastname});
};