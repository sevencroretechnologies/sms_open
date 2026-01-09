# i18n and RTL Test Checklist

**Prompt 497: Create i18n and RTL Test Checklist**

This checklist validates localization and RTL layout changes across the Smart School Management System.

## Localization Testing

### Language Switching
- [ ] Language switcher is visible in header/settings
- [ ] Switching language updates UI immediately
- [ ] Language preference persists after logout/login
- [ ] Language preference is stored in user profile
- [ ] Fallback to default language works correctly

### Translation Coverage
- [ ] Navigation menu items are translated
- [ ] Page titles are translated
- [ ] Form labels are translated
- [ ] Button text is translated
- [ ] Error messages are translated
- [ ] Success messages are translated
- [ ] Validation messages are translated
- [ ] Email templates are translated
- [ ] SMS templates are translated

### Date and Time Formatting
- [ ] Dates display in locale format
- [ ] Times display in locale format (12h/24h)
- [ ] Date pickers use locale format
- [ ] Relative time displays correctly ("2 hours ago")
- [ ] Calendar week starts on correct day for locale

### Number Formatting
- [ ] Numbers use locale decimal separator
- [ ] Numbers use locale thousands separator
- [ ] Currency displays with correct symbol
- [ ] Currency symbol position is correct for locale
- [ ] Percentages format correctly

## RTL Layout Testing

### General Layout
- [ ] HTML dir attribute is set to "rtl"
- [ ] Body text aligns to right
- [ ] Sidebar appears on right side
- [ ] Main content shifts to left
- [ ] Scrollbars appear on left side

### Navigation
- [ ] Sidebar menu items align right
- [ ] Dropdown menus open correctly
- [ ] Breadcrumbs display in correct order
- [ ] Back/forward icons are mirrored
- [ ] Chevron icons point correct direction

### Forms
- [ ] Form labels align right
- [ ] Input fields align right
- [ ] Placeholder text aligns right
- [ ] Validation errors appear correctly
- [ ] Submit buttons align correctly
- [ ] Form groups stack correctly

### Tables
- [ ] Table headers align right
- [ ] Table cells align right
- [ ] Action buttons align correctly
- [ ] Pagination controls are mirrored
- [ ] Sort icons are positioned correctly

### Cards and Panels
- [ ] Card headers align right
- [ ] Card content aligns right
- [ ] Icons position correctly
- [ ] Action buttons align correctly

### Charts
- [ ] Chart legends display correctly
- [ ] Chart tooltips display correctly
- [ ] X-axis labels align correctly
- [ ] Y-axis appears on right side
- [ ] Bar charts render correctly

### Buttons and Icons
- [ ] Button text aligns correctly
- [ ] Icons position correctly (before/after text)
- [ ] Button groups order correctly
- [ ] Dropdown arrows point correctly

### Modals and Dialogs
- [ ] Modal headers align right
- [ ] Modal content aligns right
- [ ] Close button positions correctly
- [ ] Modal footer buttons align correctly

### Alerts and Notifications
- [ ] Alert text aligns right
- [ ] Alert icons position correctly
- [ ] Dismiss button positions correctly
- [ ] Toast notifications appear correctly

## Page-Specific Testing

### Dashboard
- [ ] Statistics cards display correctly
- [ ] Charts render correctly in RTL
- [ ] Recent activity list aligns correctly
- [ ] Quick action buttons align correctly

### Student Management
- [ ] Student list table displays correctly
- [ ] Student form fields align correctly
- [ ] Student profile displays correctly
- [ ] Document uploads work correctly

### Attendance
- [ ] Attendance grid displays correctly
- [ ] Date picker works correctly
- [ ] Status indicators align correctly
- [ ] Bulk actions work correctly

### Fees
- [ ] Fee structure table displays correctly
- [ ] Payment form aligns correctly
- [ ] Receipt prints correctly in RTL
- [ ] Currency displays correctly

### Exams
- [ ] Exam schedule displays correctly
- [ ] Mark entry form aligns correctly
- [ ] Report cards print correctly in RTL
- [ ] Grade tables display correctly

### Reports
- [ ] Report filters align correctly
- [ ] Report tables display correctly
- [ ] PDF exports render correctly in RTL
- [ ] Excel exports include correct alignment

### Settings
- [ ] Settings forms align correctly
- [ ] Toggle switches position correctly
- [ ] Language management UI works correctly

## Browser Testing

### Desktop Browsers
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)

### Mobile Browsers
- [ ] Chrome Mobile
- [ ] Safari Mobile
- [ ] Samsung Internet

## Responsive Testing

### Breakpoints
- [ ] Desktop (1200px+) - RTL layout correct
- [ ] Tablet (768px-1199px) - RTL layout correct
- [ ] Mobile (< 768px) - RTL layout correct

### Mobile-Specific
- [ ] Hamburger menu opens from correct side
- [ ] Swipe gestures work correctly
- [ ] Touch targets are accessible

## Accessibility Testing

### Screen Readers
- [ ] Content reads in correct order
- [ ] Language attribute is set correctly
- [ ] ARIA labels are translated

### Keyboard Navigation
- [ ] Tab order is correct for RTL
- [ ] Focus indicators are visible
- [ ] Keyboard shortcuts work correctly

## Print Testing

- [ ] Print layout respects RTL direction
- [ ] Headers and footers align correctly
- [ ] Page numbers position correctly
- [ ] Tables print correctly

## API Testing

- [ ] API returns localized error messages
- [ ] API accepts locale parameter
- [ ] Translation endpoint returns correct data

## Sign-Off

| Role | Name | Date | Signature |
|------|------|------|-----------|
| Developer | | | |
| QA Tester | | | |
| Product Owner | | | |

## Notes

_Add any additional notes or issues discovered during testing:_

---

**Last Updated:** 2026-01-09
**Version:** 1.0
