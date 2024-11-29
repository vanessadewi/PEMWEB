// Mendapatkan elemen dengan id "login-link" yang mengarah pada tombol sign-in (login)
const sign_in_btn = document.querySelector("#login-link");

// Mendapatkan elemen dengan id "register-link" yang mengarah pada tombol sign-up (register)
const sign_up_btn = document.querySelector("#register-link");

// Mendapatkan elemen dengan class "container" yang biasanya berfungsi untuk membungkus konten utama
const container = document.querySelector(".container");

// Menambahkan event listener untuk tombol sign-up (register) saat diklik
sign_up_btn.addEventListener("click", (e) => {
  e.preventDefault();  // Mencegah aksi default dari tombol (misalnya, berpindah halaman)
  
  // Menambahkan class "register-mode" ke elemen dengan class "container" untuk mengubah tampilannya
  container.classList.add("register-mode");
});

// Menambahkan event listener untuk tombol sign-in (login) saat diklik
sign_in_btn.addEventListener("click", (e) => {
  e.preventDefault();  // Mencegah aksi default dari tombol (misalnya, berpindah halaman)
  
  // Menghapus class "register-mode" dari elemen dengan class "container" untuk kembali ke tampilan login
  container.classList.remove("register-mode");
});
