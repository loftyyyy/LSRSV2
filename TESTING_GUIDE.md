# Quick Test Guide: Chart Theme Switching

## What Was Fixed
Chart labels in all report pages and dashboard now properly update their color when you toggle between dark mode and light mode.

## How to Test

### Quick Test (2 minutes)
1. Navigate to `/customers/reports`
2. Click "Generate Report" button
3. Wait for charts to render
4. Look for the theme toggle button (usually in top-right corner or sidebar)
5. Click it to switch from dark to light mode
6. **Expected Result**: Chart labels, axis text, and legends should change to black
7. Click theme toggle again
8. **Expected Result**: Chart labels should change back to light gray

### Comprehensive Test (10 minutes)

#### Test Each Report Page

**Customer Reports Page** (`/customers/reports`)
```
✓ Start in Dark Mode → Charts show light gray text
✓ Generate Report → 4 charts render (Status, Rentals, Acquisition, Comparison)
✓ Toggle to Light Mode → All text becomes black
✓ Verify:
  - Legend text is black
  - Axis labels are black
  - Grid lines are light
  - Tooltip text is black
✓ Toggle back to Dark Mode → Text returns to light gray
```

**Inventory Reports Page** (`/inventories/reports`)
```
✓ Navigate to page in Dark Mode
✓ Generate Report → 6 charts render
✓ Toggle to Light Mode → All charts update to black text
✓ Verify charts:
  1. Status Chart (Doughnut) - Legend text changes
  2. Condition Chart (Pie) - Legend text changes
  3. Monthly Chart (Line) - Axis labels change
  4. Item Type Chart (Pie) - Legend text changes
  5. Top Items Chart (Bar) - Axis labels change
  6. Value Chart (Bar) - Axis labels change
```

**Rental Reports Page** (`/rentals/reports`)
```
✓ Navigate to page in Dark Mode
✓ Generate Report → 6 charts render
✓ Toggle to Light Mode → All charts update
✓ Verify charts update correctly
```

**Dashboard Page** (`/dashboard`)
```
✓ Navigate to page in Dark Mode
✓ Charts automatically load
✓ Toggle to Light Mode → All 6 charts update
✓ Verify:
  - Daily Revenue Chart (Line) updates
  - Weekly Rentals Chart (Bar) updates
  - Item Status Chart (Doughnut) updates
  - Rental Status Chart (Bar) updates
  - Top Items Chart (Doughnut) updates
  - Top Customers Chart (Bar) updates
```

### What Should Change When Toggling Theme

| Element | Dark Mode | Light Mode |
|---------|-----------|-----------|
| Legend Text | #e5e7eb (light gray) | #000000 (black) |
| Axis Labels | #e5e7eb (light gray) | #000000 (black) |
| Grid Lines | #27272a (dark gray) | #d1d5db (light gray) |
| Tooltip Text | #e5e7eb (light gray) | #000000 (black) |
| Point Labels | #e5e7eb (light gray) | #000000 (black) |

### What Should NOT Change
- Chart data values
- Chart colors (dataset colors remain the same)
- Chart type or layout
- Page layout or structure

### Browser DevTools Testing

To verify theme switching is working correctly, open your browser's Developer Tools:

1. **Press F12** to open DevTools
2. **Go to Console tab**
3. You should see console logs like:
   ```
   [THEME DEBUG] Theme changed to: LIGHT
   [THEME DEBUG] Text Color: #000000
   [THEME DEBUG] Grid Color: #d1d5db
   [THEME DEBUG] Updating statusChartInstance
   [THEME DEBUG] Updating rentalsChartInstance
   [THEME DEBUG] Updating acquisitionChartInstance
   [THEME DEBUG] Updating comparisonChartInstance
   [THEME DEBUG] All charts updated successfully
   ```
4. **Check for Errors**: Should see no red error messages

### Performance Testing

1. **Rapid Theme Toggles**: Toggle the theme 5-10 times quickly
   - Expected: Charts update smoothly without flickering
   - Expected: No console errors
   - Expected: No noticeable performance lag

2. **Data Loading**: While report is generating, toggle theme
   - Expected: Theme change queues and applies after data loads
   - Expected: No conflicts or visual artifacts

### Troubleshooting

**Charts don't update color when theme changes:**
- Check browser console for errors (F12 → Console)
- Verify dark class is being added/removed from `<html>` element:
  - DevTools → Inspector → Click HTML tag
  - Look for `dark` class in class attribute
- Refresh page and try again

**Charts look correct in dark mode but wrong in light mode:**
- The text color for light mode should be pure black (#000000)
- If text is gray, that's incorrect
- Check that grid color for light mode is light gray (#d1d5db)

**Only some charts update:**
- Refresh the page
- Generate report again
- All charts should update together

## What's New vs Old Behavior

### Before (Broken)
1. ❌ Charts render with correct colors initially
2. ❌ Toggle theme
3. ❌ Charts labels stay dark (unreadable on light background)
4. ❌ Only way to fix: refresh page

### After (Fixed)
1. ✅ Charts render with correct colors initially
2. ✅ Toggle theme
3. ✅ ALL chart elements update immediately:
   - Legend text color
   - Axis labels color
   - Grid lines color
   - Tooltip text color
   - Point labels color
4. ✅ No page refresh needed

## Summary of Changes

| Page | Chart Count | Status | Notes |
|------|------------|--------|-------|
| Customer Reports | 4 | ✅ Fixed | Implemented generic color updater |
| Inventory Reports | 6 | ✅ Fixed | Applied same pattern |
| Rental Reports | 6 | ✅ Fixed | Applied same pattern |
| Dashboard | 6 | ✅ Fixed | Applied same pattern |
| **TOTAL** | **22** | ✅ **All Fixed** | Comprehensive theme support |

## Questions or Issues?

If you encounter any issues:
1. Check the browser console (F12) for error messages
2. Try refreshing the page
3. Clear browser cache and reload
4. Check that your theme toggle button is working (page background should change)
5. Verify the `dark` class is being added/removed from `<html>` element

---

**All charts in the application now properly support dark/light mode theme switching!** 🎉
