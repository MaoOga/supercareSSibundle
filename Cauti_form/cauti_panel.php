<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>CAUTI Bundle Checklist - Nurse Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    />

    <style>
      :root {
        --bg-body: #f3f4f6;
        --bg-header: #ffffff;
        --bg-card: #ffffff;
        --bg-table-header: #ecfdf5;
        --text-primary: #111827;
        --text-secondary: #6b7280;
        --text-icon: rgb(26, 175, 81);
        --border-primary: #86efac;
        --border-secondary: #e5e7eb;
        --button-bg: rgb(26, 175, 81);
        --button-bg-hover: rgb(22, 163, 74);
        --button-text: #ffffff;
        --link-text: rgb(26, 175, 81);
        --link-text-hover: rgb(22, 163, 74);
        --table-row-hover: #f9fafb;
        --dropdown-bg: #ffffff;
        --dropdown-border: #e5e7eb;
        --dropdown-hover-bg: #ecfdf5;
        --dropdown-hover-text: rgb(26, 175, 81);
        --label-color: rgb(46, 226, 130);
        --search-icon: #9ca3af;
        --clear-button-bg: #e5e7eb;
        --clear-button-bg-hover: #d1d5db;
        --clear-button-text: #374151;
      }

      .dark {
        --bg-body: #1f2937;
        --bg-header: #374151;
        --bg-card: #374151;
        --bg-table-header: #064e3b;
        --text-primary: #f9fafb;
        --text-secondary: #d1d5db;
        --text-icon: #6ee7b7;
        --border-primary: #064e3b;
        --border-secondary: #4b5563;
        --button-bg: #4ade80;
        --button-bg-hover: #22c55e;
        --button-text: #1f2937;
        --link-text: #4ade80;
        --link-text-hover: #22c55e;
        --table-row-hover: #4b5563;
        --dropdown-bg: #374151;
        --dropdown-border: #4b5563;
        --dropdown-hover-bg: #064e3b;
        --dropdown-hover-text: #22c55e;
        --label-color: #6ee7b7;
        --search-icon: #d1d5db;
        --clear-button-bg: #4b5563;
        --clear-button-bg-hover: #6b7280;
        --clear-button-text: #f9fafb;
      }

      body {
        font-family: "Inter", sans-serif;
        background-color: var(--bg-body);
        color: var(--text-primary);
        overflow-x: hidden;
      }

      *,
      *::before,
      *::after {
        box-sizing: border-box;
      }

      .table-container {
        max-height: 65vh;
        overflow-y: auto;
      }

      .table-row {
        transition: all 0.2s ease;
      }

      .table-row:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: var(--table-row-hover);
      }

      .sort-icon {
        transition: transform 0.2s ease;
      }

      .sort-icon.asc {
        transform: rotate(180deg);
      }

      .fade-in {
        animation: fadeIn 0.5s ease-in;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(10px);
        }

        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      .stat-card {
        background: linear-gradient(
          135deg,
          rgb(110, 210, 130) 0%,
          rgb(56, 161, 105) 100%
        );
        color: #ffffff;
        padding: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
          0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .dark .stat-card {
        background: linear-gradient(
          135deg,
          rgb(74, 222, 128) 0%,
          rgb(34, 197, 94) 100%
        );
      }

      .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
      }

      .complication-badge {
        animation: pulse 2s infinite;
      }

      @keyframes pulse {
        0%,
        100% {
          opacity: 1;
        }

        50% {
          opacity: 0.7;
        }
      }

      .filter-feedback {
        display: none;
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-top: 0.5rem;
      }

      .filter-feedback.active {
        display: block;
      }

      @media (max-width: 640px) {
        .nurse-table {
          display: block;
          overflow-x: hidden;
          white-space: normal;
          width: 100%;
        }

        .nurse-table thead {
          display: none;
        }

        .nurse-table tbody,
        .nurse-table tr {
          display: block;
          white-space: normal;
        }

        .nurse-table tr {
          margin-bottom: 1rem;
          border: 1px solid var(--border-secondary);
          border-radius: 0.75rem;
          padding: 1rem;
          background: var(--bg-card);
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .nurse-table td {
          display: flex;
          justify-content: space-between;
          align-items: center;
          padding: 0.25rem 0;
          border: none;
          min-width: 0;
          word-break: break-word;
          overflow-wrap: anywhere;
        }

        .nurse-table td:before {
          content: attr(data-label);
          font-weight: 600;
          color: var(--label-color);
          width: 40%;
          flex-shrink: 0;
        }

        .action-button {
          width: 100%;
          max-width: 200px;
          justify-content: center;
          margin-top: 0.5rem;
          margin-left: auto;
          margin-right: auto;
          border-radius: 0.25rem;
          padding: 0.5rem 1rem;
          font-size: 0.875rem;
        }

        .new-patient-button,
        .theme-toggle {
          width: 100%;
          border-radius: 0.25rem;
          padding: 0.75rem;
        }

        .stat-grid {
          grid-template-columns: 1fr;
          gap: 1rem;
        }

        .search-container {
          flex-direction: column;
          gap: 1rem;
        }

        .search-container input,
        .search-container select,
        .search-container button {
          width: 100%;
        }

        .table-container {
          max-width: 100vw;
          overflow-x: hidden;
        }

        .filter-feedback {
          text-align: center;
        }
      }

      @media (max-width: 640px) {
        .new-patient-button {
          width: 100%;
          border-radius: 0.25rem;
          padding: 0.1rem 0.25rem;
        }

        .theme-toggle {
          width: auto;
          padding: 0.5rem;
        }
      }

      @media (min-width: 1024px) {
        .stat-grid {
          grid-template-columns: repeat(4, 1fr);
        }
      }

      /* Mobile visual refinements */
      @media (max-width: 640px) {
        header .max-w-7xl {
          padding-top: 0.75rem !important;
          padding-bottom: 0.75rem !important;
        }
        header img[alt="Hospital Logo"] {
          height: 56px !important;
          width: 56px !important;
        }

        header h1 {
          font-size: 1.125rem !important;
          line-height: 1.5 !important;
        }
        header p {
          font-size: 0.875rem !important;
        }

        /* Mobile nurse ID display refinements */
        #mobileNurseIdDisplay {
          font-size: 0.75rem !important;
          padding: 0.25rem 0.5rem !important;
        }
        #mobileNurseIdDisplay i {
          font-size: 0.75rem !important;
        }
        #mobileNurseIdDisplay span {
          font-size: 0.75rem !important;
        }
      }

      /* Mobile bottom nav improvements */
      .mobile-bottom-nav {
        -webkit-tap-highlight-color: transparent;
        backdrop-filter: blur(4px);
        z-index: 1000;
      }
      .mobile-bottom-nav .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px 6px;
        color: var(--text-primary);
        text-decoration: none;
        user-select: none;
        -webkit-user-select: none;
        transition: all 0.2s ease;
        width: 100%;
        text-align: center;
      }
      .mobile-bottom-nav .nav-item:hover {
        color: var(--button-bg);
        transform: translateY(-1px);
      }
      .mobile-bottom-nav .nav-item i {
        font-size: 18px;
        line-height: 1;
      }
      .mobile-bottom-nav .nav-item span {
        font-size: 11px;
        line-height: 1;
        margin-top: 2px;
        font-weight: 500;
      }

      /* Ensure mobile nav is always visible */
      @media (max-width: 640px) {
        .mobile-bottom-nav {
          display: flex !important;
          position: fixed !important;
          bottom: 0 !important;
          left: 0 !important;
          right: 0 !important;
          background-color: var(--bg-card) !important;
          border-top: 1px solid var(--border-secondary) !important;
          box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1) !important;
        }
        .mobile-bottom-nav .grid {
          width: 100%;
          height: 100%;
        }
        .mobile-bottom-nav .nav-item {
          justify-self: center;
          align-self: center;
        }
      }

      .mobile-bottom-nav .nav-item.is-active {
        color: var(--button-bg);
      }
      .mobile-bottom-nav .nav-item:active {
        transform: translateY(1px);
      }

      /* Logout popup styles */
      #logoutPopup {
        backdrop-filter: blur(4px);
      }

      #logoutPopup .bg-white {
        animation: popupSlideIn 0.3s ease-out;
      }

      @keyframes popupSlideIn {
        from {
          opacity: 0;
          transform: translateY(-20px) scale(0.95);
        }
        to {
          opacity: 1;
          transform: translateY(0) scale(1);
        }
      }

      /* Mobile responsive adjustments */
      @media (max-width: 640px) {
        #logoutPopup .max-w-md {
          max-width: calc(100vw - 2rem);
          margin: 1rem;
        }
      }

      /* Hide main content until session check is complete */
      #mainContent {
        display: none;
      }

      /* Show main content when session is verified */
      .session-verified #mainContent {
        display: block;
      }
    </style>
  </head>

  <body>
    <!-- Loading Screen -->
    <div
      id="loadingScreen"
      class="fixed inset-0 bg-white flex items-center justify-center z-50"
    >
      <div class="text-center">
        <div
          class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-500 mx-auto mb-4"
        ></div>
        <p class="text-gray-600">Checking session...</p>
      </div>
    </div>

    <div id="mainContent">
      <header
        class="shadow-lg sticky top-0 z-20 border-b"
        style="
          background-color: var(--bg-header);
          border-color: var(--button-bg);
        "
      >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <img
                src="../assets/supercare-hospital_logo.png"
                alt="Hospital Logo"
                style="height: 56px; width: 56px"
              />
              <div>
                <h1
                  class="text-xl sm:text-2xl font-bold"
                  style="color: var(--text-primary)"
                >
                  Supercare Hospital CAUTI Nurse Panel
                </h1>
                <p
                  class="text-sm hidden sm:block"
                  style="color: var(--text-secondary)"
                >
                  Catheter-Associated Urinary Tract Infection Monitoring System
                </p>
              </div>
            </div>

            <!-- Mobile Nurse ID Display (Top Right) -->
            <div class="sm:hidden flex items-center">
              <div
                class="flex items-center gap-1 px-1.5 py-0.5 rounded border text-xs"
                style="
                  border-color: var(--border-primary);
                  background-color: var(--bg-card);
                  color: var(--text-secondary);
                "
                id="mobileNurseIdDisplay"
              >
                <i
                  class="fas fa-user-nurse text-xs"
                  style="color: var(--text-icon)"
                ></i>
                <span class="font-medium text-xs">ID:</span>
                <span class="font-bold text-xs" id="mobileNurseIdValue"
                  >Loading...</span
                >
              </div>
            </div>

            <div class="hidden sm:flex items-center gap-3 sm:gap-4">
              <a
                id="newPatientButton"
                href="cauti.html"
                target="_blank"
                rel="noopener noreferrer"
                class="new-patient-button text-white px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300 text-sm sm:text-base hover:bg-[var(--button-bg-hover)]"
                style="background-color: var(--button-bg)"
              >
                <i class="fas fa-plus"></i>
                <span>New CAUTI Record</span>
              </a>
              <div class="flex items-center gap-3">
                <!-- Nurse ID Display -->
                <div
                  class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-lg border text-sm"
                  style="
                    border-color: var(--border-primary);
                    background-color: var(--bg-card);
                    color: var(--text-secondary);
                  "
                  id="nurseIdDisplay"
                >
                  <i
                    class="fas fa-user-nurse text-xs"
                    style="color: var(--text-icon)"
                  ></i>
                  <span class="font-medium">Nurse ID:</span>
                  <span class="font-bold" id="nurseIdValue">Loading...</span>
                </div>

                <!-- Logout Button -->
                <button
                  onclick="showLogoutPopup()"
                  class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-lg transition-colors duration-200"
                  id="logoutBtn"
                >
                  <i class="fas fa-sign-out-alt"></i>
                  <span>Logout</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <main
        id="mainContent"
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-32 sm:pb-6"
      >
        <div class="grid stat-grid gap-4 sm:gap-6 mb-6 sm:mb-8">
          <div class="stat-card text-white p-4 sm:p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/90 text-sm font-medium">Total Patients</p>
                <p class="text-2xl sm:text-3xl font-bold" id="totalPatients">
                  0
                </p>
              </div>
              <i class="fas fa-users text-2xl sm:text-3xl text-white/80"></i>
            </div>
          </div>
          <div class="stat-card text-white p-4 sm:p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/90 text-sm font-medium">CAUTI Cases</p>
                <p
                  class="text-2xl sm:text-3xl font-bold"
                  id="cautiCasesCount"
                >
                  0
                </p>
              </div>
              <i
                class="fas fa-exclamation-triangle text-2xl sm:text-3xl text-white/80"
              ></i>
            </div>
          </div>
          <div class="stat-card text-white p-4 sm:p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/90 text-sm font-medium">
                  Active Catheters
                </p>
                <p
                  class="text-2xl sm:text-3xl font-bold"
                  id="activeCathetersCount"
                >
                  0
                </p>
              </div>
              <i
                class="fas fa-syringe text-2xl sm:text-3xl text-white/80"
              ></i>
            </div>
          </div>
          <div class="stat-card text-white p-4 sm:p-6 rounded-xl shadow-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-white/90 text-sm font-medium">Pending Reviews</p>
                <p
                  class="text-2xl sm:text-3xl font-bold"
                  id="pendingReviewsCount"
                >
                  0
                </p>
              </div>
              <i class="fas fa-clock text-2xl sm:text-3xl text-white/80"></i>
            </div>
          </div>
        </div>

        <div
          class="rounded-xl shadow-lg p-4 sm:p-6 mb-6 border"
          style="
            background-color: var(--bg-card);
            border-color: var(--border-primary);
          "
        >
          <div class="search-container flex flex-wrap items-end gap-3">
            <div class="relative w-full min-w-0 md:flex-1 md:min-w-[240px]">
              <input
                type="text"
                id="searchInput"
                placeholder="Search by UHID, Name, or Ward..."
                class="w-full p-3 pl-10 border rounded-lg shadow-sm focus:outline-none focus:ring-2 text-sm sm:text-base"
                style="
                  border-color: var(--border-secondary);
                  background-color: var(--bg-card);
                  color: var(--text-primary);
                  --tw-ring-color: var(--button-bg);
                "
              />
              <i
                class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2"
                style="color: var(--search-icon)"
              ></i>
            </div>
            <div class="w-full md:w-56">
              <select
                id="filterStatus"
                class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 text-sm sm:text-base"
                style="
                  border-color: var(--border-secondary);
                  background-color: var(--bg-card);
                  color: var(--text-primary);
                  --tw-ring-color: var(--button-bg);
                "
              >
                <option value="all">All Status</option>
                <option value="active-catheter">Active Catheter</option>
                <option value="catheter-removed">Catheter Removed</option>
                <option value="cauti-positive">CAUTI Positive</option>
                <option value="pending-review">Pending Review</option>
              </select>
            </div>

            <div class="w-full md:w-auto md:ml-auto flex gap-2">
              <button
                id="applyFilters"
                class="px-4 py-3 rounded-lg flex items-center justify-center gap-2 text-sm sm:text-base hover:bg-[var(--button-bg-hover)]"
                style="
                  background-color: var(--button-bg);
                  color: var(--button-text);
                "
              >
                <i class="fas fa-filter"></i>
                <span>Apply Filters</span>
              </button>
              <button
                id="clearFilters"
                class="px-4 py-3 rounded-lg flex items-center justify-center gap-2 text-sm sm:text-base hover:bg-[var(--clear-button-bg-hover)]"
                style="
                  background-color: var(--clear-button-bg);
                  color: var(--clear-button-text);
                "
              >
                <i class="fas fa-times"></i>
                <span>Clear</span>
              </button>
            </div>
          </div>
          <div id="filterFeedback" class="filter-feedback"></div>
        </div>

        <div
          class="rounded-xl shadow-lg border fade-in"
          style="
            background-color: var(--bg-card);
            border-color: var(--border-primary);
          "
        >
          <div
            class="p-4 sm:p-6 border-b"
            style="border-color: var(--border-secondary)"
          >
            <div
              class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
            >
              <h2
                class="text-lg sm:text-xl font-semibold"
                style="color: var(--text-primary)"
              >
                Recent CAUTI Records (<span id="recordCount">0</span>
                total)
              </h2>
            </div>
          </div>
          <div class="table-container">
            <table class="nurse-table w-full text-sm sm:text-base">
              <thead
                class="sticky top-0 z-10"
                style="background-color: var(--bg-table-header)"
              >
                <tr style="color: var(--text-primary)">
                  <th class="p-3 sm:p-4 text-left font-semibold">UHID</th>
                  <th class="p-3 sm:p-4 text-left font-semibold">Name</th>
                  <th class="p-3 sm:p-4 text-left font-semibold">Age/Sex</th>
                  <th class="p-3 sm:p-4 text-left font-semibold">Ward</th>
                  <th class="p-3 sm:p-4 text-left font-semibold">
                    Catheter Date
                  </th>
                  <th class="p-3 sm:p-4 text-left font-semibold">Status</th>
                  <th class="p-3 sm:p-4 text-left font-semibold"></th>
                </tr>
              </thead>
              <tbody id="patientTableBody">
                <!-- Loading message - will be replaced by JavaScript -->
                <tr>
                  <td
                    colspan="7"
                    style="
                      text-align: center;
                      padding: 2rem;
                      color: var(--text-secondary);
                    "
                  >
                    <i
                      class="fas fa-spinner fa-spin"
                      style="margin-right: 8px; font-size: 24px"
                    ></i>
                    Loading patient data...
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>

      <!-- Mobile Bottom Navigation -->
      <nav
        id="mobileNav"
        class="mobile-bottom-nav sm:hidden fixed bottom-0 left-0 right-0 border-t shadow-lg z-30"
        style="
          background-color: var(--bg-card);
          border-color: var(--border-secondary);
          padding-bottom: max(env(safe-area-inset-bottom), 8px);
        "
      >
        <div class="grid grid-cols-3 items-stretch justify-items-center">
          <button id="navHome" class="nav-item">
            <i class="fas fa-home"></i>
            <span>Home</span>
          </button>
          <a
            id="navAdd"
            href="cauti.html"
            target="_blank"
            rel="noopener noreferrer"
            class="nav-item"
          >
            <i class="fas fa-plus-circle"></i>
            <span>New</span>
          </a>

          <!-- Mobile Logout Button -->
          <button id="navLogout" onclick="showLogoutPopup()" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </button>
        </div>
      </nav>

      <!-- Logout Confirmation Popup -->
      <div
        id="logoutPopup"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden"
      >
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
          <div class="p-6">
            <div class="flex items-center mb-4">
              <div class="flex-shrink-0">
                <i
                  class="fas fa-exclamation-triangle text-yellow-500 text-2xl"
                ></i>
              </div>
              <div class="ml-3">
                <h3 class="text-lg font-medium text-gray-900">
                  Confirm Logout
                </h3>
              </div>
            </div>
            <div class="mb-6">
              <p class="text-sm text-gray-500">
                Are you sure you want to logout? You will need to login again to
                access the system.
              </p>
            </div>
            <div class="flex justify-end space-x-3">
              <button
                onclick="hideLogoutPopup()"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors duration-200"
              >
                Cancel
              </button>
              <button
                onclick="performLogout()"
                class="px-4 py-2 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-md transition-colors duration-200"
              >
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script>
      // DOM elements
      const searchInput = document.getElementById("searchInput");
      const filterStatus = document.getElementById("filterStatus");
      const applyFilters = document.getElementById("applyFilters");
      const clearFilters = document.getElementById("clearFilters");
      const filterFeedback = document.getElementById("filterFeedback");
      const tableBody = document.getElementById("patientTableBody");
      const recordCount = document.getElementById("recordCount");
      const mainContent = document.getElementById("mainContent");
      // Statistics elements
      const totalPatients = document.getElementById("totalPatients");
      const cautiCasesCount = document.getElementById("cautiCasesCount");
      const activeCathetersCount = document.getElementById("activeCathetersCount");
      const pendingReviewsCount = document.getElementById("pendingReviewsCount");
      // Mobile nav elements
      const mobileNav = document.getElementById("mobileNav");
      const navHome = document.getElementById("navHome");
      const navAdd = document.getElementById("navAdd");

      // Mobile nav: Home scroll-to-top
      navHome.addEventListener("click", () =>
        window.scrollTo({ top: 0, behavior: "smooth" })
      );
      
      // Mobile logout functionality
      const navLogout = document.getElementById("navLogout");
      if (navLogout) {
        navLogout.addEventListener("click", (e) => {
          e.preventDefault();
          showLogoutPopup();
        });
      }

      // Search and filter functionality
      function performSearch() {
        const searchTerm = searchInput.value.toLowerCase().trim();
        const statusFilter = filterStatus.value;
        const rows = tableBody.getElementsByTagName("tr");
        let visibleCount = 0;

        // Prepare feedback message
        let messages = [];
        if (searchTerm) {
          messages.push(`search: "${searchTerm}"`);
        }
        if (statusFilter !== "all") {
          const statusText =
            filterStatus.options[filterStatus.selectedIndex].text;
          messages.push(`status: ${statusText}`);
        }
        if (messages.length > 0) {
          filterFeedback.textContent = `Filters applied: ${messages.join(
            ", "
          )}`;
          filterFeedback.classList.add("active");
          filterFeedback.classList.remove("text-red-600");
        } else {
          filterFeedback.textContent = "";
          filterFeedback.classList.remove("active", "text-red-600");
        }

        for (let row of rows) {
          if (row.children.length < 7) continue; // Skip empty state row

          const uhid = (row.dataset.uhid || "").toLowerCase();
          const name = (row.dataset.name || "").toLowerCase();
          const ward = (row.dataset.ward || "").toLowerCase();
          const catheterStatus = row.dataset.catheterStatus || "";
          const cautiStatus = row.dataset.cautiStatus || "";
          const reviewStatus = row.dataset.review || "";
          let matchSearch = true;
          let matchFilter = true;

          // Search match
          if (searchTerm) {
            matchSearch =
              uhid.includes(searchTerm) ||
              name.includes(searchTerm) ||
              ward.includes(searchTerm);
          }

          // Status filter match
          if (statusFilter !== "all") {
            switch (statusFilter) {
              case "active-catheter":
                matchFilter = catheterStatus === "Active";
                break;
              case "catheter-removed":
                matchFilter = catheterStatus === "Removed";
                break;
              case "cauti-positive":
                matchFilter = cautiStatus === "Positive";
                break;
              case "pending-review":
                matchFilter = reviewStatus === "Pending";
                break;
            }
          }

          row.style.display = matchSearch && matchFilter ? "" : "none";
          if (matchSearch && matchFilter) {
            visibleCount++;
          }
        }

        recordCount.textContent = visibleCount;
      }

      // Event listeners
      applyFilters.addEventListener("click", () => {
        performSearch();
      });

      clearFilters.addEventListener("click", () => {
        searchInput.value = "";
        filterStatus.value = "all";
        filterFeedback.textContent = "";
        filterFeedback.classList.remove("active", "text-red-600");
        performSearch();
      });

      // Load patient data from database
      function loadPatientData() {
        // For frontend-only version, show empty state or sample data
        // When backend is ready, uncomment the fetch code below
        
        /*
        fetch("get_cauti_patients.php")
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              populatePatientTable(data.patients);
              recordCount.textContent = data.total_count;
              // Update statistics
              if (data.statistics) {
                totalPatients.textContent = data.statistics.total_patients;
                cautiCasesCount.textContent = data.statistics.cauti_cases;
                activeCathetersCount.textContent = data.statistics.active_catheters;
                pendingReviewsCount.textContent = data.statistics.pending_reviews;
              }
            } else {
              console.error("Error loading patient data:", data.message);
              showEmptyState();
            }
          })
          .catch((error) => {
            console.error("Network error:", error);
            showEmptyState();
          });
        */
        
        // Show empty state for frontend-only version
        showEmptyState();
      }
      
      // Show empty state
      function showEmptyState() {
        tableBody.innerHTML = `
          <tr>
            <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
              <i class="fas fa-inbox" style="margin-right: 8px; font-size: 24px;"></i>
              No CAUTI records found. Backend not connected.
            </td>
          </tr>
        `;
        recordCount.textContent = "0";
        // Set statistics to 0
        totalPatients.textContent = "0";
        cautiCasesCount.textContent = "0";
        activeCathetersCount.textContent = "0";
        pendingReviewsCount.textContent = "0";
      }

      // Populate the patient table with data
      function populatePatientTable(patients) {
        if (patients.length === 0) {
          tableBody.innerHTML = `
            <tr>
              <td colspan="7" style="text-align: center; padding: 2rem; color: var(--text-secondary);">
                <i class="fas fa-inbox" style="margin-right: 8px; font-size: 24px;"></i>
                No CAUTI records found
              </td>
            </tr>
          `;
          return;
        }

        tableBody.innerHTML = "";

        patients.forEach((patient) => {
          const ageSex = `${patient.age || "N/A"}/${patient.sex || "N/A"}`;
          const catheterStatus = patient.catheter_status || "N/A";
          const cautiStatus = patient.cauti_status || "Negative";
          const reviewStatus = patient.review_status || "Pending";

          const row = document.createElement("tr");
          row.className = "table-row border-b";
          row.style.cssText =
            "background-color: var(--bg-card); border-color: var(--border-secondary);";
          row.dataset.uhid = patient.uhid;
          row.dataset.name = patient.name;
          row.dataset.ward = patient.ward;
          row.dataset.catheterStatus = catheterStatus;
          row.dataset.cautiStatus = cautiStatus;
          row.dataset.review = reviewStatus;

          // Determine status badges
          let statusBadges = "";
          
          // Catheter Status
          if (catheterStatus === "Active") {
            statusBadges += `
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-syringe mr-1"></i>Active Catheter
              </span>
            `;
          } else if (catheterStatus === "Removed") {
            statusBadges += `
              <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-check mr-1"></i>Catheter Removed
              </span>
            `;
          }

          // CAUTI Status
          if (cautiStatus === "Positive") {
            statusBadges += `
              <span class="complication-badge bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-exclamation-triangle mr-1"></i>CAUTI Positive
              </span>
            `;
          }

          // Review Status
          if (reviewStatus === "Completed") {
            statusBadges += `
              <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Completed</span>
            `;
          } else if (reviewStatus === "In Progress") {
            statusBadges += `
              <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">In Progress</span>
            `;
          } else {
            statusBadges += `
              <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Pending</span>
            `;
          }

          row.innerHTML = `
            <td class="p-3 sm:p-4" data-label="UHID">
              <span class="font-medium">${patient.uhid || "N/A"}</span>
            </td>
            <td class="p-3 sm:p-4" data-label="Name">
              <span class="font-medium">${patient.name || "N/A"}</span>
            </td>
            <td class="p-3 sm:p-4" data-label="Age/Sex">${ageSex}</td>
            <td class="p-3 sm:p-4" data-label="Ward">${
              patient.ward || "N/A"
            }</td>
            <td class="p-3 sm:p-4" data-label="Catheter Date">${
              patient.catheter_date || "N/A"
            }</td>
            <td class="p-3 sm:p-4" data-label="Status">
              <div class="flex flex-col gap-1 sm:gap-2">
                ${statusBadges}
              </div>
            </td>
            <td class="p-3 sm:p-4">
              <div class="flex justify-center">
                <a href="cauti.html?patient_id=${
                  patient.patient_id
                }" target="_blank" rel="noopener noreferrer" 
                   class="action-button w-full max-w-[200px] mx-auto sm:mx-0 text-white px-3 sm:px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-all duration-300 text-sm hover:bg-[var(--button-bg-hover)]"
                   style="background-color: var(--button-bg)">
                  <i class="fas fa-eye"></i>
                  <span>View/Edit</span>
                </a>
              </div>
            </td>
          `;

          tableBody.appendChild(row);
        });
      }

      // Function to load and display nurse ID
      async function loadNurseInfo() {
        try {
          // Fetch session information from sessionStorage
          const nurseInfo = sessionStorage.getItem("nurseInfo");
          
          // Desktop nurse ID display
          const nurseIdValue = document.getElementById("nurseIdValue");
          const nurseIdDisplay = document.getElementById("nurseIdDisplay");

          // Mobile nurse ID display
          const mobileNurseIdValue =
            document.getElementById("mobileNurseIdValue");
          const mobileNurseIdDisplay = document.getElementById(
            "mobileNurseIdDisplay"
          );

          if (nurseInfo) {
            const nurse = JSON.parse(nurseInfo);

            if (nurseIdValue && nurseIdDisplay) {
              // Display nurse ID on desktop
              nurseIdValue.textContent = nurse.nurse_id || nurse.username || "Unknown";
              nurseIdDisplay.style.display = "flex";
            }

            if (mobileNurseIdValue && mobileNurseIdDisplay) {
              // Display nurse ID on mobile
              mobileNurseIdValue.textContent = nurse.nurse_id || nurse.username || "Unknown";
              mobileNurseIdDisplay.style.display = "flex";
            }

            console.log("Nurse ID displayed:", nurse.nurse_id || nurse.username);
          } else {
            // For frontend-only version, show "Guest" or default nurse ID
            if (nurseIdValue && nurseIdDisplay) {
              nurseIdValue.textContent = "Guest";
              nurseIdDisplay.style.display = "flex";
            }
            if (mobileNurseIdValue && mobileNurseIdDisplay) {
              mobileNurseIdValue.textContent = "Guest";
              mobileNurseIdDisplay.style.display = "flex";
            }
          }
        } catch (error) {
          console.error("Error loading nurse info:", error);
          // Show default for frontend-only version
          const nurseIdValue = document.getElementById("nurseIdValue");
          const nurseIdDisplay = document.getElementById("nurseIdDisplay");
          const mobileNurseIdValue = document.getElementById("mobileNurseIdValue");
          const mobileNurseIdDisplay = document.getElementById("mobileNurseIdDisplay");

          if (nurseIdValue && nurseIdDisplay) {
            nurseIdValue.textContent = "Guest";
            nurseIdDisplay.style.display = "flex";
          }
          if (mobileNurseIdValue && mobileNurseIdDisplay) {
            mobileNurseIdValue.textContent = "Guest";
            mobileNurseIdDisplay.style.display = "flex";
          }
        }
      }

      // Logout functionality
      function showLogoutPopup() {
        const popup = document.getElementById("logoutPopup");
        popup.classList.remove("hidden");
        // Prevent body scroll when popup is open
        document.body.style.overflow = "hidden";
      }

      function hideLogoutPopup() {
        const popup = document.getElementById("logoutPopup");
        popup.classList.add("hidden");
        // Restore body scroll
        document.body.style.overflow = "auto";
      }

      async function performLogout() {
        try {
          // Show loading state
          const logoutBtn = document.querySelector(
            '#logoutPopup button[onclick="performLogout()"]'
          );
          const originalText = logoutBtn.innerHTML;
          logoutBtn.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Logging out...';
          logoutBtn.disabled = true;

          // Clear session storage
          sessionStorage.removeItem("nurseInfo");

          // For frontend-only version, just redirect to login
          window.location.href = "cauti_login.html";
        } catch (error) {
          console.error("Logout error:", error);
          alert("Logout failed. Please try again.");
          // Restore button state
          const logoutBtn = document.querySelector(
            '#logoutPopup button[onclick="performLogout()"]'
          );
          logoutBtn.innerHTML = "Logout";
          logoutBtn.disabled = false;
        }
      }

      // Close popup when clicking outside
      document.addEventListener("click", function (event) {
        const popup = document.getElementById("logoutPopup");
        if (event.target === popup) {
          hideLogoutPopup();
        }
      });

      // Close popup with Escape key
      document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
          hideLogoutPopup();
        }
      });

      // Hide loading screen and show main content
      function hideLoadingScreen() {
        const loadingScreen = document.getElementById("loadingScreen");
        if (loadingScreen) {
          loadingScreen.style.display = "none";
        }
        // Add session-verified class to show main content
        document.body.classList.add("session-verified");
      }

      // Load patient data when page loads
      document.addEventListener("DOMContentLoaded", function () {
        // Hide loading screen
        hideLoadingScreen();

        // Load nurse info and patient data
        loadNurseInfo();
        loadPatientData();

        // Refresh data every 30 seconds
        setInterval(loadPatientData, 30000);
      });
    </script>
  </body>
</html>

