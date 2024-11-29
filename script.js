const sign_in_btn = document.querySelector("#login-link");
const sign_up_btn = document.querySelector("#register-link");
const container = document.querySelector(".container");

sign_up_btn.addEventListener("click", (e) => {
  e.preventDefault();
  container.classList.add("register-mode");
});

sign_in_btn.addEventListener("click", (e) => {
  e.preventDefault();
  container.classList.remove("register-mode");
});