const mongoose = require('mongoose');

// Chaîne de connexion à la base de données
// Pour une installation locale :
const mongoURI = 'mongodb://localhost:27017/ecoride';

// Pour MongoDB Atlas, la chaîne de connexion aura un format différent, par exemple :
// const mongoURI = 'mongodb+srv://<username>:<password>@cluster0.mongodb.net/ecoride?retryWrites=true&w=majority';

mongoose.connect(mongoURI, {
  useNewUrlParser: true,
  useUnifiedTopology: true,
})
.then(() => console.log('Connexion à MongoDB réussie !'))
.catch(err => console.error('Erreur de connexion à MongoDB:', err));

// Optionnel : Exporter la connexion pour l'utiliser ailleurs dans ton projet
module.exports = mongoose;