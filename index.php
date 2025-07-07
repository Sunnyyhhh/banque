<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
  <style>
    body {
      font-family: sans-serif;
      background: #f5f5f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    form {
      background: white;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px #aaa;
      width: 300px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
    }
    button {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background-color: #2c3e50;
      color: white;
      border: none;
      cursor: pointer;
    }
    .error {
      color: red;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<form onsubmit="login(event)">
  <h2>Connexion</h2>
  <input type="email" id="email" placeholder="Email" required>
  <input type="password" id="mot_de_passe" placeholder="Mot de passe" required>
  <button type="submit">Se connecter</button>
  <div class="error" id="erreur"></div>
</form>

<script>
  const apiBase = "http://localhost/t/banque/ws";

  function ajax(method, url, data, callback) {
    const xhr = new XMLHttpRequest();
    xhr.open(method, apiBase + url, true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = () => {
      if (xhr.readyState === 4) {
        try {
          const res = JSON.parse(xhr.responseText);
          callback(res, xhr.status);
        } catch (e) {
          console.error("Erreur JSON:", e, xhr.responseText);
          document.getElementById("erreur").textContent = "Erreur interne du serveur.";
        }
      }
    };
    xhr.send(data);
  }

  function login(event) {
    event.preventDefault();

    const email = document.getElementById("email").value;
    const mot_de_passe = document.getElementById("mot_de_passe").value;

    const data = `email=${encodeURIComponent(email)}&mot_de_passe=${encodeURIComponent(mot_de_passe)}`;

    ajax("POST", "/login", data, (res, status) => {
      if (status === 200 && res.status === "success") {
        localStorage.setItem("utilisateur", JSON.stringify(res.utilisateur));
        localStorage.setItem("nb_valides", res.nb_valides);
        localStorage.setItem("nb_non_valides", res.nb_non_valides);
        localStorage.setItem("email", email); 
        localStorage.setItem("type_pret", res.type_pret);
        window.location.href = "ws/views/dashboard.html";
      } else {
        document.getElementById("erreur").textContent = res.message || "Email ou mot de passe invalide.";
      }
    });
  }
</script>

</body>
</html>