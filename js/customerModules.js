function showModule(moduleId) {
  const modules = [
    "overview", // overview of accoung
    "bookAVehicle", // step by step booking
    "myBookings", // ption to view details, cancel, or modify reservations
    "payment", // for payment hist
    "account", //Personal info, contact details, and preferences. Change password or PIN.
    "help", // FAQs, contact support,
  ];

  // hide all
  modules.forEach((module) => {
    document.getElementById(module).classList.add("hidden");
  });

  // show the selected one
  document.getElementById(moduleId).classList.remove("hidden");
}

window.addEventListener("load", () => {
  showModule("overview");
});

// click handler for nav items
document.querySelectorAll(".navItem").forEach((item) => {
  item.addEventListener("click", (e) => {
    e.preventDefault();
    const moduleId = item.getAttribute("data-module");
    showModule(moduleId);
  });
});
