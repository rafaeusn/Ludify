// models/usuarioModel.js
const Usuario = {
    inserirUsuario: (db, usuario, callback) => {
        const sql = `INSERT INTO Usuario (Email, Nome, AnoNasc, CPF, fk_Telefone_Telefone_PK, Logradouro, Numero, CEP) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)`;
        db.query(sql, [
            usuario.email,
            usuario.nome,
            usuario.anoNasc,
            usuario.cpf,
            usuario.fk_Telefone_Telefone_PK,
            usuario.logradouro,
            usuario.numero,
            usuario.cep
        ], callback);
    },

    buscarUsuarios: (db, callback) => {
        db.query("SELECT * FROM Usuario", callback);
    }
};

module.exports = Usuario;
