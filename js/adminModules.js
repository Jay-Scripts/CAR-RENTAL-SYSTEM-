function showModule(moduleId) {
  const modules = [
    "dashboard",
    "vehicleStatus",
    "reservations",
    "payments",
    "vehicleInventory",
    "reports",
    "accountsApproval",
    "reports2",
    "reports3",
  ];

  // hide all modules
  modules.forEach((module) => {
    document.getElementById(module).classList.add("hidden");
  });

  // show the selected module
  document.getElementById(moduleId).classList.remove("hidden");

  // remember last module
  localStorage.setItem("lastModule", moduleId);
}

// on page load
window.addEventListener("load", () => {
  const lastModule = localStorage.getItem("lastModule") || "dashboard";
  showModule(lastModule);
});

// click handler for nav items
document.querySelectorAll(".navItem").forEach((item) => {
  item.addEventListener("click", (e) => {
    e.preventDefault();
    const moduleId = item.getAttribute("data-module");
    showModule(moduleId);
  });
});
