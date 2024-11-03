var express = require('express');
var app = express();

app.use(express.static('../Ludify'));
const port = 3000;
app.get('/hello', (req, res) => {
    res.send("Hello, world");
});

app.listen(port, () => {
    console.log("Server running");
});