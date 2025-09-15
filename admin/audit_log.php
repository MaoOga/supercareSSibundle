<?php
// Audit Log - Session Authentication Required
require_once '../auth/admin_session_config.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    redirectToAdminLogin('Please log in to access audit logs');
}

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>SSI Bundle - Professional Audit Log</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      :root {
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --accent-color: #3b82f6;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --error-color: #ef4444;
      }

      body {
        font-family: "Inter", sans-serif;
        background-color: var(--bg-body);
        color: var(--text-primary);
      }

      .audit-card {
        background: var(--bg-card);
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
      }

      .status-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
      }

      .status-success {
        background-color: #dcfce7;
        color: #166534;
      }
      .status-failed {
        background-color: #fee2e2;
        color: #991b1b;
      }
      .status-pending {
        background-color: #fef3c7;
        color: #92400e;
      }

      .action-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 500;
      }

      .action-create {
        background-color: #dbeafe;
        color: #1e40af;
      }
      .action-update {
        background-color: #fef3c7;
        color: #92400e;
      }
      .action-delete {
        background-color: #fee2e2;
        color: #991b1b;
      }
      .action-login {
        background-color: #dcfce7;
        color: #166534;
      }
      .action-backup {
        background-color: #e0e7ff;
        color: #3730a3;
      }
      .action-export {
        background-color: #f3e8ff;
        color: #7c3aed;
      }

      .loading-spinner {
        border: 2px solid #f3f4f6;
        border-top: 2px solid var(--accent-color);
        border-radius: 50%;
        width: 20px;
        height: 20px;
        animation: spin 1s linear infinite;
      }

      @keyframes spin {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      .mobile-bottom-nav {
        -webkit-tap-highlight-color: transparent;
        backdrop-filter: blur(4px);
      }
      .mobile-bottom-nav .nav-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px 6px;
        color: var(--text-primary);
        text-decoration: none;
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
      @media (max-width: 640px) {
        .max-w-7xl {
          padding-bottom: 80px;
        }
        .header-buttons {
          flex-direction: column;
          gap: 0.5rem;
        }
        .header-buttons button {
          width: 100%;
          justify-content: center;
        }
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

        /* Mobile-specific styles for audit log */
        .audit-card {
          margin-bottom: 0.5rem;
          padding: 1rem !important;
        }

        /* Statistics cards mobile layout */
        .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4 {
          grid-template-columns: repeat(2, 1fr) !important;
          gap: 0.5rem !important;
        }

        /* Charts mobile layout */
        .grid.grid-cols-1.lg\\:grid-cols-2 {
          grid-template-columns: 1fr !important;
          gap: 0.75rem !important;
        }

        /* Filters mobile layout */
        .grid.grid-cols-1.md\\:grid-cols-2.lg\\:grid-cols-4.gap-4 {
          grid-template-columns: 1fr !important;
          gap: 0.75rem !important;
        }

        /* Filter buttons mobile layout */
        .flex.justify-between.items-center.mt-4 {
          flex-direction: column !important;
          gap: 1rem !important;
          align-items: stretch !important;
        }

        .flex.justify-between.items-center.mt-4 > div {
          width: 100% !important;
        }

        .flex.space-x-2 {
          justify-content: center !important;
        }

        .flex.items-center.space-x-4 {
          justify-content: center !important;
          flex-wrap: wrap !important;
          gap: 0.5rem !important;
        }

        /* Table mobile optimization */
        .overflow-x-auto {
          font-size: 0.875rem !important;
        }

        .overflow-x-auto th,
        .overflow-x-auto td {
          padding: 0.5rem 0.25rem !important;
          white-space: normal !important;
        }

        /* Modal mobile optimization */
        #detailModal .bg-white {
          margin: 1rem !important;
          max-height: 85vh !important;
        }

        /* Pagination mobile optimization */
        #pagination {
          flex-direction: column !important;
          gap: 1rem !important;
          align-items: center !important;
        }

        #pagination > div {
          width: 100% !important;
          justify-content: center !important;
        }

        /* Action badges mobile */
        .action-badge {
          font-size: 10px !important;
          padding: 2px 6px !important;
        }

        /* Status badges mobile */
        .status-badge {
          font-size: 10px !important;
          padding: 2px 6px !important;
        }

        /* Mobile button adjustments */
        .sm\\:hidden button {
          width: 60px !important;
          height: 28px !important;
          padding: 0 !important;
        }

        /* Center mobile buttons */
        .sm\\:hidden .flex.items-center.justify-end.space-x-1.mt-3 {
          justify-content: center !important;
        }

        /* Ensure mobile buttons are centered */
        .sm\\:hidden .flex.items-center.justify-end.space-x-1.mt-3 {
          justify-content: center !important;
          display: flex !important;
          align-items: center !important;
          margin-right: 0 !important;
          margin-left: 0 !important;
          width: 100% !important;
        }

        /* Chart containers mobile */
        #actionTypeChart,
        #dailyActivityChart {
          max-height: 250px !important;
          min-height: 200px !important;
        }

        /* Mobile container padding */
        .max-w-7xl.mx-auto.px-4.sm\\:px-6.lg\\:px-8.py-8 {
          padding: 0.75rem !important;
        }

        /* Ensure proper spacing for all containers on mobile */
        .max-w-7xl.mx-auto.px-4.sm\\:px-6.lg\\:px-8 {
          padding-left: 0.75rem !important;
          padding-right: 0.75rem !important;
        }
        
        /* Mobile navigation improvements */
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

      .fade-in {
        animation: fadeIn 0.3s ease-in;
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

      /* Prevent container growth issues */
      .audit-card {
        max-height: none;
        overflow: visible;
      }

      /* Ensure charts have proper sizing */
      #actionTypeChart,
      #dailyActivityChart {
        max-height: 300px;
        min-height: 200px;
      }

      /* Prevent table overflow issues */
      .overflow-x-auto {
        max-width: 100%;
        overflow-x: auto;
        overflow-y: visible;
      }

      /* Ensure modal doesn't cause layout issues */
      #detailModal {
        overflow: hidden;
      }

      #detailModal .bg-white {
        max-height: 90vh;
        overflow-y: auto;
      }

      /* Text truncation utilities */
      .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
      }

      .truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      /* Mobile touch improvements */
      @media (max-width: 640px) {
        .cursor-pointer {
          -webkit-tap-highlight-color: rgba(0, 0, 0, 0.1);
        }

        /* Improve touch targets */
        button,
        .nav-item,
        .cursor-pointer {
          min-height: 30px;
          min-width: 44px;
        }

        /* Better spacing for mobile cards */
        .space-y-3 > * + * {
          margin-top: 0.75rem;
        }

        /* Mobile card hover effects */
        .bg-gray-50.rounded-lg.p-4.border.border-gray-200.cursor-pointer:active {
          background-color: #f3f4f6;
          transform: scale(0.98);
          transition: all 0.1s ease;
        }
      }

      /* Refresh button animation styles */
      .fa-spin {
        animation: fa-spin 1s infinite linear;
      }

      @keyframes fa-spin {
        0% {
          transform: rotate(0deg);
        }
        100% {
          transform: rotate(360deg);
        }
      }

      /* Disabled button styles */
      button:disabled {
        opacity: 0.6;
        cursor: not-allowed;
      }

      button:disabled:hover {
        transform: none !important;
      }
    </style>
  </head>
  <body class="min-h-screen">
    <!-- Header -->
    <header
      class="sticky top-0 z-20 border-b"
      style="
        background-color: var(--bg-card);
        border-color: var(--border-color);
      "
    >
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <img
              src="../assets/supercare-hospital_logo.png"
              alt="Hospital Logo"
              style="height: 70px; width: 70px"
            />
                         <div>
               <h1
                 class="text-xl sm:text-2xl font-bold"
                 style="color: var(--text-primary)"
               >
                 Supercare Hospital SSI Admin Panel
               </h1>
               <p
                 class="text-sm hidden sm:block"
                 style="color: var(--text-secondary)"
               >
                 Patient Records Management
               </p>
               <p
                 class="text-xs sm:text-sm font-medium"
                 style="color: var(--error-color)"
               >
                 <i class="fas fa-user-shield mr-1"></i>
                 Logged in as: <span id="adminUsername">Loading...</span>
               </p>
             </div>
          </div>
          <div class="hidden sm:flex items-center gap-3 sm:gap-4">
            <a
              href="admin_patient_records.php"
              class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-red-700 transition-colors min-w-[120px]"
              style="background-color: #dc2626; color: white"
            >
              <i class="fas fa-user-injured"></i>
              <span>View Patient Records</span>
            </a>
            <a
                             href="admin.php"
              class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-red-700 transition-colors min-w-[120px]"
              style="background-color: #dc2626; color: white"
            >
              <i class="fas fa-shield-halved"></i>
              <span>Admin Panel</span>
            </a>
            <button onclick="showLogoutPopup()" class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-red-700 min-w-[120px] bg-red-600 text-white">
              <i class="fas fa-sign-out-alt"></i>
              <span>Logout</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Statistics Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-4 sm:mb-8 mt-4 sm:mt-8"
      >
        <div
          class="audit-card p-6"
          style="
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: white;
          "
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-white/90">Total Activities</p>
              <p id="totalActivities" class="text-2xl font-bold text-white">
                —
              </p>
            </div>
            <div class="p-3 bg-white/20 rounded-full">
              <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
          </div>
        </div>

                 <div
           class="audit-card p-6"
           style="
             background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
             color: white;
           "
         >
           <div class="flex items-center justify-between">
             <div>
                               <p class="text-sm font-medium text-white/90">Total Backup</p>
               <p id="totalBackups" class="text-2xl font-bold text-white">—</p>
             </div>
             <div class="p-3 bg-white/20 rounded-full">
               <i class="fas fa-database text-white text-xl"></i>
             </div>
           </div>
         </div>

        <div
          class="audit-card p-6"
          style="
            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
            color: white;
          "
        >
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-white/90">Changes Made</p>
              <p id="todayActivities" class="text-2xl font-bold text-white">
                —
              </p>
            </div>
            <div class="p-3 bg-white/20 rounded-full">
              <i class="fas fa-edit text-white text-xl"></i>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-8">
        <div class="audit-card p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Activity by Action Type
          </h3>
          <canvas id="actionTypeChart" height="200"></canvas>
        </div>

        <div class="audit-card p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">
            Daily Activity Trend
          </h3>
          <canvas id="dailyActivityChart" height="200"></canvas>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="audit-card p-6 mb-4 sm:mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Action Type</label
            >
            <select
              id="actionTypeFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Actions</option>
              <option value="CREATE">Create</option>
              <option value="UPDATE">Update</option>
              <option value="DELETE">Delete</option>
              <option value="LOGIN">Login</option>
              <option value="LOGOUT">Logout</option>
              <option value="BACKUP">Backup</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Entity Type</label
            >
            <select
              id="entityTypeFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Entities</option>
              <option value="NURSE">Nurse</option>
              <option value="SURGEON">Surgeon</option>
              <option value="PATIENT">Patient</option>
              <option value="BACKUP">Backup</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Status</label
            >
            <select
              id="statusFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">All Status</option>
              <option value="SUCCESS">Success</option>
              <option value="FAILED">Failed</option>
              <option value="PENDING">Pending</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2"
              >Date Range</label
            >
            <select
              id="dateRangeFilter"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="7">Last 7 days</option>
              <option value="30" selected>Last 30 days</option>
              <option value="90">Last 90 days</option>
              <option value="365">Last year</option>
            </select>
          </div>
        </div>

        <div class="flex justify-between items-center mt-4">
          <div class="flex space-x-2">
            <button
              onclick="applyFilters()"
              class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
            >
              Apply Filters
            </button>
            <button
              onclick="clearFilters()"
              class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
            >
              Clear
            </button>
          </div>

          <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">Show:</span>
            <select
              id="pageSize"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="25">25</option>
              <option value="50" selected>50</option>
              <option value="100">100</option>
            </select>
            <span class="text-sm text-gray-600">entries per page</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Audit Log Table -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="audit-card overflow-hidden">
        <div
          class="px-6 py-4 border-b border-gray-200 flex justify-between items-center"
        >
          <div>
            <h3 class="text-lg font-semibold text-gray-900">
              Audit Log Entries
            </h3>
            <p id="logCount" class="text-sm text-gray-600 mt-1">Loading...</p>
          </div>
          <button
            onclick="clearAllLogs()"
            class="rounded-md flex items-center justify-center gap-1 text-xs font-medium hover:bg-red-600 transition-colors"
            style="
              background-color: var(--error-color);
              color: white;
              width: 80px;
              height: 32px;
              padding: 0;
            "
          >
            <i class="fas fa-trash text-xs"></i>
            <span>Clear All</span>
          </button>
        </div>

                 <!-- Desktop Table View -->
         <div class="hidden sm:block">
           <table class="w-full table-fixed">
             <thead class="bg-gray-50">
               <tr>
                                                     <th
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-48"
                  >
                    Timestamp
                  </th>
                  <th
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32"
                  >
                  Performed By
                  </th>
                  <th
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24"
                  >
                    Action
                  </th>
                  <th
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32"
                  >
                    Entity
                  </th>
                  <th
                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-64"
                  >
                    Description
                  </th>
                 <th
                   class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20"
                 >
                   Status
                 </th>
                 <th
                   class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20"
                 >
                   Actions
                 </th>
               </tr>
             </thead>
            <tbody
              id="auditTableBody"
              class="bg-white divide-y divide-gray-200"
            >
              <tr>
                <td colspan="7" class="px-6 py-4 text-center">
                  <div class="flex items-center justify-center space-x-2">
                    <div class="loading-spinner"></div>
                    <span class="text-gray-500">Loading audit logs...</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden space-y-3 p-4" id="auditMobileCards">
          <div class="flex items-center justify-center space-x-2 py-8">
            <div class="loading-spinner"></div>
            <span class="text-gray-500">Loading audit logs...</span>
          </div>
        </div>

        <!-- Pagination -->
        <div
          id="pagination"
          class="px-6 py-4 border-t border-gray-200 flex items-center justify-between"
        >
          <!-- Pagination content will be inserted here -->
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <div
      id="detailModal"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50"
    >
      <div
        class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto"
      >
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">
              Audit Log Details
            </h3>
            <button
              onclick="closeDetailModal()"
              class="text-gray-400 hover:text-gray-600"
            >
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>
        <div id="modalContent" class="p-6">
          <!-- Modal content will be inserted here -->
        </div>
      </div>
    </div>

    <script>
      let currentPage = 1;
      let currentFilters = {};
      let actionTypeChart = null;
      let dailyActivityChart = null;

      // Initialize the page
      document.addEventListener("DOMContentLoaded", function () {
        // Load initial data
        loadAuditStats();
        loadAuditLogs();

        // Add event listeners
        document
          .getElementById("pageSize")
          .addEventListener("change", function () {
            currentPage = 1;
            loadAuditLogs();
          });

        document
          .getElementById("dateRangeFilter")
          .addEventListener("change", function () {
            loadAuditStats();
          });

        // Prevent multiple rapid calls
        let refreshTimeout;
        window.addEventListener("resize", function () {
          clearTimeout(refreshTimeout);
          refreshTimeout = setTimeout(function () {
            if (actionTypeChart) {
              actionTypeChart.resize();
            }
            if (dailyActivityChart) {
              dailyActivityChart.resize();
            }
          }, 250);
        });

        // Cleanup charts when page is unloaded
        window.addEventListener("beforeunload", function () {
          if (actionTypeChart) {
            actionTypeChart.destroy();
            actionTypeChart = null;
          }
          if (dailyActivityChart) {
            dailyActivityChart.destroy();
            dailyActivityChart = null;
          }
        });
      });

      // Load audit statistics
      let statsLoading = false;
      async function loadAuditStats() {
        if (statsLoading) {
          console.log("Stats loading already in progress, skipping...");
          return;
        }

        statsLoading = true;

        try {
          const days = document.getElementById("dateRangeFilter").value;
          const response = await fetch(`../audit/get_audit_stats.php?days=${days}`);
          
          if (response.status === 401) {
            // Unauthorized - redirect to login
            window.location.href = 'admin_login_simple.html';
            return;
          }
          
          const data = await response.json();

          if (data.success) {
            updateStatistics(data.data);
            updateCharts(data.data);
          } else {
            console.error("Failed to load audit stats:", data.message);
          }
        } catch (error) {
          console.error("Error loading audit stats:", error);
        } finally {
          statsLoading = false;
        }
      }

             // Update statistics cards
       function updateStatistics(data) {
         const summary = data.summary;

         document.getElementById("totalActivities").textContent =
           summary.total_activities.toLocaleString();
         document.getElementById("totalBackups").textContent =
           summary.total_backups || 0;

         // Calculate today's activities
         const today = new Date().toISOString().split("T")[0];
         const todayActivities = data.daily_activity.find(
           (item) => item.date === today
         );
         document.getElementById("todayActivities").textContent = todayActivities
           ? todayActivities.count
           : 0;
       }

      // Update charts
      function updateCharts(data) {
        updateActionTypeChart(data.action_types);
        updateDailyActivityChart(data.daily_activity);
      }

      // Update action type chart
      function updateActionTypeChart(actionTypes) {
        const canvas = document.getElementById("actionTypeChart");
        if (!canvas) {
          console.error("Action type chart canvas not found");
          return;
        }

        const ctx = canvas.getContext("2d");

        // Destroy existing chart if it exists
        if (actionTypeChart) {
          actionTypeChart.destroy();
          actionTypeChart = null;
        }

        // Check if we have data to display
        if (!actionTypes || actionTypes.length === 0) {
          console.log("No action type data to display");
          return;
        }

        try {
          actionTypeChart = new Chart(ctx, {
            type: "doughnut",
            data: {
              labels: actionTypes.map((item) => item.action_type),
              datasets: [
                {
                  data: actionTypes.map((item) => item.count),
                  backgroundColor: [
                    "#3b82f6",
                    "#10b981",
                    "#f59e0b",
                    "#ef4444",
                    "#8b5cf6",
                    "#06b6d4",
                    "#84cc16",
                    "#f97316",
                  ],
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              plugins: {
                legend: {
                  position: "bottom",
                },
              },
            },
          });
        } catch (error) {
          console.error("Error creating action type chart:", error);
        }
      }

      // Update daily activity chart
      function updateDailyActivityChart(dailyActivity) {
        const canvas = document.getElementById("dailyActivityChart");
        if (!canvas) {
          console.error("Daily activity chart canvas not found");
          return;
        }

        const ctx = canvas.getContext("2d");

        // Destroy existing chart if it exists
        if (dailyActivityChart) {
          dailyActivityChart.destroy();
          dailyActivityChart = null;
        }

        // Check if we have data to display
        if (!dailyActivity || dailyActivity.length === 0) {
          console.log("No daily activity data to display");
          return;
        }

        try {
          dailyActivityChart = new Chart(ctx, {
            type: "line",
            data: {
              labels: dailyActivity.map((item) => item.formatted_date),
              datasets: [
                {
                  label: "Activities",
                  data: dailyActivity.map((item) => item.count),
                  borderColor: "#3b82f6",
                  backgroundColor: "rgba(59, 130, 246, 0.1)",
                  tension: 0.4,
                },
              ],
            },
            options: {
              responsive: true,
              maintainAspectRatio: false,
              scales: {
                y: {
                  beginAtZero: true,
                },
              },
            },
          });
        } catch (error) {
          console.error("Error creating daily activity chart:", error);
        }
      }

      // Load audit logs
      let logsLoading = false;
      async function loadAuditLogs() {
        if (logsLoading) {
          console.log("Logs loading already in progress, skipping...");
          return;
        }

        logsLoading = true;

        try {
          const pageSize = document.getElementById("pageSize").value;
          const params = new URLSearchParams({
            page: currentPage,
            limit: pageSize,
            ...currentFilters,
          });

          const response = await fetch(`../audit/get_audit_logs.php?${params}`);
          
          if (response.status === 401) {
            // Unauthorized - redirect to login
            window.location.href = 'admin_login_simple.html';
            return;
          }
          
          const data = await response.json();

          if (data.success) {
            renderAuditLogs(data.data);
          } else {
            console.error("Failed to load audit logs:", data.message);
          }
        } catch (error) {
          console.error("Error loading audit logs:", error);
        } finally {
          logsLoading = false;
        }
      }

      // Render audit logs
      function renderAuditLogs(data) {
        const tbody = document.getElementById("auditTableBody");
        const mobileCards = document.getElementById("auditMobileCards");
        const logs = data.logs;
        const pagination = data.pagination;

        if (logs.length === 0) {
          tbody.innerHTML = `
                     <tr>
                         <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                             No audit logs found matching the current filters.
                         </td>
                     </tr>
                 `;
          mobileCards.innerHTML = `
                     <div class="text-center text-gray-500 py-8">
                         No audit logs found matching the current filters.
                     </div>
                 `;
        } else {
                     // Desktop table view
           tbody.innerHTML = logs
             .map(
               (log) => `
                      <tr class="hover:bg-gray-50">
                          <td class="px-4 py-4">
                              <div class="text-sm font-medium text-gray-900 truncate">${
                                log.formatted_time
                              }</div>
                          </td>
                          <td class="px-4 py-4">
                              <div class="text-sm font-medium text-gray-900 truncate">${
                                log.admin_user
                              }</div>
                          </td>
                          <td class="px-4 py-4">
                              <span class="action-badge action-${log.action_type.toLowerCase()}">${
                 log.action_type
               }</span>
                          </td>
                          <td class="px-4 py-4">
                              <div>
                                  <div class="text-sm font-medium text-gray-900 truncate">${
                                    log.entity_type
                                  }</div>
                                  <div class="text-sm text-gray-500 truncate">${
                                    log.entity_name || "—"
                                  }</div>
                              </div>
                          </td>
                          <td class="px-4 py-4">
                              <div class="text-sm text-gray-900 truncate">${
                                log.description
                              }</div>
                          </td>
                          <td class="px-4 py-4">
                              <span class="status-badge status-${log.status.toLowerCase()}">${
                 log.status
               }</span>
                          </td>
                          <td class="px-4 py-4">
                              <div class="flex items-center space-x-1">
                                  <button
                                    onclick="showLogDetails(${log.audit_id})"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-medium rounded hover:bg-blue-50"
                                    style="width: 32px; height: 24px; padding: 0;"
                                  >
                                    <i class="fas fa-eye text-xs"></i>
                                  </button>
                                  <button
                                    onclick="deleteLog(${log.audit_id})"
                                    class="text-red-600 hover:text-red-800 text-xs font-medium rounded hover:bg-red-50"
                                    style="width: 32px; height: 24px; padding: 0;"
                                  >
                                    <i class="fas fa-trash text-xs"></i>
                                  </button>
                              </div>
                          </td>
                      </tr>
                  `
             )
            .join("");

          // Mobile card view
          mobileCards.innerHTML = logs
            .map(
              (log) => `
                     <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                         <div class="flex items-start justify-between mb-3">
                             <div class="flex-1">
                                 <div class="flex items-center gap-2 mb-2">
                                     <span class="action-badge action-${log.action_type.toLowerCase()}">${
                log.action_type
              }</span>
                                     <span class="status-badge status-${log.status.toLowerCase()}">${
                log.status
              }</span>
                                 </div>
                                 <div class="text-sm font-medium text-gray-900 mb-1">${
                                   log.admin_user
                                 }</div>
                                                                   <div class="text-xs text-gray-500">${
                                                                     log.formatted_time
                                                                   }</div>
                             </div>
                         </div>
                         <div class="space-y-2 text-sm">
                             <div class="flex justify-between">
                                 <span class="text-gray-600">Entity:</span>
                                 <span class="font-medium">${
                                   log.entity_type
                                 }</span>
                             </div>
                             <div class="flex justify-between">
                                 <span class="text-gray-600">Name:</span>
                                 <span class="font-medium">${
                                   log.entity_name || "—"
                                 }</span>
                             </div>

                         </div>
                         <div class="mt-3 pt-3 border-t border-gray-200">
                             <div class="text-sm text-gray-900 line-clamp-2">${
                               log.description
                             }</div>
                                                           <div class="flex items-center justify-end space-x-1 mt-3">
                                                                     <button
                                       onclick="showLogDetails(${log.audit_id})"
                                       class="text-xs bg-blue-600 text-white rounded hover:bg-blue-700"
                                       style="width: 60px; height: 28px; padding: 0;"
                                   >
                                       <i class="fas fa-eye mr-1 text-xs"></i>View
                                   </button>
                                   <button
                                       onclick="deleteLog(${log.audit_id})"
                                       class="text-xs bg-red-600 text-white rounded hover:bg-red-700"
                                       style="width: 60px; height: 28px; padding: 0;"
                                   >
                                       <i class="fas fa-trash mr-1 text-xs"></i>Delete
                                   </button>
                              </div>
                         </div>
                     </div>
                 `
            )
            .join("");
        }

        // Update log count
        document.getElementById("logCount").textContent = `Showing ${
          (pagination.current_page - 1) * pagination.limit + 1
        } to ${Math.min(
          pagination.current_page * pagination.limit,
          pagination.total_count
        )} of ${pagination.total_count} entries`;

        // Render pagination
        renderPagination(pagination);
      }

      // Render pagination
      function renderPagination(pagination) {
        const paginationDiv = document.getElementById("pagination");

        if (pagination.total_pages <= 1) {
          paginationDiv.innerHTML = "";
          return;
        }

        let paginationHTML = `
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-700">
                        Page ${pagination.current_page} of ${pagination.total_pages}
                    </span>
                </div>
                <div class="flex items-center space-x-2">
            `;

        // Previous button
        if (pagination.has_prev) {
          paginationHTML += `
                    <button onclick="changePage(${
                      pagination.current_page - 1
                    })" 
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        Previous
                    </button>
                `;
        }

        // Page numbers
        const startPage = Math.max(1, pagination.current_page - 2);
        const endPage = Math.min(
          pagination.total_pages,
          pagination.current_page + 2
        );

        for (let i = startPage; i <= endPage; i++) {
          if (i === pagination.current_page) {
            paginationHTML += `
                        <button class="px-3 py-2 text-sm bg-blue-600 text-white rounded-lg">
                            ${i}
                        </button>
                    `;
          } else {
            paginationHTML += `
                        <button onclick="changePage(${i})" 
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                            ${i}
                        </button>
                    `;
          }
        }

        // Next button
        if (pagination.has_next) {
          paginationHTML += `
                    <button onclick="changePage(${
                      pagination.current_page + 1
                    })" 
                            class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        Next
                    </button>
                `;
        }

        paginationHTML += "</div>";
        paginationDiv.innerHTML = paginationHTML;
      }

      // Change page
      function changePage(page) {
        currentPage = page;
        loadAuditLogs();
      }

      // Apply filters
      function applyFilters() {
        currentFilters = {
          action_type: document.getElementById("actionTypeFilter").value,
          entity_type: document.getElementById("entityTypeFilter").value,
          status: document.getElementById("statusFilter").value,
        };

        // Remove empty filters
        Object.keys(currentFilters).forEach((key) => {
          if (!currentFilters[key]) {
            delete currentFilters[key];
          }
        });

        currentPage = 1;
        loadAuditLogs();
      }

      // Clear filters
      function clearFilters() {
        document.getElementById("actionTypeFilter").value = "";
        document.getElementById("entityTypeFilter").value = "";
        document.getElementById("statusFilter").value = "";
        currentFilters = {};
        currentPage = 1;
        loadAuditLogs();
      }

      // Show log details
      async function showLogDetails(auditId) {
        try {
          const response = await fetch(
            `../audit/get_audit_logs.php?audit_id=${auditId}`
          );
          
          if (response.status === 401) {
            // Unauthorized - redirect to login
            window.location.href = 'admin_login_simple.html';
            return;
          }
          
          const data = await response.json();

          if (data.success && data.data.logs.length > 0) {
            const log = data.data.logs[0];
            showDetailModal(log);
          }
        } catch (error) {
          console.error("Error loading log details:", error);
        }
      }

      // Show detail modal
      function showDetailModal(log) {
        const modal = document.getElementById("detailModal");
        const content = document.getElementById("modalContent");

        content.innerHTML = `
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Basic Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Audit ID</dt>
                                    <dd class="text-sm text-gray-900">${
                                      log.audit_id
                                    }</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Timestamp</dt>
                                    <dd class="text-sm text-gray-900">${
                                      log.formatted_time
                                    }</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Admin User</dt>
                                    <dd class="text-sm text-gray-900">${
                                      log.admin_user
                                    }</dd>
                                </div>
                                
                            </dl>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 mb-2">Action Details</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Action Type</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span class="action-badge action-${log.action_type.toLowerCase()}">${
          log.action_type
        }</span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Entity Type</dt>
                                    <dd class="text-sm text-gray-900">${
                                      log.entity_type
                                    }</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Entity Name</dt>
                                    <dd class="text-sm text-gray-900">${
                                      log.entity_name || "—"
                                    }</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="text-sm text-gray-900">
                                        <span class="status-badge status-${log.status.toLowerCase()}">${
          log.status
        }</span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Description</h4>
                        <p class="text-sm text-gray-900">${log.description}</p>
                    </div>
                    
                    ${
                      log.details_before || log.details_after
                        ? `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            ${
                              log.details_before
                                ? `
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">Before</h4>
                                    <pre class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg overflow-x-auto">${JSON.stringify(
                                      log.details_before,
                                      null,
                                      2
                                    )}</pre>
                                </div>
                            `
                                : ""
                            }
                            ${
                              log.details_after
                                ? `
                                <div>
                                    <h4 class="font-medium text-gray-900 mb-2">After</h4>
                                    <pre class="text-sm text-gray-900 bg-gray-50 p-3 rounded-lg overflow-x-auto">${JSON.stringify(
                                      log.details_after,
                                      null,
                                      2
                                    )}</pre>
                                </div>
                            `
                                : ""
                            }
                        </div>
                    `
                        : ""
                    }
                    
                    ${
                      log.error_message
                        ? `
                        <div>
                            <h4 class="font-medium text-red-900 mb-2">Error Message</h4>
                            <p class="text-sm text-red-700 bg-red-50 p-3 rounded-lg">${log.error_message}</p>
                        </div>
                    `
                        : ""
                    }
                </div>
            `;

        modal.classList.remove("hidden");
        modal.classList.add("flex");
      }

      // Close detail modal
      function closeDetailModal() {
        const modal = document.getElementById("detailModal");
        modal.classList.add("hidden");
        modal.classList.remove("flex");
      }

      // Clear all audit logs
      function clearAllLogs() {
        showConfirmPopup(
          "Are you sure you want to clear all audit logs? This action cannot be undone.",
          () => {
            // Show loading state
            const clearButtons = document.querySelectorAll(
              'button[onclick="clearAllLogs()"]'
            );
            clearButtons.forEach((btn) => {
              btn.disabled = true;
              btn.innerHTML =
                '<i class="fas fa-spinner fa-spin"></i><span>Clearing...</span>';
            });

            fetch("../audit/clear_audit_logs.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ confirm: true }),
            })
              .then((response) => {
                if (response.status === 401) {
                  // Unauthorized - redirect to login
                  window.location.href = 'admin_login_new.html';
                  return;
                }
                return response.json();
              })
              .then((data) => {
                if (!data) return; // Already redirected
                
                if (data.success) {
                  showMessage(
                    "All audit logs have been cleared successfully.",
                    "success"
                  );
                  loadAuditLogs(); // Reload the data
                } else {
                  showMessage(
                    "Error clearing audit logs: " +
                      (data.message || "Unknown error"),
                    "error"
                  );
                }
              })
              .catch((error) => {
                console.error("Error:", error);
                showMessage(
                  "Network error occurred while clearing audit logs.",
                  "error"
                );
              })
              .finally(() => {
                // Reset button states
                clearButtons.forEach((btn) => {
                  btn.disabled = false;
                  btn.innerHTML =
                    '<i class="fas fa-trash"></i><span>Clear All</span>';
                });
              });
          }
        );
      }

      // Delete individual audit log
      function deleteLog(auditId) {
        showConfirmPopup(
          "Are you sure you want to delete this audit log entry? This action cannot be undone.",
          () => {
            // Show loading state for the specific delete button
            const deleteButtons = document.querySelectorAll(
              `button[onclick="deleteLog(${auditId})"]`
            );
            deleteButtons.forEach((btn) => {
              btn.disabled = true;
              btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            });

            fetch("../audit/delete_audit_log.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ audit_id: auditId }),
            })
              .then((response) => {
                if (response.status === 401) {
                  // Unauthorized - redirect to login
                  window.location.href = 'admin_login_new.html';
                  return;
                }
                return response.json();
              })
              .then((data) => {
                if (!data) return; // Already redirected
                
                if (data.success) {
                  showMessage(
                    "Audit log entry deleted successfully.",
                    "success"
                  );
                  loadAuditLogs(); // Reload the data
                } else {
                  showMessage(
                    "Error deleting audit log: " +
                      (data.message || "Unknown error"),
                    "error"
                  );
                }
              })
              .catch((error) => {
                console.error("Error:", error);
                showMessage(
                  "Network error occurred while deleting audit log.",
                  "error"
                );
              })
              .finally(() => {
                // Reset button states
                deleteButtons.forEach((btn) => {
                  btn.disabled = false;
                  btn.innerHTML = '<i class="fas fa-trash"></i>';
                });
              });
          }
        );
      }

      // Close modal when clicking outside
      document
        .getElementById("detailModal")
        .addEventListener("click", function (e) {
          if (e.target === this) {
            closeDetailModal();
          }
        });

      // Message display function (Popup Modal)
      function showMessage(message, type = "info") {
        // Remove any existing popup
        const existingPopup = document.getElementById("messagePopup");
        if (existingPopup) {
          existingPopup.remove();
        }

        // Create popup container
        const popupContainer = document.createElement("div");
        popupContainer.id = "messagePopup";
        popupContainer.className = `fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-50 transition-opacity duration-300 ease-out opacity-0`;

        // Determine colors and icon based on type
        let bgColor, textColor, iconClass, iconColor;
        if (type === "success") {
          bgColor = "bg-green-50";
          textColor = "text-green-800";
          iconClass = "fas fa-check-circle";
          iconColor = "text-green-500";
        } else if (type === "error") {
          bgColor = "bg-red-50";
          textColor = "text-red-800";
          iconClass = "fas fa-exclamation-circle";
          iconColor = "text-red-500";
        } else {
          bgColor = "bg-blue-50";
          textColor = "text-blue-800";
          iconClass = "fas fa-info-circle";
          iconColor = "text-blue-500";
        }

        popupContainer.innerHTML = `
           <div class="relative ${bgColor} rounded-lg shadow-xl p-6 sm:p-8 max-w-sm w-full mx-4 transform scale-95 transition-transform duration-300 ease-out">
             <div class="flex flex-col items-center text-center">
               <div class="mb-4">
                 <i class="${iconClass} ${iconColor} text-4xl"></i>
               </div>
               <p class="text-lg font-semibold ${textColor} mb-6">${message}</p>
               <button
                 onclick="closeMessagePopup()"
                 class="px-6 py-2 rounded-md text-white font-medium focus:outline-none focus:ring-2 focus:ring-opacity-75 w-full"
                 style="background-color: var(--accent-color);"
               >
                 OK
               </button>
             </div>
           </div>
         `;

        document.body.appendChild(popupContainer);

        // Animate in
        setTimeout(() => {
          popupContainer.classList.remove("opacity-0");
          popupContainer.querySelector("div").classList.remove("scale-95");
        }, 10);

        // Auto remove after 5 seconds
        setTimeout(() => {
          closeMessagePopup();
        }, 5000);
      }

      // Function to close popup
      function closeMessagePopup() {
        const popup = document.getElementById("messagePopup");
        if (popup) {
          popup.classList.add("opacity-0");
          popup.querySelector("div").classList.add("scale-95");
          setTimeout(() => {
            popup.remove();
          }, 300); // Match transition duration
        }
      }

      // Function to show confirmation popup
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
               <button onclick="handleConfirmAction()" class="px-4 py-2 rounded-lg text-white font-medium bg-red-600 hover:bg-red-700 transition-colors">
                 Confirm
               </button>
             </div>
           </div>
         `;

        // Add to page
        popupContainer.appendChild(popupContent);
        document.body.appendChild(popupContainer);

        // Store the onConfirm callback globally or on the element
        window.currentConfirmAction = onConfirm;
      }

      // Function to handle confirmation action
      function handleConfirmAction() {
        if (window.currentConfirmAction) {
          window.currentConfirmAction();
        }
        closeConfirmPopup();
      }

      // Function to close confirmation popup
      function closeConfirmPopup() {
        const popup = document.getElementById("confirmPopup");
        if (popup) {
          popup.remove();
        }
        window.currentConfirmAction = null; // Clear the callback
      }

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

       // Add event listeners when DOM is loaded
       document.addEventListener("DOMContentLoaded", function() {
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
    </script>

         <!-- Mobile Bottom Navigation -->
     <nav
       class="mobile-bottom-nav sm:hidden fixed bottom-0 left-0 right-0 border-t z-30"
       style="
         background-color: var(--bg-card);
         border-color: var(--border-color);
         padding-bottom: max(env(safe-area-inset-bottom), 8px);
       "
     >
       <div class="flex items-center justify-around">
         <!-- Admin -->
         <a href="admin.php" class="nav-item">
           <i class="fas fa-shield-halved"></i>
           <span>Admin</span>
         </a>
         
         <!-- Patients -->
         <a href="admin_patient_records.php" class="nav-item">
           <i class="fas fa-user-injured"></i>
           <span>Patients</span>
         </a>
         
         <!-- Audit -->
         <a href="#" class="nav-item is-active">
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
  </body>
</html>
