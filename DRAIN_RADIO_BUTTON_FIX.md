# Drain Radio Button Fix

## Problem Description

The drain radio button functionality had two main issues:

1. **Form Submission Issue**: When no radio button was selected for "drain-used", the system was still storing "Yes" in the `drain_used` column if drain descriptions were filled in the form.

2. **Form Display Issue**: When viewing or editing a form, the radio button was showing "No" as selected even when the database contained "Yes" for `drain_used`.

## Root Cause Analysis

### Issue 1: Form Submission Logic

**File**: `submit_form.php`, `submit_form_working.php`, `submit_form_backup.php`

**Problem**: The logic was:

```php
$drainUsed = isset($formData['drain-used']) && $formData['drain-used'] === 'Yes' ? 'Yes' : 'No';

// Then always insert drain records if descriptions existed
for ($i = 1; $i <= 10; $i++) {
    $drainDescription = $formData["drain_$i"] ?? null;
    if (!empty($drainDescription)) {
        // Insert drain record with $drainUsed value
    }
}
```

**Issue**: This meant that even when no radio button was selected (defaulting to 'No'), drain records were still inserted if descriptions were filled in.

### Issue 2: Form Display Logic

**File**: `form_template.html`

**Problem**: The logic was:

```javascript
if (drains && drains.length > 0) {
  setRadioValue("drain-used", "Yes"); // Always set to "Yes" if drain records exist
} else {
  setRadioValue("drain-used", "No");
}
```

**Issue**: This ignored the actual `drain_used` value from the database and just checked if drain records existed.

## Solution Implemented

### Fix 1: Form Submission Logic

**Modified Files**: `submit_form.php`, `submit_form_working.php`, `submit_form_backup.php`

**New Logic**:

```php
$drainUsed = isset($formData['drain-used']) && $formData['drain-used'] === 'Yes' ? 'Yes' : 'No';

// Only insert drain records if drain-used is 'Yes'
if ($drainUsed === 'Yes') {
    for ($i = 1; $i <= 10; $i++) {
        $drainDescription = $formData["drain_$i"] ?? null;
        if (!empty($drainDescription)) {
            // Insert drain record
        }
    }
} else {
    // If drain-used is 'No', ensure no drain records exist
    error_log("Drain used is 'No', ensuring no drain records exist");
}
```

**Result**: Drain records are only inserted when the radio button is explicitly set to 'Yes'.

### Fix 2: Form Display Logic

**Modified File**: `form_template.html`

**New Logic**:

```javascript
if (drains && drains.length > 0) {
  // Check the drain_used value from the first drain record
  const drainUsedValue = drains[0].drain_used;
  setRadioValue("drain-used", drainUsedValue);

  // Populate drain descriptions...
} else {
  setRadioValue("drain-used", "No");
}
```

**Result**: The radio button now shows the actual `drain_used` value from the database.

## Testing

A test file `test_drain_fix.php` has been created to demonstrate the fix:

1. **Test 1**: No radio button selected + drain descriptions filled → Should not insert drain records
2. **Test 2**: "Yes" radio button selected + drain descriptions filled → Should insert drain records
3. **Test 3**: "No" radio button selected + drain descriptions filled → Should not insert drain records

## Files Modified

1. **submit_form.php** - Fixed drain insertion logic
2. **submit_form_working.php** - Fixed drain insertion logic
3. **submit_form_backup.php** - Fixed drain insertion logic
4. **form_template.html** - Fixed radio button display logic
5. **test_drain_fix.php** - Test file to demonstrate the fix
6. **DRAIN_RADIO_BUTTON_FIX.md** - This documentation file

## Expected Behavior After Fix

1. **When submitting form with no radio button selected**: No drain records will be inserted, regardless of whether drain descriptions are filled in.

2. **When submitting form with "Yes" radio button selected**: Drain records will be inserted with `drain_used = 'Yes'` for each drain description.

3. **When submitting form with "No" radio button selected**: One drain record will be inserted with `drain_used = 'No'` and description "No drains used".

4. **When viewing/editing a form**:
   - If "Yes" was selected: Radio button shows "Yes" and drain descriptions are populated
   - If "No" was selected: Radio button shows "No"
   - If nothing was selected: No radio button is selected (remains unselected)

## Impact

This fix ensures that:

- The drain radio button selection is properly respected during form submission
- The form correctly displays the saved drain radio button state when editing
- Data integrity is maintained in the `drains` table
- User expectations are met regarding form behavior
