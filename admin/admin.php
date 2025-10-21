<?php
/**
 * Admin Panel - Protected Page
 * Serves admin.html with session protection
 */

require_once '../auth/admin_session_config.php';

// Protect this page - only logged in admins can access
if (!isAdminLoggedIn()) {
    header('Location: ../admin/admin_login_new.html?msg=' . urlencode('Please log in to access the admin panel'));
    exit;
}

// Get current admin info for potential use in the HTML
$admin = getCurrentAdmin();

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
    <title>SSI Admin Panel</title>
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
        --dropdown-bg: #ffffff;
        --dropdown-border: #e5e7eb;
        --dropdown-hover-bg: #fee2e2;
        --dropdown-hover-text: #b91c1c;
        --label-color: #ef4444; /* red-500 */
        --search-icon: #9ca3af;
        --clear-button-bg: #e5e7eb;
        --clear-button-bg-hover: #d1d5db;
        --clear-button-text: #374151;
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
      .stat-card {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: #fff;
        border-radius: 0.75rem;
        padding: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
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
        color: var(--button-bg);
      }
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
    </style>
    <script>
      // Simple localStorage data store for static prototype
      const STORAGE_KEYS = {
        nurses: "admin_nurses",
        surgeons: "admin_surgeons",
      };

      function loadStore(key, fallback) {
        try {
          return JSON.parse(localStorage.getItem(key)) ?? fallback;
        } catch {
          return fallback;
        }
      }
      function saveStore(key, value) {
        localStorage.setItem(key, JSON.stringify(value));
      }

      function nowIso() {
        return new Date().toISOString();
      }

      // Nurses CRUD
      function renderNurses() {
        const tbody = document.getElementById("nurseTableBody");
        const cardBody = document.getElementById("nurseCardBody");

        // Show loading state
        const loadingHtml =
          '<div class="p-3 text-center text-sm" style="color: var(--text-secondary);"><i class="fas fa-spinner fa-spin mr-2"></i>Loading nurses...</div>';
        tbody.innerHTML =
          '<tr><td colspan="7" class="p-3 text-center text-sm" style="color: var(--text-secondary);"><i class="fas fa-spinner fa-spin mr-2"></i>Loading nurses...</td></tr>';
        cardBody.innerHTML = loadingHtml;

        fetch("get_nurses_simple.php")
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
              const nurses = data.nurses;

              if (nurses.length === 0) {
                const emptyHtml =
                  '<div class="p-3 text-center text-sm" style="color: var(--text-secondary);">No nurses yet</div>';
                tbody.innerHTML =
                  '<tr><td colspan="7" class="p-3 text-center text-sm" style="color: var(--text-secondary);">No nurses yet</td></tr>';
                cardBody.innerHTML = emptyHtml;
              } else {
                // Render table for desktop
                tbody.innerHTML = nurses
                  .map(
                    (n, idx) => {
                      const formAccessBadge = n.form_access ? 
                        (n.form_access === 'ssi' ? '<span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">SSI</span>' :
                         n.form_access === 'cauti' ? '<span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">CAUTI</span>' :
                         '<span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">' + n.form_access.toUpperCase() + '</span>') :
                        '<span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">N/A</span>';
                      
                      return `
               <tr class="border-b" style="border-color: var(--border-secondary);">
                 <td class="p-3">${idx + 1}</td>
                 <td class="p-3 font-medium">${n.nurse_id}</td>
                 <td class="p-3">${n.name || "—"}</td>
                 <td class="p-3">${n.email || "—"}</td>
                 <td class="p-3"><span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs">••••••</span></td>
                 <td class="p-3">${formAccessBadge}</td>
                 <td class="p-3 text-right">
                   <button class="px-2 py-1 text-xs rounded border" style="border-color: var(--border-secondary);" onclick="editNurse('${
                     n.id
                   }')"><i class="fas fa-edit mr-1"></i>Edit</button>
                   <button class="ml-2 px-2 py-1 text-xs rounded border" style="border-color: var(--border-secondary);" onclick="deleteNurse('${
                     n.id
                   }')"><i class="fas fa-trash mr-1"></i>Delete</button>
                 </td>
               </tr>
             `;
                    }
                  )
                  .join("");

                // Render cards for mobile
                cardBody.innerHTML = nurses
                  .map(
                    (n, idx) => {
                      const formAccessBadge = n.form_access ? 
                        (n.form_access === 'ssi' ? '<span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-semibold">SSI</span>' :
                         n.form_access === 'cauti' ? '<span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">CAUTI</span>' :
                         '<span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">' + n.form_access.toUpperCase() + '</span>') :
                        '<span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs">N/A</span>';
                      
                      return `
               <div class="bg-white border rounded-lg p-4 mb-3 shadow-sm" style="border-color: var(--border-secondary);">
                 <div class="flex items-center justify-between mb-3">
                   <div class="flex items-center gap-2">
                     <span class="text-sm font-medium text-gray-500">#${
                       idx + 1
                     }</span>
                     <span class="text-sm font-semibold" style="color: var(--text-primary);">${
                       n.nurse_id
                     }</span>
                   </div>
                   <div class="flex gap-2">
                     <button class="px-2 py-1 text-xs rounded border" style="border-color: var(--border-secondary);" onclick="editNurse('${
                       n.id
                     }')"><i class="fas fa-edit mr-1"></i>Edit</button>
                     <button class="px-2 py-1 text-xs rounded border" style="border-color: var(--border-secondary);" onclick="deleteNurse('${
                       n.id
                     }')"><i class="fas fa-trash mr-1"></i>Delete</button>
                   </div>
                 </div>
                 <div class="space-y-2">
                   <div class="flex items-center gap-2">
                     <i class="fas fa-user text-xs" style="color: var(--text-secondary);"></i>
                     <span class="text-sm">${n.name || "—"}</span>
                   </div>
                   <div class="flex items-center gap-2">
                     <i class="fas fa-envelope text-xs" style="color: var(--text-secondary);"></i>
                     <span class="text-sm">${n.email || "—"}</span>
                   </div>
                   <div class="flex items-center gap-2">
                     <i class="fas fa-lock text-xs" style="color: var(--text-secondary);"></i>
                     <span class="inline-block px-2 py-1 bg-gray-100 rounded text-xs">••••••</span>
                   </div>
                   <div class="flex items-center gap-2">
                     <i class="fas fa-file-medical text-xs" style="color: var(--text-secondary);"></i>
                     ${formAccessBadge}
                   </div>
                 </div>
               </div>
             `;
                    }
                  )
                  .join("");
              }
            } else {
              const errorHtml =
                '<div class="p-3 text-center text-sm" style="color: #dc2626;">Error loading nurses: ' +
                data.message +
                "</div>";
              tbody.innerHTML =
                '<tr><td colspan="7" class="p-3 text-center text-sm" style="color: #dc2626;">Error loading nurses: ' +
                data.message +
                "</td></tr>";
              cardBody.innerHTML = errorHtml;
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            const errorHtml =
              '<div class="p-3 text-center text-sm" style="color: #dc2626;">Network error occurred</div>';
            tbody.innerHTML =
              '<tr><td colspan="7" class="p-3 text-center text-sm" style="color: #dc2626;">Network error occurred</td></tr>';
            cardBody.innerHTML = errorHtml;
          });
      }
      function handleNurseSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const isEditing = form.dataset.editingId;

        // Validate password if not editing or if password is provided
        if (!isEditing || form.password.value.trim()) {
          const password = form.password.value;

          // Check each requirement individually and show specific messages
          if (password.length < 8) {
            showMessage(
              "Password should be at least 8 characters long",
              "error",
              "nurse"
            );
            return;
          }

          if (!/[A-Z]/.test(password)) {
            showMessage(
              "Password should contain at least one uppercase letter",
              "error",
              "nurse"
            );
            return;
          }

          if (!/[a-z]/.test(password)) {
            showMessage(
              "Password should contain at least one lowercase letter",
              "error",
              "nurse"
            );
            return;
          }

          if (!/[0-9]/.test(password)) {
            showMessage(
              "Password should contain at least one number",
              "error",
              "nurse"
            );
            return;
          }

          if (!/[!@#$%^&*]/.test(password)) {
            showMessage(
              "Password should contain at least one special character (!@#$%^&*)",
              "error",
              "nurse"
            );
            return;
          }
        }

        const formData = new FormData();
        formData.append("nurseId", form.nurseId.value.trim());
        formData.append("name", form.name.value.trim());
        formData.append("email", form.email.value.trim());
        formData.append("password", form.password.value);
        formData.append("role", form.role.value);
        formData.append("formAccess", form.formAccess.value);

        if (isEditing) {
          formData.append("id", isEditing);
        }

        const url = isEditing ? "update_nurse.php" : "create_nurse.php";

        // Show loading state
        const submitBtn = document.getElementById("nurseSubmit");
        const originalText = submitBtn.innerText;
        submitBtn.innerText = "Processing...";
        submitBtn.disabled = true;

        fetch(url, {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Show success message
              showMessage(data.message, "success", "nurse");

              form.reset();
              form.dataset.editingId = "";
              document.getElementById("nurseSubmit").innerText = "Create Nurse";
              renderNurses();
              refreshStats(); // Refresh stats after successful operation
            } else {
              showMessage(data.message, "error", "nurse");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showMessage("Network error occurred", "error");
          })
          .finally(() => {
            submitBtn.innerText = originalText;
            submitBtn.disabled = false;
          });
      }
      function editNurse(id) {
        // Fetch nurse data from database
        fetch("get_nurses_simple.php")
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
              const nurse = data.nurses.find((n) => n.id == id);
              if (nurse) {
                const form = document.getElementById("nurseForm");
                form.dataset.editingId = id;
                form.nurseId.value = nurse.nurse_id || "";
                form.name.value = nurse.name || "";
                form.email.value = nurse.email || "";
                form.role.value = nurse.role || "nurse";
                form.formAccess.value = nurse.form_access || "";
                form.password.value = ""; // Clear password field for security
                document.getElementById("nurseSubmit").innerText =
                  "Update Nurse";
                window.scrollTo({
                  top: form.getBoundingClientRect().top + window.scrollY - 80,
                  behavior: "smooth",
                });
              }
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showMessage("Error loading nurse data", "error");
          });
      }
      function deleteNurse(id) {
        // Show confirmation popup instead of browser confirm
        showConfirmPopup(
          "Are you sure you want to delete this nurse account?",
          () => {
            // User confirmed, proceed with deletion
            const formData = new FormData();
            formData.append("id", id);

            fetch("delete_nurse.php", {
              method: "POST",
              body: formData,
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  showMessage(data.message, "success", "nurse");
                  renderNurses();
                  refreshStats(); // Refresh stats after successful deletion
                } else {
                  showMessage(data.message, "error", "nurse");
                }
              })
              .catch((error) => {
                console.error("Error:", error);
                showMessage("Network error occurred", "error");
              });
          }
        );
      }

      // Surgeons CRUD
      function renderSurgeons() {
        const tbody = document.getElementById("surgeonTableBody");

        // Show loading state
        tbody.innerHTML =
          '<tr><td colspan="3" class="p-3 text-center text-sm" style="color: var(--text-secondary);"><i class="fas fa-spinner fa-spin mr-2"></i>Loading surgeons...</td></tr>';

        fetch("get_surgeons.php")
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
              const surgeons = data.surgeons;
              tbody.innerHTML =
                surgeons.length === 0
                  ? '<tr><td colspan="3" class="p-3 text-center text-sm" style="color: var(--text-secondary);">No surgeons yet</td></tr>'
                  : surgeons
                      .map(
                        (s, idx) => `
                 <tr class="border-b" style="border-color: var(--border-secondary);">
                   <td class="p-3">${idx + 1}</td>
                   <td class="p-3 font-medium">${s.name}</td>
                   <td class="p-3 text-right">
                     <button class="px-2 py-1 text-xs rounded border" style="border-color: var(--border-secondary);" onclick="deleteSurgeon('${
                       s.id
                     }')"><i class="fas fa-trash mr-1"></i>Delete</button>
                   </td>
                 </tr>
               `
                      )
                      .join("");
            } else {
              tbody.innerHTML =
                '<tr><td colspan="3" class="p-3 text-center text-sm" style="color: #dc2626;">Error loading surgeons: ' +
                data.message +
                "</td></tr>";
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            tbody.innerHTML =
              '<tr><td colspan="3" class="p-3 text-center text-sm" style="color: #dc2626;">Network error occurred</td></tr>';
          });
      }
      function handleSurgeonSubmit(e) {
        e.preventDefault();
        const form = e.target;

        const formData = new FormData();
        formData.append("name", form.surgeonName.value.trim());

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerText;
        submitBtn.innerText = "Adding...";
        submitBtn.disabled = true;

        fetch("create_surgeon.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              showMessage(data.message, "success", "surgeon");
              form.reset();
              renderSurgeons();
              refreshStats(); // Refresh stats after successful operation
            } else {
              showMessage(data.message, "error", "surgeon");
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            showMessage("Network error occurred", "error");
          })
          .finally(() => {
            submitBtn.innerText = originalText;
            submitBtn.disabled = false;
          });
      }
      function deleteSurgeon(id) {
        // Show confirmation popup instead of browser confirm
        showConfirmPopup(
          "Are you sure you want to delete this surgeon?",
          () => {
            // User confirmed, proceed with deletion
            const formData = new FormData();
            formData.append("id", id);

            fetch("delete_surgeon.php", {
              method: "POST",
              body: formData,
            })
              .then((response) => response.json())
              .then((data) => {
                if (data.success) {
                  showMessage(data.message, "success", "surgeon");
                  renderSurgeons();
                  refreshStats(); // Refresh stats after successful deletion
                } else {
                  showMessage(data.message, "error", "surgeon");
                }
              })
              .catch((error) => {
                console.error("Error:", error);
                showMessage("Network error occurred", "error");
              });
          }
        );
      }

       // Session activity tracking removed - no authentication required

       window.addEventListener("DOMContentLoaded", () => {
         document
           .getElementById("nurseForm")
           .addEventListener("submit", handleNurseSubmit);
         document
           .getElementById("surgeonForm")
           .addEventListener("submit", handleSurgeonSubmit);
         renderNurses();
         renderSurgeons();
         
         // Activity tracking removed - no session authentication required
         
         // Logout functionality removed - no session authentication required
       });
    </script>
  </head>
  <body>
    <header
      class="sticky top-0 z-20 border-b"
      style="
        background-color: var(--bg-header);
        border-color: var(--border-primary);
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
                Manage users, surgeons, and audit analytics
              </p>
              <p
                class="text-xs sm:text-sm font-medium"
                style="color: var(--text-icon)"
              >
                <i class="fas fa-user-shield mr-1"></i>
                Logged in as: <span id="adminUsername">Loading...</span>
              </p>
            </div>
          </div>
                     <div class="hidden sm:flex items-center gap-3 sm:gap-4">
             <a
               href="admin_patient_records.php"
               class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-[var(--button-bg-hover)] min-w-[120px]"
               style="
                 background-color: var(--button-bg);
                 color: var(--button-text);
               "
             >
               <i class="fas fa-user-injured"></i>
               <span>View Patient Records</span>
             </a>

             <a
               href="audit_log.php"
               class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-[var(--button-bg-hover)] min-w-[120px]"
               style="
                 background-color: var(--button-bg);
                 color: var(--button-text);
               "
             >
               <i class="fas fa-chart-line"></i>
               <span>Audit Log</span>
             </a>

             <!-- Logout button -->
             <button onclick="showLogoutPopup()" class="px-4 py-2 rounded-lg flex items-center justify-center gap-2 text-sm font-medium hover:bg-red-700 min-w-[120px] bg-red-600 text-white">
               <i class="fas fa-sign-out-alt"></i>
               <span>Logout</span>
             </button>
           </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 pb-24 sm:pb-6">
      <!-- Top Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="stat-card p-4 sm:p-6 shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/90 text-sm font-medium">Total Nurses</p>
              <p id="statNurses" class="text-2xl sm:text-3xl font-bold">—</p>
            </div>
            <i class="fas fa-user-nurse text-2xl sm:text-3xl text-white/80"></i>
          </div>
        </div>
        <div class="stat-card p-4 sm:p-6 shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/90 text-sm font-medium">Total Surgeons</p>
              <p id="statSurgeons" class="text-2xl sm:text-3xl font-bold">—</p>
            </div>
            <i class="fas fa-user-md text-2xl sm:text-3xl text-white/80"></i>
          </div>
        </div>
        <div class="stat-card p-4 sm:p-6 shadow-lg">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-white/90 text-sm font-medium">Total Patients</p>
              <p id="statPatients" class="text-2xl sm:text-3xl font-bold">—</p>
            </div>
            <i
              class="fas fa-user-injured text-2xl sm:text-3xl text-white/80"
            ></i>
          </div>
        </div>
      </div>

      <script>
        // Message display function - Popup Modal
        function showMessage(message, type = "info", targetSection = null) {
          // Remove any existing popup
          const existingPopup = document.getElementById("messagePopup");
          if (existingPopup) {
            existingPopup.remove();
          }

          // Create popup container
          const popupContainer = document.createElement("div");
          popupContainer.id = "messagePopup";
          popupContainer.className =
            "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50";

          // Create popup content
          const popupContent = document.createElement("div");
          popupContent.className = `max-w-sm mx-4 p-6 rounded-lg shadow-xl transform transition-all ${
            type === "success"
              ? "bg-green-50 border border-green-200"
              : type === "error"
              ? "bg-red-50 border border-red-200"
              : "bg-blue-50 border border-blue-200"
          }`;

          // Create icon and message
          const iconClass =
            type === "success"
              ? "fas fa-check-circle text-green-500"
              : type === "error"
              ? "fas fa-exclamation-circle text-red-500"
              : "fas fa-info-circle text-blue-500";

          const textColor =
            type === "success"
              ? "text-green-800"
              : type === "error"
              ? "text-red-800"
              : "text-blue-800";

          popupContent.innerHTML = `
             <div class="flex items-center justify-center mb-4">
               <i class="${iconClass} text-3xl"></i>
             </div>
             <div class="text-center">
               <p class="${textColor} font-medium text-lg mb-4">${message}</p>
               <button onclick="closeMessagePopup()" class="px-4 py-2 rounded-lg text-white font-medium ${
                 type === "success"
                   ? "bg-green-600 hover:bg-green-700"
                   : type === "error"
                   ? "bg-red-600 hover:bg-red-700"
                   : "bg-blue-600 hover:bg-blue-700"
               } transition-colors">
                 OK
               </button>
             </div>
           `;

          // Add to page
          popupContainer.appendChild(popupContent);
          document.body.appendChild(popupContainer);

          // Auto close after 5 seconds
          setTimeout(() => {
            closeMessagePopup();
          }, 5000);
        }

        // Function to close popup
        function closeMessagePopup() {
          const popup = document.getElementById("messagePopup");
          if (popup) {
            popup.remove();
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

        // Update stats after DOM ready renders tables
        function updateStats() {
          // Fetch nurses count
          fetch("get_nurses_simple.php")
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
                document.getElementById("statNurses").innerText =
                  data.total_count;
              }
            })
            .catch((error) => {
              console.error("Error loading nurses stats:", error);
            });

          // Fetch surgeons count
          fetch("get_surgeons.php")
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
                document.getElementById("statSurgeons").innerText =
                  data.total_count;
              }
            })
            .catch((error) => {
              console.error("Error loading surgeons stats:", error);
            });

          // Fetch patients count
          fetch("../forms/get_patients.php")
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
                document.getElementById("statPatients").innerText =
                  data.total_count || data.patients?.length || 0;
              } else {
                document.getElementById("statPatients").innerText = "0";
              }
            })
            .catch((error) => {
              console.error("Error loading patients stats:", error);
              document.getElementById("statPatients").innerText = "0";
            });
        }

        // Function to refresh stats after operations
        function refreshStats() {
          updateStats();
        }

        // Toggle password visibility
        function togglePasswordVisibility(inputId) {
          const input = document.getElementById(inputId);
          const eyeIcon = document.getElementById("passwordEye");

          if (input.type === "password") {
            input.type = "text";
            eyeIcon.className = "fas fa-eye-slash";
          } else {
            input.type = "password";
            eyeIcon.className = "fas fa-eye";
          }
        }

        document.addEventListener("DOMContentLoaded", () =>
          setTimeout(updateStats, 0)
        );
      </script>

      <!-- Nurse Accounts -->
      <section
        class="rounded-xl shadow-lg p-4 sm:p-6 mb-6 border"
        style="
          background-color: var(--bg-card);
          border-color: var(--border-primary);
        "
      >
        <div class="mb-4">
          <h2 class="text-lg sm:text-xl font-semibold">Nurse Accounts</h2>
        </div>
        <form
          id="nurseForm"
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 mb-4"
        >
          <input
            name="nurseId"
            placeholder="Nurse ID"
            class="p-3 border rounded focus:outline-none"
            style="border-color: var(--border-secondary)"
            required
          />
          <input
            name="name"
            placeholder="Full Name"
            class="p-3 border rounded focus:outline-none"
            style="border-color: var(--border-secondary)"
          />
          <input
            name="email"
            placeholder="Email"
            type="email"
            class="p-3 border rounded focus:outline-none"
            style="border-color: var(--border-secondary)"
          />
          <div class="relative">
            <input
              name="password"
              id="nursePassword"
              placeholder="Password"
              type="password"
              class="p-3 border rounded focus:outline-none w-full"
              style="border-color: var(--border-secondary)"
              required
            />
            <button
              type="button"
              onclick="togglePasswordVisibility('nursePassword')"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700"
            >
              <i id="passwordEye" class="fas fa-eye"></i>
            </button>
          </div>

          <select
            name="formAccess"
            class="p-3 border rounded focus:outline-none"
            style="border-color: var(--border-secondary)"
            required
          >
            <option value="" disabled selected>Select Form Access</option>
            <option value="ssi">SSI Form</option>
            <option value="cauti">CAUTI Form</option>
          </select>

          <input name="role" type="hidden" value="nurse" />
          <button
            id="nurseSubmit"
            type="submit"
            class="px-4 py-3 rounded text-white sm:col-span-2 lg:col-span-1"
            style="background-color: var(--button-bg)"
          >
            Create Nurse
          </button>
        </form>

        <!-- Desktop Table View -->
        <div class="overflow-x-auto hidden sm:block">
          <table class="w-full text-sm">
            <thead style="background-color: var(--bg-table-header)">
              <tr>
                <th class="p-3 text-left">#</th>
                <th class="p-3 text-left">Nurse ID</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3 text-left">Password</th>
                <th class="p-3 text-left">Form Access</th>
                <th class="p-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody id="nurseTableBody"></tbody>
          </table>
        </div>

        <!-- Mobile Card View -->
        <div class="sm:hidden" id="nurseCardBody">
          <!-- Cards will be populated here -->
        </div>
      </section>

      <!-- Surgeon Management -->
      <section
        class="rounded-xl shadow-lg p-4 sm:p-6 mb-6 border"
        style="
          background-color: var(--bg-card);
          border-color: var(--border-primary);
        "
      >
        <div class="mb-4">
          <h2 class="text-lg sm:text-xl font-semibold">Surgeon Directory</h2>
        </div>
        <form id="surgeonForm" class="flex flex-col sm:flex-row gap-3 mb-4">
          <input
            name="surgeonName"
            placeholder="Surgeon Full Name"
            class="flex-1 p-3 border rounded focus:outline-none"
            style="border-color: var(--border-secondary)"
            required
          />
          <button
            type="submit"
            class="px-4 py-3 rounded text-white w-full sm:w-auto"
            style="background-color: var(--button-bg)"
          >
            Add Surgeon
          </button>
        </form>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead style="background-color: var(--bg-table-header)">
              <tr>
                <th class="p-3 text-left">#</th>
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-right">Actions</th>
              </tr>
            </thead>
            <tbody id="surgeonTableBody"></tbody>
          </table>
        </div>
      </section>
    </main>

         <!-- Mobile Bottom Navigation -->
     <nav
       class="mobile-bottom-nav sm:hidden fixed bottom-0 left-0 right-0 border-t z-30"
       style="
         background-color: var(--bg-card);
         border-color: var(--border-secondary);
         padding-bottom: max(env(safe-area-inset-bottom), 8px);
       "
     >
       <div class="flex items-center justify-around">
         <!-- Admin -->
         <a href="#" class="nav-item is-active">
           <i class="fas fa-shield-halved"></i>
           <span>Admin</span>
         </a>
         
         <!-- Patients -->
         <a href="admin_patient_records.php" class="nav-item">
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


     <!-- Loading Screen -->
     <div id="loadingScreen" class="fixed inset-0 bg-white flex items-center justify-center z-50" style="display: none;">
       <div class="text-center">
         <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-red-500 mx-auto mb-4"></div>
         <p class="text-gray-600">Checking session...</p>
       </div>
     </div>

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
       // Session monitoring and admin display
       async function loadAdminInfo() {
         try {
           const response = await fetch("../auth/admin_session_check.php", {
             method: 'POST',
             headers: {
               'Content-Type': 'application/json',
             }
           });
           const sessionData = await response.json();

           if (sessionData.logged_in && sessionData.admin) {
             const admin = sessionData.admin;
             
             // Display admin ID
             const adminIdValue = document.getElementById("adminIdValue");
             const adminIdDisplay = document.getElementById("adminIdDisplay");
             
             if (adminIdValue && adminIdDisplay) {
               adminIdValue.textContent = admin.username || "Admin";
               adminIdDisplay.style.display = "flex";
             }

             // Update admin username in header
             const adminUsername = document.getElementById("adminUsername");
             if (adminUsername) {
               adminUsername.textContent = admin.username || "Admin";
             }


             console.log("Admin session active:", admin.username || "Unknown");
           } else {
             console.log("Session expired, redirecting to login");
             window.location.href = "../admin/admin_login_new.html?msg=" + encodeURIComponent("Session expired. Please log in again.");
           }
         } catch (error) {
           console.error("Error loading admin info:", error);
           window.location.href = "../admin/admin_login_new.html?msg=" + encodeURIComponent("Session check failed. Please log in again.");
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
           const originalText = logoutBtn.innerHTML;
           logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging out...';
           logoutBtn.disabled = true;

           // Call logout endpoint
           const response = await fetch("../auth/admin_logout_new.php", {
             method: "GET",
             headers: {
               "Content-Type": "application/json",
             }
           });

           if (response.ok) {
             // Success - redirect to login page
             window.location.href = "../admin/admin_login_new.html?msg=" + encodeURIComponent("You have been logged out successfully");
           } else {
             // Error - show message and restore button
             alert("Logout failed. Please try again.");
             logoutBtn.innerHTML = originalText;
             logoutBtn.disabled = false;
           }
         } catch (error) {
           console.error("Logout error:", error);
           alert("Logout failed. Please try again.");
           // Restore button state
           const logoutBtn = document.getElementById("confirmLogout");
           logoutBtn.innerHTML = "Logout";
           logoutBtn.disabled = false;
         }
       }

       // Initialize activity monitoring
       function initializeActivityMonitoring() {
         const events = [
           'mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 
           'touchmove', 'click', 'keydown', 'wheel', 'resize'
         ];
         
         let activityTimeout;
         
         function resetActivityTimer() {
           if (activityTimeout) {
             clearTimeout(activityTimeout);
           }
           
           activityTimeout = setTimeout(() => {
             loadAdminInfo();
           }, 1000);
         }
         
         events.forEach(event => {
           document.addEventListener(event, resetActivityTimer, true);
         });
         
         window.addEventListener('focus', resetActivityTimer);
         window.addEventListener('blur', resetActivityTimer);
         
         console.log('Admin activity monitoring initialized');
       }

       // Initialize when page loads
       document.addEventListener("DOMContentLoaded", function() {
         loadAdminInfo();
         initializeActivityMonitoring();
         
         // Refresh admin info every 30 seconds
         setInterval(loadAdminInfo, 30000);

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

     <style>
       /* Navigation styles */
       .nav-item {
         display: flex;
         flex-direction: column;
         align-items: center;
         padding: 8px 12px;
         border-radius: 8px;
         transition: all 0.2s ease;
         color: var(--text-primary);
         text-decoration: none;
         min-width: 60px;
       }

       .nav-item:hover {
         background-color: rgba(0, 0, 0, 0.05);
       }

       .nav-item.is-active {
         color: var(--button-bg);
         background-color: rgba(220, 38, 38, 0.1);
       }

       .nav-item i {
         font-size: 18px;
         margin-bottom: 4px;
       }

       .nav-item span {
         font-size: 12px;
         font-weight: 500;
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

       /* Mobile responsive adjustments */
       @media (max-width: 640px) {
         #logoutPopup .max-w-md {
           max-width: calc(100vw - 2rem);
           margin: 1rem;
         }
         
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
         
         .mobile-bottom-nav .nav-item.is-active {
           color: #dc2626; /* Red color for active state */
           background-color: transparent !important; /* Remove any background */
         }
         
         .mobile-bottom-nav .nav-item.is-active i {
           color: #dc2626; /* Red color for active icon */
         }
       }
     </style>

  </body>
</html>
