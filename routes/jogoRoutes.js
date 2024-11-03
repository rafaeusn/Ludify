// routes/jogosRoutes.js
const express = require('express');
const router = express.Router();
const jogosController = require('../controllers/jogoController');

// Defina as rotas específicas para jogos, por exemplo:
router.post('/jogos', jogosController.createJogo);

module.exports = router;
