function showModule(moduleId) {
  const modules = [
    "overview", // overview of account
    "bookAVehicle", // step by step booking
    "myBookings", // option to view details, cancel, or modify reservations
    "payment", // for payment history
    "account", // personal info, contact details, and preferences
    "help", // FAQs, contact support
  ];

  // hide all
  modules.forEach((module) => {
    document.getElementById(module).classList.add("hidden");
  });

  // show the selected one
  document.getElementById(moduleId).classList.remove("hidden");

  // save last opened
  localStorage.setItem("lastModule", moduleId);
}

window.addEventListener("load", () => {
  // get saved module or default to overview
  const lastModule = localStorage.getItem("lastModule") || "overview";
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
