var app = require('express')();
var server = require('http').Server(app);
var io = require('socket.io')(server);
var redis = require('redis');

server.listen(8890);
io.on('connection', function (socket) {

    console.log("new client connected");
    var redisClient = redis.createClient();
    redisClient.subscribe('comment', 'votes', 'closePoll', 'pollTimeout', 'deleteParticipant', 'reopenPoll', 'editPoll');
    redisClient.on("message", function(channel, message) {
        console.log("channel: " + channel);
        socket.emit(channel, message);
    });

    socket.on('disconnect', function() {
        redisClient.quit();
    });
});
