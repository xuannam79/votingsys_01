var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');

server.listen(8890);
io.on('connection', function (socket) {

  console.log("new client connected");
  var redisClient = redis.createClient();
  redisClient.subscribe('message', 'comment', 'votes', 'closePoll', 'pollTimeout', 'deleteParticipant');

  redisClient.on("message", function(channel, message) {
    console.log("new message in queue "+ message + "channel");
    socket.emit(channel, message);
  });

  redisClient.on("comment", function(channel, comment) {
    console.log("new comment in queue "+ comment + "channel");
    socket.emit(channel, comment);
  });

  redisClient.on("votes", function(channel, votes) {
    console.log("new votes in queue "+ votes + "channel");
    socket.emit(channel, votes);
  });

  redisClient.on("closePoll", function(channel, closePoll) {
    console.log("new closePoll in queue "+ closePoll + "channel");
    socket.emit(channel, closePoll);
  });

  redisClient.on("pollTimeout", function(channel, pollTimeout) {
    console.log("new pollTimeout in queue "+ pollTimeout + "channel");
    socket.emit(channel, pollTimeout);
  });

  redisClient.on("deleteParticipant", function(channel, deleteParticipant) {
    console.log("new deleteParticipant in queue "+ deleteParticipant + "channel");
    socket.emit(channel, deleteParticipant);
  });

  socket.on('disconnect', function() {
    redisClient.quit();
  });

});
