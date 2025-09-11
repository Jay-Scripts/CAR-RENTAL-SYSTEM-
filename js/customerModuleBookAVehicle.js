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
