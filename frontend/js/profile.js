// --- Forms ---
const registerForm = document.getElementById("registerForm");
const loginForm = document.getElementById("loginForm");

// --- Swap Button ---
const swapButton = document.getElementById("swapButton");
const swapText = document.getElementById("swapText");
const formTitle = document.getElementById("formTitle");
const formSubtitle = document.getElementById("formSubtitle");

// --- Password toggles ---
const password = document.getElementById("password");
const toggle = document.getElementById("togglePassword");
if (toggle) {
  toggle.addEventListener("click", () => {
    password.type = password.type === "password" ? "text" : "password";
    toggle.textContent = password.type === "password" ? "Show" : "Hide";
  });
}

const loginPassword = document.getElementById("loginPassword");
const toggleLoginPassword = document.getElementById("toggleLoginPassword");
if (toggleLoginPassword) {
  toggleLoginPassword.addEventListener("click", () => {
    loginPassword.type = loginPassword.type === "password" ? "text" : "password";
    toggleLoginPassword.textContent = loginPassword.type === "password" ? "Show" : "Hide";
  });
}

// --- Password strength ---
const strengthMeter = document.getElementById("strengthMeter");
function calcStrength(pw) {
  let score = 0;
  if (!pw) return 0;
  if (pw.length >= 8) score += 1;
  if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) score += 1;
  if (/[0-9]/.test(pw)) score += 1;
  if (/[^A-Za-z0-9]/.test(pw)) score += 1;
  return Math.min(100, (score / 4) * 100);
}
if (password) {
  password.addEventListener("input", (e) => {
    const val = e.target.value;
    const pct = calcStrength(val);
    strengthMeter.style.width = pct + "%";
    if (pct < 25) strengthMeter.style.background = "crimson";
    else if (pct < 50) strengthMeter.style.background = "orange";
    else if (pct < 75) strengthMeter.style.background = "goldenrod";
    else strengthMeter.style.background = "seagreen";
  });
}

// --- Form submission ---
const handleFormSubmission = (form) => {
  if (!form) return;
  form.addEventListener(
    "submit",
    (event) => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      } else {
        event.preventDefault();
        console.log(`${form.id} submitted successfully!`);
      }
      form.classList.add("was-validated");
    },
    false
  );
};

handleFormSubmission(registerForm);
handleFormSubmission(loginForm);

// --- Toggle Register / Login ---
if (swapButton) {
  swapButton.addEventListener("click", (e) => {
    e.preventDefault();

    const isRegisterVisible = !registerForm.classList.contains("d-none");

    registerForm.classList.toggle("d-none", isRegisterVisible);
    loginForm.classList.toggle("d-none", !isRegisterVisible);

    formTitle.textContent = isRegisterVisible
      ? "Welcome back!"
      : "Create your account";
    formSubtitle.textContent = isRegisterVisible
      ? "Log in to continue where you left off."
      : "Quickly create an account to start shopping with us.";
    swapText.textContent = isRegisterVisible
      ? "Don't have an account?"
      : "Already have an account?";
    swapButton.textContent = isRegisterVisible ? "Sign up" : "Sign in";
  });
}
