// Récupération du bouton Inscription et du formulaire d'inscription
const inscriptionBtn = document.getElementById('inscription');
const inscriptionForm = document.getElementById('inscription-form');

// Ajout d'un écouteur d'événements sur le bouton Inscription
inscriptionBtn.addEventListener('click', () => {
  // Affichage du formulaire d'inscription
  inscriptionForm.style.display = 'block';

  // Masquage du formulaire de connexion
  connexionForm.style.display = 'none';
});

// Récupération du bouton Connexion et du formulaire de connexion
const connexionBtn = document.getElementById('connexion');
const connexionForm = document.getElementById('connexion-form');

// Ajout d'un écouteur d'événements sur le bouton Connexion
connexionBtn.addEventListener('click', () => {
  // Affichage du formulaire de connexion
  connexionForm.style.display = 'block';

  // Masquage du formulaire d'inscription
  inscriptionForm.style.display = 'none';
});


