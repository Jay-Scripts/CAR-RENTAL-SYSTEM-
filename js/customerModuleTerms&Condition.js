const checkbox2 = document.getElementById("checkbox2");
const checkmark2 = document.getElementById("checkmark2");
const openModal = document.getElementById("openModal");
const modalOverlay = document.getElementById("modalOverlay");
const closeModal = document.getElementById("closeModal");
const acceptModal = document.getElementById("acceptModal");
const declineModal = document.getElementById("declineModal");
const customerBooking = document.getElementById("customer_booking");
let agreed2 = false;

// Initially disable booking button
customerBooking.disabled = true;
customerBooking.classList.add("cursor-not-allowed", "opacity-50");

openModal.addEventListener("click", () => {
  modalOverlay.classList.remove("hidden");
  modalOverlay.classList.add("flex", "fade-in");
});

closeModal.addEventListener("click", () => {
  modalOverlay.classList.add("hidden");
  modalOverlay.classList.remove("flex", "fade-in");
});

acceptModal.addEventListener("click", () => {
  agreed2 = true;
  checkbox2.classList.remove("border-gray-300");
  checkbox2.classList.add("border-green-500", "bg-green-500");
  checkmark2.classList.remove("hidden");
  modalOverlay.classList.add("hidden");
  modalOverlay.classList.remove("flex", "fade-in");

  // Enable booking button
  customerBooking.disabled = false;
  customerBooking.classList.remove("cursor-not-allowed", "opacity-50");
});

declineModal.addEventListener("click", () => {
  agreed2 = false;
  checkbox2.classList.add("border-gray-300");
  checkbox2.classList.remove("border-green-500", "bg-green-500");
  checkmark2.classList.add("hidden");
  modalOverlay.classList.add("hidden");
  modalOverlay.classList.remove("flex", "fade-in");

  // Disable booking button again if declined
  customerBooking.disabled = true;
  customerBooking.classList.add("cursor-not-allowed", "opacity-50");
});

// Close modal when clicking outside
modalOverlay.addEventListener("click", (e) => {
  if (e.target === modalOverlay) {
    modalOverlay.classList.add("hidden");
    modalOverlay.classList.remove("flex", "fade-in");
  }
});
