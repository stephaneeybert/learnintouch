// A user, be it a teacher or a participant, is identified by his PHP sessionID value, as each participant has logged in the application and received a PHP sessionID and a unique socket session id. The PHP sessionID is stored in a Redis server so that it can be accessed by the server side socket.

var utils = require('./utils.js');
var server = require('./server.js');

var copilotElearningSubscriptions = [];
var copilotElearningClasses = [];

// Listen on the elearning namespace
server.io.of('/elearning').on('connection', function(socket) {
  console.log('The elearning socket server received a connection');

  // The copilot feature allows a teacher and his participant to do an exercise together, and to see in real time the exercise being done. The two of them can provide answers to the exercise, and the other can see the provided answers live. They can also change the current page of questions. And they can use a shared whiteboard. For this feature, the teacher and his participant are confined into a socket room, with the room name being the participant's elearningClassId or elearningSubscriptionId value.

  socket.on('watchLiveCopilot', function(data) {
    // The room needs to be joined, not only for the teacher to watch the answers live, but also to share the whiteboard
    // Join the room named with the class id or alternatively with the subscription id
    if ('undefined' != typeof data.elearningSubscriptionId) {
      if ('undefined' == typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
        copilotElearningSubscriptions[data.elearningSubscriptionId] = {};
      }
      // There are multiple sockets (one for each client) for a subscription, all of these sockets having the same session
      // That is: subscription-> one socket id per client -> same session
      copilotElearningSubscriptions[data.elearningSubscriptionId][socketSessionId] = sessionID;
      socket.join(data.elearningSubscriptionId);
      socket.broadcast.to(data.elearningSubscriptionId).send("The subscription id: " + data.elearningSubscriptionId + " is now watched.");
      socket.send("You are notified by the class " + data.elearningClassId);
    }
    if ('undefined' != typeof data.elearningClassId) {
      if ('undefined' == typeof copilotElearningClasses[data.elearningClassId]) {
        copilotElearningClasses[data.elearningClassId] = {};
      }
      // There are multiple sockets (one for each client) for a class, all of these sockets having the same session
      // That is: class-> one socket id per client -> same session
      copilotElearningClasses[data.elearningClassId][socketSessionId] = sessionID;
      socket.join(data.elearningClassId);
      socket.broadcast.to(data.elearningClassId).send("The class id: " + data.elearningClassId + " is now watched.");
      socket.send("You are notified by the subscription " + data.elearningSubscriptionId);
    }
  });

  socket.on('updateTab', function(data) {
    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for (var socketSessionId in copilotElearningSubscriptions[data.elearningSubscriptionId]) {
        var currentSessionID = copilotElearningSubscriptions[data.elearningSubscriptionId][socketSessionId];
        if (currentSessionID == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateTab', {'elearningSubscriptionId': data.elearningSubscriptionId, 'elearningExercisePageId': data.elearningExercisePageId});
          return;
        }
      }
    }
    socket.send("updateTab: You are not being watched live yet.");
  });

  socket.on('updateQuestion', function(data) {
    socket.broadcast.to('liveResultAdminPages').emit('updateResult', data);

    if ('undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
      for (var socketSessionId in copilotElearningSubscriptions[data.elearningSubscriptionId]) {
        var currentSessionID = copilotElearningSubscriptions[data.elearningSubscriptionId][socketSessionId];
        if (currentSessionID == sessionID) {
          socket.broadcast.to(data.elearningSubscriptionId).emit('updateQuestion', data);
          return;
        }
      }
    }
    socket.send("updateQuestion: You are not being watched live yet.");
  });

  socket.on('updateWhiteboard', function(data) {
    var alreadySent = false;
    if ('undefined' != typeof data.elearningClassId && 'undefined' != typeof copilotElearningClasses[data.elearningClassId]) {
      for (var socketSessionId in copilotElearningClasses[data.elearningClassId]) {
        var currentSessionID = copilotElearningClasses[data.elearningClassId][socketSessionId];
        if (currentSessionID == sessionID) {
          socket.broadcast.to(data.elearningClassId).emit('updateWhiteboard', data);
          alreadySent = true;
        }
      }
    }
    if (false == alreadySent) {
      if ('undefined' != typeof data.elearningSubscriptionId && 'undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
        for (var socketSessionId in copilotElearningSubscriptions[data.elearningSubscriptionId]) {
          var currentSessionID = copilotElearningSubscriptions[data.elearningSubscriptionId][socketSessionId];
          if (currentSessionID == sessionID) {
            socket.broadcast.to(data.elearningSubscriptionId).emit('updateWhiteboard', data);
          }
        }
      }
    }
    socket.send("updateWhiteboard: You are not being watched live yet.");
  });

  socket.on('clearWhiteboard', function(data) {
    var alreadySent = false;
    if ('undefined' != typeof data.elearningClassId && 'undefined' != typeof copilotElearningClasses[data.elearningClassId]) {
      for (var socketSessionId in copilotElearningClasses[data.elearningClassId]) {
        var currentSessionID = copilotElearningClasses[data.elearningClassId][socketSessionId];
        if (currentSessionID == sessionID) {
          socket.broadcast.to(data.elearningClassId).emit('clearWhiteboard', data);
          alreadySent = true;
        }
      }
    }
    if (false == alreadySent) {
      if ('undefined' != typeof data.elearningSubscriptionId && 'undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
        for (var socketSessionId in copilotElearningSubscriptions[data.elearningSubscriptionId]) {
          var currentSessionID = copilotElearningSubscriptions[data.elearningSubscriptionId][socketSessionId];
          if (currentSessionID == sessionID) {
            socket.broadcast.to(data.elearningSubscriptionId).emit('clearWhiteboard', data);
          }
        }
      }
    }
    socket.send("clearWhiteboard: You are not being watched live yet.");
  });

  socket.on('showParticipantWhiteboard', function(data) {
    var alreadySent = false;
    if ('undefined' != typeof data.elearningClassId && 'undefined' != typeof copilotElearningClasses[data.elearningClassId]) {
      for (var socketSessionId in copilotElearningClasses[data.elearningClassId]) {
        var currentSessionID = copilotElearningClasses[data.elearningClassId][socketSessionId];
        if (currentSessionID == sessionID) {
          socket.broadcast.to(data.elearningClassId).emit('showParticipantWhiteboard', data);
          alreadySent = true;
        }
      }
    }
    if (false == alreadySent) {
      if ('undefined' != typeof data.elearningSubscriptionId && 'undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
        for (var socketSessionId in copilotElearningSubscriptions[data.elearningSubscriptionId]) {
          var currentSessionID = copilotElearningSubscriptions[data.elearningSubscriptionId][socketSessionId];
          if (currentSessionID == sessionID) {
            socket.broadcast.to(data.elearningSubscriptionId).emit('showParticipantWhiteboard', data);
          }
        }
      }
    }
    socket.send("showParticipantWhiteboard: You are not being watched live yet.");
  });

  socket.on('hideParticipantWhiteboard', function(data) {
    var alreadySent = false;
    if ('undefined' != typeof data.elearningClassId && 'undefined' != typeof copilotElearningClasses[data.elearningClassId]) {
      for (var socketSessionId in copilotElearningClasses[data.elearningClassId]) {
        var currentSessionID = copilotElearningClasses[data.elearningClassId][socketSessionId];
        if (currentSessionID == sessionID) {
          socket.broadcast.to(data.elearningClassId).emit('hideParticipantWhiteboard', data);
          alreadySent = true;
        }
      }
    }
    if (false == alreadySent) {
      if ('undefined' != typeof data.elearningSubscriptionId && 'undefined' != typeof copilotElearningSubscriptions[data.elearningSubscriptionId]) {
        for (var socketSessionId in copilotElearningSubscriptions[data.elearningSubscriptionId]) {
          var currentSessionID = copilotElearningSubscriptions[data.elearningSubscriptionId][socketSessionId];
          if (currentSessionID == sessionID) {
            socket.broadcast.to(data.elearningSubscriptionId).emit('hideParticipantWhiteboard', data);
          }
        }
      }
    }
    socket.send("hideParticipantWhiteboard: You are not being watched live yet.");
  });

  // The live results feature allows an administrator to watch the results of an exercise being done, in real time. Any administrator can watch any participant doing an exercise. Note that an administrator is not to be confused with a teacher.
  socket.on('watchLiveResult', function() {
    socket.join('liveResultAdminPages');
  });

  socket.on('disconnect', function(data) {
    socket.leave('liveResultAdminPages');
    console.log("Disconnecting sessionID: " + sessionID);

    var copilotElearningSubscription;
    copilotElearningSubscriptions.forEach(function (copilotElearningSubscription, copilotElearningSubscriptionId) {
      if ('undefined' != typeof copilotElearningSubscription) {
        for (var socketSessionId in copilotElearningSubscription) {
          var currentSessionID = copilotElearningSubscription[socketSessionId];
          if (currentSessionID == sessionID) {
            console.log("Leaving the live watch for the elearning subscription: " + copilotElearningSubscriptionId);
            copilotElearningSubscription[socketSessionId] = '';
            socket.leave(copilotElearningSubscriptionId);
          }
        }
      }
    });

  });

  // Get the session variable from the handshake
  var sessionID = socket.request.sessionID;
  // Get the unique socket session id variable from the handshake
  var socketSessionId = socket.request.socketSessionId;

  // Avoid a session time out on the redis server
  // Simply reload the session data and touch its timestamp
  var intervalID = setInterval(function() {
    if ('undefined' != typeof socket.request) {
      if ('undefined' != typeof socket.request.session) {
        socket.request.session.reload(function() {
          socket.request.session.touch().save();
          console.log("Reloaded the session for " + socket.request.address + ":" + socket.request.port);
        });
      }
    }
  }, 5 * 60 * 1000);

});

