const btn = document.getElementById("userMenuBtn");
const dropdown = document.getElementById("userDropdown");

btn.addEventListener("click", () => {
  dropdown.classList.toggle("hidden");
});

// Optional: close when clicking outside
window.addEventListener("click", (e) => {
  if (!btn.contains(e.target) && !dropdown.contains(e.target)) {
    dropdown.classList.add("hidden");
  }
});
