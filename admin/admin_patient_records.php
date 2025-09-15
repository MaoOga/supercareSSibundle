<?php
// Admin Patient Records - Session Authentication Required
require_once '../auth/admin_session_config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirectToAdminLogin('Please log in to access patient records');
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Patient Records - SSI Admin Panel</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    :root {
      --bg-body: #f3f4f6;
      --bg-header: #ffffff;
      --bg-card: #ffffff;
      --bg-table-header: #fee2e2; /* red-200 */
      --text-primary: #111827;
      --text-secondary: #6b7280;
      --text-icon: #dc2626; /* red-600 */
      --border-primary: #fecaca; /* red-300 */
      --border-secondary: #e5e7eb;
      --button-bg: #dc2626; /* red-600 */
      --button-bg-hover: #b91c1c; /* red-700 */
      --button-text: #ffffff;
      --link-text: #dc2626;
      --link-text-hover: #b91c1c;
      --table-row-hover: #fef2f2; /* red-50 */
    }
    
    body { 
      font-family: 'Inter', sans-serif; 
      background-color: var(--bg-body); 
      color: var(--text-primary); 
      overflow-x: hidden;
    }
    
    .patient-card {
      transition: all 0.3s ease;
      border-radius: 12px;
      overflow: hidden;
    }
    
    .patient-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .complication-badge {
      background-color: #fef2f2;
      color: #dc2626;
      animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.7; }
    }
    
    .action-button {
      transition: all 0.3s ease;
    }
    
    .action-button:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    /* Mobile header consistency */
    @media (max-width: 640px) {
      header .max-w-7xl {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
      }
      header img[alt="Hospital Logo"] {
        height: 48px !important;
        width: 48px !important;
      }
      header h1 {
        font-size: 1rem !important;
        line-height: 1.4 !important;
      }
      header p {
        font-size: 0.875rem !important;
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
    }
    .mobile-bottom-nav .nav-item i {
      font-size: 18px;
      line-height: 1;
    }
    .mobile-bottom-nav .nav-item span {
      font-size: 11px;
      margin-top: 2px;
    }
    .mobile-bottom-nav .nav-item.is-active {
      color: #dc2626; /* Red color for active state */
      background-color: transparent !important; /* Remove any background */
    }
    
    .mobile-bottom-nav .nav-item.is-active i {
      color: #dc2626; /* Red color for active icon */
    }
    
    /* Logout popup styles */
    .logout-popup {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 9999;
    }
    
    .logout-popup-content {
      background: white;
      border-radius: 12px;
      padding: 24px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .logout-popup-icon {
      font-size: 48px;
      color: #dc2626;
      margin-bottom: 16px;
    }
    
    .logout-popup-title {
      font-size: 20px;
      font-weight: 600;
      color: #111827;
      margin-bottom: 8px;
    }
    
    .logout-popup-message {
      font-size: 14px;
      color: #6b7280;
      margin-bottom: 24px;
    }
    
    .logout-popup-buttons {
      display: flex;
      gap: 12px;
      justify-content: center;
    }
    
    .logout-popup-btn {
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      border: none;
      min-width: 100px;
    }
    
    .logout-popup-btn-cancel {
      background-color: #f3f4f6;
      color: #374151;
    }
    
    .logout-popup-btn-cancel:hover {
      background-color: #e5e7eb;
    }
    
    .logout-popup-btn-confirm {
      background-color: #dc2626;
      color: white;
    }
    
    .logout-popup-btn-confirm:hover {
      background-color: #b91c1c;
    }
    
    .logout-popup-btn-confirm:disabled {
      background-color: #9ca3af;
      cursor: not-allowed;
    }
    
    /* Logout popup animations */
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
    
    /* Mobile navigation improvements */
    @media (max-width: 640px) {
      .nav-item {
        min-width: 60px;
        padding: 8px 4px;
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }
      
      .nav-item i {
        font-size: 18px;
        margin-bottom: 2px;
      }
      
      .nav-item span {
        font-size: 11px;
        font-weight: 500;
      }
      
      /* Mobile bottom nav specific */
      .mobile-bottom-nav {
        padding: 8px 0;
      }
      
      .mobile-bottom-nav .nav-item {
        border-radius: 8px;
        transition: all 0.2s ease;
      }
      
      .mobile-bottom-nav .nav-item:hover {
        background-color: rgba(0, 0, 0, 0.05);
      }
      
      /* Mobile responsive adjustments for logout popup */
      #logoutPopup .max-w-md {
        max-width: calc(100vw - 2rem);
        margin: 1rem;
      }
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="sticky top-0 z-20 border-b" style="background-color: var(--bg-header); border-color: var(--border-primary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
          <img src="../assets/supercare-hospital_logo.png" alt="Hospital Logo" style="height: 70px; width: 70px">
          <div>
            <h1 class="text-xl sm:text-2xl font-bold" style="color: var(--text-primary)">
              Supercare Hospital SSI Admin Panel
            </h1>
            <p class="text-sm hidden sm:block" style="color: var(--text-secondary)">
              Patient Records Management
            </p>
            <p class="text-xs sm:text-sm font-medium" style="color: var(--text-icon);">
              <i class="fas fa-user-shield mr-1"></i>
              Logged in as: <span id="adminUsername">Loading...</span>
            </p>
          </div>
        </div>
        <div class="hidden sm:flex items-center gap-3 sm:gap-4">
          <a href="admin.php" class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-[var(--button-bg-hover)] min-w-[120px]" style="background-color: var(--button-bg); color: var(--button-text);">
            <i class="fas fa-shield-halved"></i>
            <span>Admin Panel</span>
          </a>
          <a href="audit_log.php" class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-[var(--button-bg-hover)] min-w-[120px]" style="background-color: var(--button-bg); color: var(--button-text);">
            <i class="fas fa-chart-line"></i>
            <span>Audit Log</span>
          </a>
          <button onclick="showLogoutPopup()" class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-red-700 min-w-[120px] bg-red-600 text-white">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Main Content -->
  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 sm:pb-6">
    <!-- Search Form -->
    <div class="rounded-xl shadow-lg p-4 sm:p-6 mb-6 border" style="background-color: var(--bg-card); border-color: var(--border-primary);">
      <div class="mb-4">
        <h2 class="text-lg sm:text-xl font-semibold">Search Patient Records</h2>
        <p class="text-sm" style="color: var(--text-secondary);">Search and view patient records added by nurses</p>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
        <div>
          <label for="query" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
            <i class="fas fa-search mr-2"></i>Search UHID/Name
          </label>
          <input type="text" id="query" placeholder="Enter UHID or patient name" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" style="border-color: var(--border-secondary);">
        </div>
        
        <div>
          <label for="status" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
            <i class="fas fa-filter mr-2"></i>Status
          </label>
          <select id="status" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" style="border-color: var(--border-secondary);">
            <option value="all">All Status</option>
            <option value="pending">Pending Review</option>
            <option value="completed">Completed</option>
            <option value="complications">With Complications</option>
            <option value="no-complications">No Complications</option>
          </select>
        </div>
        
        <div>
          <label for="startDate" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
            <i class="fas fa-calendar mr-2"></i>Start Date
          </label>
          <input type="date" id="startDate" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" style="border-color: var(--border-secondary);">
        </div>
        
        <div>
          <label for="endDate" class="block text-sm font-medium mb-2" style="color: var(--text-primary);">
            <i class="fas fa-calendar mr-2"></i>End Date
          </label>
          <input type="date" id="endDate" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent" style="border-color: var(--border-secondary);">
        </div>
      </div>
      
      <div class="flex flex-col sm:flex-row gap-3">
        <button id="runSearch" class="px-6 py-3 rounded-lg text-white font-medium flex items-center justify-center gap-2 hover:bg-[var(--button-bg-hover)] transition-colors" style="background-color: var(--button-bg);">
          <i class="fas fa-search"></i>
          <span>Search Records</span>
        </button>
        <button id="clear" class="px-6 py-3 rounded-lg border font-medium flex items-center justify-center gap-2 hover:bg-gray-50 transition-colors" style="border-color: var(--border-secondary); color: var(--text-primary);">
          <i class="fas fa-times"></i>
          <span>Clear</span>
        </button>
      </div>
    </div>

    <!-- Results Section -->
    <div id="results" class="rounded-xl shadow-lg border" style="background-color: var(--bg-card); border-color: var(--border-secondary);">
      <!-- Loading State -->
      <div id="loadingState" class="hidden p-8 text-center">
        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2" style="border-color: var(--button-bg);"></div>
        <p class="mt-2 text-sm" style="color: var(--text-secondary);">Searching...</p>
      </div>

      <!-- No Results State -->
      <div id="noResultsState" class="hidden p-8 text-center">
        <i class="fas fa-search text-4xl mb-4" style="color: var(--text-secondary);"></i>
        <p class="text-lg font-medium mb-2">No results found</p>
        <p class="text-sm" style="color: var(--text-secondary);">Try adjusting your search criteria</p>
      </div>

      <!-- Results Cards -->
      <div id="resultsCards" class="hidden">
        <div class="p-6 border-b" style="border-color: var(--border-secondary);">
          <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">Search Results</h2>
            <span id="resultCount" class="text-sm" style="color: var(--text-secondary);"></span>
          </div>
        </div>
        
        <div id="cardsContainer" class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Patient cards will be populated here -->
        </div>
      </div>
    </div>
  </main>

  <!-- Mobile Bottom Navigation -->
  <nav class="mobile-bottom-nav sm:hidden fixed bottom-0 left-0 right-0 border-t z-30" style="background-color: var(--bg-card); border-color: var(--border-secondary); padding-bottom: max(env(safe-area-inset-bottom), 8px);">
    <div class="flex items-center justify-around">
      <!-- Admin -->
      <a href="admin.php" class="nav-item">
        <i class="fas fa-shield-halved"></i>
        <span>Admin</span>
      </a>
      
      <!-- Patients -->
      <a href="#" class="nav-item is-active">
        <i class="fas fa-user-injured"></i>
        <span>Patients</span>
      </a>
      
      <!-- Audit -->
      <a href="audit_log.php" class="nav-item">
        <i class="fas fa-chart-line"></i>
        <span>Audit</span>
      </a>
      
      <!-- Logout -->
      <a href="#" class="nav-item logout-btn text-red-600 hover:text-red-700" onclick="showLogoutPopup(); return false;">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </a>
    </div>
  </nav>

  <!-- Logout Confirmation Popup -->
  <div id="logoutPopup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden" style="backdrop-filter: blur(4px);">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" style="animation: popupSlideIn 0.3s ease-out;">
      <div class="p-6">
        <div class="text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
            <i class="fas fa-sign-out-alt text-red-600 text-xl"></i>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">Confirm Logout</h3>
          <div class="mb-6">
            <p class="text-sm text-gray-500">
              Are you sure you want to logout? You will need to login again to access the admin panel.
            </p>
          </div>
          <div class="flex space-x-3">
            <button id="cancelLogout" class="flex-1 bg-gray-300 text-gray-700 py-2 px-4 rounded-md text-sm font-medium hover:bg-gray-400 transition-colors duration-200">
              Cancel
            </button>
            <button id="confirmLogout" class="flex-1 bg-red-600 text-white py-2 px-4 rounded-md text-sm font-medium hover:bg-red-700 transition-colors duration-200">
              Logout
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // DOM Elements
    const runBtn = document.getElementById('runSearch');
    const clearBtn = document.getElementById('clear');
    const query = document.getElementById('query');
    const status = document.getElementById('status');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const results = document.getElementById('results');
    const loadingState = document.getElementById('loadingState');
    const noResultsState = document.getElementById('noResultsState');
    const resultsCards = document.getElementById('resultsCards');
    const cardsContainer = document.getElementById('cardsContainer');
    const resultCount = document.getElementById('resultCount');

    // Search functionality
    async function performSearch() {
      console.log('Performing search...');
      const searchQuery = query.value.trim();
      const statusFilter = status.value;
      const startDateValue = startDate.value;
      const endDateValue = endDate.value;

      console.log('Search parameters:', { searchQuery, statusFilter, startDateValue, endDateValue });

      // Show loading state
      loadingState.classList.remove('hidden');
      noResultsState.classList.add('hidden');
      resultsCards.classList.add('hidden');

      try {
        // Build query parameters
        const params = new URLSearchParams();
        if (searchQuery) params.append('query', searchQuery);
        if (statusFilter !== 'all') params.append('status', statusFilter);
        if (startDateValue) params.append('startDate', startDateValue);
        if (endDateValue) params.append('endDate', endDateValue);

        // Fetch search results
        const response = await fetch(`../forms/search_patients.php?${params.toString()}`);
        const data = await response.json();

        // Debug: Log the response
        console.log('Search response:', data);

        // Hide loading state
        loadingState.classList.add('hidden');

        if (data.success) {
          const patients = data.patients || [];
          
          if (patients.length === 0) {
            noResultsState.classList.remove('hidden');
          } else {
            displayResults(patients);
          }
        } else {
          console.error('Search failed:', data.message);
          noResultsState.classList.remove('hidden');
        }
      } catch (error) {
        console.error('Search error:', error);
        loadingState.classList.add('hidden');
        noResultsState.classList.remove('hidden');
      }
    }

    function displayResults(patients) {
      resultCount.textContent = `${patients.length} record(s) found`;
      
      const cardsHTML = patients.map(patient => `
        <div class="patient-card bg-white border rounded-xl shadow-lg overflow-hidden" style="border-color: var(--border-secondary);">
          <div class="p-6">
            <!-- Header with UHID and Status -->
            <div class="flex justify-between items-start mb-4">
              <div>
                <h3 class="text-lg font-semibold text-gray-900">${patient.uhid || '—'}</h3>
                <p class="text-sm text-gray-600">${patient.name || '—'}</p>
              </div>
              <div class="flex flex-col gap-2">
                ${getStatusBadges(patient)}
              </div>
            </div>
            
            <!-- Patient Details -->
            <div class="grid grid-cols-2 gap-4 mb-4">
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Age/Sex</p>
                <p class="text-sm text-gray-900">${patient.age || '—'}/${patient.sex || '—'}</p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ward</p>
                <p class="text-sm text-gray-900">${patient.ward || '—'}</p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Surgeon</p>
                <p class="text-sm text-gray-900">${patient.surgeon || '—'}</p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Surgery Date</p>
                <p class="text-sm text-gray-900">${patient.dos || '—'}</p>
              </div>
            </div>
            
            <!-- Diagnosis and Procedure -->
            <div class="mb-4">
              <div class="mb-2">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Diagnosis</p>
                <p class="text-sm text-gray-900">${patient.diagnosis || '—'}</p>
              </div>
              <div>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Procedure</p>
                <p class="text-sm text-gray-900">${patient.procedure || '—'}</p>
              </div>
            </div>
            
            <!-- Action Button -->
            <div class="flex justify-end">
              <button onclick="viewDetails('${patient.patient_id}')" class="action-button px-4 py-2 rounded-lg text-sm font-medium text-white" style="background-color: var(--button-bg);">
                <i class="fas fa-eye mr-2"></i>View Details
              </button>
            </div>
          </div>
        </div>
      `).join('');

      cardsContainer.innerHTML = cardsHTML;
      resultsCards.classList.remove('hidden');
    }

    function getStatusBadges(patient) {
      let badges = '';
      
      // Review status badge
      const reviewStatus = patient.review_status || 'Pending';
      let reviewClass = 'bg-yellow-100 text-yellow-800';
      if (reviewStatus === 'Completed') {
        reviewClass = 'bg-green-100 text-green-800';
      }
      
      badges += `
        <span class="inline-block px-2 py-1 rounded-full text-xs font-medium ${reviewClass}">
          ${reviewStatus}
        </span>
      `;
      
      // Complications badge
      if (patient.has_complications) {
        badges += `
          <span class="complication-badge inline-block px-2 py-1 rounded-full text-xs font-medium">
            <i class="fas fa-exclamation-triangle mr-1"></i>Complications
          </span>
        `;
      }
      
      return badges;
    }

    function viewDetails(patientId) {
      // Open the form in read-only mode for admin viewing
              window.open(`../forms/form.php?patient_id=${patientId}&readonly=true`, '_blank');
    }

    function clearSearch() {
      query.value = '';
      status.value = 'all';
      startDate.value = '';
      endDate.value = '';
      
      // Hide results
      loadingState.classList.add('hidden');
      noResultsState.classList.add('hidden');
      resultsCards.classList.add('hidden');
    }

    // Event listeners
    runBtn.addEventListener('click', performSearch);
    clearBtn.addEventListener('click', clearSearch);

    // Allow Enter key to trigger search
    query.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch();
      }
    });

    // Custom confirmation popup
    function showConfirmPopup(message, onConfirm) {
      // Remove any existing popup
      const existingPopup = document.getElementById("confirmPopup");
      if (existingPopup) {
        existingPopup.remove();
      }

      // Create popup container
      const popupContainer = document.createElement("div");
      popupContainer.id = "confirmPopup";
      popupContainer.className = "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50";

      // Create popup content
      const popupContent = document.createElement("div");
      popupContent.className = "max-w-sm mx-4 p-6 rounded-lg shadow-xl bg-white border border-gray-200";

      popupContent.innerHTML = `
         <div class="flex items-center justify-center mb-4">
           <i class="fas fa-question-circle text-yellow-500 text-3xl"></i>
         </div>
         <div class="text-center">
           <p class="text-gray-800 font-medium text-lg mb-6">${message}</p>
           <div class="flex gap-3 justify-center">
             <button onclick="closeConfirmPopup()" class="px-4 py-2 rounded-lg text-gray-600 font-medium bg-gray-200 hover:bg-gray-300 transition-colors">
               Cancel
             </button>
             <button onclick="confirmAction()" class="px-4 py-2 rounded-lg text-white font-medium bg-red-600 hover:bg-red-700 transition-colors">
               Confirm
             </button>
           </div>
         </div>
       `;

      // Add to page
      popupContainer.appendChild(popupContent);
      document.body.appendChild(popupContainer);

      // Store the callback function globally
      window.confirmActionCallback = onConfirm;
    }

    // Function to close confirmation popup
    function closeConfirmPopup() {
      const popup = document.getElementById("confirmPopup");
      if (popup) {
        popup.remove();
      }
      window.confirmActionCallback = null;
    }

    // Function to execute the confirmed action
    function confirmAction() {
      if (window.confirmActionCallback) {
        window.confirmActionCallback();
      }
      closeConfirmPopup();
    }

    // Session activity tracking removed - no authentication required

    // Logout functionality removed - no session authentication required

    // Activity tracking removed - no session authentication required

    // Session Management Functions
    async function checkAdminSession() {
      try {
        const response = await fetch('../auth/admin_session_check.php', {
          method: 'GET',
          credentials: 'same-origin'
        });
        
        if (!response.ok) {
          throw new Error('Session check failed');
        }
        
        const data = await response.json();
        
        if (data.logged_in) {
          // Update admin username display
          const adminUsername = document.getElementById("adminUsername");
          if (adminUsername) {
            adminUsername.textContent = data.admin.username || "Admin";
          }
          
          console.log("Admin session active:", data.admin.username);
          return true;
        } else {
          console.log("Session expired, redirecting to login");
          window.location.href = 'admin_login_new.html?msg=' + encodeURIComponent('Session expired. Please login again.');
          return false;
        }
      } catch (error) {
        console.error("Session check failed:", error);
        window.location.href = 'admin_login_new.html?msg=' + encodeURIComponent('Session error. Please login again.');
        return false;
      }
    }

    // Show logout popup
    function showLogoutPopup() {
      const popup = document.getElementById("logoutPopup");
      popup.classList.remove("hidden");
      // Prevent body scroll when popup is open
      document.body.style.overflow = "hidden";
    }

    // Hide logout popup
    function hideLogoutPopup() {
      const popup = document.getElementById("logoutPopup");
      popup.classList.add("hidden");
      // Restore body scroll
      document.body.style.overflow = "auto";
    }

    // Handle logout with loading state
    async function handleLogout() {
      try {
        // Show loading state
        const logoutBtn = document.getElementById("confirmLogout");
        const originalText = logoutBtn.textContent;
        logoutBtn.disabled = true;
        logoutBtn.textContent = "Logging out...";
        logoutBtn.classList.add("opacity-75");

        // Call logout API
        const response = await fetch("../auth/admin_logout_new.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          credentials: "same-origin",
        });

        if (response.ok) {
          console.log("Logout successful");
          // Redirect to login page
          window.location.href = "admin_login_new.html?msg=" + encodeURIComponent("You have been logged out successfully");
        } else {
          console.error("Logout failed");
          // Still redirect to login page
          window.location.href = "admin_login_new.html?msg=" + encodeURIComponent("Logout completed");
        }
      } catch (error) {
        console.error("Logout error:", error);
        // Still redirect to login page
        window.location.href = "admin_login_new.html?msg=" + encodeURIComponent("Logout completed");
      }
    }

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
      // Check session on page load
      checkAdminSession();
      
      // Check session every 30 seconds
      setInterval(checkAdminSession, 30000);
      
      // Logout popup event listeners
      document.getElementById("cancelLogout").addEventListener("click", hideLogoutPopup);
      document.getElementById("confirmLogout").addEventListener("click", handleLogout);

      // Close popup when clicking outside
      document.getElementById("logoutPopup").addEventListener("click", function(e) {
        if (e.target === this) {
          hideLogoutPopup();
        }
      });

      // Close popup with Escape key
      document.addEventListener("keydown", function(e) {
        if (e.key === "Escape") {
          hideLogoutPopup();
        }
      });
    });

    // Auto-search on page load (without prefilled dates)
    setTimeout(performSearch, 100);
  </script>
</body>
</html>
