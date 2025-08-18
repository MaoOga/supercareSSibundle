<?php
// Admin Patient Records with Proper Session Management
require_once 'admin_session_manager.php';

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Validate admin session
if (!$adminSession->validateSession()) {
    // Redirect to login if session is invalid
    header('Location: admin_login_new.html?msg=session_expired');
    exit();
}

// Get admin user info from session
$adminUser = $adminSession->getAdminInfo();
if (!$adminUser) {
    header('Location: admin_login_new.html?msg=invalid_session');
    exit();
}
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
    
    .complication-badge {
      background-color: #fef2f2;
      color: #dc2626;
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
      color: var(--button-bg);
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="sticky top-0 z-20 border-b" style="background-color: var(--bg-header); border-color: var(--border-primary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
      <div class="flex justify-between items-center">
        <div class="flex items-center gap-3">
          <img src="supercare-hospital_logo.png" alt="Hospital Logo" style="height: 70px; width: 70px">
          <div>
            <h1 class="text-xl sm:text-2xl font-bold" style="color: var(--text-primary)">
              Supercare Hospital SSI Admin Panel
            </h1>
            <p class="text-sm hidden sm:block" style="color: var(--text-secondary)">
              Patient Records Management
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
          <button id="adminLogoutBtn" class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium border min-w-[120px] hover:bg-gray-50 transition-colors" style="border-color: var(--border-secondary); color: var(--text-primary);">
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
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
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

      <!-- Results Table -->
      <div id="resultsTable" class="hidden">
        <div class="p-6 border-b" style="border-color: var(--border-secondary);">
          <div class="flex justify-between items-center">
            <h2 class="text-lg font-semibold">Search Results</h2>
            <span id="resultCount" class="text-sm" style="color: var(--text-secondary);"></span>
          </div>
        </div>
        
        <div class="table-container">
          <table class="w-full">
            <thead class="sticky top-0" style="background-color: var(--bg-table-header);">
              <tr class="border-b" style="border-color: var(--border-secondary);">
                <th class="p-3 sm:p-4 text-left font-semibold text-sm">UHID</th>
                <th class="p-3 sm:p-4 text-left font-semibold text-sm">Name</th>
                <th class="p-3 sm:p-4 text-left font-semibold text-sm">Age/Sex</th>
                <th class="p-3 sm:p-4 text-left font-semibold text-sm">Ward</th>
                <th class="p-3 sm:p-4 text-left font-semibold text-sm">Surgeon</th>
                <th class="p-3 sm:p-4 text-left font-semibold text-sm">Surgery Date</th>
                <th class="p-3 sm:p-4 text-left font-semibold text-sm">Status</th>
                <th class="p-3 sm:p-4 text-center font-semibold text-sm">Actions</th>
              </tr>
            </thead>
            <tbody id="tableBody">
              <!-- Results will be populated here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- Mobile Bottom Navigation -->
  <nav class="mobile-bottom-nav sm:hidden fixed bottom-0 left-0 right-0 border-t z-30" style="background-color: var(--bg-card); border-color: var(--border-secondary); padding-bottom: max(env(safe-area-inset-bottom), 8px);">
    <div class="grid grid-cols-4 items-stretch">
      <a href="admin.php" class="nav-item">
        <i class="fas fa-shield-halved"></i>
        <span>Admin</span>
      </a>
      <a href="#" class="nav-item is-active">
        <i class="fas fa-user-injured"></i>
        <span>Patients</span>
      </a>
      <a href="audit_log.php" class="nav-item">
        <i class="fas fa-chart-line"></i>
        <span>Audit</span>
      </a>
      <button id="adminNavLogout" class="nav-item">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </button>
    </div>
  </nav>

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
    const resultsTable = document.getElementById('resultsTable');
    const tableBody = document.getElementById('tableBody');
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
      resultsTable.classList.add('hidden');

      try {
        // Build query parameters
        const params = new URLSearchParams();
        if (searchQuery) params.append('query', searchQuery);
        if (statusFilter !== 'all') params.append('status', statusFilter);
        if (startDateValue) params.append('startDate', startDateValue);
        if (endDateValue) params.append('endDate', endDateValue);

        // Fetch search results
        const response = await fetch(`search_patients.php?${params.toString()}`);
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
      
      const tableHTML = patients.map(patient => `
        <tr class="table-row border-b" style="border-color: var(--border-secondary);">
          <td class="p-3 sm:p-4 font-medium">${patient.uhid || '—'}</td>
          <td class="p-3 sm:p-4">${patient.name || '—'}</td>
          <td class="p-3 sm:p-4">${patient.age || '—'}/${patient.sex || '—'}</td>
          <td class="p-3 sm:p-4">${patient.ward || '—'}</td>
          <td class="p-3 sm:p-4">${patient.surgeon || '—'}</td>
          <td class="p-3 sm:p-4">${patient.surgery_date || '—'}</td>
          <td class="p-3 sm:p-4">
            <span class="inline-block px-2 py-1 rounded-full text-xs font-medium ${getStatusClass(patient.status)}">
              ${patient.status || 'Unknown'}
            </span>
          </td>
          <td class="p-3 sm:p-4 text-center">
            <button onclick="viewDetails('${patient.uhid}')" class="action-button px-3 py-1 rounded text-xs font-medium text-white mr-2" style="background-color: var(--button-bg);">
              <i class="fas fa-eye mr-1"></i>View
            </button>
          </td>
        </tr>
      `).join('');

      tableBody.innerHTML = tableHTML;
      resultsTable.classList.remove('hidden');
    }

    function getStatusClass(status) {
      switch (status?.toLowerCase()) {
        case 'completed':
          return 'bg-green-100 text-green-800';
        case 'pending':
          return 'bg-yellow-100 text-yellow-800';
        case 'cancelled':
          return 'bg-red-100 text-red-800';
        default:
          return 'bg-gray-100 text-gray-800';
      }
    }

    function viewDetails(uhid) {
      // Redirect to patient details page or show modal
      window.open(`get_patient_data.php?uhid=${uhid}`, '_blank');
    }

    function clearSearch() {
      query.value = '';
      status.value = 'all';
      startDate.value = '';
      endDate.value = '';
      
      // Hide results
      loadingState.classList.add('hidden');
      noResultsState.classList.add('hidden');
      resultsTable.classList.add('hidden');
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

    // Session activity tracking
    let activityTimeout;
    let lastActivityTime = Date.now();

    // Function to update session activity
    function updateSessionActivity() {
      fetch('update_session_activity.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ timestamp: Date.now() })
      })
      .then(response => response.json())
      .then(data => {
        if (!data.success) {
          // Session expired, redirect to login
          window.location.href = 'admin_login_new.html?msg=session_expired';
        }
      })
      .catch(error => {
        console.error('Error updating session activity:', error);
      });
    }

    // Function to reset activity timer
    function resetActivityTimer() {
      lastActivityTime = Date.now();
      clearTimeout(activityTimeout);
      
      // Update session activity every 5 minutes
      activityTimeout = setTimeout(() => {
        updateSessionActivity();
      }, 5 * 60 * 1000); // 5 minutes
    }

    // Track user activity
    function trackActivity() {
      const now = Date.now();
      // Only update if more than 1 minute has passed since last activity
      if (now - lastActivityTime > 60000) {
        resetActivityTimer();
      }
    }

    // Add activity listeners
    function addActivityListeners() {
      const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
      events.forEach(event => {
        document.addEventListener(event, trackActivity, true);
      });
    }

    // Logout functionality
    function logout() {
      showConfirmPopup("Are you sure you want to logout?", function() {
        fetch('admin_logout_new.php')
          .then(() => {
            window.location.href = 'admin_login_new.html';
          })
          .catch((error) => {
            console.error('Logout error:', error);
            window.location.href = 'admin_login_new.html';
          });
      });
    }

    document.getElementById('adminLogoutBtn')?.addEventListener('click', logout);
    document.getElementById('adminNavLogout')?.addEventListener('click', logout);

    // Initialize activity tracking
    addActivityListeners();
    resetActivityTimer();

    // Initialize with current date range (last 30 days)
    const today = new Date();
    const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
    
    startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
    endDate.value = today.toISOString().split('T')[0];

    // Auto-search on page load
    setTimeout(performSearch, 100);
  </script>
</body>
</html>
