document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".js-patient-form");
    if (form) {
        form.addEventListener("submit", function (event) {
            let errors = [];

            let firstName = document.getElementById("first_name").value.trim();
            let lastName = document.getElementById("last_name").value.trim();
            let dob = document.getElementById("date_of_birth").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let email = document.getElementById("email").value.trim();

            if (!firstName) errors.push("Le prénom est obligatoire.");
            if (!lastName) errors.push("Le nom est obligatoire.");
            if (!dob) errors.push("La date de naissance est obligatoire.");
            if (!phone) errors.push("Le téléphone est obligatoire.");
            if (email && !/^\S+@\S+\.\S+$/.test(email))
                errors.push("Le format de l’email est invalide.");

            if (errors.length > 0) {
                event.preventDefault();
                alert(errors.join("\n"));
            }
        });
    }
});
