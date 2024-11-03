var express = require('express');
var app = express();
app.use(express.json());
app.use(express.static('../Ludify'));
var mysql = require('mysql');

const port = 3000;

var con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "ludify"
});

con.connect(function (err) {
    if (err) throw err;
    console.log("Connected to database!");
});

app.get('/api/usuarios', (req, res) => {
    res.status(200).json(usuarios);
});

app.post('/api/usuarios', (req, res) => {
    console.log(req.body); // Verifique os dados recebidos
    var usuario = {
        email: req.body.email,
        nome: req.body.nome || null,  // Se não existir, use null
        anoNasc: req.body.anoNasc || null,
        cpf: req.body.cpf || null,
        fk_Telefone_Telefone_PK: req.body.fk_Telefone_Telefone_PK || null,
        logradouro: req.body.logradouro || null,
        numero: req.body.numero || null,
        cep: req.body.cep || null
    };
    
    // Verifique se o email está presente
    if (!usuario.email) {
        res.status(400).send("O campo email é obrigatório.");
        return;
    }
    
    var sql = `INSERT INTO Usuario (Email, Nome, AnoNasc, CPF, fk_Telefone_Telefone_PK, Logradouro, Numero, CEP) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?)`;
    con.query(sql, [usuario.email, usuario.nome, usuario.anoNasc, usuario.cpf, usuario.fk_Telefone_Telefone_PK, usuario.logradouro, usuario.numero, usuario.cep], function (err, result) {
        if (err) {
            console.error("Erro ao inserir o usuário:", err);
            res.status(500).send("Erro ao inserir o usuário.");
            return;
        }
        console.log("Usuário inserido com sucesso!");
        res.status(201).json({ message: "Usuário inserido com sucesso!", id: result.insertId });
    });
});


app.listen(port, () => {
    console.log("Server running on port " + port);
});
