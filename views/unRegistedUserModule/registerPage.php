<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <link
    href="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.css"
    rel="stylesheet" />
  <link rel="stylesheet" href="../../css/styles.css" />
</head>

<body>
  <header class="fade-in">
    <nav class="bg-[#df927f] border-black shadow-sm shadow-black">
      <div class="flex flex-wrap items-center justify-between p-5">
        <a
          href="#"
          class="flex items-center space-x-3 rtl:space-x-reverse"
          id="headerName">
          <span
            class="self-center text-2xl md:text-4xl whitespace-nowrap text-white font-bold">CarRental</span>
        </a>
        <button
          data-collapse-toggle="navbar-dropdown"
          type="button"
          class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200"
          aria-controls="navbar-dropdown"
          aria-expanded="false">
          <span class="sr-only">Open main menu</span>
          <svg
            class="w-5 h-5"
            aria-hidden="true"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 17 14">
            <path
              stroke="currentColor"
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M1 1h15M1 7h15M1 13h15" />
          </svg>
        </button>
        <div class="hidden w-full md:block md:w-auto" id="navbar-dropdown">
          <ul
            class="flex flex-col font-medium p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-transparent md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0">
            <li>
              <a
                href="../../index.html"
                class="block py-2 px-3 text-white rounded-sm md:bg-transparent md:p-0"
                aria-current="page">Home</a>
            </li>

            <li>
              <a
                href="./about.html"
                class="block py-2 px-3 text-white rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">About</a>
            </li>
            <li>
              <a
                href="contacts.html"
                class="block py-2 px-3 text-white rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Contacts</a>
            </li>
            <li>
              <a
                href="./privacyPolicy.html"
                class="block py-2 px-3 text-white rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0">Privacy Policy</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  <main
    class="flex flex-col justify-around h-[100vh] fade-in bg-[url('../../src/images/mainbg.jpg')] bg-cover bg-center w-full"
    style="animation-delay: 0.2s">
    <section
      class="flex items-center justify-center mx-4 sm:mx-10 
         max-h-screen sm:max-h-screen 
         overflow-y-auto p-4 m-7 rounded-lg w-full">
      <?php include "../../includes/customerRegistration.php" ?>
    </section>

  </main>

  <footer
    class="bg-[#837671] shadow-sm fade-in"
    style="animation-delay: 0.5s">
    <div class="w-full mx-auto p-4 md:py-8">
      <div class="sm:flex sm:items-center sm:justify-between">
        <a
          href="https://flowbite.com/"
          class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
          <span
            class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">CarRental</span>
        </a>
        <ul
          class="flex flex-wrap items-center mb-6 text-sm font-medium text-white sm:mb-0">
          <li>
            <a href="#" class="hover:underline me-4 md:me-6">Contact</a>
          </li>
          <li>
            <a href="#" class="hover:underline me-4 md:me-6">Terms and Condition</a>
          </li>
          <li>
            <a href="#" class="hover:underline me-4 md:me-6">Privacy Policy
            </a>
          </li>
        </ul>
      </div>
      <hr
        class="my-6 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
      <span class="block text-sm text-white sm:text-center">© 2025
        <a href="https://flowbite.com/" class="hover:underline">CarRental™ </a>All rights reserved.</span>
    </div>
  </footer>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@3.1.2/dist/flowbite.min.js"></script>
</body>

</html>