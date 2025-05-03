const mongoose = require('mongoose');

// Définition du schéma pour un avis sur l’application
const reviewSchema = new mongoose.Schema({
  user: { 
    type: String,
    required: true 
  },
  rating: { 
    type: Number, 
    min: 1, 
    max: 5, 
    required: true 
  },
  comment: { 
    type: String, 
    required: true 
  },
  date: { 
    type: Date, 
    default: Date.now 
  },
  versionApp: { 
    type: String // Ce champ est optionnel et permet de suivre la version de l'app pour l'avis
  },
  // Ajout possible d'autres champs ici si besoin (par exemple, modération, likes, etc.)
});

// Création du modèle à partir du schéma
const Review = mongoose.model('Review', reviewSchema);

// Exportation du modèle pour l'utiliser dans d'autres parties de l'application
module.exports = Review;