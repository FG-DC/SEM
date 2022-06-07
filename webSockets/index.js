const httpServer = require("http").createServer();

const io = require("socket.io")(httpServer, {
  allowEIO3: true,
  cors: {
    origin: "http://192.168.1.98:8081",
    methods: ["GET", "POST"],
    credentials: true,
  },
});

httpServer.listen(8585, function () {
  console.log("listening on *:8080");
});

io.on("connection", function (socket) {
  console.log(`client ${socket.id} has connected`);

  socket.on("logged_in", function (userId) {
    userId = parseInt(userId);
    socket.join(userId);
  });

  socket.on("logged_out", function (userId) {
    socket.leave(userId);
  });

  socket.on("divisionUpdate", function (userId) {
    userId = parseInt(userId);
    io.to(userId).emit("divisionUpdate");
    io.to(userId).emit("navbarUpdate");
  });

  socket.on("equipmentUpdate", function (userId) {
    userId = parseInt(userId);
    io.to(userId).emit("equipmentUpdate");
    io.to(userId).emit("navbarUpdate");
  });

  socket.on("affiliateUpdate", function (userId) {
    userId = parseInt(userId);
    io.to(userId).emit("affiliateUpdate");
    io.to(userId).emit("navbarUpdate");
  });

  socket.on("profileUpdate", function (userId) {
    userId = parseInt(userId);
    io.to(userId).emit("profileUpdate");
    io.to(userId).emit("navbarUpdate");
  });

  socket.on("trainingExamples", function (userId) {
    userId = parseInt(userId);
    io.to(userId).emit("trainingExamples");
    io.to(userId).emit("navbarUpdate");
  });
});
