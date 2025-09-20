<?php
session_start();

if (!isset($_SESSION['customer_name'])) {
  header("Location: ../unRegistedUserModule/loginPage.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="../../css/styles.css" />
  <link
    rel="icon"
    type="image/x-icon"
    href="../../src/images/fav/systemLogo.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link
    href="https://fonts.googleapis.com/css2?family=Sarina&family=Shrikhand&family=Yeseva+One&display=swap"
    rel="stylesheet" />

  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Sarina&family=Shrikhand&display=swap"
    rel="stylesheet" />
  <style>
    .step {
      display: none;
    }

    .step.active {
      display: block;
    }
  </style>
  <title>Customer's Dashboard</title>
</head>

<body class="[#f0f0f0] h-screen">
  <div class="flex items-center justify-start rtl:justify-end">
    <button
      data-drawer-target="logo-sidebar"
      data-drawer-toggle="logo-sidebar"
      aria-controls="logo-sidebar"
      type="button"
      class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
      <span class="sr-only">Open sidebar</span>
      <svg
        class="w-6 h-6"
        aria-hidden="true"
        fill="currentColor"
        viewBox="0 0 20 20"
        xmlns="http://www.w3.org/2000/svg">
        <path
          clip-rule="evenodd"
          fill-rule="evenodd"
          d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
      </svg>
    </button>
  </div>
  <aside
    id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
    aria-label="Sidebar">
    <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    SIDEBAR - STARTS HERE                                                                                                        =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
    <div class="w-64 bg-white text-black flex flex-col h-screen">
      <img
        class="animate-fadeSlide"
        src="../../src/svg/systemLogo.jpg"
        alt="" />
      <nav class="flex-1 p-4 space-y-2 border-t-2 border-[#986555]">
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200"
          data-module="overview">
          Overview
        </a>
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200"
          data-module="bookAVehicle">
          Book a Vehicle
        </a>
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200"
          data-module="myBookings">
          My Bookings
        </a>
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200"
          data-module="payment">
          Payment
        </a>
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200"
          data-module="account">
          Account
        </a>
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-200"
          data-module="help">
          Help
        </a>
      </nav>
      <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                             VEHICLE INVENTORY NAVBAR - ENDS HERE                                                                                                =
    =================================================================================================================================================================================================================================================== 
    -->
    </div>
    <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    SIDEBAR - ENDS HERE                                                                                                          =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
  </aside>

  <div class="sm:ml-64">
    <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    MAIN CONTENT - STARTS HERE                                                                                                   =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
    <main class="flex-1 p-6 overflow-y-auto h-screen bg-[#f0f0f0]">
      <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    HEADER - STARTS HERE                                                                                                         =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
      <header
        class="animate-fadeSlide flex flex-col sm:flex-row sm:items-center sm:justify-between bg-white shadow px-4 sm:px-6 py-4 rounded-lg mb-6">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
          <h1 class="text-xl sm:text-2xl font-bold text-gray-800">
            Car Rentals Dashboard
          </h1>
          <p class="text-xs sm:text-sm text-gray-500">
            Manage vehicles, reservations, and reports
          </p>
        </div>
        <div
          class="flex items-center justify-between sm:justify-end space-x-4">
          <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                    USER PROFILE                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
          <div class="flex items-center space-x-2">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              class="w-7 h-7"
              fill="none"
              stroke="currentColor"
              stroke-width="1.5"
              stroke-linecap="round"
              stroke-linejoin="round">
              <!-- User head -->
              <circle cx="9" cy="7" r="4" />
              <path d="M2 21v-2a4 4 0 0 1 4-4h6" />
              <path
                d="M17 11l5 2v3a7 7 0 0 1-5 6.7A7 7 0 0 1 12 16v-3l5-2z" />
            </svg>
            <div class="relative inline-block text-left">
              <button
                id="userMenuBtn"
                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 rounded-md">
                <div class="text-left">
                  <p class="font-medium text-gray-700">
                    <?php echo htmlspecialchars($_SESSION['customer_name']); ?>
                  </p>
                  <p class="text-gray-500 text-xs">Customer</p>
                </div>
                <svg
                  class="w-4 h-4 text-gray-500"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7" />
                </svg>
              </button>
              <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                    DROP DOWN                                                                                                                    =
    =================================================================================================================================================================================================================================================== 
    -->
              <div
                id="userDropdown"
                class="absolute right-0 mt-2 w-40 bg-white border border-gray-200 rounded-md shadow-lg hidden">
                <div class="border-t border-gray-200"></div>
                <a
                  href="../../includes/logout.php"
                  class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Logout</a>
              </div>
            </div>
          </div>
        </div>
      </header>
      <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    HEADER - ENDS HERE                                                                                                     =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
      <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    DASHBOARDS - STARTS HERE                                                                                                     =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
      <!-- Overview -->
      <section id="overview" class="bg-gray-100 p-6 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-2">Overview</h2>
        <p>Quick overview of your account and bookings.</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
          <div class="bg-white rounded shadow p-4 text-center">
            <h3 class="font-semibold">Total Bookings</h3>
            <p class="text-2xl font-bold">8</p>
          </div>
          <div class="bg-white rounded shadow p-4 text-center">
            <h3 class="font-semibold">Active Rentals</h3>
            <p class="text-2xl font-bold">3</p>
          </div>
          <div class="bg-white rounded shadow p-4 text-center">
            <h3 class="font-semibold">Pending Payments</h3>
            <p class="text-2xl font-bold">2</p>
          </div>
        </div>
      </section>
      <!-- Book a Vehicle -->
      <section id="bookAVehicle" class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <h2 class="text-2xl font-bold mb-2 text-gray-800">Book a Vehicle</h2>
        <p class="text-sm text-gray-500 mb-6">Complete the steps to reserve your vehicle.</p>

        <!-- Step Container -->
        <div class="flex flex-col items-center space-y-6">

          <!-- Step 1: Booking Details -->
          <div class="step active w-full max-w-3xl bg-white p-8 rounded-xl shadow border border-[#df927f]">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Step 1: Booking Details</h2>
            <div class="mb-5">
              <label class="block mb-2 text-sm font-medium text-gray-700">Details About the Trip:</label>
              <input type="text" id="tripDetails"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5" />
            </div>
            <div class="grid md:grid-cols-2 gap-4 mb-5">
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Pick-up Date</label>
                <input type="date" id="pickUpDate"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5" />
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Drop-off Date</label>
                <input type="date" id="dropOffDate"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5" />
              </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4 mb-5">
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Pick-up Time</label>
                <input type="time" id="pickUpTime"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5" />
              </div>
              <div>
                <label class="block mb-2 text-sm font-medium text-gray-700">Drop-off Time</label>
                <input type="time" id="dropOffTime"
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5" />
              </div>
            </div>
            <button onclick="nextStep()"
              class="w-full text-white bg-[#df927f] hover:bg-[#c97c65] font-medium rounded-lg text-sm px-5 py-2.5">
              Next
            </button>
          </div>

          <!-- Step 2: Car Selection -->
          <div class="step w-full max-w-3xl bg-white p-8 rounded-xl shadow border border-[#df927f]">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Step 2: Select Your Car</h2>

            <div class="flex flex-wrap justify-center gap-4">
              <!-- Car 1 -->
              <label class="relative cursor-pointer w-72">
                <input type="radio" name="selectedCar" value="Toyota Vios XLE 2022" class="hidden peer" checked />
                <div
                  class="bg-gray-50 border border-[#df927f] rounded-lg p-4 w-full hover:shadow-md transition peer-checked:ring-2 peer-checked:ring-[#df927f] peer-checked:bg-orange-50">
                  <img src="../../src/images/carsImage/toyotaviospearlwhite.png" alt="Toyota Vios"
                    class="w-full h-40 object-contain mb-2" />
                  <h3 class="text-gray-800 font-bold text-center">Toyota Vios XLE 2022</h3>
                  <p class="text-gray-500 text-center mt-1">4 Passenger • Automatic • Blue Metallic</p>
                  <p class="text-[#df927f] font-semibold text-center mt-2">PHP 2,000</p>
                </div>
              </label>

              <!-- Car 2 -->
              <label class="relative cursor-pointer w-72">
                <input type="radio" name="selectedCar" value="Honda Civic 2023" class="hidden peer" />
                <div
                  class="bg-gray-50 border border-[#df927f] rounded-lg p-4 w-full hover:shadow-md transition peer-checked:ring-2 peer-checked:ring-[#df927f] peer-checked:bg-orange-50">
                  <img src="../../src/images/carsImage/blueviosXLE2022.png" alt="Honda Civic"
                    class="w-full h-40 object-contain mb-2" />
                  <h3 class="text-gray-800 font-bold text-center">Honda Civic 2023</h3>
                  <p class="text-gray-500 text-center mt-1">4 Passenger • Automatic • Red</p>
                  <p class="text-[#df927f] font-semibold text-center mt-2">PHP 2,500</p>
                </div>
              </label>
            </div>

            <button onclick="nextStep()"
              class="w-full mt-5 text-white bg-[#df927f] hover:bg-[#c97c65] font-medium rounded-lg text-sm px-5 py-2.5">
              Next
            </button>
          </div>


          <!-- Step 3: Terms & Conditions -->
          <div class="step w-full max-w-3xl bg-white p-8 rounded-xl shadow border border-[#df927f]">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Step 3: Terms & Conditions</h2>
            <div class="bg-gray-50 p-4 rounded-lg max-h-[50vh] overflow-y-auto text-sm text-gray-700">
              <ol class="list-decimal list-inside space-y-2">
                <li>Package includes vehicle, fuel, toll fees, driver.</li>
                <li>Extra hours incur additional fees.</li>
                <li>Payments via cash or GCash.</li>
                <li>No-show within 3 hours = canceled reservation.</li>
                <li>Cleaning fees PHP 500 for mess caused by client.</li>
                <li>Renter responsible for damages.</li>
                <li>Must provide valid ID.</li>
              </ol>
              <div class="mt-4 flex items-center gap-2">
                <input type="checkbox" id="agree" onclick="toggleNext(this)" />
                <label for="agree" class="text-gray-700">I agree to the terms & conditions</label>
              </div>
              <button id="nextBtn" onclick="nextStep()" disabled
                class="w-full mt-4 text-white bg-[#df927f] hover:bg-[#c97c65] font-medium rounded-lg text-sm px-5 py-2.5">
                Next
              </button>
            </div>
          </div>

          <!-- Step 4: Summary -->
          <div class="step w-full max-w-3xl bg-white p-8 rounded-xl shadow border border-[#df927f]">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Step 4: Summary</h2>
            <div class="bg-gray-50 p-6 rounded-lg text-sm space-y-2">
              <p><strong>Trip Details:</strong> <span id="summaryTrip"></span></p>
              <p><strong>Pick-up Date:</strong> <span id="summaryPickUp"></span></p>
              <p><strong>Drop-off Date:</strong> <span id="summaryDropOff"></span></p>
              <p><strong>Car Selected:</strong> <span id="summaryCar"></span></p>
              <p><strong>Total:</strong> PHP <span id="summaryPrice"></span></p>
            </div>
            <button onclick="nextStep()"
              class="w-full mt-5 text-white bg-[#df927f] hover:bg-[#c97c65] font-medium rounded-lg text-sm px-5 py-2.5">
              Proceed to Payment
            </button>
          </div>

          <!-- Step 5: Payment -->
          <div class="step w-full max-w-3xl bg-white p-8 rounded-xl shadow border border-[#df927f]">
            <h2 class="text-xl font-bold text-gray-800 mb-6 text-center">Step 5: Payment</h2>
            <div class="bg-gray-50 p-6 rounded-lg flex flex-col items-center">
              <p class="mb-4">Scan GCash QR to pay:</p>
              <img src="https://via.placeholder.com/200x200" alt="GCash QR Code" class="mb-4" />
              <button
                class="w-full text-white bg-[#df927f] hover:bg-[#c97c65] font-medium rounded-lg text-sm px-5 py-2.5">
                Upload Receipt
              </button>
            </div>
          </div>

        </div>
      </section>


      <!-- My Bookings -->
      <section
        id="myBookings"
        class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <h2 class="text-2xl font-bold mb-2">My Bookings</h2>
        <p>View details, cancel, or modify your reservations.</p>
        <table
          class="w-full mt-4 text-sm bg-white rounded shadow overflow-hidden">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-3 py-2 text-left">Vehicle</th>
              <th class="px-3 py-2 text-left">Pickup Date</th>
              <th class="px-3 py-2 text-left">Return Date</th>
              <th class="px-3 py-2 text-left">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b">
              <td class="px-3 py-2">Toyota Vios</td>
              <td class="px-3 py-2">2025-09-10</td>
              <td class="px-3 py-2">2025-09-12</td>
              <td class="px-3 py-2 text-green-600">Confirmed</td>
            </tr>
            <tr class="border-b">
              <td class="px-3 py-2">Honda Civic</td>
              <td class="px-3 py-2">2025-09-15</td>
              <td class="px-3 py-2">2025-09-18</td>
              <td class="px-3 py-2 text-yellow-600">Pending</td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Payment -->
      <section
        id="payment"
        class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <h2 class="text-2xl font-bold mb-2">Payment</h2>
        <p>View payment history and status.</p>
        <table
          class="w-full mt-4 text-sm bg-white rounded shadow overflow-hidden">
          <thead class="bg-gray-200">
            <tr>
              <th class="px-3 py-2 text-left">Booking</th>
              <th class="px-3 py-2 text-left">Amount</th>
              <th class="px-3 py-2 text-left">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-b">
              <td class="px-3 py-2">Toyota Vios (Sep 10-12)</td>
              <td class="px-3 py-2">₱2,500</td>
              <td class="px-3 py-2 text-green-600">Paid</td>
            </tr>
            <tr class="border-b">
              <td class="px-3 py-2">Honda Civic (Sep 15-18)</td>
              <td class="px-3 py-2">₱3,200</td>
              <td class="px-3 py-2 text-yellow-600">Pending</td>
            </tr>
          </tbody>
        </table>
      </section>

      <!-- Account -->
      <section
        id="account"
        class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <h2 class="text-2xl font-bold mb-2">Account</h2>
        <p>
          Personal info, contact details, preferences, and change
          password/PIN.
        </p>
        <div class="mt-4 space-y-2 bg-white rounded shadow p-4">
          <p><span class="font-semibold">Name:</span> Juan Dela Cruz</p>
          <p>
            <span class="font-semibold">Email:</span> juan.delacruz@email.com
          </p>
          <p><span class="font-semibold">Phone:</span> +63 912 345 6789</p>
          <p>
            <span class="font-semibold">Preferred Vehicle Type:</span> Sedan
          </p>
        </div>
      </section>

      <!-- Help -->
      <section
        id="help"
        class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <h2 class="text-2xl font-bold mb-2">Help</h2>
        <p>FAQs, contact support, and guidance.</p>
        <ul class="mt-4 space-y-2 bg-white rounded shadow p-4">
          <li>How do I cancel a booking?</li>
          <li>How do I update my personal information?</li>
          <li>How can I contact customer support?</li>
        </ul>
      </section>

      <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    REPORTS - ENDS HERE                                                                                                          =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
    </main>

    <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    MAIN CONTENTS - ENDS HERE                                                                                                    =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->

    <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    JAVASCRIPT LINKS STARTS HERE                                                                                                 =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
  </div>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
  <script src="../../js/customerModules.js"></script>
  <script src="../../js/adminDropdownLogout.js"></script>
  <script src="../../js/customerModuleBookAVehicle.js"></script>

  <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    JAVASCRIPT LINKS ENDS HERE                                                                                                   =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
</body>

</html>