var utils = require('utils.js');
var server = require('server.js');

var elearningSubscriptions = [];

server.io.of('/elearning').on('connection', function(socket) {

  socket.on('watchLiveCopilot', function(data) {
    if ('undefined' == typeof elearningSubscriptions[data.elearningSubscriptionId]) {
      elearningSubscriptions[data.elearningSubscriptionId] = [];
    }
    elearningSubscriptions[data.elearningSubscriptionId].push(sessionID);
    socket.join(data.elearningSubscriptionId);
    socket.send("You are now able to be watched.");
    socket.broadcast.to(data.elearningSubscriptionId).send("The subscription id: " + data.elearningSubscriptionId + " is now watched.");
  });

  socket.on('watchLiveResult', function() {
    socket.join('liveResultAdminPages');
  });

  socket.on('updateTab', function(data) {
    if ('undefined' != typeof elearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < elearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (elearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateTab', {'elearningSubscriptionId': data.elearningSubscriptionId, 'elearningExercisePageId': data.elearningExercisePageId});
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('updateQuestion', function(data) {
    socket.broadcast.to('liveResultAdminPages').emit('updateResult', data);

    if ('undefined' != typeof elearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < elearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (elearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateQuestion', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('updateWhiteboard', function(data) {
    if ('undefined' != typeof elearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < elearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (elearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateWhiteboard', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('watchLivePoll', function() {
    socket.join('livePollGroup');
  });

  socket.on('disconnect', function(data) {
    console.log("Disconnecting sessionID: " + sessionID);

    socket.leave('liveResultAdminPages');
    socket.leave('livePollGroup');

    for(i = 0; i < elearningSubscriptions.length; i++) {
      if ('undefined' != typeof elearningSubscriptions[i]) {
        for(j = 0; j < elearningSubscriptions[i].length; j++) {
          if (elearningSubscriptions[i][j] == sessionID) {
            console.log("Leaving the live watch for the elearning subscription: " + i);          
            elearningSubscriptions[i].splice(j, 1);
            socket.leave(i);
          }
        }
      }
    }
  });

  // Store the session variables from the handshake as this data 
  // is not available in the "disconnect" handler
  var sessionID = socket.handshake.sessionID;
  socket.set('sessionID', sessionID, function() { 
    console.log('Set the sessionID: ', sessionID);
  });

  // Avoid a session time out on the redis server
  // Simply reload the session data and touch its timestamp
  var intervalID = setInterval(function() {
    if ('undefined' != typeof socket.handshake) {
      if ('undefined' != typeof socket.handshake.session) {
        socket.handshake.session.reload(function() {
          socket.handshake.session.touch().save();
          console.log("Reloaded the session for " + socket.handshake.address + ":" + socket.handshake.port);
        });
      }
    }
  }, 5 * 60 * 1000);

});

