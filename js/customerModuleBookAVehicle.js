let currentStep = 0;
const steps = document.querySelectorAll(".step");
function showStep(n) {
  steps.forEach((step, i) => step.classList.remove("active"));
  steps[n].classList.add("active");
}

function nextStep() {
  if (currentStep === 3) updateSummary();
  if (currentStep < steps.length - 1) currentStep++;
  showStep(currentStep);
}

function toggleNext(checkbox) {
  document.getElementById("nextBtn").disabled = !checkbox.checked;
}

function selectCar(name, price) {
  window.selectedCar = { name, price };
  alert(name + " selected for PHP " + price);
}

function updateSummary() {
  document.getElementById("summaryTrip").innerText =
    document.getElementById("tripDetails").value;
  document.getElementById("summaryPickUp").innerText =
    document.getElementById("pickUpDate").value;
  document.getElementById("summaryDropOff").innerText =
    document.getElementById("dropOffDate").value;
  document.getElementById("summaryCar").innerText = selectedCar
    ? selectedCar.name
    : "";
  document.getElementById("summaryPrice").innerText = selectedCar
    ? selectedCar.price
    : "";
}

const checkbox2 = document.getElementById("checkbox2");
const checkmark2 = document.getElementById("checkmark2");
const openModal = document.getElementById("openModal");
const modalOverlay = document.getElementById("modalOverlay");
const closeModal = document.getElementById("closeModal");
const acceptModal = document.getElementById("acceptModal");
const declineModal = document.getElementById("declineModal");
let agreed2 = false;

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
});

declineModal.addEventListener("click", () => {
  agreed2 = false;
  checkbox2.classList.add("border-gray-300");
  checkbox2.classList.remove("border-green-500", "bg-green-500");
  checkmark2.classList.add("hidden");
  modalOverlay.classList.add("hidden");
  modalOverlay.classList.remove("flex", "fade-in");
});

// Close modal when clicking outside
modalOverlay.addEventListener("click", (e) => {
  if (e.target === modalOverlay) {
    modalOverlay.classList.add("hidden");
    modalOverlay.classList.remove("flex", "fade-in");
  }
});

document.querySelectorAll(".dateInput").forEach((input) => {
  input.addEventListener("input", function () {
    this.value = this.value.replace(/[^0-9/]/g, ""); // allow only numbers and "/"
  });
});
