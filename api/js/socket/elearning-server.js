var utils = require('utils.js');
var server = require('server.js');

var copilotElearningSubscriptions = [];
var pollElearningClasses = [];

server.io.of('/elearning').on('connection', function(socket) {

  socket.on('watchLiveCopilot', function(data) {
    if ('undefined' == typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      copilotElearningSubscriptions[data.elearningSubscriptionId] = [];
    }
    copilotElearningSubscriptions[data.elearningSubscriptionId].push(sessionID);
    socket.join(data.elearningSubscriptionId);
    socket.send("You are now able to be watched.");
    socket.broadcast.to(data.elearningSubscriptionId).send("The subscription id: " + data.elearningSubscriptionId + " is now watched.");
  });

  socket.on('watchLiveResult', function() {
    socket.join('liveResultAdminPages');
  });

  socket.on('updateTab', function(data) {
    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < copilotElearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (copilotElearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateTab', {'elearningSubscriptionId': data.elearningSubscriptionId, 'elearningExercisePageId': data.elearningExercisePageId});
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('updateQuestion', function(data) {
    socket.broadcast.to('liveResultAdminPages').emit('updateResult', data);

    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < copilotElearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (copilotElearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateQuestion', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('updateWhiteboard', function(data) {
    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < copilotElearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (copilotElearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateWhiteboard', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('toggleParticipantWhiteboard', function(data) {
    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < copilotElearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (copilotElearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('toggleParticipantWhiteboard', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('watchLivePoll', function(data) {
    if ('undefined' == typeof pollElearningClasses[data.elearningClassId]) {
      pollElearningClasses[data.elearningClassId] = [];
    }
    pollElearningClasses[data.elearningClassId].push(sessionID);
    socket.join(data.elearningClassId);
    socket.send("You are now able to do a live poll.");
    socket.broadcast.to(data.elearningClassId).send("The class id: " + data.elearningClassId + " is now polled.");
  });

  socket.on('updatePoll', function(data) {
    socket.broadcast.to('livePollGroup').emit('updatePoll', data);

    if ('undefined' != typeof pollElearningClasses[data.elearningClassId]) {
      for(i = 0; i < pollElearningClasses[data.elearningClassId].length; i++) {
        if (pollElearningClasses[data.elearningClassId][i] == sessionID) {
          socket.broadcast.to(data.elearningClassId).emit('updatePoll', data);
          return;
        }
      }
    }
    socket.send("You are not being polled yet.");
  });

  socket.on('disconnect', function(data) {
    console.log("Disconnecting sessionID: " + sessionID);

    socket.leave('liveResultAdminPages');
    socket.leave('livePollGroup');

    for(i = 0; i < copilotElearningSubscriptions.length; i++) {
      if ('undefined' != typeof copilotElearningSubscriptions[i]) {
        for(j = 0; j < copilotElearningSubscriptions[i].length; j++) {
          if (copilotElearningSubscriptions[i][j] == sessionID) {
            console.log("Leaving the live watch for the elearning subscription: " + i);          
            copilotElearningSubscriptions[i].splice(j, 1);
            socket.leave(i);
          }
        }
      }
    }

    for(i = 0; i < pollElearningClasses.length; i++) {
      if ('undefined' != typeof pollElearningClasses[i]) {
        for(j = 0; j < pollElearningClasses[i].length; j++) {
          if (pollElearningClasses[i][j] == sessionID) {
            console.log("Leaving the poll for the elearning class: " + i);          
            pollElearningClasses[i].splice(j, 1);
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

