var express = require('express');
var app = express();
app.use(express.json());
app.use(express.static('../Ludify'));
const port = 3000;

const usuarios = [];

const router = express.Router();
app.get('/api/usuarios', (req, res) => {
    res.status(200).json(usuarios);
});

app.post('api/usuarios', (req, res) => {
    var usuario = req.body;
    usuario.id = 1;
    usuarios.push(usario);
    res.status(201).json(usuario);
});

app.listen(port, () => {
    console.log("Server running");
});