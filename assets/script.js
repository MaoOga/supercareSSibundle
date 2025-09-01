function autoGrow(textarea) {
  textarea.style.height = "auto";
  textarea.style.height = textarea.scrollHeight + "px";
}

// ======== Audit logging (shared with admin.html via localStorage) ========
const AUDIT_KEY = "admin_audit_logs";
function loadAuditLogs() {
  try {
    return JSON.parse(localStorage.getItem(AUDIT_KEY)) || [];
  } catch {
    return [];
  }
}
function saveAuditLogs(logs) {
  localStorage.setItem(AUDIT_KEY, JSON.stringify(logs));
}
function nowIso() {
  return new Date().toISOString();
}
function getNurseIdForAudit() {
  try {
    return localStorage.getItem("nurseId") || "Nurse";
  } catch {
    return "Nurse";
  }
}
function getUHIDForAudit() {
  const el = document.querySelector('input[name="uhid"]');
  return el ? el.value : "";
}
function addAudit(action, entity, details) {
  const logs = loadAuditLogs();
  const entry = {
    id: crypto && crypto.randomUUID ? crypto.randomUUID() : String(Date.now()),
    ts: nowIso(),
    action,
    entity,
    nurse: getNurseIdForAudit(),
    page: "index",
    uhid: getUHIDForAudit(),
    ...details,
  };
  logs.unshift(entry);
  saveAuditLogs(logs);
}

// Track field edits: record before/after
const previousFieldValues = new WeakMap();
document.addEventListener("focusin", function (e) {
  const t = e.target;
  if (
    !(
      t instanceof HTMLInputElement ||
      t instanceof HTMLTextAreaElement ||
      t instanceof HTMLSelectElement
    )
  )
    return;
  const before =
    t.type === "checkbox" || t.type === "radio" ? t.checked : t.value;
  previousFieldValues.set(t, before);
});
document.addEventListener("change", function (e) {
  const t = e.target;
  if (
    !(
      t instanceof HTMLInputElement ||
      t instanceof HTMLTextAreaElement ||
      t instanceof HTMLSelectElement
    )
  )
    return;
  const before = previousFieldValues.get(t);
  const after =
    t.type === "checkbox" || t.type === "radio" ? t.checked : t.value;
  const fieldName = t.name || t.id || "unknown_field";
  if (before !== after) {
    addAudit("update", "form_field", { field: fieldName, before, after });
  }
});

$(function () {
  $(".datepicker").datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: "dd/mm/yy",
    showButtonPanel: true,
  });
  // Disable browser autofill suggestions on date inputs
  $(".datepicker").attr({
    autocomplete: "off",
    autocorrect: "off",
    autocapitalize: "off",
    spellcheck: "false",
  });
});

function printForm() {
  window.print();
}

// Hide empty datepicker inputs on print, restore after
(function setupPrintVisibilityForEmptyDatepickers() {
  function toggleEmptyDatepickers(hide) {
    $(".datepicker").each(function () {
      const isEmpty = !$(this).val();
      if (isEmpty) {
        if (hide) {
          $(this).addClass("hide-on-print");
        } else {
          $(this).removeClass("hide-on-print");
        }
      }
    });
  }

  // Before print
  if (window.matchMedia) {
    const mediaQueryList = window.matchMedia("print");
    mediaQueryList.addListener(function (mql) {
      if (mql.matches) {
        toggleEmptyDatepickers(true);
      } else {
        toggleEmptyDatepickers(false);
      }
    });
  }

  window.addEventListener("beforeprint", function () {
    toggleEmptyDatepickers(true);
  });
  window.addEventListener("afterprint", function () {
    toggleEmptyDatepickers(false);
  });
})();

function showMessage(message, type) {
  const messageBox = $("#messageBox");
  messageBox.text(message).removeClass("success error").addClass(type).show();
  setTimeout(function () {
    messageBox.hide();
  }, 4000);
}

function addAntibioticRow() {
  const table = $("#antibiotic-table tbody");
  const rowCount = table.find(".antibiotic-row").length + 1;
  const newRow = `
                <tr class="antibiotic-row">
                    <td class="tg-k2l0">${rowCount}</td>
                    <td class="tg-1wig"><textarea name="drug-name_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2;"></textarea></td>
                    <td class="tg-1wig"><textarea name="dosage_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2;"></textarea></td>
                    <td class="tg-1wig"><input type="text" name="antibiotic_usage[startedon]_${rowCount}" class="datepicker" placeholder="dd/mm/yyyy" style="width: 100px;" value=""></td>
                    <td class="tg-1wig"><input type="text" name="antibiotic_usage[stoppeon]_${rowCount}" class="datepicker" placeholder="dd/mm/yyyy" style="width: 100px;" value=""></td>
                </tr>
            `;
  table.append(newRow);
  table
    .find(".datepicker")
    .datepicker({
      dateFormat: "dd/mm/yy",
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
    })
    .attr({
      autocomplete: "off",
      autocorrect: "off",
      autocapitalize: "off",
      spellcheck: "false",
    });
  updateAntibioticRowNumbers();
  addAudit("create", "antibiotic_row", { after: { count: rowCount } });
}

function removeAntibioticRow() {
  const table = $("#antibiotic-table tbody");
  const rows = table.find(".antibiotic-row");
  if (rows.length > 1) {
    rows.last().remove();
    updateAntibioticRowNumbers();
    addAudit("delete", "antibiotic_row", {
      before: { remaining: rows.length - 1 },
    });
  } else {
    showMessage("At least one row must remain.", "error");
  }
}

function updateAntibioticRowNumbers() {
  const rows = $("#antibiotic-table tbody .antibiotic-row");
  rows.each(function (index) {
    $(this)
      .find("td:first-child")
      .text(index + 1);
    $(this)
      .find("textarea, input")
      .each(function () {
        const name = $(this).attr("name");
        const newName = name.replace(/_\d+$/, `_${index + 1}`);
        $(this).attr("name", newName);
      });
  });
}

function addDrainRow() {
  const table = $("table.tg:has(.drain-row) tbody");
  const rowCount = table.find(".drain-row").length + 1;
  const newRow = `
                <tr class="drain-row">
                    <td class="tg-1wig" colspan="4">
                        <label class="flex gap-2 items-center w-full">
                            <span class="whitespace-nowrap label-bold">${rowCount}. </span>
                            <textarea name="drain_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2;"></textarea>
                        </label>
                    </td>
                </tr>
            `;
  table.append(newRow);
  table
    .find("textarea")
    .off("input")
    .on("input", function () {
      autoGrow(this);
    });
  addAudit("create", "drain_row", { after: { count: rowCount } });
}

function removeDrainRow() {
  const table = $("table.tg:has(.drain-row) tbody");
  const rows = table.find(".drain-row");
  if (rows.length > 1) {
    rows.last().remove();
    updateDrainRowNumbers();
    addAudit("delete", "drain_row", { before: { remaining: rows.length - 1 } });
  } else {
    showMessage("At least one row must remain.", "error");
  }
}

function updateDrainRowNumbers() {
  const rows = $("table.tg:has(.drain-row) tbody .drain-row");
  rows.each(function (index) {
    $(this)
      .find("span")
      .text(`${index + 1}.`);
    $(this)
      .find("textarea")
      .attr("name", `drain_${index + 1}`);
  });
}

function addPostOperativeRow() {
  const table = $("#post-operative-table tbody");
  const rowCount = table.find(".post-operative-row").length + 1;
  const newRow = `
                <tr class="post-operative-row">
                    <td class="tg-k2l0">${rowCount}</td>
                    <td class="tg-0lax"><input type="text" name="post-operative[date]_${rowCount}" class="datepicker" placeholder="dd/mm/yyyy" style="width: 100px;" value=""></td>
                    <td class="tg-0lax"><textarea name="post-dosage_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="type-ofdischarge_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="tenderness-pain_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="swelling_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                    <td class="tg-0lax"><textarea name="Fever_${rowCount}" class="input-overlay input-full" rows="1" oninput="autoGrow(this)" style="min-height:28px; line-height:1.2; text-align:center;"></textarea></td>
                </tr>
            `;
  table.append(newRow);
  table
    .find(".datepicker")
    .datepicker({
      dateFormat: "dd/mm/yy",
      changeMonth: true,
      changeYear: true,
      showButtonPanel: true,
    })
    .attr({
      autocomplete: "off",
      autocorrect: "off",
      autocapitalize: "off",
      spellcheck: "false",
    });
  table
    .find("textarea")
    .off("input")
    .on("input", function () {
      autoGrow(this);
    });
  updatePostOperativeRowNumbers();
  addAudit("create", "post_op_row", { after: { count: rowCount } });
}

function removePostOperativeRow() {
  const table = $("#post-operative-table tbody");
  const rows = table.find(".post-operative-row");
  if (rows.length > 1) {
    rows.last().remove();
    updatePostOperativeRowNumbers();
    addAudit("delete", "post_op_row", {
      before: { remaining: rows.length - 1 },
    });
  } else {
    showMessage("At least one row must remain.", "error");
  }
}

function updatePostOperativeRowNumbers() {
  const rows = $("#post-operative-table tbody .post-operative-row");
  rows.each(function (index) {
    $(this)
      .find("td:first-child")
      .text(index + 1);
    $(this)
      .find("textarea, input")
      .each(function () {
        const name = $(this).attr("name");
        const newName = name.replace(/_\d+$/, `_${index + 1}`);
        $(this).attr("name", newName);
      });
  });
}
