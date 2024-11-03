// models/jogoModel.js
const Jogo = {
    inserirJogo: (db, jogo, callback) => {
        console.log('TESTEMODEL123');  // Para debug
        const sql = `INSERT INTO Jogo (Titulo, classificacao_indicativa) 
                     VALUES (?, ?)`;
        db.query(sql, [
            jogo.Titulo,  // Mudado para Titulo com letra maiúscula
            jogo.classificacao_indicativa
        ], callback);
    },

    buscarJogos: (db, callback) => {
        db.query("SELECT * FROM Jogo", callback);
    }
};

module.exports = Jogo;
