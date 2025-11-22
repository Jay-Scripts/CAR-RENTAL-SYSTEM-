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
                        Rental Agent Dashboard
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
                <?php
                include "../../includes/rentalAgentForInspection.php";
                ?>
            </section>

            <!-- Payments -->
            <section
                id="payments"
                class="hidden bg-gray-500/20 rounded-lg shadow p-6">
                <?php
                include "../../includes/rentalAgentPaymentPenalty.php";
                ?>
            </section>

            <!-- Turnover Vehicle -->
            <section
                id="turnoverVehicle"
                class="hidden bg-gray-500/20 rounded-lg shadow p-6">
                <?php
                include "../../includes/rentalAgentTurnOverVehicle.php";
                ?>
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