import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js";
import { 
  getAuth, 
  GoogleAuthProvider, 
  signInWithPopup 
} from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";
import { sendPasswordResetEmail } from "https://www.gstatic.com/firebasejs/11.6.0/firebase-auth.js";

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
const provider = new GoogleAuthProvider();

// 1. Connexion Google
const handleGoogleLogin = async () => {
  try {
    const result = await signInWithPopup(auth, provider);
    const user = result.user;
    
    const response = await fetch('google-login.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        uid: user.uid,
        email: user.email,
        displayName: user.displayName || '',
        photoURL: user.photoURL || ''
      })
    });

    const data = await response.json();
    window.location.href = data.redirect || 'accueil.php';

  } catch (error) {
    alert(`Erreur Google : ${error.message.replace('Firebase: ', '')}`);
  }
};

// 2. Connexion SQL Classique
const handleSQLLogin = async (email, password) => {
  try {
    const response = await fetch('connexion.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `action=sql_login&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`,
      credentials: 'include'
    });

    const data = await response.json();
    
    if (data.success) {
      window.location.href = data.redirect;
    } else {
      alert(data.error || 'Identifiants incorrects');
    }

  } catch (error) {
    alert('Erreur de connexion au serveur');
  }
};

// Écouteurs d'événements
document.getElementById("google-login-btn").addEventListener("click", (e) => {
  e.preventDefault();
  handleGoogleLogin();
});

document.getElementById("bout").addEventListener("click", (e) => {
  e.preventDefault();
  const email = document.querySelector("input[name='email']").value;
  const password = document.querySelector("input[name='password']").value;
  handleSQLLogin(email, password);
});

// Affichage du formulaire de réinitialisation
document.getElementById("forgot-password-link").addEventListener("click", function(e) {
  e.preventDefault();
  document.getElementById("forgot-password-form").style.display = "block";
});

// Envoyer l'email de réinitialisation
document.getElementById("send-reset-email").addEventListener("click", async function(e) {
  e.preventDefault();
  const email = document.getElementById("reset-email").value.trim();
  if (!email) {
    alert("Veuillez entrer votre email.");
    return;
  }
  try {
    await sendPasswordResetEmail(auth, email);
    alert("Un email de réinitialisation a été envoyé. Vérifiez votre boîte mail.");
  } catch (error) {
    alert("Erreur : " + error.message);
  }
});