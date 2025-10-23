// Auto-grow function for inputs and textareas
function autoGrow(element) {
  element.style.height = "auto";
  element.style.height = element.scrollHeight + "px";
}

// Initialize textareas to be clickable and functional
function initializeTextareas() {
  const textareas = document.querySelectorAll(".form-input");
  textareas.forEach((textarea) => {
    // Ensure textarea is clickable
    textarea.style.cursor = "text";
    textarea.style.pointerEvents = "auto";

    // Add click event to focus
    textarea.addEventListener("click", function (e) {
      e.stopPropagation();
      this.focus();
    });

    // Add touch event for mobile
    textarea.addEventListener("touchstart", function (e) {
      e.stopPropagation();
      this.focus();
    });

    // Initialize height
    autoGrow(textarea);
  });
}

// Initialize when DOM is loaded
document.addEventListener("DOMContentLoaded", function () {
  initializeTextareas();

  // Initialize timepicker functionality
  activateTimepicker();

  // Initialize datetime picker functionality
  activateDateTimePicker();

  // Initialize structured datetime input validation
  initializeStructuredDateTimeValidation();

  // Initialize structured time input validation
  initializeStructuredTimeValidation();

  // Wait a bit for jQuery UI to load if it's still loading
  setTimeout(function () {
    // Initialize datepickers if available
    if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
      try {
        jQuery(".datepicker").datepicker({
          dateFormat: "dd/mm/yy",
          changeMonth: true,
          changeYear: true,
          yearRange: "c-10:c+10",
        });
        console.log("Datepickers initialized successfully");
      } catch (error) {
        console.error("Error initializing datepickers:", error);
        addDatepickerFallback();
      }
    } else {
      console.warn("jQuery UI datepicker not available, using fallback");
      addDatepickerFallback();
    }
    centerDatepickers();

    // Center symptomatic table date and time inputs
    const symptomaticDateInputs = document.querySelectorAll(
      "#symptomatic-table input.datepicker"
    );
    symptomaticDateInputs.forEach(function (input) {
      input.style.textAlign = "center";
      const parentCell = input.closest("td");
      if (parentCell) parentCell.style.textAlign = "center";
    });

    const symptomaticTimeInputs = document.querySelectorAll(
      "#symptomatic-table input.time-pickable"
    );
    symptomaticTimeInputs.forEach(function (input) {
      input.style.textAlign = "center";
      const parentCell = input.closest("td");
      if (parentCell) parentCell.style.textAlign = "center";
    });
  }, 100);
});

// Add a new antibiotic row
function addAntibioticRow() {
  const tbody = document.querySelector("#antibiotic-table tbody");
  const rows = tbody.querySelectorAll("tr.antibiotic-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update serial no
  newRow.querySelectorAll("td")[0].textContent = String(nextIndex);

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `drug_name_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }
  if (textareas[1]) {
    textareas[1].name = `dosage_route_frequency_${nextIndex}`;
    textareas[1].value = "";
    autoGrow(textareas[1]);
  }

  // Update date input names and clear values
  const inputs = newRow.querySelectorAll("input.datepicker");
  if (inputs[0]) {
    inputs[0].name = `antibiotic_started_on_${nextIndex}`;
    inputs[0].value = "";
    inputs[0].id = `antibiotic_started_on_${nextIndex}`;
    // Remove any existing datepicker classes/attributes
    inputs[0].removeAttribute("data-datepicker-initialized");
    inputs[0].className = "datepicker";
    inputs[0].style.width = "100px";
    inputs[0].placeholder = "dd/mm/yyyy";
  }
  if (inputs[1]) {
    inputs[1].name = `antibiotic_stopped_on_${nextIndex}`;
    inputs[1].value = "";
    inputs[1].id = `antibiotic_stopped_on_${nextIndex}`;
    // Remove any existing datepicker classes/attributes
    inputs[1].removeAttribute("data-datepicker-initialized");
    inputs[1].className = "datepicker";
    inputs[1].style.width = "100px";
    inputs[1].placeholder = "dd/mm/yyyy";
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs specifically
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    // Find the newly added datepicker inputs and initialize them
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      // Destroy any existing datepicker instance first
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added datepickers and their cells
  inputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });
}

// Remove the last antibiotic row (keep at least one)
function removeAntibioticRow() {
  const tbody = document.querySelector("#antibiotic-table tbody");
  const rows = tbody.querySelectorAll("tr.antibiotic-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// Ensure datepicker inputs and their table cells are centered
function centerDatepickers() {
  const dateInputs = document.querySelectorAll("input.datepicker");
  dateInputs.forEach(function (input) {
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });
}

// Reinitialize all datepickers (useful for debugging)
function reinitializeDatepickers() {
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    // Destroy existing datepickers first
    jQuery(".datepicker").datepicker("destroy");
    // Reinitialize with proper settings
    jQuery(".datepicker").datepicker({
      dateFormat: "dd/mm/yy",
      changeMonth: true,
      changeYear: true,
      yearRange: "c-10:c+10",
    });
    centerDatepickers();
  }
}

// Add fallback for datepicker inputs if jQuery UI fails to load
function addDatepickerFallback() {
  const dateInputs = document.querySelectorAll("input.datepicker");
  dateInputs.forEach(function (input) {
    // Add type="date" as fallback if datepicker doesn't work
    if (!input.hasAttribute("data-datepicker-initialized")) {
      input.setAttribute("type", "date");
      input.setAttribute("data-datepicker-initialized", "true");
    }
  });
}

// Debug function to check datepicker status (can be called from browser console)
function debugDatepickers() {
  console.log("=== Datepicker Debug Info ===");
  console.log("jQuery available:", !!window.jQuery);
  console.log(
    "jQuery UI datepicker available:",
    !!(window.jQuery && jQuery.fn && jQuery.fn.datepicker)
  );

  const dateInputs = document.querySelectorAll("input.datepicker");
  console.log("Total datepicker inputs found:", dateInputs.length);

  dateInputs.forEach(function (input, index) {
    console.log(`Input ${index + 1}:`, {
      name: input.name,
      hasDatepicker: jQuery(input).hasClass("hasDatepicker"),
      type: input.type,
      initialized: input.hasAttribute("data-datepicker-initialized"),
    });
  });
}

// Add a new ventilator row
function addVentilatorRow() {
  const tbody = document.querySelector("#ventilator-table tbody");
  const rows = tbody.querySelectorAll("tr.ventilator-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update structured datetime input names and clear values for ventilator date/time
  const structuredInputs = newRow.querySelectorAll(
    ".structured-datetime-input"
  );

  if (structuredInputs[0]) {
    // Update ventilator in inputs
    const inInputs = structuredInputs[0].querySelectorAll("input, select");
    inInputs[0].name = `ventilator_in_day_${nextIndex}`;
    inInputs[0].value = "";
    inInputs[0].setAttribute("maxlength", "2");
    inInputs[1].name = `ventilator_in_month_${nextIndex}`;
    inInputs[1].value = "";
    inInputs[1].setAttribute("maxlength", "2");
    inInputs[2].name = `ventilator_in_year_${nextIndex}`;
    inInputs[2].value = "";
    inInputs[2].setAttribute("maxlength", "4");
    inInputs[3].name = `ventilator_in_hour_${nextIndex}`;
    inInputs[3].value = "";
    inInputs[3].setAttribute("maxlength", "2");
    inInputs[4].name = `ventilator_in_minute_${nextIndex}`;
    inInputs[4].value = "";
    inInputs[4].setAttribute("maxlength", "2");
    inInputs[5].name = `ventilator_in_meridiem_${nextIndex}`;
    inInputs[5].value = "AM";
  }

  if (structuredInputs[1]) {
    // Update ventilator out inputs
    const outInputs = structuredInputs[1].querySelectorAll("input, select");
    outInputs[0].name = `ventilator_out_day_${nextIndex}`;
    outInputs[0].value = "";
    outInputs[0].setAttribute("maxlength", "2");
    outInputs[1].name = `ventilator_out_month_${nextIndex}`;
    outInputs[1].value = "";
    outInputs[1].setAttribute("maxlength", "2");
    outInputs[2].name = `ventilator_out_year_${nextIndex}`;
    outInputs[2].value = "";
    outInputs[2].setAttribute("maxlength", "4");
    outInputs[3].name = `ventilator_out_hour_${nextIndex}`;
    outInputs[3].value = "";
    outInputs[3].setAttribute("maxlength", "2");
    outInputs[4].name = `ventilator_out_minute_${nextIndex}`;
    outInputs[4].value = "";
    outInputs[4].setAttribute("maxlength", "2");
    outInputs[5].name = `ventilator_out_meridiem_${nextIndex}`;
    outInputs[5].value = "AM";
  }

  // Update textarea names and clear values (for total ventilator days)
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `total_ventilator_days_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }

  tbody.appendChild(newRow);
}

// Remove the last ventilator row (keep at least one)
function removeVentilatorRow() {
  const tbody = document.querySelector("#ventilator-table tbody");
  const rows = tbody.querySelectorAll("tr.ventilator-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// Timepicker functionality
function activateTimepicker() {
  document.querySelectorAll(".time-pickable").forEach((timePickable) => {
    let activePicker = null;

    timePickable.addEventListener("focus", () => {
      if (activePicker) return;

      activePicker = showTimePicker(timePickable);

      const onClickAway = ({ target }) => {
        if (
          target === activePicker ||
          target === timePickable ||
          activePicker.contains(target)
        ) {
          return;
        }

        document.removeEventListener("mousedown", onClickAway);
        document.body.removeChild(activePicker);
        activePicker = null;
      };

      document.addEventListener("mousedown", onClickAway);
    });
  });
}

function showTimePicker(timePickable) {
  const picker = buildTimePicker(timePickable);
  const { bottom: top, left } = timePickable.getBoundingClientRect();

  picker.style.top = `${top}px`;
  picker.style.left = `${left}px`;

  document.body.appendChild(picker);

  return picker;
}

function buildTimePicker(timePickable) {
  const picker = document.createElement("div");
  const hourOptions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(
    numberToOption
  );
  const minuteOptions = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55].map(
    numberToOption
  );

  picker.classList.add("time-picker");
  picker.innerHTML = `
      <select class="time-picker__select">
          ${hourOptions.join("")}
      </select>
      :
      <select class="time-picker__select">
          ${minuteOptions.join("")}
      </select>
      <select class="time-picker__select">
          <option value="am">am</option>
          <option value="pm">pm</option>
      </select>
  `;

  const selects = getSelectsFromTimePicker(picker);

  selects.hour.addEventListener(
    "change",
    () => (timePickable.value = getTimeStringFromTimePicker(picker))
  );
  selects.minute.addEventListener(
    "change",
    () => (timePickable.value = getTimeStringFromTimePicker(picker))
  );
  selects.meridiem.addEventListener(
    "change",
    () => (timePickable.value = getTimeStringFromTimePicker(picker))
  );

  if (timePickable.value) {
    const { hour, minute, meridiem } = getTimePartsFromPickable(timePickable);

    selects.hour.value = hour;
    selects.minute.value = minute;
    selects.meridiem.value = meridiem;
  }

  return picker;
}

function getTimePartsFromPickable(timePickable) {
  const pattern = /^(\d+):(\d+) (am|pm)$/;
  const match = timePickable.value.match(pattern);
  if (!match) return { hour: "01", minute: "00", meridiem: "am" };

  const [hour, minute, meridiem] = Array.from(match).splice(1);

  return {
    hour,
    minute,
    meridiem,
  };
}

function getSelectsFromTimePicker(timePicker) {
  const [hour, minute, meridiem] = timePicker.querySelectorAll(
    ".time-picker__select"
  );

  return {
    hour,
    minute,
    meridiem,
  };
}

function getTimeStringFromTimePicker(timePicker) {
  const selects = getSelectsFromTimePicker(timePicker);

  return `${selects.hour.value}:${selects.minute.value} ${selects.meridiem.value}`;
}

function numberToOption(number) {
  const padded = number.toString().padStart(2, "0");

  return `<option value="${padded}">${padded}</option>`;
}

// Combined DateTime Picker functionality
function activateDateTimePicker() {
  document
    .querySelectorAll(".datetime-pickable")
    .forEach((datetimePickable) => {
      let activePicker = null;

      datetimePickable.addEventListener("focus", () => {
        if (activePicker) return;

        activePicker = showDateTimePicker(datetimePickable);

        const onClickAway = ({ target }) => {
          if (
            target === activePicker ||
            target === datetimePickable ||
            activePicker.contains(target)
          ) {
            return;
          }

          document.removeEventListener("mousedown", onClickAway);
          document.body.removeChild(activePicker);
          activePicker = null;
        };

        document.addEventListener("mousedown", onClickAway);
      });
    });
}

function showDateTimePicker(datetimePickable) {
  const picker = buildDateTimePicker(datetimePickable);
  const { bottom: top, left } = datetimePickable.getBoundingClientRect();

  picker.style.top = `${top}px`;
  picker.style.left = `${left}px`;

  document.body.appendChild(picker);

  return picker;
}

function buildDateTimePicker(datetimePickable) {
  const picker = document.createElement("div");
  const hourOptions = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map(
    numberToOption
  );
  const minuteOptions = [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55].map(
    numberToOption
  );

  picker.classList.add("datetime-pi   cker");
  picker.innerHTML = `
      <div class="datetime-picker__section">
          <label class="datetime-picker__label">Date</label>
          <input type="date" class="datetime-picker__date" id="date-input">
      </div>
      <div class="datetime-picker__section">
          <label class="datetime-picker__label">Time</label>
          <div class="datetime-picker__time">
              <select class="datetime-picker__select" id="hour-select">
                  ${hourOptions.join("")}
              </select>
              <span class="datetime-picker__separator">:</span>
              <select class="datetime-picker__select" id="minute-select">
                  ${minuteOptions.join("")}
              </select>
              <select class="datetime-picker__select" id="meridiem-select">
                  <option value="am">AM</option>
                  <option value="pm">PM</option>
              </select>
          </div>
      </div>
  `;

  const dateInput = picker.querySelector("#date-input");
  const hourSelect = picker.querySelector("#hour-select");
  const minuteSelect = picker.querySelector("#minute-select");
  const meridiemSelect = picker.querySelector("#meridiem-select");

  // Set current date as default
  const today = new Date();
  dateInput.value = today.toISOString().split("T")[0];

  // Parse existing value if any
  if (datetimePickable.value) {
    const { date, hour, minute, meridiem } =
      getDateTimePartsFromPickable(datetimePickable);
    if (date) dateInput.value = date;
    if (hour) hourSelect.value = hour;
    if (minute) minuteSelect.value = minute;
    if (meridiem) meridiemSelect.value = meridiem;
  }

  // Make the entire date input area clickable
  dateInput.style.cursor = "pointer";
  dateInput.addEventListener("click", function (e) {
    e.stopPropagation();
    this.showPicker(); // This opens the native date picker
  });

  // Update the input when any value changes
  const updateValue = () => {
    datetimePickable.value = getDateTimeStringFromPicker(picker);
  };

  dateInput.addEventListener("change", updateValue);
  hourSelect.addEventListener("change", updateValue);
  minuteSelect.addEventListener("change", updateValue);
  meridiemSelect.addEventListener("change", updateValue);

  return picker;
}

function getDateTimePartsFromPickable(datetimePickable) {
  // Expected format: "dd/mm/yyyy HH:MM am/pm"
  const pattern = /^(\d{2}\/\d{2}\/\d{4})\s+(\d+):(\d+)\s+(am|pm)$/i;
  const match = datetimePickable.value.match(pattern);

  if (!match) {
    return { date: null, hour: "01", minute: "00", meridiem: "am" };
  }

  const [, dateStr, hour, minute, meridiem] = match;

  // Convert dd/mm/yyyy to yyyy-mm-dd for date input
  const [day, month, year] = dateStr.split("/");
  const date = `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;

  return {
    date,
    hour: hour.padStart(2, "0"),
    minute: minute.padStart(2, "0"),
    meridiem: meridiem.toLowerCase(),
  };
}

function getDateTimeStringFromPicker(picker) {
  const dateInput = picker.querySelector("#date-input");
  const hourSelect = picker.querySelector("#hour-select");
  const minuteSelect = picker.querySelector("#minute-select");
  const meridiemSelect = picker.querySelector("#meridiem-select");

  if (!dateInput.value) return "";

  // Convert yyyy-mm-dd to dd/mm/yyyy
  const [year, month, day] = dateInput.value.split("-");
  const formattedDate = `${day}/${month}/${year}`;

  const time = `${hourSelect.value}:${minuteSelect.value} ${meridiemSelect.value}`;

  return `${formattedDate} ${time}`;
}

// Add a new symptomatic row
function addSymptomaticRow() {
  const tbody = document.querySelector("#symptomatic-table tbody");
  const rows = tbody.querySelectorAll("tr.symptomatic-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `symptomatic_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `symptomatic_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].removeAttribute("placeholder");
  }

  // Update structured time input names and clear values
  const structuredTimeInputs = newRow.querySelectorAll(
    ".structured-time-input"
  );
  if (structuredTimeInputs[0]) {
    const timeInputs =
      structuredTimeInputs[0].querySelectorAll("input, select");
    timeInputs[0].name = `symptomatic_hour_${nextIndex}`;
    timeInputs[0].value = "";
    timeInputs[0].setAttribute("maxlength", "2");
    timeInputs[1].name = `symptomatic_minute_${nextIndex}`;
    timeInputs[1].value = "";
    timeInputs[1].setAttribute("maxlength", "2");
    timeInputs[2].name = `symptomatic_meridiem_${nextIndex}`;
    timeInputs[2].value = "AM";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `symptomatic_status_${nextIndex}`;
    textareas[0].value = "";
    textareas[0].removeAttribute("placeholder");
    autoGrow(textareas[0]);
  }
  if (textareas[1]) {
    textareas[1].name = `wbc_value_${nextIndex}`;
    textareas[1].value = "";
    textareas[1].removeAttribute("placeholder");
    autoGrow(textareas[1]);
  }
  if (textareas[2]) {
    textareas[2].name = `creat_value_${nextIndex}`;
    textareas[2].value = "";
    textareas[2].removeAttribute("placeholder");
    autoGrow(textareas[2]);
  }
  if (textareas[3]) {
    textareas[3].name = `symptomatic_notes_${nextIndex}`;
    textareas[3].value = "";
    textareas[3].removeAttribute("placeholder");
    autoGrow(textareas[3]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });

  // Center alignment for newly added time inputs
  const newTimeInputs = newRow.querySelectorAll("input.time-pickable");
  newTimeInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });
}

// Remove the last symptomatic row (keep at least one)
function removeSymptomaticRow() {
  const tbody = document.querySelector("#symptomatic-table tbody");
  const rows = tbody.querySelectorAll("tr.symptomatic-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// Add a new mode row
function addModeRow() {
  const tbody = document.querySelector("#mode-table tbody");
  const rows = tbody.querySelectorAll("tr.mode-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `mode_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `mode_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100px";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].removeAttribute("placeholder");
  }

  // Update structured time input names and clear values
  const structuredTimeInputs = newRow.querySelectorAll(
    ".structured-time-input"
  );
  if (structuredTimeInputs[0]) {
    const timeInputs =
      structuredTimeInputs[0].querySelectorAll("input, select");
    timeInputs[0].name = `mode_hour_${nextIndex}`;
    timeInputs[0].value = "";
    timeInputs[0].setAttribute("maxlength", "2");
    timeInputs[1].name = `mode_minute_${nextIndex}`;
    timeInputs[1].value = "";
    timeInputs[1].setAttribute("maxlength", "2");
    timeInputs[2].name = `mode_meridiem_${nextIndex}`;
    timeInputs[2].value = "AM";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `mode_value_${nextIndex}`;
    textareas[0].value = "";
    textareas[0].removeAttribute("placeholder");
    autoGrow(textareas[0]);
  }
  if (textareas[1]) {
    textareas[1].name = `mode_fio2_${nextIndex}`;
    textareas[1].value = "";
    textareas[1].removeAttribute("placeholder");
    autoGrow(textareas[1]);
  }
  if (textareas[2]) {
    textareas[2].name = `mode_peep_${nextIndex}`;
    textareas[2].value = "";
    textareas[2].removeAttribute("placeholder");
    autoGrow(textareas[2]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });

  // Add validation to newly added time inputs
  setTimeout(addStructuredTimeValidation, 100);
}

// Remove the last mode row (keep at least one)
function removeModeRow() {
  const tbody = document.querySelector("#mode-table tbody");
  const rows = tbody.querySelectorAll("tr.mode-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// Initialize structured datetime input validation
function initializeStructuredDateTimeValidation() {
  // Add validation to existing inputs
  addStructuredDateTimeValidation();

  // Re-validate when new rows are added
  const originalAddVentilatorRow = window.addVentilatorRow;
  window.addVentilatorRow = function () {
    originalAddVentilatorRow();
    // Add validation to newly added inputs
    setTimeout(addStructuredDateTimeValidation, 100);
  };
}

// Add validation to structured datetime inputs
function addStructuredDateTimeValidation() {
  // Day inputs (1-31, max 2 digits)
  document.querySelectorAll("input.datetime-day").forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value;
      // Remove any non-numeric characters
      value = value.replace(/[^0-9]/g, "");
      // Limit to 2 digits
      if (value.length > 2) {
        value = value.substring(0, 2);
      }
      // Validate range
      if (value && (parseInt(value) < 1 || parseInt(value) > 31)) {
        value = value.substring(0, 1);
      }
      e.target.value = value;
    });

    input.addEventListener("keypress", function (e) {
      // Only allow numbers
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  });

  // Month inputs (1-12, max 2 digits)
  document.querySelectorAll("input.datetime-month").forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value;
      value = value.replace(/[^0-9]/g, "");
      if (value.length > 2) {
        value = value.substring(0, 2);
      }
      if (value && (parseInt(value) < 1 || parseInt(value) > 12)) {
        value = value.substring(0, 1);
      }
      e.target.value = value;
    });

    input.addEventListener("keypress", function (e) {
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  });

  // Year inputs (any 4-digit number)
  document.querySelectorAll("input.datetime-year").forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value;
      value = value.replace(/[^0-9]/g, "");
      if (value.length > 4) {
        value = value.substring(0, 4);
      }
      e.target.value = value;
    });

    input.addEventListener("keypress", function (e) {
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  });

  // Hour inputs (1-12, max 2 digits)
  document.querySelectorAll("input.datetime-hour").forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value;
      value = value.replace(/[^0-9]/g, "");
      if (value.length > 2) {
        value = value.substring(0, 2);
      }
      if (value && (parseInt(value) < 1 || parseInt(value) > 12)) {
        value = value.substring(0, 1);
      }
      e.target.value = value;
    });

    input.addEventListener("keypress", function (e) {
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  });

  // Minute inputs (0-59, max 2 digits)
  document.querySelectorAll("input.datetime-minute").forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value;
      value = value.replace(/[^0-9]/g, "");
      if (value.length > 2) {
        value = value.substring(0, 2);
      }
      if (value && (parseInt(value) < 0 || parseInt(value) > 59)) {
        value = value.substring(0, 1);
      }
      e.target.value = value;
    });

    input.addEventListener("keypress", function (e) {
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  });
}

// Initialize structured time input validation
function initializeStructuredTimeValidation() {
  // Add validation to existing inputs
  addStructuredTimeValidation();

  // Re-validate when new rows are added
  const originalAddSymptomaticRow = window.addSymptomaticRow;
  window.addSymptomaticRow = function () {
    originalAddSymptomaticRow();
    // Add validation to newly added inputs
    setTimeout(addStructuredTimeValidation, 100);
  };

  // Re-validate when new mode rows are added
  const originalAddModeRow = window.addModeRow;
  window.addModeRow = function () {
    originalAddModeRow();
    // Add validation to newly added inputs
    setTimeout(addStructuredTimeValidation, 100);
  };
}

// Add validation to structured time inputs
function addStructuredTimeValidation() {
  // Hour inputs (1-12, max 2 digits)
  document.querySelectorAll("input.time-hour").forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value;
      value = value.replace(/[^0-9]/g, "");
      if (value.length > 2) {
        value = value.substring(0, 2);
      }
      if (value && (parseInt(value) < 1 || parseInt(value) > 12)) {
        value = value.substring(0, 1);
      }
      e.target.value = value;
    });

    input.addEventListener("keypress", function (e) {
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  });

  // Minute inputs (0-59, max 2 digits)
  document.querySelectorAll("input.time-minute").forEach((input) => {
    input.addEventListener("input", function (e) {
      let value = e.target.value;
      value = value.replace(/[^0-9]/g, "");
      if (value.length > 2) {
        value = value.substring(0, 2);
      }
      if (value && (parseInt(value) < 0 || parseInt(value) > 59)) {
        value = value.substring(0, 1);
      }
      e.target.value = value;
    });

    input.addEventListener("keypress", function (e) {
      if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
      }
    });
  });
}

// GCS Table Functions
function addGcsRow() {
  const tbody = document.querySelector("#gcs-table tbody");
  const lastRow = tbody.querySelector("tr:last-child");
  const newRow = lastRow.cloneNode(true);

  // Get the current row number
  const currentRowNumber = tbody.querySelectorAll("tr").length;
  const newRowNumber = currentRowNumber + 1;

  // Update input names and clear values
  const inputs = newRow.querySelectorAll("input, textarea, select");
  inputs.forEach((input) => {
    const name = input.getAttribute("name");
    if (name) {
      const newName = name.replace(/\d+$/, newRowNumber);
      input.setAttribute("name", newName);
    }

    // Clear values
    if (input.type === "text" || input.type === "number") {
      input.value = "";
    } else if (input.tagName === "TEXTAREA") {
      input.value = "";
    } else if (input.tagName === "SELECT") {
      input.selectedIndex = 0;
    }

    // Set maxlength for time inputs
    if (
      input.classList.contains("time-hour") ||
      input.classList.contains("time-minute")
    ) {
      input.setAttribute("maxlength", "2");
    }
  });

  tbody.appendChild(newRow);

  // Initialize datepicker and structured time validation for the new row
  initializeDatepickers();
  initializeStructuredTimeValidation();
}

function removeGcsRow() {
  const tbody = document.querySelector("#gcs-table tbody");
  const rows = tbody.querySelectorAll("tr");

  // Keep at least one row
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// SEDATION Table Functions
function addSedationRow() {
  const tbody = document.querySelector("#sedation-table tbody");
  const lastRow = tbody.querySelector("tr:last-child");
  const newRow = lastRow.cloneNode(true);

  // Get the current row number
  const currentRowNumber = tbody.querySelectorAll("tr").length;
  const newRowNumber = currentRowNumber + 1;

  // Update input names and clear values
  const inputs = newRow.querySelectorAll("input, textarea, select");
  inputs.forEach((input) => {
    const name = input.getAttribute("name");
    if (name) {
      const newName = name.replace(/\d+$/, newRowNumber);
      input.setAttribute("name", newName);
    }

    // Clear values
    if (input.type === "text" || input.type === "number") {
      input.value = "";
    } else if (input.tagName === "TEXTAREA") {
      input.value = "";
    } else if (input.tagName === "SELECT") {
      input.selectedIndex = 0;
    }

    // Set maxlength for time inputs
    if (
      input.classList.contains("time-hour") ||
      input.classList.contains("time-minute")
    ) {
      input.setAttribute("maxlength", "2");
    }
  });

  tbody.appendChild(newRow);

  // Initialize datepicker and structured time validation for the new row
  initializeDatepickers();
  initializeStructuredTimeValidation();
}

function removeSedationRow() {
  const tbody = document.querySelector("#sedation-table tbody");
  const rows = tbody.querySelectorAll("tr");

  // Keep at least one row
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// DVT PROPHYLAXIS Table Functions
function addDvtRow() {
  const tbody = document.querySelector("#dvt-table tbody");
  const lastRow = tbody.querySelector("tr:last-child");
  const newRow = lastRow.cloneNode(true);

  // Get the current row number
  const currentRowNumber = tbody.querySelectorAll("tr").length;
  const newRowNumber = currentRowNumber + 1;

  // Update input names and clear values
  const inputs = newRow.querySelectorAll("input, textarea, select");
  inputs.forEach((input) => {
    const name = input.getAttribute("name");
    if (name) {
      const newName = name.replace(/\d+$/, newRowNumber);
      input.setAttribute("name", newName);
    }

    // Clear values
    if (input.type === "text" || input.type === "number") {
      input.value = "";
    } else if (input.tagName === "TEXTAREA") {
      input.value = "";
    } else if (input.tagName === "SELECT") {
      input.selectedIndex = 0;
    }

    // Set maxlength for time inputs
    if (
      input.classList.contains("time-hour") ||
      input.classList.contains("time-minute")
    ) {
      input.setAttribute("maxlength", "2");
    }
  });

  tbody.appendChild(newRow);

  // Initialize datepicker and structured time validation for the new row
  initializeDatepickers();
  initializeStructuredTimeValidation();
}

function removeDvtRow() {
  const tbody = document.querySelector("#dvt-table tbody");
  const rows = tbody.querySelectorAll("tr");

  // Keep at least one row
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// CATETHER Table Functions
function addCatheterRow() {
  const tbody = document.querySelector("#catheter-table tbody");
  const rows = tbody.querySelectorAll("tr.catheter-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `catheter_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `catheter_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }
  if (dateInputs[1]) {
    dateInputs[1].name = `catheter_changed_on_${nextIndex}`;
    dateInputs[1].value = "";
    dateInputs[1].id = `catheter_changed_on_${nextIndex}`;
    dateInputs[1].removeAttribute("data-datepicker-initialized");
    dateInputs[1].className = "datepicker";
    dateInputs[1].style.width = "100%";
    dateInputs[1].style.boxSizing = "border-box";
    dateInputs[1].style.textAlign = "center";
    dateInputs[1].placeholder = "dd/mm/yyyy";
  }
  if (dateInputs[2]) {
    dateInputs[2].name = `catheter_removed_on_${nextIndex}`;
    dateInputs[2].value = "";
    dateInputs[2].id = `catheter_removed_on_${nextIndex}`;
    dateInputs[2].removeAttribute("data-datepicker-initialized");
    dateInputs[2].className = "datepicker";
    dateInputs[2].style.width = "100%";
    dateInputs[2].style.boxSizing = "border-box";
    dateInputs[2].style.textAlign = "center";
    dateInputs[2].placeholder = "dd/mm/yyyy";
  }

  // Update structured time input names and clear values
  const structuredTimeInputs = newRow.querySelectorAll(
    ".structured-time-input"
  );
  if (structuredTimeInputs[0]) {
    const timeInputs =
      structuredTimeInputs[0].querySelectorAll("input, select");
    timeInputs[0].name = `catheter_hour_${nextIndex}`;
    timeInputs[0].value = "";
    timeInputs[0].setAttribute("maxlength", "2");
    timeInputs[1].name = `catheter_minute_${nextIndex}`;
    timeInputs[1].value = "";
    timeInputs[1].setAttribute("maxlength", "2");
    timeInputs[2].name = `catheter_meridiem_${nextIndex}`;
    timeInputs[2].value = "AM";
  }

  // Update catheter out date input (separate from time)
  const outDateInput = newRow.querySelector('input[name^="catheter_out_date"]');
  if (outDateInput) {
    outDateInput.name = `catheter_out_date_${nextIndex}`;
    outDateInput.value = "";
    outDateInput.id = `catheter_out_date_${nextIndex}`;
    outDateInput.removeAttribute("data-datepicker-initialized");
    outDateInput.className = "datepicker";
    outDateInput.style.width = "100%";
    outDateInput.style.boxSizing = "border-box";
    outDateInput.style.textAlign = "center";
    outDateInput.placeholder = "dd/mm/yyyy";
  }

  // Update structured time input for catheter out time
  const outTimeInputs = newRow.querySelectorAll(".structured-time-input");
  if (outTimeInputs[1]) {
    // Second structured time input (for out time)
    const timeInputs = outTimeInputs[1].querySelectorAll("input, select");
    timeInputs[0].name = `catheter_out_hour_${nextIndex}`;
    timeInputs[0].value = "";
    timeInputs[0].setAttribute("maxlength", "2");
    timeInputs[1].name = `catheter_out_minute_${nextIndex}`;
    timeInputs[1].value = "";
    timeInputs[1].setAttribute("maxlength", "2");
    timeInputs[2].name = `catheter_out_meridiem_${nextIndex}`;
    timeInputs[2].value = "AM";
  }

  // Update textarea names and clear values (for total catheter days)
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `total_catheter_days_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });

  // Add validation to newly added structured inputs
  setTimeout(addStructuredTimeValidation, 100);
}

// Remove the last catheter row (keep at least one)
function removeCatheterRow() {
  const tbody = document.querySelector("#catheter-table tbody");
  const rows = tbody.querySelectorAll("tr.catheter-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// CVC Table Functions
function addCvcRow() {
  const tbody = document.querySelector("#cvc-table tbody");
  const rows = tbody.querySelectorAll("tr.cvc-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  dateInputs.forEach((input, index) => {
    if (index === 0) {
      input.name = `cvc_insert_date_${nextIndex}`;
      input.id = `cvc_insert_date_${nextIndex}`;
    } else {
      input.name = `cvc_remove_date_${nextIndex}`;
      input.id = `cvc_remove_date_${nextIndex}`;
    }
    input.value = "";
    input.removeAttribute("data-datepicker-initialized");
    input.className = "datepicker";
    input.style.width = "80px";
    input.style.boxSizing = "border-box";
    input.style.textAlign = "center";
  });

  // Update time input names and clear values
  const hourInputs = newRow.querySelectorAll("input.time-hour");
  hourInputs.forEach((input, index) => {
    if (index === 0) {
      input.name = `cvc_insert_hour_${nextIndex}`;
    } else {
      input.name = `cvc_remove_hour_${nextIndex}`;
    }
    input.value = "";
  });

  const minuteInputs = newRow.querySelectorAll("input.time-minute");
  minuteInputs.forEach((input, index) => {
    if (index === 0) {
      input.name = `cvc_insert_minute_${nextIndex}`;
    } else {
      input.name = `cvc_remove_minute_${nextIndex}`;
    }
    input.value = "";
  });

  const meridiemSelects = newRow.querySelectorAll("select.time-meridiem");
  meridiemSelects.forEach((select, index) => {
    if (index === 0) {
      select.name = `cvc_insert_meridiem_${nextIndex}`;
    } else {
      select.name = `cvc_remove_meridiem_${nextIndex}`;
    }
    select.selectedIndex = 0;
  });

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  textareas.forEach((textarea) => {
    textarea.name = `cvc_total_days_${nextIndex}`;
    textarea.value = "";
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
  });

  tbody.appendChild(newRow);

  // Re-initialize datepicker for new inputs
  setTimeout(() => {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach((input) => {
      if (typeof $.fn.datepicker !== "undefined") {
        $(input).datepicker({
          dateFormat: "dd/mm/yy",
          changeMonth: true,
          changeYear: true,
          yearRange: "-100:+10",
        });
      }
    });
  }, 100);
}

function removeCvcRow() {
  const tbody = document.querySelector("#cvc-table tbody");
  const rows = tbody.querySelectorAll("tr.cvc-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// CVC TIP Table Functions
function addCvcTipRow() {
  const tbody = document.querySelector("#cvc-tip-table tbody");
  const rows = tbody.querySelectorAll("tr.cvc-tip-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  dateInputs.forEach((input, index) => {
    if (index === 0) {
      input.name = `cvc_tip_sending_date_${nextIndex}`;
      input.id = `cvc_tip_sending_date_${nextIndex}`;
    } else {
      input.name = `cvc_tip_reporting_date_${nextIndex}`;
      input.id = `cvc_tip_reporting_date_${nextIndex}`;
    }
    input.value = "";
    input.removeAttribute("data-datepicker-initialized");
    input.className = "datepicker";
    input.style.width = "100px";
    input.style.boxSizing = "border-box";
    input.style.textAlign = "center";
  });

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  textareas.forEach((textarea, index) => {
    if (index === 0) {
      textarea.name = `cvc_tip_sample_type_${nextIndex}`;
    } else {
      textarea.name = `cvc_tip_cs_report_${nextIndex}`;
    }
    textarea.value = "";
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
  });

  tbody.appendChild(newRow);

  // Re-initialize datepicker for new inputs
  setTimeout(() => {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach((input) => {
      if (typeof $.fn.datepicker !== "undefined") {
        $(input).datepicker({
          dateFormat: "dd/mm/yy",
          changeMonth: true,
          changeYear: true,
          yearRange: "-100:+10",
        });
      }
    });
  }, 100);
}

function removeCvcTipRow() {
  const tbody = document.querySelector("#cvc-tip-table tbody");
  const rows = tbody.querySelectorAll("tr.cvc-tip-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// UPDATED INFOs Table Functions
function addUpdatedInfoRow() {
  const tbody = document.querySelector("#updated-info-table tbody");
  const rows = tbody.querySelectorAll("tr.updated-info-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  dateInputs.forEach((input) => {
    input.name = `updated_info_date_${nextIndex}`;
    input.id = `updated_info_date_${nextIndex}`;
    input.value = "";
    input.removeAttribute("data-datepicker-initialized");
    input.className = "datepicker";
    input.style.width = "100px";
    input.style.boxSizing = "border-box";
    input.style.textAlign = "center";
  });

  // Update time input names and clear values
  const hourInputs = newRow.querySelectorAll("input.time-hour");
  hourInputs.forEach((input) => {
    input.name = `updated_info_hour_${nextIndex}`;
    input.value = "";
  });

  const minuteInputs = newRow.querySelectorAll("input.time-minute");
  minuteInputs.forEach((input) => {
    input.name = `updated_info_minute_${nextIndex}`;
    input.value = "";
  });

  const meridiemSelects = newRow.querySelectorAll("select.time-meridiem");
  meridiemSelects.forEach((select) => {
    select.name = `updated_info_meridiem_${nextIndex}`;
    select.selectedIndex = 0;
  });

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  textareas.forEach((textarea) => {
    textarea.name = `updated_info_notes_${nextIndex}`;
    textarea.value = "";
    textarea.style.height = "auto";
    textarea.style.height = textarea.scrollHeight + "px";
  });

  tbody.appendChild(newRow);

  // Re-initialize datepicker for new inputs
  setTimeout(() => {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach((input) => {
      if (typeof $.fn.datepicker !== "undefined") {
        $(input).datepicker({
          dateFormat: "dd/mm/yy",
          changeMonth: true,
          changeYear: true,
          yearRange: "-100:+10",
        });
      }
    });
  }, 100);
}

function removeUpdatedInfoRow() {
  const tbody = document.querySelector("#updated-info-table tbody");
  const rows = tbody.querySelectorAll("tr.updated-info-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// PROBLEM Table Functions
function addProblemRow() {
  const tbody = document.querySelector("#problem-table tbody");
  const rows = tbody.querySelectorAll("tr.problem-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `problem_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `problem_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }

  // Update structured time input names and clear values
  const structuredTimeInputs = newRow.querySelectorAll(
    ".structured-time-input"
  );
  if (structuredTimeInputs[0]) {
    const timeInputs =
      structuredTimeInputs[0].querySelectorAll("input, select");
    timeInputs[0].name = `problem_hour_${nextIndex}`;
    timeInputs[0].value = "";
    timeInputs[0].setAttribute("maxlength", "2");
    timeInputs[1].name = `problem_minute_${nextIndex}`;
    timeInputs[1].value = "";
    timeInputs[1].setAttribute("maxlength", "2");
    timeInputs[2].name = `problem_meridiem_${nextIndex}`;
    timeInputs[2].value = "AM";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `types_of_symptoms_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }
  if (textareas[1]) {
    textareas[1].name = `pain_burning_sensation_${nextIndex}`;
    textareas[1].value = "";
    autoGrow(textareas[1]);
  }

  // Update fever input names and clear values
  const feverContainer = newRow.querySelector(".fever-input-container");
  if (feverContainer) {
    const feverInput = feverContainer.querySelector(".fever-temperature-input");

    if (feverInput) {
      feverInput.name = `fever_temperature_${nextIndex}`;
      feverInput.value = "";
      feverInput.placeholder = "36.5";
      feverInput.style.backgroundColor = "";
      feverInput.title = "";
    }
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });

  // Add validation to newly added structured inputs
  setTimeout(addStructuredTimeValidation, 100);
}

// Remove the last problem row (keep at least one)
function removeProblemRow() {
  const tbody = document.querySelector("#problem-table tbody");
  const rows = tbody.querySelectorAll("tr.problem-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// URINE RE Table Functions
function addUrineReRow() {
  const tbody = document.querySelector("#urine-re-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-re-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `urine_re_sending_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `urine_re_sending_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }
  if (dateInputs[1]) {
    dateInputs[1].name = `urine_re_reporting_date_${nextIndex}`;
    dateInputs[1].value = "";
    dateInputs[1].id = `urine_re_reporting_date_${nextIndex}`;
    dateInputs[1].removeAttribute("data-datepicker-initialized");
    dateInputs[1].className = "datepicker";
    dateInputs[1].style.width = "100%";
    dateInputs[1].style.boxSizing = "border-box";
    dateInputs[1].style.textAlign = "center";
    dateInputs[1].placeholder = "dd/mm/yyyy";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `urine_re_sample_type_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }
  if (textareas[1]) {
    textareas[1].name = `urine_re_result_${nextIndex}`;
    textareas[1].value = "";
    autoGrow(textareas[1]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });
}

// Remove the last urine re row (keep at least one)
function removeUrineReRow() {
  const tbody = document.querySelector("#urine-re-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-re-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// URINE OUTPUT Table Functions
function addUrineOutputRow() {
  const tbody = document.querySelector("#urine-output-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-output-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `urine_output_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `urine_output_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }

  // Update structured time input names and clear values
  const structuredTimeInputs = newRow.querySelectorAll(
    ".structured-time-input"
  );
  if (structuredTimeInputs[0]) {
    const timeInputs =
      structuredTimeInputs[0].querySelectorAll("input, select");
    timeInputs[0].name = `urine_output_hour_${nextIndex}`;
    timeInputs[0].value = "";
    timeInputs[0].setAttribute("maxlength", "2");
    timeInputs[1].name = `urine_output_minute_${nextIndex}`;
    timeInputs[1].value = "";
    timeInputs[1].setAttribute("maxlength", "2");
    timeInputs[2].name = `urine_output_meridiem_${nextIndex}`;
    timeInputs[2].value = "AM";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `urine_output_amount_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });

  // Add validation to newly added structured inputs
  setTimeout(addStructuredTimeValidation, 100);
}

// Remove the last urine output row (keep at least one)
function removeUrineOutputRow() {
  const tbody = document.querySelector("#urine-output-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-output-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// URINE RE PUS CELLS Table Functions
function addUrineRePusRow() {
  const tbody = document.querySelector("#urine-re-pus-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-re-pus-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `urine_re_pus_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `urine_re_pus_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }

  // Update structured time input names and clear values
  const structuredTimeInputs = newRow.querySelectorAll(
    ".structured-time-input"
  );
  if (structuredTimeInputs[0]) {
    const timeInputs =
      structuredTimeInputs[0].querySelectorAll("input, select");
    timeInputs[0].name = `urine_re_pus_hour_${nextIndex}`;
    timeInputs[0].value = "";
    timeInputs[0].setAttribute("maxlength", "2");
    timeInputs[1].name = `urine_re_pus_minute_${nextIndex}`;
    timeInputs[1].value = "";
    timeInputs[1].setAttribute("maxlength", "2");
    timeInputs[2].name = `urine_re_pus_meridiem_${nextIndex}`;
    timeInputs[2].value = "AM";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `urine_re_pus_cells_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });

  // Add validation to newly added structured inputs
  setTimeout(addStructuredTimeValidation, 100);
}

// Remove the last urine re pus row (keep at least one)
function removeUrineRePusRow() {
  const tbody = document.querySelector("#urine-re-pus-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-re-pus-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// URINE RESULT Table Functions
function addUrineResultRow() {
  const tbody = document.querySelector("#urine-result-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-result-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `urine_result_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `urine_result_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `urine_result_color_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }
  if (textareas[1]) {
    textareas[1].name = `urine_result_cloudy_${nextIndex}`;
    textareas[1].value = "";
    autoGrow(textareas[1]);
  }
  if (textareas[2]) {
    textareas[2].name = `urine_result_catheter_obs_${nextIndex}`;
    textareas[2].value = "";
    autoGrow(textareas[2]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });
}

// Remove the last urine result row (keep at least one)
function removeUrineResultRow() {
  const tbody = document.querySelector("#urine-result-table tbody");
  const rows = tbody.querySelectorAll("tr.urine-result-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// IMMUNO SUPPRESSANTS Table Functions
function addImmunoSuppressantsRow() {
  const tbody = document.querySelector("#immuno-suppressants-table tbody");
  const rows = tbody.querySelectorAll("tr.immuno-suppressants-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `immuno_suppressants_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `immuno_suppressants_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `immuno_suppressants_injection_name_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }

  // Update text input names and clear values
  const textInputs = newRow.querySelectorAll(
    'input[type="text"]:not(.datepicker)'
  );
  if (textInputs[0]) {
    textInputs[0].name = `immuno_suppressants_start_on_${nextIndex}`;
    textInputs[0].value = "";
  }
  if (textInputs[1]) {
    textInputs[1].name = `immuno_suppressants_stop_on_${nextIndex}`;
    textInputs[1].value = "";
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });
}

// Remove the last immuno suppressants row (keep at least one)
function removeImmunoSuppressantsRow() {
  const tbody = document.querySelector("#immuno-suppressants-table tbody");
  const rows = tbody.querySelectorAll("tr.immuno-suppressants-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// CREATININE LEVEL Table Functions
function addCreatinineLevelRow() {
  const tbody = document.querySelector("#creatinine-level-table tbody");
  const rows = tbody.querySelectorAll("tr.creatinine-level-row");
  if (rows.length === 0) return;

  const lastRow = rows[rows.length - 1];
  const newRow = lastRow.cloneNode(true);

  const nextIndex = rows.length + 1;

  // Update date input names and clear values
  const dateInputs = newRow.querySelectorAll("input.datepicker");
  if (dateInputs[0]) {
    dateInputs[0].name = `creatinine_level_date_${nextIndex}`;
    dateInputs[0].value = "";
    dateInputs[0].id = `creatinine_level_date_${nextIndex}`;
    dateInputs[0].removeAttribute("data-datepicker-initialized");
    dateInputs[0].className = "datepicker";
    dateInputs[0].style.width = "100%";
    dateInputs[0].style.boxSizing = "border-box";
    dateInputs[0].style.textAlign = "center";
    dateInputs[0].placeholder = "dd/mm/yyyy";
  }

  // Update textarea names and clear values
  const textareas = newRow.querySelectorAll("textarea");
  if (textareas[0]) {
    textareas[0].name = `creatinine_level_result_${nextIndex}`;
    textareas[0].value = "";
    autoGrow(textareas[0]);
  }

  tbody.appendChild(newRow);

  // Initialize datepickers for newly added inputs
  if (window.jQuery && jQuery.fn && jQuery.fn.datepicker) {
    const newDateInputs = newRow.querySelectorAll("input.datepicker");
    newDateInputs.forEach(function (input) {
      if (jQuery(input).hasClass("hasDatepicker")) {
        jQuery(input).datepicker("destroy");
      }
      jQuery(input).datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear: true,
        yearRange: "c-10:c+10",
      });
    });
  }

  // Center alignment for newly added date inputs
  const newDateInputs = newRow.querySelectorAll("input.datepicker");
  newDateInputs.forEach(function (input) {
    if (!input) return;
    input.style.textAlign = "center";
    const parentCell = input.closest("td");
    if (parentCell) parentCell.style.textAlign = "center";
  });
}

// Remove the last creatinine level row (keep at least one)
function removeCreatinineLevelRow() {
  const tbody = document.querySelector("#creatinine-level-table tbody");
  const rows = tbody.querySelectorAll("tr.creatinine-level-row");
  if (rows.length > 1) {
    tbody.removeChild(rows[rows.length - 1]);
  }
}

// Make debug function available globally
window.debugDatepickers = debugDatepickers;
window.reinitializeDatepickers = reinitializeDatepickers;
