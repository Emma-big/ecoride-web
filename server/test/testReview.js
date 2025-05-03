// testReview.js
const mongoose = require('../db');    // Connexion à la base de données
const Review = require('../models/Review');  // Import du modèle Review

// Création d'un nouvel avis
const nouvelAvis = new Review({
  user: 'JeanDupont',
  rating: 4,
  comment: 'Application vraiment pratique et intuitive !',
  versionApp: '1.2.3'
});

// Sauvegarde de l'avis dans la base
nouvelAvis.save()
  .then(result => {
    console.log('Avis sauvegardé avec succès :', result);
    mongoose.connection.close();
  })
  .catch(err => {
    console.error('Erreur lors de la sauvegarde de l\'avis :', err);
    mongoose.connection.close();
  });