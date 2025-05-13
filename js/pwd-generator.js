function generatePassword() {
    fetch("https://password-generator20.p.rapidapi.com/password?length=10&spchar=2&numbers=2&umlauts=true&quantity=1", {
        method: "GET",
        headers: {
            "x-rapidapi-key": "40f6854e3cmsh5259f0f08cecdabp14df4cjsnb0edbfb9f150", 
            "x-rapidapi-host": "password-generator20.p.rapidapi.com"
        }
    })
    .then(response => response.json())
    .then(data => {
        
        const password = Array.isArray(data) ? data[0] : (data.passwords ? data.passwords[0] : "Erreur");
        document.getElementById("passwordField").value = password;
    })
    .catch(error => console.error("Erreur API : ", error));
}
X-RapidAPI-Key


