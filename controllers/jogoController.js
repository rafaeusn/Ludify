// controllers/usuarioController.js
const Jogo = require('../models/jogoModel');

exports.createJogo = (req, res) => {
    const db = req.app.get('db');

    // Validação de entrada
    const jogo = {
        Titulo: req.body.titulo,
        classificacao_indicativa: req.body.classificacao || null
    };

    if (!jogo.Titulo) {
        res.status(400).send("O campo email é obrigatório.");
        return;
    }

    // Chama o model para inserir o usuário no banco
    Jogo.inserirJogo(db, jogo, (err, result) => {
        if (err) {
            console.error("Erro ao inserir o jogo:", err);
            res.status(500).send("Erro ao inserir o jogo.");
        } else {
            res.status(201).json({ message: "jogo inserido com sucesso!", id: result.insertId });
        }
    });
};
