# Debug Logging Reference

## Overview
Debug logging has been added to the customer reports page to help diagnose chart theme switching issues. This document explains the debug messages and how to interpret them.

## Debug Messages

### Location
File: `resources/views/customers/reports.blade.php` (lines 870-902)

### Message Format
All debug messages are prefixed with `[THEME DEBUG]` for easy filtering in console.

### Messages Explained

#### 1. Theme Detection
```
[THEME DEBUG] Theme changed to: DARK
[THEME DEBUG] Theme changed to: LIGHT
```
- **Appears when**: User toggles theme or page detects theme change
- **Indicates**: MutationObserver detected class change on `<html>` element
- **What it means**: 
  - If shows DARK: `dark` class is present on html element
  - If shows LIGHT: `dark` class is not present on html element

#### 2. Color Configuration
```
[THEME DEBUG] Text Color: #e5e7eb
[THEME DEBUG] Grid Color: #27272a
```
(Dark mode example)

```
[THEME DEBUG] Text Color: #000000
[THEME DEBUG] Grid Color: #d1d5db
```
(Light mode example)

- **Appears when**: Theme changes
- **Indicates**: Colors being applied to charts
- **Dark Mode Expected**: 
  - Text: `#e5e7eb` (light gray)
  - Grid: `#27272a` (dark gray)
- **Light Mode Expected**:
  - Text: `#000000` (pure black)
  - Grid: `#d1d5db` (light gray)

#### 3. Chart Update Status
```
[THEME DEBUG] Updating statusChartInstance
[THEME DEBUG] Updating rentalsChartInstance
[THEME DEBUG] Updating acquisitionChartInstance
[THEME DEBUG] Updating comparisonChartInstance
```
- **Appears when**: Each chart is being updated
- **Indicates**: Specific chart found and colors being applied
- **If missing**: That chart instance hasn't been created yet or isn't available

#### 4. Completion
```
[THEME DEBUG] All charts updated successfully
```
- **Appears when**: All charts have been updated
- **Indicates**: Theme change is complete
- **If missing**: Something failed during update process

#### 5. Early Return (No Stats)
```
[THEME DEBUG] No currentStats, skipping theme update
```
- **Appears when**: Theme changes before report is generated
- **Indicates**: No data to work with yet
- **Why it happens**: User toggles theme before clicking "Generate Report"
- **Is this a problem?**: No, perfectly normal - charts will update correctly once report is generated

## How to View Debug Messages

### In Browser
1. **Open DevTools**: Press `F12`
2. **Go to Console tab**
3. **Look for `[THEME DEBUG]` messages**

### Filter Debug Messages
In browser console, type:
```javascript
console.log('%c[THEME DEBUG] Filter active', 'color: green')
```

Or in the console filter box, type: `THEME DEBUG`

### Copy All Debug Logs
1. Right-click in console
2. Select "Save as..."
3. Or select all with Ctrl+A and copy

## Troubleshooting with Debug Logs

### Scenario 1: Charts don't update on theme change
**Check the logs for:**
- Are you seeing "Theme changed to:" messages?
  - If NO: Theme detection not working, check if `dark` class actually changes on `<html>`
  - If YES: Continue to next check
  
- Are you seeing "Updating [chart]Instance" messages?
  - If NO: Charts aren't created yet, generate a report first
  - If YES: Continue to next check
  
- Are you seeing "All charts updated successfully"?
  - If NO: Update process failed, check for JavaScript errors above the logs
  - If YES: Update worked, check if colors actually changed visually

### Scenario 2: Wrong colors showing
**Check the logs for:**
```
[THEME DEBUG] Text Color: #1f2937  ← WRONG! Should be #e5e7eb or #000000
[THEME DEBUG] Grid Color: #e5e7eb  ← WRONG! Should be #27272a or #d1d5db
```

If you see wrong color codes:
- There may be a logic error in color detection
- The `isDark` variable might not be calculating correctly
- Check if Tailwind's `dark` class is actually being applied

### Scenario 3: Only some charts update
**Check the logs for:**
```
[THEME DEBUG] Updating statusChartInstance
[THEME DEBUG] Updating rentalsChartInstance
← Missing these might indicate chart not fully initialized
```

If some charts aren't logged:
- That chart might not be created yet
- Try generating the report again
- Refresh and retry

## Removing Debug Logs

When ready for production, remove all `console.log` calls from the observer:
1. Lines 867-870 (initial logs)
2. Lines 871-873 (color logs)
3. Lines 875-908 (per-chart logs)

Or keep them - they help diagnose user-reported issues!

## Related Console Messages

### From updateCharts() function
When you generate a report, you might also see:
```
[THEME DEBUG] Updating statusChartInstance
[THEME DEBUG] Updating rentalsChartInstance
[THEME DEBUG] Updating acquisitionChartInstance
[THEME DEBUG] Updating comparisonChartInstance
```
These come from the initial chart creation when generating the report.

### From Chart.js
You might see:
```
Chart.js:7 (12) Chart instance alias not found. New instance has been created. This aliasing of chart instances is not supported.
```
- This is informational from Chart.js
- Not an error, just a warning about chart instance tracking
- Can be safely ignored

## Performance Monitoring

You can use these logs to monitor performance:

```javascript
// Add timing to see how long updates take
console.time('[THEME] Chart update');
// ... chart updates ...
console.timeEnd('[THEME] Chart update');
```

Expected time: **< 100ms** for all 4 charts

If it takes longer:
- Browser might be slow
- Multiple other scripts running
- Hardware performance issues

## Testing Checklist Using Debug Logs

- [ ] Generate report and see 4 "Updating" messages
- [ ] Toggle theme and see "Theme changed to:" message
- [ ] See 4 "Updating" messages in MutationObserver
- [ ] See "All charts updated successfully"
- [ ] Verify color codes match expected values
- [ ] No JavaScript errors above the logs
- [ ] Visual colors match logged color codes

## Debug Log Locations

### Customer Reports
- **File**: `resources/views/customers/reports.blade.php`
- **Lines**: 867-902
- **Charts**: 4 (Status, Rentals, Acquisition, Comparison)
- **Logs**: Per chart + completion

### Other Pages
- **Inventory Reports**: Added similar logs, but less verbose for cleaner console
- **Rental Reports**: Added similar logs, but less verbose for cleaner console
- **Dashboard**: Added similar logs, but less verbose for cleaner console

## Recommendation

- **Development**: Keep debug logs enabled for troubleshooting
- **Staging**: Keep enabled to trace user-reported issues
- **Production**: Can keep or remove - they don't affect performance (logged to console only)

## Advanced: Enable Verbose Logging

To make other pages verbose like customer reports, modify their observers to add more logs:

```javascript
console.log('[THEME DEBUG] Theme changed to:', isDark ? 'DARK' : 'LIGHT');
console.log('[THEME DEBUG] Text Color:', textColor);
console.log('[THEME DEBUG] Grid Color:', gridColor);
// Add before each updateChartColors() call:
console.log('[THEME DEBUG] Updating [chartName]');
// Add at the end:
console.log('[THEME DEBUG] All charts updated successfully');
```

## Summary

The debug logging system provides detailed insights into:
1. ✅ Theme detection (is it firing?)
2. ✅ Color calculation (are colors correct?)
3. ✅ Chart updates (which charts are updating?)
4. ✅ Completion status (did everything finish?)

Use these logs to quickly diagnose theme switching issues without needing to modify code!
