function showModule(moduleId) {
  const modules = [
    "dashboard",
    "vehicleStatus",
    "reservations",
    "payments",
    "vehicleInventory",
    "reports",
  ];

  // hide all
  modules.forEach((module) => {
    document.getElementById(module).classList.add("hidden");
  });

  // show the selected one
  document.getElementById(moduleId).classList.remove("hidden");
}

window.addEventListener("load", () => {
  showModule("dashboard");
});

// click handler for nav items
document.querySelectorAll(".navItem").forEach((item) => {
  item.addEventListener("click", (e) => {
    e.preventDefault();
    const moduleId = item.getAttribute("data-module");
    showModule(moduleId);
  });
});
