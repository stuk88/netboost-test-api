// Import required modules
import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

// Create express app
const app = express();


app.use(express.json());
app.use(express.urlencoded({ extended: true }));


// Create http server using the express app
const server = createServer(app);

// Create socket.io instance and attach it to the http server
const io = new Server(server, { cors: { origin: "*"} });  

// Define a connection event handler for socket.io
io.on('connection', (socket) => {
  console.log('New client connected');

  // Send a welcome message to the client
  socket.emit('message', 'Welcome to the server!');

  // Receive a message from the client
  socket.on('message', (data) => {
    console.log('Received message:', data);

    // Broadcast the received message to all connected clients
    io.emit('message', data);
  });

  socket.on('connect_channel', (data) => {
    socket.join(data.channel);
  })

  // Handle disconnection of a client
  socket.on('disconnect', () => {
    console.log('Client disconnected');
  });
});



app.post('/broadcast/:channel_name', (req, res) => {

  // TODO: add secret key validation for security
  const channel = req.params.channel_name;
  const message = {url: req.body.url};

  console.log("Sending url resulted ok to client channel", channel, req.body);


  // Broadcast the message to the specified channel
  io.to(channel).emit('message', message);

  res.status(200).send();
});

// Start the server
const port = 3000;
server.listen(port, () => {
  console.log(`Server started on port ${port}`);
});