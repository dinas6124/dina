document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("inscription");

  form.addEventListener("submit", function (e) {
      const username = form.username.value.trim();
      const email = form.email.value.trim();
      const password = form.pass_word.value;

      let errors = [];

      // Vérif nom d'utilisateur
      if (username.length < 4) {
          errors.push("Le nom d'utilisateur doit contenir au moins 4 caractères.");
      }

      // Vérif email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(email)) {
          errors.push("L'adresse email est invalide.");
      }

      // Vérif mot de passe
      const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/;
      if (!passwordRegex.test(password)) {
          errors.push("Le mot de passe doit contenir au moins 8 caractères, une majuscule, un chiffre et un caractère spécial.");
      }

      // Si y'a une erreurs on empêche l'envoi
      if (errors.length > 0) {
          e.preventDefault(); 
          alert(errors.join("\n")); 
      }
  });
});
