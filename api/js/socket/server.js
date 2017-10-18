var http = require('http');
var connect = require('connect');
var cookie = require('cookie');
var redis = require('redis');
var ioredis = require('socket.io-redis');
var socketio = require('socket.io');

var utils = require('./utils.js');
var config = require('./config');

var httpServer = http.createServer(utils.httpHandler);

var SOCKETIO_PORT = 9001;
httpServer.listen(SOCKETIO_PORT, function() {
  console.log('The NodeJS server [port: ' + SOCKETIO_PORT + '] is listening...');
});
module.exports.io = socketio.listen(httpServer);
  
module.exports.io.adapter(ioredis({ host: config.redis.hostname, port: config.redis.port }));
var redisClient = redis.createClient(config.redis.port, config.redis.hostname);

// When a client socket attempts to connect, it sends the cookies in its handshake. By comparing the unique socket session id sent in a handshake cookie, with the one already stored in the Redis store, we can make sure that the socket attempting to connect, is originating from a legitimate logged in user. When the user logged in the application, a socket session id was created and saved in the Redis store. The Redis store acting as the PHP session store, it keeps all the logged in user session variables under the PHP sessionID value. The socketSessionId is to have a unique id per client. Note that, because the socket.id is renewed on each client page refresh, it cannot be used, and a custom unique client id socketSessionId is being used.
module.exports.io.use(function (socket, handler) {
  if (socket.request.headers.cookie) {
    socket.request.cookies = cookie.parse(decodeURIComponent(socket.request.headers.cookie));
    socket.request.sessionID = socket.request.cookies['PHPSESSID'];
    socket.request.socketSessionId = socket.request.cookies['socketSessionId'];
    console.log("Authorization attempt with sessionID: " + socket.request.sessionID + " and socketSessionId: " + socket.request.socketSessionId);
    redisClient.get("PHPREDIS_SESSION:" + socket.request.sessionID, function (error, reply) {
      if (error) {
        console.log("The redis client had an error: " + error);
        return handler(new Error('The connection was refused because the redis client had an error.'));
      } else if (!reply) {
        console.log('The connection was refused because the redis client did not find the sessionID.');
        return handler(new Error('The connection was refused because the redis client did not find the sessionID.'));
      } else {
        var redisSocketSessionId = utils.getRedisValue(reply, "socketSessionId");
        if ('undefined' == typeof socket.request.socketSessionId || redisSocketSessionId != socket.request.socketSessionId) {
          console.log('The connection was refused because the socketSessionId was invalid.');
          return handler(new Error('The connection was refused because the socketSessionId was invalid.'));
        } else {
          console.log('The connection was granted.');
          handler();
        }
      }
    });
  } else {
    console.log('The connection was refused because no cookie was transmitted.');
    return handler(new Error('The connection was refused because no cookie was transmitted.'));
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
