// server.js

// Importer Express et initialiser l'application
const express = require('express');
const app = express();
const port = 3000; // Tu peux choisir un autre port si besoin

// Importer la connexion à la base (db.js) et le modèle Review
require('./db'); // Vérifie que le chemin correspond à la localisation du fichier db.js
const Review = require('./models/Review');

// Importer Joi pour la validation des entrées
const Joi = require('joi');

// Middleware pour parser le JSON dans le corps des requêtes
app.use(express.json());

// Définir un schéma de validation avec Joi pour les avis
const reviewValidationSchema = Joi.object({
  user: Joi.string().required(),
  rating: Joi.number().min(1).max(5).required(),
  comment: Joi.string().required(),
  versionApp: Joi.string().optional()
});

// Route pour ajouter un avis (méthode POST) avec validation
app.post('/reviews', async (req, res) => {
  // Valider le corps de la requête avec Joi
  const { error } = reviewValidationSchema.validate(req.body);
  if (error) {
    return res.status(400).json({ error: error.details[0].message });
  }

  try {
    // Créer un nouvel avis avec les données validées
    const newReview = new Review(req.body);
    // Sauvegarder l'avis dans la base MongoDB
    await newReview.save();
    // Répondre avec le nouvel avis enregistré et le code HTTP 201 (création)
    res.status(201).json(newReview);
  } catch (err) {
    // En cas d'erreur, renvoyer un message d'erreur avec le code HTTP 500
    res.status(500).json({ error: 'Erreur lors de l’enregistrement de l’avis', details: err });
  }
});

// Route pour récupérer l'ensemble des avis (méthode GET)
app.get('/reviews', async (req, res) => {
  try {
    // Récupérer tous les avis de la collection
    const reviews = await Review.find();
    res.json(reviews);
  } catch (err) {
    res.status(500).json({ error: 'Erreur lors de la récupération des avis', details: err });
  }
});

// Route pour mettre à jour un avis par son ID (méthode PUT)
app.put('/reviews/:id', async (req, res) => {
  try {
    const reviewId = req.params.id;
    const updatedReview = await Review.findByIdAndUpdate(reviewId, req.body, { new: true });
    if (!updatedReview) {
      return res.status(404).json({ error: 'Avis non trouvé' });
    }
    res.json(updatedReview);
  } catch (err) {
    res.status(500).json({ error: 'Erreur lors de la mise à jour de l’avis', details: err });
  }
});

// Route pour supprimer un avis par son ID (méthode DELETE)
app.delete('/reviews/:id', async (req, res) => {
  try {
    const reviewId = req.params.id;
    const deletedReview = await Review.findByIdAndDelete(reviewId);
    if (!deletedReview) {
      return res.status(404).json({ error: 'Avis non trouvé' });
    }
    res.json({ message: 'Avis supprimé avec succès' });
  } catch (err) {
    res.status(500).json({ error: 'Erreur lors de la suppression de l’avis', details: err });
  }
});

// Lancer le serveur et écouter sur le port choisi
app.listen(port, () => {
  console.log(`Serveur Node.js en écoute sur le port ${port}`);
});
