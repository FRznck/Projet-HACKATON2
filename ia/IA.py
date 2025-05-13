from flask import Flask, request, jsonify
from flask_cors import CORS
import fitz  
from sentence_transformers import SentenceTransformer, util
import os
import re
import nltk
from nltk.corpus import stopwords
from dotenv import load_dotenv
import mysql.connector  
import google.generativeai as genai

genai.configure(api_key=os.getenv("GOOGLE_GEMINI_API_KEY"))

# Charger les variables d'environnement
load_dotenv()

# Configurer Flask
app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# Dossier pour stocker les candidatures
APPLICATION_FOLDER = 'applications'
os.makedirs(APPLICATION_FOLDER, exist_ok=True)

# Configurer les modèles et dossiers
model = SentenceTransformer('sentence-transformers/all-MiniLM-L6-v2')
UPLOAD_FOLDER = 'uploads'
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

# Télécharger les stopwords une seule fois
nltk.download('stopwords')
stop_words = set(stopwords.words('french'))

# Fonction pour extraire du texte d'un fichier PDF
def extract_text_from_pdf(pdf_path):
    doc = fitz.open(pdf_path)
    text = ""
    for page in doc:
        text += page.get_text("text") + "\n"
    return text

# Fonction pour récupérer les offres depuis la base de données MySQL
def get_offres_from_db():
    offres = []
    # Connexion à la base de données
    conn = mysql.connector.connect(
        host="localhost",  
        user="root",  
        password="root", 
        database="hackathon"  
    )
    cursor = conn.cursor()
    cursor.execute("SELECT titre, description FROM offres") 
    rows = cursor.fetchall() 
    for row in rows:
        offres.append(row)  
    conn.close() 
    return offres

# Fonction pour extraire les mots-clés communs entre deux textes
def extract_common_keywords(text1, text2):
    words1 = set([word.lower() for word in text1.split() if word.isalpha() and word.lower() not in stop_words])
    words2 = set([word.lower() for word in text2.split() if word.isalpha() and word.lower() not in stop_words])
    common = words1.intersection(words2)
    return list(common)

# Fonction pour attribuer un badge selon le score
def get_match_badge(score):
    if score >= 0.8:
        return "Excellente correspondance"
    elif score >= 0.6:
        return "Bonne correspondance"
    else:
        return "Correspondance faible"

# Fonction pour générer un résumé personnalisé
def generate_mini_summary(titre, keywords):
    if not keywords:
        return f"Votre profil présente quelques correspondances pour le poste de {titre}."
    skills = ", ".join(keywords[:5])
    return f"Votre profil correspond bien pour le poste de {titre} grâce à vos compétences en {skills}."

# Endpoint pour analyser 1 CV contre toutes les offres (à revoir pour l'optimiser)
@app.route('/analyze_cv', methods=['POST'])
def analyze_cv():
    try:
        fichier = request.files.get('cv')
        offre_text = request.form.get('offre')  
        if not fichier or fichier.filename == '':
            return jsonify({"error": "Veuillez sélectionner un CV"}), 400
        if not offre_text:
            return jsonify({"error": "Veuillez sélectionner une offre"}), 400

        path = os.path.join(UPLOAD_FOLDER, fichier.filename)
        fichier.save(path)

        cv_text = extract_text_from_pdf(path)
        cv_embedding = model.encode(cv_text, convert_to_tensor=True)

        offres = get_offres_from_db()

        results = []
        for titre, description in offres:
            offre_embedding = model.encode(description, convert_to_tensor=True)
            similarity_score = util.pytorch_cos_sim(cv_embedding, offre_embedding).item()
            badge = get_match_badge(similarity_score)
            keywords = extract_common_keywords(cv_text, description)
            summary = generate_mini_summary(titre, keywords)

            results.append({
                "titre": titre,
                "similarite": similarity_score,
                "badge": badge,
                "keywords": keywords,
                "summary": summary
            })

        os.remove(path)
        sorted_results = sorted(results, key=lambda x: x['similarite'], reverse=True)

        return jsonify(sorted_results)

    except Exception as e:
        return jsonify({"error": str(e)}), 500

# Endpoint pour postuler à une offre
@app.route('/apply', methods=['POST'])
def apply_offer():
    try:
        fichier = request.files.get('cv')
        offre_title = request.form.get('offre')
        candidat_id = request.form.get('candidat_id')

        if not fichier or not offre_title or not candidat_id:
            return jsonify({"error": "CV, offre ou identifiant candidat manquant"}), 400

        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="root",
            database="hackathon"
        )
        cursor = conn.cursor()

        # Récupérer l'id de l'offre
        cursor.execute("SELECT id_offre FROM offres WHERE titre = %s", (offre_title,))
        offre_row = cursor.fetchone()
        if not offre_row:
            conn.close()
            return jsonify({"error": "Offre introuvable"}), 400
        offre_id = offre_row[0]

        # verifie si le candidat a déjà postulé
        cursor.execute("SELECT id_candidature FROM candidatures WHERE id_utilisateur = %s AND id_offre = %s", (candidat_id, offre_id))
        if cursor.fetchone():
            conn.close()
            return jsonify({"error": "Vous avez déjà postulé à cette offre."}), 400

        # Sauvegarde du CV
        safe_name = f"{offre_title.replace(' ', '_')}_candidat{candidat_id}_{fichier.filename}"
        path = os.path.join(APPLICATION_FOLDER, safe_name)
        fichier.save(path)

        # Récupérer l'id_recruteur depuis l'offre
        cursor.execute("SELECT id_recruteur FROM offres WHERE id_offre = %s", (offre_id,))
        recruteur_row = cursor.fetchone()
        id_recruteur = recruteur_row[0] if recruteur_row else 0  # 0 ou une autre valeur par défaut

        # Insertion de la candidature avec id_recruteur
        cursor.execute(
            "INSERT INTO candidatures (id_utilisateur, id_offre, cv_fichier, lettre_fichier, id_recruteur) VALUES (%s, %s, %s, %s, %s)",
            (candidat_id, offre_id, safe_name, "", id_recruteur)
        )
        conn.commit()
        conn.close()

        return jsonify({
            "message": f"Votre candidature pour « {offre_title} » a bien été reçue !"
        })
    except Exception as e:
        return jsonify({"error": str(e)}), 500

# Endpoint pour lister les candidatures
@app.route('/list_applications', methods=['GET'])
def list_applications():
    try:
        files = []
        for filename in os.listdir(APPLICATION_FOLDER):
            path = os.path.join(APPLICATION_FOLDER, filename)
            if os.path.isfile(path):
                files.append({
                    "filename": filename,
                    "uploaded_at": os.path.getmtime(path)
                })
        # Tri par date décroissante
        files.sort(key=lambda x: x["uploaded_at"], reverse=True)
        return jsonify(files)
    except Exception as e:
        return jsonify({"error": str(e)}), 500

# Endpoint pour générer une lettre de motivation
@app.route('/generate_letter', methods=['POST'])
def generate_letter():
    try:
        data = request.get_json()
        nom = data.get('nom', '')
        poste = data.get('poste', '')
        entreprise = data.get('entreprise', '')
        motivations = data.get('motivations', '')

        prompt = (
            f"Rédige une lettre de motivation professionnelle et personnalisée pour un candidat nommé {nom}, "
            f"qui postule au poste de {poste} chez {entreprise}. "
            f"Voici ses motivations : {motivations}. "
            f"Fais une lettre structurée, polie, en français, adaptée à une candidature spontanée."
        )

       

        gemini_model = genai.GenerativeModel("gemini-2.0-flash")
        response = gemini_model.generate_content(prompt)
        lettre = response.text.strip()
        return jsonify({"letter": lettre})
    except Exception as e:
        return jsonify({"error": str(e)}), 500
    

# Endpoint pour Chatbot
@app.route('/api/chat', methods=['POST'])
def chat():
    try:
        data = request.get_json()
        question = data.get('question', '').lower()
        context = data.get('context', [])  # Historique de conversation

        # Règles contextuelles
        if "aide" in question or "menu" in question:
            return jsonify({
                "answer": "Que souhaitez-vous faire ?",
                "options": [
                    {"text": "📄 Postuler à une offre", "value": "postuler"},
                    {"text": "📝 Générer lettre de motivation", "value": "lettre"},
                    {"text": "🔍 Recherche d'offres", "value": "recherche"},
                    {"text": "📊 Suivi candidature", "value": "suivi"}
                ],
                "type": "buttons"
            })

        if "postuler" in context:
            return jsonify({
                "answer": "Parfait ! Quel est le numéro de l'offre qui vous intéresse ?",
                "type": "input",
                "context": ["postuler"]
            })

        # Système de personnalisation
        response = {
            "answer": gemini_response(question), # Utilisez Gemini
            "quick_replies": [
                "Quelles sont les étapes pour qu’un candidat postule à une offre ? ",
                "Comment fonctionne l’analyse de matching IA ?",
                "Comment la lettre de motivation est-elle générée automatiquement ? "
            ]
        }

        return jsonify(response)

    except Exception as e:
        return jsonify({"answer": f"⚠️ Oups ! Un problème est survenu : {str(e)}"})

def gemini_response(prompt):
    prompt = (
        f"Tu es un assistant énergique et enthousiaste qui aide des candidats sur une plateforme web de recrutement "
        f"qui met en relation des candidats et des recruteurs, enrichie par des fonctionnalités d’intelligence artificielle "
        f"pour améliorer le matching entre les profils et les offres d’emploi. Réponds en français de manière concise et motivante à : "
        f" Fonctionnalités principales  : Espace candidat : Les candidats peuvent s’inscrire, se connecter, consulter les offres, postuler, et suivre leurs candidatures."
        f" Les candidats peuvent uploader leur CV (PDF), L’IA analyse le CV et compare son contenu à toutes les offres en base."
        f"Pour chaque offre, l’IA calcule un score de similarité, extrait les mots-clés communs, attribue un badge de correspondance (excellente, bonne, faible) et génère un mini-résumé personnalisé.  "
        f"Génération de lettre de motivation : Un formulaire permet de générer automatiquement une lettre personnalisée à partir du nom, poste, entreprise et motivations du candidat."
        f"Postulation simplifiée : Après analyse, le candidat peut postuler directement à une offre, son CV est sauvegardé et une notification est envoyée."
        f"Notifications : Les candidats reçoivent des notifications lors des changements de statut de leurs candidatures."
        f"Notifications : Les candidats reçoivent des notifications lors des changements de statut de leurs candidatures."
        f"Tu dois répondre qu’aux questions concernant la plateforme de recrutement, ses fonctionnalités, ses endpoints, et son fonctionnement interne. Ne pas répondre à des questions hors sujet ou sur d’autres projets.{prompt}"

    )
    model = genai.GenerativeModel("gemini-2.0-flash")
    return model.generate_content(prompt).text


@app.route('/postuler', methods=['POST'])
def postuler():
    try:
        cv_file = request.files.get('cv')
        lettre_file = request.files.get('lettre')
        id_offre = request.form.get('id_offre')
        id_utilisateur = request.form.get('id_utilisateur')
        lettre_motivation = request.form.get('lettre_motivation', '')

        # Sauvegarder les fichiers et préparer les chemins
        cv_path = os.path.join(UPLOAD_FOLDER, cv_file.filename)
        lettre_path = os.path.join(UPLOAD_FOLDER, lettre_file.filename)
        cv_file.save(cv_path)
        lettre_file.save(lettre_path)

        # Connexion à la base de données
        conn = mysql.connector.connect(
            host="localhost",
            user="root",
            password="root",
            database="hackathon"
        )
        cursor = conn.cursor()
        sql = """
            INSERT INTO candidatures (id_candidat, id_offre, lettre_motivation, date_candidature, statut, cv_fichier, lettre_fichier, id_utilisateur)
            VALUES (%s, %s, %s, NOW(), 'En attente', %s, %s, %s)
        """
        cursor.execute(sql, (id_utilisateur, id_offre, lettre_motivation, cv_path, lettre_path, id_utilisateur))
        conn.commit()
        conn.close()
        return jsonify({"success": True})
    except Exception as e:
        return jsonify({"error": str(e)}), 500


# lancement app
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)


