import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
import { getAuth, createUserWithEmailAndPassword, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";

const firebaseConfig = {
  apiKey: "AIzaSyBLVewIoMzGKIbxbbBmMQoJtJQgnOaf-74",
  authDomain: "plateforme-intelligente.firebaseapp.com",
  projectId: "plateforme-intelligente",
  storageBucket: "plateforme-intelligente.firebasestorage.app",
  messagingSenderId: "591528984268",
  appId: "1:591528984268:web:4a5430f6264a3a1c4a4c69"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
auth.languageCode = "en"; 
const provider = new GoogleAuthProvider();

// Google button
const googleButton = document.getElementById("google-login-btn");

googleButton.addEventListener("click", function (event) {
  event.preventDefault();
  
  signInWithPopup(auth, provider)
  .then(async (result) => {
    const user = result.user;
    const idToken = await user.getIdToken();
    
    // Envoi des données au backend
    try {
      const response = await fetch('google-signup.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          uid: user.uid,
          email: user.email,
          displayName: user.displayName || '',
          photoURL: user.photoURL || '',
          idToken: idToken
        })
      });

      const data = await response.json();
      
      if (data.success) {
        window.location.href = "/projet-HACKATON-Coumba/frontend/accueil.php";
      } else {
        alert(data.error || "Erreur lors de la synchronisation avec notre base de données");
      }
    } catch (error) {
      console.error("Erreur lors de l'envoi au serveur:", error);
      alert("Erreur de connexion avec notre serveur");
    }
  })
  .catch((error) => {
    const errorCode = error.code;
    const errorMessage = error.message;
    console.error("Erreur Google Auth:", errorCode, errorMessage);
    alert(`Erreur lors de la connexion Google: ${errorMessage}`);
  });
});

// Submit button (email/password)
const submit = document.getElementById("submit"); 
submit.addEventListener("click", function (event) {
  event.preventDefault();

  // Inputs
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();

  if (!email || !password) {
    alert("Veuillez remplir tous les champs.");
    return;
  }

  createUserWithEmailAndPassword(auth, email, password)
    .then((userCredential) => {
      const user = userCredential.user;
      alert("Création de compte réussie !");
      window.location.href = "/projet-HACKATON-Coumba/frontend/accueil.php"; 
    })
    .catch((error) => {
      const errorCode = error.code;
      const errorMessage = error.message;
      console.error("Erreur lors de la création du compte :", errorCode, errorMessage);
      alert(`Erreur lors de la création du compte: ${errorMessage}`);
    });
});