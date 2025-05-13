const container = document.getElementById('chatbot-container');
const box = document.getElementById('chatbot-box');
const toggle = document.getElementById('chatbot-toggle');
const closeBtn = document.getElementById('chatbot-close');
const form = document.getElementById('chatbot-form');
const input = document.getElementById('chatbot-input');
const messages = document.getElementById('chatbot-messages');

// Gestion de l'affichage du chatbot
toggle.addEventListener('click', () => {
  box.style.display = 'flex';
  toggle.style.display = 'none';
  input.focus();
});

closeBtn.addEventListener('click', () => {
  box.style.display = 'none';
  toggle.style.display = 'block';
});

// Fonction principale d'affichage des messages
function appendMessage(content, sender) {
  const msg = document.createElement('div');
  msg.className = `message ${sender}`;
  msg.style.animation = 'slideIn 0.3s ease';

  const bubble = document.createElement('div');
  bubble.className = 'bubble';

  if (typeof content === 'string') {
    bubble.innerHTML = content;
  } else {
    if (content.answer) {
      bubble.innerHTML = content.answer;
    }

    // Gestion des boutons d'options
    if (content.options) {
      const optionsDiv = document.createElement('div');
      optionsDiv.className = 'options';
      content.options.forEach(opt => {
        const btn = document.createElement('button');
        btn.className = 'option-btn';
        btn.textContent = opt.text;
        btn.onclick = () => handleInteraction(opt.value);
        optionsDiv.appendChild(btn);
      });
      bubble.appendChild(optionsDiv);
    }

    // Gestion des réponses rapides
    if (content.quick_replies) {
      const quickRepliesDiv = document.createElement('div');
      quickRepliesDiv.className = 'quick-replies';
      content.quick_replies.forEach(text => {
        const span = document.createElement('span');
        span.textContent = text;
        span.onclick = () => handleInteraction(text);
        quickRepliesDiv.appendChild(span);
      });
      bubble.appendChild(quickRepliesDiv);
    }
  }

  msg.appendChild(bubble);
  messages.appendChild(msg);
  messages.scrollTop = messages.scrollHeight;
}

// Gestion des interactions
async function handleInteraction(value) {
  appendMessage(value, 'user');
  showTypingIndicator();

  try {
    const response = await fetch('http://localhost:5000/api/chat', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ question: value })
    });

    const data = await response.json();
    removeTypingIndicator();
    appendMessage(data, 'bot');
  } catch (error) {
    console.error('Erreur:', error);
    removeTypingIndicator();
    appendMessage("Désolé, je n'arrive pas à me connecter au serveur 😞", 'bot');
  }
}

// Indicateur de saisie
function showTypingIndicator() {
  const typingDiv = document.createElement('div');
  typingDiv.className = 'message bot typing';
  typingDiv.innerHTML = `
    <div class="bubble">
      <div class="typing-indicator">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
      </div>
    </div>
  `;
  messages.appendChild(typingDiv);
  messages.scrollTop = messages.scrollHeight;
}

function removeTypingIndicator() {
  document.querySelector('.typing')?.remove();
}

// Gestion du formulaire
form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const question = input.value.trim();
  if (!question) return;

  input.value = '';
  handleInteraction(question);
});

// Initialisation
appendMessage({
  answer: "👋 Bonjour ! Je suis Chatia votre assistant de recrutement. Comment puis-je vous aider ?",
  quick_replies: [
    "Comment postuler ?",
    "Comment fonctionne l’analyse de matching IA ?",
    "Comment la lettre de motivation est-elle générée ? "
  ]
}, 'bot');