document.addEventListener('DOMContentLoaded', () => {
    // On garde une référence au dernier CV uploadé
    let lastCVFile = null;
  
    // Gestion des animations de chargement
    function toggleLoading(btn, isLoading) {
      btn.disabled = isLoading;
      const spinner = btn.querySelector('.loading-spinner');
      if (spinner) {
        spinner.style.display = isLoading ? 'inline-block' : 'none';
      }
    }
  
  
    function handleFormSubmit(e, callback) {
      e.preventDefault();
      const btn = e.target.querySelector('button');
      toggleLoading(btn, true);
  
      // Exécution du code. on gère les erreurs avec un try/catch
      callback(e)
        .catch(err => {
          alert(`Erreur : ${err.message}`);
        })
        .finally(() => {
          toggleLoading(btn, false);
        });
    }
  
    // --- Analyse de CV ---
    const analyzeForm = document.getElementById('analyzeForm');
    if (analyzeForm) {
      analyzeForm.addEventListener('submit', e =>
        handleFormSubmit(e, async (event) => {
          const inputFile = event.target.querySelector('input[type="file"]').files[0];
          if (!inputFile) {
            throw new Error("Veuillez sélectionner un CV");
          }
          lastCVFile = inputFile;
  
          const selectedOffer = event.target.querySelector('select[name="offre"]').value;
          const formData = new FormData();
          formData.append('cv', inputFile);
          formData.append('offre', selectedOffer);
  
          const resp = await fetch('http://localhost:5000/analyze_cv', {
            method: 'POST',
            body: formData
          });
          const results = await resp.json();
          if (results.error) throw new Error(results.error);
  
          const resultsHTML = results.map(item => `
            <div class="custom-card border mt-3 p-3">
              <h5>${item.titre}</h5>
              <div class="d-flex align-items-center mb-2">
                <span class="badge bg-primary">${(item.similarite * 100).toFixed(1)}%</span>
                <span class="badge bg-success ms-2">${item.badge}</span>
              </div>
              <div class="text-muted fst-italic mb-2">${item.summary}</div>
              <div>
                ${item.keywords.map(kw => `<span class="badge bg-light text-dark me-1 mb-1">${kw}</span>`).join('')}
              </div>
              <div class="mt-3">
                <button type="button" class="btn btn-success"
                        onclick="postuler('${encodeURIComponent(item.titre)}')">
                  Postuler
                </button>
              </div>
            </div>
          `).join('');
  
          document.getElementById('analyzeResults').innerHTML = resultsHTML;
        })
      );
    }
  
    // --- Génération de lettre de motivation ---
    const letterForm = document.getElementById('letterForm');
    if (letterForm) {
      letterForm.addEventListener('submit', e =>
        handleFormSubmit(e, async (event) => {
          const data = {
            nom: event.target.nom.value,
            poste: event.target.poste.value,
            entreprise: event.target.entreprise.value,
            motivations: event.target.motivations.value
          };
  
          const resp = await fetch('http://localhost:5000/generate_letter', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
          });
          const result = await resp.json();
          if (result.error) throw new Error(result.error);
  
          document.getElementById('letterOutput').innerHTML = `
            <div class="custom-card border">
              <div class="card-body">
                <h5 class="fw-bold mb-3">Lettre générée :</h5>
                <pre class="result-pre">${result.letter}</pre>
              </div>
            </div>
          `;
        })
      );
    }
  
    // --- Fonction pour envoyer la candidature ---
    window.postuler = async function(encodedTitre) {
      const titre = decodeURIComponent(encodedTitre);
      if (!lastCVFile) {
        return alert("Veuillez d'abord analyser votre CV avant de postuler.");
      }
      const formData = new FormData();
      formData.append('cv', lastCVFile);
      formData.append('offre', titre);
  
      // Ajout de l'identifiant candidat
      if (!window.candidatId) {
        return alert("Identifiant candidat manquant. Veuillez vous connecter.");
      }
      formData.append('candidat_id', window.candidatId);
  
      try {
        const resp = await fetch('http://localhost:5000/apply', {
          method: 'POST',
          body: formData
        });
        const data = await resp.json();
        if (data.error) throw new Error(data.error);
        alert(data.message);
      } catch (err) {
        alert("Erreur lors de la candidature : " + err.message);
      }
    };
  });
  