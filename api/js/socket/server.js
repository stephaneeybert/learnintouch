var http = require('http');
var connect = require('connect');
var cookie = require('cookie');
var socketio = require('socket.io');
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
  console.log('The NodeJS is set up');
});

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
  console.log('The NodeJS server [port: ' + portNumber + '] is listening...');
});

// When a client socket attempts to connect, it sends the cookies in its handshake. By comparing the unique socket session id sent in a handshake cookie, with the one already stored in the Redis store, we can make sure that the socket attempting to connect, is originating from a legitimate logged in user. When the user logged in the application, a socket session id was created and saved in the Redis store. The Redis store acting as the PHP session store, it keeps all the logged in user session variables under the PHP sessionID value. The socketSessionId is to have a unique id per client. Note that, because the socket.id is renewed on each client page refresh, it cannot be used, and a custom unique client id socketSessionId is being used.
module.exports.io.set('authorization', function (handshakeData, handler) {
  if (handshakeData.headers.cookie) {
    handshakeData.cookies = cookie.parse(decodeURIComponent(handshakeData.headers.cookie));
    handshakeData.sessionID = handshakeData.cookies['PHPSESSID'];
    handshakeData.socketSessionId = handshakeData.cookies['socketSessionId'];
    console.log("Authorization attempt with sessionID: " + handshakeData.sessionID + " and socketSessionId: " + handshakeData.socketSessionId);
    redisClient.get("PHPREDIS_SESSION:" + handshakeData.sessionID, function (error, reply) {
      if (error) {
        console.log("The redis client had an error: " + error);
        return handler('The connection was refused because the redis client had an error.', false);
      } else if (!reply) {
        console.log('The connection was refused because the redis client did not find the sessionID.');
        return handler('The connection was refused because the redis client did not find the sessionID.', false);
      } else {
        var redisSocketSessionId = utils.getRedisValue(reply, "socketSessionId");
        if ('undefined' == typeof handshakeData.socketSessionId || redisSocketSessionId != handshakeData.socketSessionId) {
          console.log('The connection was refused because the socketSessionId was invalid.');
          return handler('The connection was refused because the socketSessionId was invalid.', false);
        } else {
          console.log('The connection was granted.');
          handler(null, true);
        }
      }
    });
  } else {
    console.log('The connection was refused because no cookie was transmitted.');
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
