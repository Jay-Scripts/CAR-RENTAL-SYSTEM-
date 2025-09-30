<?php
include "../../config/db.php";

session_start();
$userId = $_SESSION['user_id'] ?? null;


if (!isset($_SESSION['rental_agent_name'])) {
  header("Location: ../rentalAgentDashboard/rentalAgentLoginPage.php");
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
  <title>Rental Agent Dashboard</title>
</head>

<body class="[#f0f0f0] h-screen">
  <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    LOGIN MODAL - STARTS HERE                                                                                                    =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
  <!-- Login Modal (default visible) -->

  <!-- 
    <div
      id="loginModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
    >
      <div
        class="relative w-full max-w-sm sm:max-w-md md:max-w-lg p-6 bg-white rounded-2xl shadow-2xl"
      >
        <div class="flex justify-center mb-4">
          <img
            class="h-20 sm:h-28 md:h-32 animate-fadeSlide"
            src="../../src/svg/systemLogo.jpg"
            alt="System Logo"
          />
        </div>
        <h2
          class="text-center text-xl sm:text-2xl font-bold mb-2 text-gray-800"
        >
          Welcome Back
        </h2>
        <p class="text-center text-sm text-gray-500 mb-6">
          Please log in to access the rental agent dashboard
        </p>
        <form
          action="loginRentalAgentDashboard.php"
          method="POST"
          class="space-y-5"
        >
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Username
            </label>
            <input
              type="text"
              name="username"
              required
              placeholder="Enter your username"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm sm:text-base"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
              Password
            </label>
            <input
              type="password"
              name="password"
              required
              placeholder="Enter your password"
              class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none text-sm sm:text-base"
            />
            <div class="text-right mt-1">
              <a href="#" class="text-xs text-blue-600 hover:underline"
                >Forgot password?</a
              >
            </div>
          </div>
          <button
            type="submit"
            class="w-full py-3 bg-blue-600 text-white text-sm sm:text-base font-semibold rounded-lg hover:bg-blue-700 transition-colors"
          >
            Log In
          </button>
        </form>
        <p class="mt-6 text-center text-xs text-gray-500">
          Need help? Contact your system administrator.
        </p>
      </div>
    </div> -->

  <!-- 
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    LOGIN MODAL - ENDS HERE                                                                                                      =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
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
        <!-- Dashboard -->
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700"
          data-module="dashboard">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-6 h-6"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3 12h7V3H3v9zM14 21h7v-9h-7v9zM14 3v6h7V3h-7zM3 21h7v-6H3v6z" />
          </svg>
          Dashboard
        </a>

        <!-- Reservations -->
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700"
          data-module="reservations">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            stroke-width="2">
            <rect x="3" y="4" width="18" height="16" rx="2" ry="2" />
            <path d="M16 2v4M8 2v4M3 10h18" />
            <circle cx="17" cy="14" r="3" />
            <path d="M17 13v1.5l1 0.6" />
          </svg>
          For Pickup
        </a>

        <!-- Vehicle Inspection -->
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700"
          data-module="vehicleInspection">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <path d="M12 6v6l4 2" />
          </svg>
          Vehicle Inspection
        </a>

        <!-- Payments -->
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700"
          data-module="payments">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            stroke-width="2">
            <rect x="2" y="5" width="20" height="14" rx="2" ry="2" />
            <path d="M2 9h20" />
            <path d="M6 15h2M10 15h4" />
          </svg>
          Payments
        </a>

        <!-- Turnover Vehicle -->
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700"
          data-module="turnoverVehicle">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="w-6 h-6"
            fill="none"
            stroke="currentColor"
            stroke-width="2">
            <path d="M3 7h13l5 5-5 5H3z" />
            <path d="M3 17v-4" />
          </svg>
          Turnover Vehicle
        </a>
        <a
          href="#"
          class="navItem flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-700"
          data-module="reports">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="w-7 h-7"
            fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round">
            <!-- Document outline -->
            <path
              d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
            <path d="M14 2v6h6" />
            <line x1="9" y1="17" x2="9" y2="13" />
            <line x1="12" y1="17" x2="12" y2="11" />
            <line x1="15" y1="17" x2="15" y2="15" />
          </svg>
          Reports
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
            Car Rental Admin Dashboard
          </h1>
          <p class="text-xs sm:text-sm text-gray-500">
            Monitor vehicles, track your reservations, and view payment status
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
                    <?php echo htmlspecialchars($_SESSION['rental_agent_name']); ?>
                  </p>
                  <p class="text-gray-500 text-xs">Rental Agent</p>
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
                  href="../../includes/rentalAgentLogout.php"
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
      <!-- Dashboard -->
      <section id="dashboard" class="bg-gray-500/20 rounded-lg shadow p-6">
        <?php
        include "../../includes/rentalAgentDashboard.php";
        ?>
      </section>

      <!-- Reservations -->
      <section
        id="reservations"
        class="hidden bg-gray-500/20 rounded-lg shadow p-6">
        <?php
        include "../../includes/rentalAgentForPickup.php";
        ?>
      </section>

      <!-- Vehicle Inspection -->
      <section
        id="vehicleInspection"
        class="hidden bg-gray-500/20 rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-2">Vehicle Inspection</h2>
        <p class="text-gray-600 mb-4">
          Inspection records of vehicles before/after rentals.
        </p>
        <ul class="space-y-2 text-sm">
          <li class="bg-white rounded shadow p-3 flex justify-between">
            <span>Toyota Fortuner</span>
            <span class="text-green-600">Passed</span>
          </li>
          <li class="bg-white rounded shadow p-3 flex justify-between">
            <span>Mitsubishi L300</span>
            <span class="text-red-600">Needs Repair</span>
          </li>
        </ul>
      </section>

      <!-- Payments -->
      <section
        id="payments"
        class="hidden bg-gray-500/20 rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-2">Payments</h2>
        <p class="text-gray-600 mb-4">Client payment details and history.</p>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-gray-200">
              <tr>
                <th class="px-3 py-2 text-left">Customer</th>
                <th class="px-3 py-2 text-left">Amount</th>
                <th class="px-3 py-2 text-left">Status</th>
              </tr>
            </thead>
            <tbody>
              <tr class="border-b">
                <td class="px-3 py-2">Pedro Lopez</td>
                <td class="px-3 py-2">₱5,000</td>
                <td class="px-3 py-2 text-green-600">Paid</td>
              </tr>
              <tr class="border-b">
                <td class="px-3 py-2">Anna Dela Cruz</td>
                <td class="px-3 py-2">₱3,500</td>
                <td class="px-3 py-2 text-yellow-600">Pending</td>
              </tr>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Turnover Vehicle -->
      <section
        id="turnoverVehicle"
        class="hidden bg-gray-500/20 rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-2">Turnover Vehicle</h2>
        <p class="text-gray-600 mb-4">
          Track vehicles being handed over to clients.
        </p>
        <div class="space-y-3">
          <div class="bg-white rounded shadow p-3">
            <p><span class="font-semibold">Vehicle:</span> Nissan Almera</p>
            <p><span class="font-semibold">Client:</span> Carlo Reyes</p>
            <p><span class="font-semibold">Status:</span> Ready for pickup</p>
          </div>
          <div class="bg-white rounded shadow p-3">
            <p><span class="font-semibold">Vehicle:</span> Hyundai Accent</p>
            <p><span class="font-semibold">Client:</span> Lea Mendoza</p>
            <p>
              <span class="font-semibold">Status:</span> Returned - Inspection
              pending
            </p>
          </div>
        </div>
      </section>
      <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    REPORTS - STARTS HERE                                                                                                        =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
      <section
        id="reports"
        class="animate-fadeSlide hidden bg-gray-500/20 rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-2">Reports</h2>
        <div class="mt-4">
          <p class="font-semibold">Monthly Summary</p>
          <ul class="list-disc list-inside text-gray-600 mt-2">
            <li>Total Reservations: 120</li>
            <li>Total Revenue: $12,500</li>
            <li>Top Car: Toyota Vios (50 rentals)</li>
          </ul>
        </div>
      </section>
      <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    REPORTS - ENDS HERE                                                                                                          =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
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
  <script src="../../js/rentalAgentModules.js"></script>
  <script src="../../js/adminDropdownLogout.js"></script>


  <!--
    ===================================================================================================================================================================================================================================================
    =                                                                                                                                                                                                                                                 =
    =                                                                                                                    JAVASCRIPT LINKS ENDS HERE                                                                                                   =
    =                                                                                                                                                                                                                                                 =
    =================================================================================================================================================================================================================================================== 
    -->
</body>

</html>