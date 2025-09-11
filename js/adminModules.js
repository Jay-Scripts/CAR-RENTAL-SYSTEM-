function showModule(moduleId) {
  const modules = [
    "overview",
    "salesReports",
    "performanceTrend",
    "refund",
    "registerStaff",
    "modifyPosition",
    "modifyStatus",
    "stockEntry",
    "stockLevel",
    "lowStockAlerts",
    "stocksMovementHistory",
    "logWaste",
    "disableProduct",
    "enableProduct",
    "productMovementHistory",
    "satisfactionDashboard",
    "complaintsManagement",
    "rewards&LoyaltyProgram",
    "discountDashboard",
  ];

  modules.forEach((module) => {
    const el = document.getElementById(module);
    if (el) el.classList.add("hidden");
  });

  const activeModule = document.getElementById(moduleId);
  if (activeModule) activeModule.classList.remove("hidden");
}

// ==========================================
// =       ACTIVE CLASS SIDEBAR STARTS HERE =
// ==========================================
window.addEventListener("DOMContentLoaded", () => {
  const navItems = document.querySelectorAll(".navItem");

  // Get ang last active module from localStorage then fallback to 'overview'
  const activeModule = localStorage.getItem("activeModule") || "overview";
  showModule(activeModule);

  // Remove active class from all, then add to stored one
  navItems.forEach((el) => {
    el.classList.remove(
      "bg-[var(--background-color)]",
      "text-[var(--text-color)]"
    );
    if (el.dataset.module === activeModule) {
      el.classList.add(
        "bg-[var(--background-color)]",
        "text-[var(--text-color)]"
      );
    }
  });
  navItems.forEach((item) => {
    item.addEventListener("click", (e) => {
      e.preventDefault();
      const module = item.dataset.module;
      showModule(module);

      // Store to localStorage
      localStorage.setItem("activeModule", module);

      // Update active class
      navItems.forEach((el) =>
        el.classList.remove(
          "bg-[var(--background-color)]",
          "text-[var(--text-color)]"
        )
      );
      item.classList.add(
        "bg-[var(--background-color)]",
        "text-[var(--text-color)]"
      );
    });
  });
});
// ===================================================
//        ACTIVE CLASS SIDEBAR ENDS HERE             =
// ===================================================
