var http = require('http');
var https = require('https');
var connect = require('connect');
var cookie = require('cookie');
var path = require('path');
var fs = require('fs');
var redis = require('redis');
var ioredis = require('socket.io-redis');
var socketio = require('socket.io');

var utils = require('./utils.js');
var config = require('./config');

var sslKey = '';
var sslCertificate = '';
var sslChain = '';
if (fs.existsSync(config.ssl.path + config.ssl.key)) {
  sslKey = fs.readFileSync(path.resolve(config.ssl.path + config.ssl.key));
  sslCertificate = fs.readFileSync(path.resolve(config.ssl.path + config.ssl.certificate));
  sslChain = fs.readFileSync(path.resolve(config.ssl.path + config.ssl.chain));
  console.log("The virtual host HAS an SSL private key");
} else {
  console.log("The virtual host DOESN'T have an SSL private key");
}

if (sslKey) {
  console.log("Configuring the server for HTTPS");
  var options = {
    key: sslKey,
    cert: sslCertificate,
    ca: sslChain,
    requestCert: false,
    rejectUnauthorized: false
  };
  var httpsServer = https.createServer(options, utils.httpHandler);
  httpsServer.listen(config.socketio.sslport, function() {
    console.log('The NodeJS HTTPS server [port: ' + config.socketio.sslport + '] is listening...');
  });

  module.exports.io = socketio(httpsServer, {
    maxHttpBufferSize: 1e3,
    cors: {
      origin: 'http://localhost:80',
      methods: ["GET", "POST"],
      credentials: true
    },
    cookie: {
      name: 'PHPSESSID',
      httpOnly: false,
      path: "/"
    }
  });
} else {
  console.log("Configuring the server for HTTP");
  var httpServer = http.createServer(utils.httpHandler);
  httpServer.listen(config.socketio.port, function() {
    console.log('The NodeJS HTTP server [port: ' + config.socketio.port + '] is listening...');
  });

  module.exports.io = socketio(httpServer, {
    maxHttpBufferSize: 1e3,
    cors: {
      origin: 'http://localhost:80',
      methods: ["GET", "POST"],
      credentials: true
    },
    cookie: {
      name: 'PHPSESSID',
      httpOnly: false,
      path: "/"
    }
  });
}

module.exports.io.adapter(ioredis({ host: config.redis.hostname, port: config.redis.port }));
var redisClient = redis.createClient(config.redis.port, config.redis.hostname);

// When a client socket attempts to connect, it sends the cookies in its handshake. By comparing the unique socket session id sent in a handshake cookie, with the one already stored in the Redis store, we can make sure that the socket attempting to connect, is originating from a legitimate logged in user. When the user logged in the application, a socket session id was created and saved in the Redis store. The Redis store acting as the PHP session store, it keeps all the logged in user session variables under the PHP sessionID value. The socketSessionId is to have a unique id per client. Note that, because the socket.id is renewed on each client page refresh, it cannot be used, and a custom unique client id socketSessionId is being used.
module.exports.io.of('/elearning').use((socket, handler) => {
  console.log('The main namespace middleware is called');
  console.log(socket.handshake.headers.cookie);
  if (socket.handshake.headers.cookie) {
    var cookies = cookie.parse(decodeURIComponent(socket.handshake.headers.cookie));
    socket.request.sessionID = cookies['PHPSESSID'];
    socket.request.socketSessionId = cookies['socketSessionId'];
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
var httpHandleServerPostRequest = function(data) {
  console.log("Received posted data: ", data);
  var stringified = JSON.stringify(data);
  var postedData = JSON.parse(stringified);

  // Send a message to the root namespace
  server.io.of('/').emit('myDemoMessage', {'username': "From the NodeJS http server root namespace... firstname: " + postedData.firstname + " lastname: " + postedData.lastname});

  // Send a message to the /demo namespace
  server.io.of('/demo').emit('myNotepadMessage', {'notepad': "Some content for the notepad...", 'firstname': postedData.firstname, 'lastname': postedData.lastname});
};
