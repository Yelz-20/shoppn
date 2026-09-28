// Client-side validation for the register and login forms.
// This is for user experience only; the PHP actions validate again on the server.
document.addEventListener("DOMContentLoaded", function () {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const phoneRegex = /^[0-9+\-\s]{7,15}$/;
  const passRegex = /^(?=.*\d).{8,}$/;

  function showError(id, message) {
    const el = document.getElementById(id);
    if (el) el.textContent = message;
  }

  function clearErrors(form) {
    form.querySelectorAll(".field-error").forEach(function (el) {
      el.textContent = "";
    });
  }

  function setLoading(button, text) {
    button.disabled = true;
    button.textContent = text;
  }

  // ----- Register form -----
  const registerForm = document.getElementById("register-form");
  if (registerForm) {
    registerForm.addEventListener("submit", function (e) {
      clearErrors(registerForm);
      let valid = true;

      const name = document.getElementById("name").value.trim();
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value;
      const country = document.getElementById("country").value;
      const city = document.getElementById("city").value.trim();
      const contact = document.getElementById("contact").value.trim();

      if (name.length < 2) {
        showError(
          "name-error",
          "Enter your full name (at least 2 characters).",
        );
        valid = false;
      }
      if (!emailRegex.test(email)) {
        showError("email-error", "Enter a valid email address.");
        valid = false;
      }
      if (!passRegex.test(password) || password.length > 72) {
        showError(
          "password-error",
          "Use 8 to 72 characters, including at least one number.",
        );
        valid = false;
      }
      if (country === "") {
        showError("country-error", "Choose your country.");
        valid = false;
      }
      if (city.length < 2) {
        showError("city-error", "Enter your city.");
        valid = false;
      }
      if (!phoneRegex.test(contact)) {
        showError(
          "contact-error",
          "Enter 7 to 15 digits (you can use +, - and spaces).",
        );
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        return;
      }
      setLoading(
        document.getElementById("register-btn"),
        "Creating account...",
      );
    });
  }

  // ----- Login form -----
  const loginForm = document.getElementById("login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", function (e) {
      clearErrors(loginForm);
      let valid = true;

      const email = document.getElementById("login-email").value.trim();
      const password = document.getElementById("login-password").value;

      if (!emailRegex.test(email)) {
        showError("login-email-error", "Enter a valid email address.");
        valid = false;
      }
      if (password === "") {
        showError("login-password-error", "Enter your password.");
        valid = false;
      }

      if (!valid) {
        e.preventDefault();
        return;
      }
      setLoading(document.getElementById("login-btn"), "Logging in...");
    });
  }
});
