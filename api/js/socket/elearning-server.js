// A user, be it a teacher or a participant, is identified by his PHP sessionID value, as each participant has logged in the application and received a PHP sessionID and a unique socket session id. The PHP sessionID is stored in a Redis server so that it can be accessed by the server side socket.

var utils = require('./utils.js');
var server = require('./server.js');

var copilotElearningSubscriptions = [];

server.io.of('/elearning').on('connection', function(socket) {
  console.log('The elearning socket server received a connection');

  // The copilot feature allows a teacher and his participant to do an exercise together, and to see in real time the exercise being done. The two of them can provide answers to the exercise, and the other can see the provided answers live. They can also change the current page of questions. And they can use a shared whiteboard. For this feature, the teacher and his participant are confined into a socket room, with the room name being the participant's elearningSubscriptionId value.

  socket.on('watchLiveCopilot', function(data) {
    if ('undefined' == typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      copilotElearningSubscriptions[data.elearningSubscriptionId] = [];
    }
    copilotElearningSubscriptions[data.elearningSubscriptionId].push(sessionID);
    socket.join(data.elearningSubscriptionId);
    socket.send("You are now able to be watched.");
    socket.broadcast.to(data.elearningSubscriptionId).send("The subscription id: " + data.elearningSubscriptionId + " is now watched.");
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

  socket.on('clearWhiteboard', function(data) {
    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < copilotElearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (copilotElearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('clearWhiteboard', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('showParticipantWhiteboard', function(data) {
    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < copilotElearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (copilotElearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('showParticipantWhiteboard', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  socket.on('hideParticipantWhiteboard', function(data) {
    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for(i = 0; i < copilotElearningSubscriptions[data.elearningSubscriptionId].length; i++) {
        if (copilotElearningSubscriptions[data.elearningSubscriptionId][i] == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('hideParticipantWhiteboard', data);
          return;
        }
      }
    }
    socket.send("You are not being watched live yet.");
  });

  // The live results feature allows an administrator to watch the results of an exercise being done, in real time. Any administrator can watch any participant doing an exercise. Note that an administrator is not to be confused with a teacher.
  socket.on('watchLiveResult', function() {
    socket.join('liveResultAdminPages');
  });

  socket.on('disconnect', function(data) {
    console.log("Disconnecting sessionID: " + sessionID);

    socket.leave('liveResultAdminPages');

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

  });

  // Store the session variables from the handshake
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

