document.addEventListener("DOMContentLoaded", () => {
  const checkbox2 = document.getElementById("checkbox2");
  const checkmark2 = document.getElementById("checkmark2");
  const openModal = document.getElementById("openModal");
  const modalOverlay = document.getElementById("modalOverlay");
  const closeModal = document.getElementById("closeModal");
  const acceptModal = document.getElementById("acceptModal");
  const declineModal = document.getElementById("declineModal");
  const customerBooking = document.getElementById("customer_booking");

  let agreed2 = false;

  // Disable booking button on load
  customerBooking.disabled = true;
  customerBooking.classList.add("cursor-not-allowed", "opacity-50");

  // Open modal when clicking checkbox
  checkbox2.addEventListener("click", () => {
    modalOverlay.classList.remove("hidden");
    modalOverlay.classList.add("flex", "fade-in");
  });

  // Close modal with "X"
  closeModal.addEventListener("click", () => {
    modalOverlay.classList.add("hidden");
    modalOverlay.classList.remove("flex", "fade-in");
  });

  // Accept terms
  acceptModal.addEventListener("click", () => {
    agreed2 = true;

    // Checkbox green
    checkbox2.classList.remove("border-gray-300");
    checkbox2.classList.add("border-green-500", "bg-green-500");
    checkmark2.classList.remove("hidden");

    // Hide modal
    modalOverlay.classList.add("hidden");
    modalOverlay.classList.remove("flex", "fade-in");

    // Enable booking button
    customerBooking.disabled = false;
    customerBooking.classList.remove("cursor-not-allowed", "opacity-50");
    console.log("Button enabled ✅");
  });

  // Decline terms
  declineModal.addEventListener("click", () => {
    agreed2 = false;

    // Reset checkbox
    checkbox2.classList.add("border-gray-300");
    checkbox2.classList.remove("border-green-500", "bg-green-500");
    checkmark2.classList.add("hidden");

    // Hide modal
    modalOverlay.classList.add("hidden");
    modalOverlay.classList.remove("flex", "fade-in");

    // Disable button again
    customerBooking.disabled = true;
    customerBooking.classList.add("cursor-not-allowed", "opacity-50");
    console.log("Button disabled ❌");
  });

  // Extra safety: prevent submit without agreement
  document.querySelector("form").addEventListener("submit", (e) => {
    if (!agreed2) {
      e.preventDefault();
      alert("Please agree to the terms before booking.");
    }
  });
});
