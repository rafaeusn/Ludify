const express = require('express');
const app = express();
const mysql = require('mysql');
const usuarioRoutes = require('./routes/usuarioRoutes');

app.use(express.json());
app.use(express.static('../Ludify'));

// Configuração do banco de dados
const con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "ludify"
});

con.connect((err) => {
    if (err) throw err;
    console.log("Connected to database!");
});

// Passa a conexão para o model
app.set('db', con);

// Configuração das rotas
app.use('/api', usuarioRoutes);


const port = 3000;
app.listen(port, () => {
    console.log("Server running on port " + port);
});
