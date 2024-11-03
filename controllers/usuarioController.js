// controllers/usuarioController.js
const Usuario = require('../models/usuarioModel');

exports.createUsuario = (req, res) => {
    const db = req.app.get('db');

    // Validação de entrada
    const usuario = {
        email: req.body.email,
        nome: req.body.nome || null,
        anoNasc: req.body.anoNasc || null,
        cpf: req.body.cpf || null,
        fk_Telefone_Telefone_PK: req.body.fk_Telefone_Telefone_PK || null,
        logradouro: req.body.logradouro || null,
        numero: req.body.numero || null,
        cep: req.body.cep || null
    };

    if (!usuario.email) {
        res.status(400).send("O campo email é obrigatório.");
        return;
    }

    // Chama o model para inserir o usuário no banco
    Usuario.inserirUsuario(db, usuario, (err, result) => {
        if (err) {
            console.error("Erro ao inserir o usuário:", err);
            res.status(500).send("Erro ao inserir o usuário.");
        } else {
            res.status(201).json({ message: "Usuário inserido com sucesso!", id: result.insertId });
        }
    });
};
