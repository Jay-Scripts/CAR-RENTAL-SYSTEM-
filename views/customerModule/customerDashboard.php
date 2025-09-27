<?php
include "../../config/db.php";

session_start();
$userId = $_SESSION['user_id'] ?? null;


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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    .car-option {
      transition: all 0.3s ease;
    }

    .car-option input[type="radio"] {
      display: none;
    }

    .car-option input[type="radio"]:checked+.car-content {
      background-color: gray;
      color: white;
      transform: scale(1.02);
    }

    .car-option input[type="radio"]:checked+.car-content .text-orange-500 {
      color: #fbbf24 !important;
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

        <?php
        include "../../includes/customerBookAVehicleFormSubmit.php";
        ?>

      </section>


      <!-- My Bookings -->
      <section
        id="myBookings"
        class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <h2 class="text-2xl font-bold mb-2">My Bookings</h2>
        <p>View details, cancel, or modify your reservations.</p>
        <?php
        include "../../includes/customerMyBookings.php";
        ?>
      </section>

      <!-- Payment -->
      <section
        id="payment"
        class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <?php
        include "../../includes/customerPayment.php";
        ?>

      </section>

      <!-- Account -->
      <section
        id="account"
        class="hidden bg-gray-100 p-6 rounded-lg shadow mt-6">
        <?php
        include "../../includes/customerAccount.php";
        ?>
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