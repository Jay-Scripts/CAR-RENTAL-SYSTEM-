<?php
include "../../config/db.php";

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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
      font-family: 'Inter', sans-serif;
    }

    .slide-in {
      animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
      from {
        transform: translateY(-10px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .fade-in {
      animation: fadeIn 0.2s ease-in;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .terms-item {
      transition: all 0.2s ease;
    }

    .terms-item:hover {
      background-color: #f9fafb;
      transform: translateX(4px);
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
      class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 ">
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
            Car Rental Customer Dashboard
          </h1>
          <p class="text-xs sm:text-sm text-gray-500">
            Book vehicles, track your reservations, and view payment status
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

        <form action="submitBooking.php" method="POST" class="w-fit mx-auto bg-white p-8 rounded-xl shadow space-y-6">

          <h2 class="text-xl font-bold text-gray-800 mb-4">Book a Vehicle</h2>

          <!-- Trip Details -->
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-700">Trip Details</label>
            <input type="text" name="tripDetails" class="w-full p-2.5 border rounded" required>
          </div>

          <!-- Dates & Times -->
          <div class="grid grid-cols-1 md:grid-cols-1 gap-4">

            <div id="date-range-picker" date-rangepicker class="flex items-center">
              <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                  <svg class="w-4 h-4 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                  </svg>
                </div>
                <input id="datepicker-range-start" name="start" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Select date start" required>
              </div>
              <span class="mx-4 text-black">to</span>
              <div class="relative">
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                  <svg class="w-4 h-4 text-black" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                  </svg>
                </div>
                <input id="datepicker-range-end" name="end" type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5" placeholder="Select date end" required>
              </div>
            </div>

          </div>



          <!-- Car Selection -->
          <?php
          include "../../includes/customerBookAVehicle.php";
          ?>

          <!-- Terms & Conditions -->
          <div class="bg-white rounded-xl  p-6">

            <div class="flex items-start space-x-3">
              <button id="checkbox2" type="button" class="mt-1 w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center hover:border-blue-400 transition-colors">
                <svg id="checkmark2" class="w-3 h-3 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
              </button>
              <div>
                <p class="text-gray-700">
                  I agree to the
                  <button id="openModal" type="button" class="text-blue-600 hover:text-blue-800 underline font-medium hover:no-underline transition-all duration-200">
                    Terms & Conditions
                  </button>
                  of this rental agreement.
                </p>
                <p class="text-xs text-gray-500 mt-1">Click to read the full terms and conditions</p>
              </div>
            </div>
          </div>





          <!-- Modal Overlay -->
          <div id="modalOverlay" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
              <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Terms & Conditions</h2>
                <button id="closeModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                  </svg>
                </button>
              </div>
              <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="space-y-4 text-sm text-gray-600">
                  <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                    <h4 class="font-semibold text-gray-800 mb-2">1. Package Inclusions</h4>
                    <p>The rental package includes the vehicle, fuel, toll fees, and a professional driver for the duration of your booking.</p>
                  </div>
                  <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-400">
                    <h4 class="font-semibold text-gray-800 mb-2">2. Additional Hours</h4>
                    <p>Any extra hours beyond the agreed rental period will incur additional fees according to our standard rate card.</p>
                  </div>
                  <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-400">
                    <h4 class="font-semibold text-gray-800 mb-2">3. Payment Methods</h4>
                    <p>We accept both cash payments and GCash digital transactions for your convenience and security.</p>
                  </div>
                  <div class="p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                    <h4 class="font-semibold text-gray-800 mb-2">4. No-Show Policy</h4>
                    <p>Failure to show up within 3 hours of the scheduled pickup time will result in automatic cancellation of your reservation.</p>
                  </div>
                  <div class="p-4 bg-red-50 rounded-lg border-l-4 border-red-400">
                    <h4 class="font-semibold text-gray-800 mb-2">5. Cleaning Fees</h4>
                    <p>A cleaning fee of PHP 500 will be charged if any mess or damage is caused by the client during the rental period.</p>
                  </div>
                  <div class="p-4 bg-orange-50 rounded-lg border-l-4 border-orange-400">
                    <h4 class="font-semibold text-gray-800 mb-2">6. Damage Liability</h4>
                    <p>The renter is fully responsible for any damages to the vehicle that occur during the rental period.</p>
                  </div>
                  <div class="p-4 bg-teal-50 rounded-lg border-l-4 border-teal-400">
                    <h4 class="font-semibold text-gray-800 mb-2">7. Identification Requirement</h4>
                    <p>A valid government-issued identification document must be provided before vehicle pickup.</p>
                  </div>
                </div>
              </div>
              <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button id="declineModal" type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                  Decline
                </button>
                <button id="acceptModal" type="button" type="button" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                  Accept Terms
                </button>
              </div>
            </div>
          </div>

          <button
            type="submit"
            id="customer_booking"
            name="customer_booking"
            disabled
            class="w-full bg-orange-400 hover:bg-orange-500 text-white p-3 rounded-lg font-bold cursor-not-allowed opacity-50">
            Book Now
          </button>

        </form>

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
  <script src="../../js/customerModules.js"></script>
  <script src="../../js/adminDropdownLogout.js"></script>
  <script src="../../js/customerModuleBookAVehicle.js"></script>
  <script src="../../js/customerModuleTerms&Condition.js"></script>

  <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    JAVASCRIPT LINKS ENDS HERE                                                                                                   =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
</body>

</html>