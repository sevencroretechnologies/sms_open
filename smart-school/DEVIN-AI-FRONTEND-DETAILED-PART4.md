# Smart School Management System - Frontend Detailed Prompts Part 4

This document continues with comprehensive, detailed prompts for building complete frontend UI for remaining modules of Smart School Management System using DevIn AI. Each prompt includes:
- **Purpose**: Why this prompt is needed
- **Functionality**: What exactly it does
- **How it Works**: Implementation details
- **Integration**: How it connects with other features

---

## 📋 Continue from Part 3

This document continues from [`DEVIN-AI-FRONTEND-DETAILED-PART3.md`](DEVIN-AI-FRONTEND-DETAILED-PART3.md) which covered:
- Library Management Views (10 prompts)
- Transport Management Views (10 prompts)
- Hostel Management Views (10 prompts)

**Total in Part 3: 30 prompts**
**Total in Part 1 + Part 2 + Part 3: 140 prompts**

---

## 🎨 Phase 12: Communication Views (15 Prompts)

### Prompt 141: Create Notices List View

**Purpose**: Create notices listing page with search, filter, and CRUD operations.

**Functionality**: Provides comprehensive notices listing with advanced filtering.

**How it Works**:
- Creates `resources/views/admin/notices.blade.php`
- Extends app layout
- Shows page header with title and "Add Notice" button
- Shows search filter component with:
  - Search by title, content
  - Filter by date range
  - Filter by target roles
  - Filter by target classes
  - Filter by status (published/unpublished)
- Shows table with columns:
  - Notice Date
  - Title
  - Target Roles (badges)
  - Target Classes (badges)
  - Expiry Date
  - Status (published/unpublished)
  - Published By
  - Actions (view, edit, delete, publish, unpublish)
- Shows bulk actions:
  - Delete selected
  - Publish selected
  - Unpublish selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no notices

**Integration**:
- Uses NoticeController index method
- Queries Notice model with filters
- Links to add notice, edit, delete, publish, unpublish
- Links to export functionality
- Used by admin role

**Execute**: Create notices list view with search, filters, table, and responsive design.

---

### Prompt 142: Create Notices Create View

**Purpose**: Create notice creation form with targeting options.

**Functionality**: Provides form to create new notice with role and class targeting.

**How it Works**:
- Creates `resources/views/admin/notices-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Title (required)
  - Content (required, rich text editor)
  - Notice Date (required, date picker, defaults to today)
  - Expiry Date (date picker, optional)
  - Target Roles (multi-select: admin, teacher, student, parent, accountant, librarian)
  - Target Classes (multi-select, based on target roles)
  - Attachment (file upload)
  - Publish Now (checkbox)
  - Status (published/unpublished)
- Shows rich text editor (TinyMCE or similar)
- Shows attachment preview
- Shows validation errors
- Shows "Save as Draft" button
- Shows "Publish" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to notice list on success

**Integration**:
- Uses NoticeController store method
- Validates form fields
- Uploads attachment to storage
- Creates notice in notices table
- Sends notifications if published
- Links to notice list
- Used by admin role

**Execute**: Create notices create view with form, rich text editor, targeting, and responsive design.

---

### Prompt 143: Create Notices Edit View

**Purpose**: Create notice edit view with pre-filled data.

**Functionality**: Provides form to edit existing notice.

**How it Works**:
- Creates `resources/views/admin/notices-edit.blade.php`
- Extends app layout
- Shows page header with title, notice title, and "Back to List" button
- Shows notice preview card with title, content, date, expiry date
- Shows form (same as create view)
- All fields pre-filled with existing notice data
- Shows validation errors
- Shows "Update" button with loading state
- Shows "Publish" button if draft
- Shows "Cancel" button
- Shows "Delete" button (opens confirmation modal)
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful update
- Redirects to notice details on success

**Integration**:
- Uses NoticeController update method
- Validates form fields
- Uploads new attachment if provided
- Updates notice in notices table
- Links to notice details
- Used by admin role

**Execute**: Create notices edit view with pre-filled data, validation, and responsive design.

---

### Prompt 144: Create Notices Details View

**Purpose**: Create notice details view with content and targeting.

**Functionality**: Shows comprehensive notice information with targeting details.

**How it Works**:
- Creates `resources/views/admin/notices-show.blade.php`
- Extends app layout
- Shows page header with notice title and actions:
  - Edit button
  - Delete button
  - Publish/Unpublish button
  - Print Notice button
  - Send Notification button
- Shows notice details card:
  - Notice Date
  - Expiry Date
  - Target Roles (badges)
  - Target Classes (badges)
  - Published By
  - Status (published/unpublished)
  - Published At
- Shows notice content with rich text formatting
- Shows attachment with download link
- Shows "Send SMS Notification" button
- Shows "Send Email Notification" button
- Shows "View Recipients" button (shows list of targeted users)
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses NoticeController show method
- Queries Notice model
- Links to edit, delete, publish/unpublish
- Links to send notifications
- Used by admin role

**Execute**: Create notices details view with content, targeting, and responsive design.

---

### Prompt 145: Create Messages Inbox View

**Purpose**: Create messages inbox view with search, filter, and actions.

**Functionality**: Shows received messages with read/unread status.

**How it Works**:
- Creates `resources/views/admin/messages/inbox.blade.php`
- Extends app layout
- Shows page header with title and "Compose Message" button
- Shows search filter component with:
  - Search by subject, sender name
  - Filter by date range
  - Filter by read status (all/read/unread)
  - Filter by sender role
- Shows messages table with columns:
  - Checkbox for selection
  - Read/Unread icon
  - Sender (avatar and name)
  - Subject
  - Message preview
  - Attachment icon (if attached)
  - Received At
  - Actions (view, reply, delete)
- Shows bulk actions:
  - Mark as Read
  - Mark as Unread
  - Delete selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no messages

**Integration**:
- Uses MessageController inbox method
- Queries MessageRecipient model for received messages
- Links to compose, view, reply, delete
- Links to export functionality
- Used by all roles

**Execute**: Create messages inbox view with search, filters, table, and responsive design.

---

### Prompt 146: Create Messages Sent View

**Purpose**: Create messages sent view with search and filter.

**Functionality**: Shows sent messages with status.

**How it Works**:
- Creates `resources/views/admin/messages/sent.blade.php`
- Extends app layout
- Shows page header with title and "Compose Message" button
- Shows search filter component with:
  - Search by subject, recipient name
  - Filter by date range
  - Filter by recipient role
- Shows messages table with columns:
  - Recipients (avatars and names)
  - Subject
  - Message preview
  - Attachment icon (if attached)
  - Sent At
  - Read Status (read/unread count)
  - Actions (view, resend, delete)
- Shows bulk actions:
  - Delete selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no messages

**Integration**:
- Uses MessageController sent method
- Queries Message model for sent messages
- Links to compose, view, resend, delete
- Links to export functionality
- Used by all roles

**Execute**: Create messages sent view with search, filters, table, and responsive design.

---

### Prompt 147: Create Messages Compose View

**Purpose**: Create message composition view with recipient selection.

**Functionality**: Provides interface to compose and send messages.

**How it Works**:
- Creates `resources/views/admin/messages/compose.blade.php`
- Extends app layout
- Shows page header with title and "Back to Inbox" button
- Shows compose form with:
  - Recipients (multi-select with search)
    - Filter by role
    - Filter by class
    - Filter by section
  - Subject (required)
  - Message (required, textarea)
  - Attachment (file upload)
  - Send SMS Notification (checkbox)
  - Send Email Notification (checkbox)
- Shows recipient preview with avatars and names
- Shows attachment preview
- Shows validation errors
- Shows "Send" button with loading state
- Shows "Save Draft" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during sending
- Shows success message on successful send
- Redirects to sent messages on success

**Integration**:
- Uses MessageController store method
- Validates form fields
- Uploads attachment to storage
- Creates message in messages table
- Creates recipients in message_recipients table
- Sends SMS notification if checked
- Sends email notification if checked
- Links to sent messages
- Used by all roles

**Execute**: Create messages compose view with recipient selection, attachment, and responsive design.

---

### Prompt 148: Create Messages View View

**Purpose**: Create message details view with reply functionality.

**Functionality**: Shows message content with reply option.

**How it Works**:
- Creates `resources/views/admin/messages/show.blade.php`
- Extends app layout
- Shows page header with subject and actions:
  - Reply button
  - Forward button
  - Delete button
  - Print Message button
- Shows message details card:
  - Sender (avatar and name)
  - Recipients (avatars and names)
  - Subject
  - Message content
  - Attachment with download link
  - Sent/Received At
  - Read Status
- Shows reply form:
  - Recipient (auto-filled with sender)
  - Subject (auto-filled with Re: prefix)
  - Message (textarea)
  - Attachment (file upload)
  - Send button with loading state
- Shows attachment preview
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses MessageController show method
- Queries Message model
- Links to reply, forward, delete
- Used by all roles

**Execute**: Create messages view view with content, reply form, and responsive design.

---

### Prompt 149: Create SMS Logs View

**Purpose**: Create SMS logs view with search, filter, and status.

**Functionality**: Shows SMS sending history with delivery status.

**How it Works**:
- Creates `resources/views/admin/sms-logs.blade.php`
- Extends app layout
- Shows page header with title and "Send SMS" button
- Shows search filter component with:
  - Search by phone number, message content
  - Filter by date range
  - Filter by status (pending, sent, failed, delivered)
  - Filter by gateway
- Shows table with columns:
  - Recipient
  - Message (preview)
  - Gateway
  - Status (badge)
  - Sent At
  - Response
  - Actions (view, resend)
- Shows statistics cards:
  - Total SMS
  - Sent SMS
  - Failed SMS
  - Delivered SMS
  - SMS Cost
- Shows bulk actions:
  - Resend Failed
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no logs

**Integration**:
- Uses SmsLogController index method
- Queries SmsLog model with filters
- Links to send SMS, view, resend
- Links to export functionality
- Used by admin role

**Execute**: Create SMS logs view with search, filters, table, and responsive design.

---

### Prompt 150: Create SMS Send View

**Purpose**: Create SMS sending view with recipient selection.

**Functionality**: Provides interface to send bulk SMS messages.

**How it Works**:
- Creates `resources/views/admin/sms/send.blade.php`
- Extends app layout
- Shows page header with title and "Back to Logs" button
- Shows send form with:
  - Recipients (multi-select with search)
    - Filter by role
    - Filter by class
    - Filter by section
    - Filter by custom phone numbers (textarea)
  - Message (required, textarea with character count)
  - Gateway (select)
  - Schedule (checkbox)
  - Schedule Date/Time (if scheduled)
- Shows recipient preview with count
- Shows character count and SMS count estimate
- Shows SMS cost estimate
- Shows validation errors
- Shows "Send Now" button with loading state
- Shows "Schedule" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during sending
- Shows success message with sent count
- Redirects to SMS logs on success

**Integration**:
- Uses SmsLogController send method
- Validates form fields
- Creates SMS logs in sms_logs table
- Sends SMS via SMS gateway
- Links to SMS logs
- Used by admin role

**Execute**: Create SMS send view with recipient selection, message, and responsive design.

---

### Prompt 151: Create Email Logs View

**Purpose**: Create email logs view with search, filter, and status.

**Functionality**: Shows email sending history with delivery status.

**How it Works**:
- Creates `resources/views/admin/email-logs.blade.php`
- Extends app layout
- Shows page header with title and "Send Email" button
- Shows search filter component with:
  - Search by email address, subject
  - Filter by date range
  - Filter by status (pending, sent, failed)
- Shows table with columns:
  - Recipient
  - Subject
  - Body (preview)
  - Status (badge)
  - Sent At
  - Error Message
  - Actions (view, resend)
- Shows statistics cards:
  - Total Emails
  - Sent Emails
  - Failed Emails
- Shows bulk actions:
  - Resend Failed
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no logs

**Integration**:
- Uses EmailLogController index method
- Queries EmailLog model with filters
- Links to send email, view, resend
- Links to export functionality
- Used by admin role

**Execute**: Create email logs view with search, filters, table, and responsive design.

---

### Prompt 152: Create Email Send View

**Purpose**: Create email sending view with recipient selection and templates.

**Functionality**: Provides interface to send bulk emails with templates.

**How it Works**:
- Creates `resources/views/admin/email/send.blade.php`
- Extends app layout
- Shows page header with title and "Back to Logs" button
- Shows send form with:
  - Recipients (multi-select with search)
    - Filter by role
    - Filter by class
    - Filter by section
    - Filter by custom email addresses (textarea)
  - Subject (required)
  - Template (select, optional)
  - Body (required, rich text editor)
  - Attachment (file upload, multiple)
  - Schedule (checkbox)
  - Schedule Date/Time (if scheduled)
- Shows recipient preview with count
- Shows template preview
- Shows attachment previews
- Shows validation errors
- Shows "Send Now" button with loading state
- Shows "Schedule" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during sending
- Shows success message with sent count
- Redirects to email logs on success

**Integration**:
- Uses EmailLogController send method
- Validates form fields
- Uploads attachments to storage
- Creates email logs in email_logs table
- Sends email via email service
- Links to email logs
- Used by admin role

**Execute**: Create email send view with recipient selection, templates, and responsive design.

---

### Prompt 153: Create Downloads List View

**Purpose**: Create downloads listing page with search, filter, and CRUD operations.

**Functionality**: Provides downloadable content listing with targeting.

**How it Works**:
- Creates `resources/views/admin/downloads.blade.php`
- Extends app layout
- Shows page header with title and "Add Download" button
- Shows search filter component with:
  - Search by title, description
  - Filter by category
  - Filter by target roles
  - Filter by target classes
- Shows table with columns:
  - Title
  - Category
  - File Type
  - File Size
  - Target Roles (badges)
  - Target Classes (badges)
  - Uploaded At
  - Uploaded By
  - Status (active/inactive)
  - Actions (view, edit, delete, download)
- Shows bulk actions:
  - Delete selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no downloads

**Integration**:
- Uses DownloadController index method
- Queries Download model with filters
- Links to add download, edit, delete
- Links to export functionality
- Used by admin role

**Execute**: Create downloads list view with search, filters, table, and responsive design.

---

### Prompt 154: Create Downloads Create View

**Purpose**: Create download creation form with targeting options.

**Functionality**: Provides form to upload downloadable content with targeting.

**How it Works**:
- Creates `resources/views/admin/downloads-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Title (required)
  - Description (textarea)
  - Category (select)
  - File (required, file upload)
  - Target Roles (multi-select: admin, teacher, student, parent, accountant, librarian)
  - Target Classes (multi-select, based on target roles)
  - Status (active/inactive)
- Shows file preview with size and type
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Another" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to download list on success

**Integration**:
- Uses DownloadController store method
- Validates form fields
- Uploads file to storage
- Creates download in downloads table
- Links to download list
- Used by admin role

**Execute**: Create downloads create view with form, file upload, targeting, and responsive design.

---

### Prompt 155: Create Homework List View

**Purpose**: Create homework listing page with search, filter, and CRUD operations.

**Functionality**: Provides comprehensive homework listing with advanced filtering.

**How it Works**:
- Creates `resources/views/teacher/homework.blade.php`
- Extends app layout
- Shows page header with title and "Add Homework" button
- Shows search filter component with:
  - Search by title, description
  - Filter by academic session
  - Filter by class
  - Filter by section
  - Filter by subject
  - Filter by submission date
- Shows table with columns:
  - Title
  - Class
  - Section
  - Subject
  - Submission Date
  - Created At
  - Status (active/inactive)
  - Actions (view, edit, delete, submissions)
- Shows bulk actions:
  - Delete selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no homework

**Integration**:
- Uses HomeworkController index method
- Queries Homework model with filters
- Links to add homework, edit, delete, view submissions
- Links to export functionality
- Used by teacher role

**Execute**: Create homework list view with search, filters, table, and responsive design.

---

## 🎨 Phase 13: Accounting Views (10 Prompts)

### Prompt 156: Create Income Categories List View

**Purpose**: Create income categories listing page with CRUD operations.

**Functionality**: Provides income categories list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/income-categories.blade.php`
- Extends app layout
- Shows page header with title and "Add Category" button
- Shows table with columns:
  - Category Name
  - Code
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no categories

**Integration**:
- Uses IncomeCategoryController index method
- Queries IncomeCategory model
- Links to create, edit, delete
- Used by accountant role

**Execute**: Create income categories list view with table, actions, and responsive design.

---

### Prompt 157: Create Income Categories Create View

**Purpose**: Create income category creation form.

**Functionality**: Provides form to create new income category.

**How it Works**:
- Creates `resources/views/admin/income-categories-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Category Name (required)
  - Code (required, unique)
  - Description
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to category list on success

**Integration**:
- Uses IncomeCategoryController store method
- Validates form fields
- Creates category in income_categories table
- Links to category list
- Used by accountant role

**Execute**: Create income categories create view with form, validation, and responsive design.

---

### Prompt 158: Create Expense Categories List View

**Purpose**: Create expense categories listing page with CRUD operations.

**Functionality**: Provides expense categories list with create, edit, delete functionality.

**How it Works**:
- Creates `resources/views/admin/expense-categories.blade.php`
- Extends app layout
- Shows page header with title and "Add Category" button
- Shows table with columns:
  - Category Name
  - Code
  - Description
  - Status (active/inactive)
  - Actions (edit, delete)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no categories

**Integration**:
- Uses ExpenseCategoryController index method
- Queries ExpenseCategory model
- Links to create, edit, delete
- Used by accountant role

**Execute**: Create expense categories list view with table, actions, and responsive design.

---

### Prompt 159: Create Expense Categories Create View

**Purpose**: Create expense category creation form.

**Functionality**: Provides form to create new expense category.

**How it Works**:
- Creates `resources/views/admin/expense-categories-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Category Name (required)
  - Code (required, unique)
  - Description
  - Status (active/inactive)
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to category list on success

**Integration**:
- Uses ExpenseCategoryController store method
- Validates form fields
- Creates category in expense_categories table
- Links to category list
- Used by accountant role

**Execute**: Create expense categories create view with form, validation, and responsive design.

---

### Prompt 160: Create Income List View

**Purpose**: Create income listing page with search, filter, and CRUD operations.

**Functionality**: Provides comprehensive income listing with advanced filtering.

**How it Works**:
- Creates `resources/views/accountant/income.blade.php`
- Extends app layout
- Shows page header with title and "Add Income" button
- Shows search filter component with:
  - Search by title, description
  - Filter by category
  - Filter by date range
  - Filter by payment method
- Shows table with columns:
  - Income Date
  - Title
  - Category
  - Amount
  - Payment Method
  - Reference Number
  - Attachment
  - Created By
  - Actions (view, edit, delete)
- Shows statistics cards:
  - Total Income
  - This Month Income
  - This Year Income
- Shows bulk actions:
  - Delete selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no income

**Integration**:
- Uses IncomeController index method
- Queries Income model with filters
- Links to add income, edit, delete
- Links to export functionality
- Used by accountant role

**Execute**: Create income list view with search, filters, table, and responsive design.

---

### Prompt 161: Create Income Create View

**Purpose**: Create income entry form with attachment.

**Functionality**: Provides form to add new income entry.

**How it Works**:
- Creates `resources/views/accountant/income-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Category (required, select)
  - Title (required)
  - Description (textarea)
  - Amount (required, number)
  - Income Date (required, date picker, defaults to today)
  - Payment Method (select)
  - Reference Number
  - Attachment (file upload)
- Shows attachment preview
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Another" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to income list on success

**Integration**:
- Uses IncomeController store method
- Validates form fields
- Uploads attachment to storage
- Creates income in income table
- Links to income list
- Used by accountant role

**Execute**: Create income create view with form, attachment, and responsive design.

---

### Prompt 162: Create Expenses List View

**Purpose**: Create expenses listing page with search, filter, and CRUD operations.

**Functionality**: Provides comprehensive expenses listing with advanced filtering.

**How it Works**:
- Creates `resources/views/accountant/expenses.blade.php`
- Extends app layout
- Shows page header with title and "Add Expense" button
- Shows search filter component with:
  - Search by title, description
  - Filter by category
  - Filter by date range
  - Filter by payment method
- Shows table with columns:
  - Expense Date
  - Title
  - Category
  - Amount
  - Payment Method
  - Reference Number
  - Attachment
  - Created By
  - Actions (view, edit, delete)
- Shows statistics cards:
  - Total Expenses
  - This Month Expenses
  - This Year Expenses
- Shows bulk actions:
  - Delete selected
  - Export selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no expenses

**Integration**:
- Uses ExpenseController index method
- Queries Expense model with filters
- Links to add expense, edit, delete
- Links to export functionality
- Used by accountant role

**Execute**: Create expenses list view with search, filters, table, and responsive design.

---

### Prompt 163: Create Expenses Create View

**Purpose**: Create expense entry form with attachment.

**Functionality**: Provides form to add new expense entry.

**How it Works**:
- Creates `resources/views/accountant/expenses-create.blade.php`
- Extends app layout
- Shows page header with title and "Back to List" button
- Shows form with:
  - Category (required, select)
  - Title (required)
  - Description (textarea)
  - Amount (required, number)
  - Expense Date (required, date picker, defaults to today)
  - Payment Method (select)
  - Reference Number
  - Attachment (file upload)
- Shows attachment preview
- Shows validation errors
- Shows "Save" button with loading state
- Shows "Save & Add Another" button
- Shows "Cancel" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during submission
- Shows success message on successful creation
- Redirects to expense list on success

**Integration**:
- Uses ExpenseController store method
- Validates form fields
- Uploads attachment to storage
- Creates expense in expenses table
- Links to expense list
- Used by accountant role

**Execute**: Create expenses create view with form, attachment, and responsive design.

---

### Prompt 164: Create Accounting Report View

**Purpose**: Create accounting report view with statistics and charts.

**Functionality**: Shows comprehensive accounting reports with analytics.

**How it Works**:
- Creates `resources/views/accountant/report.blade.php`
- Extends app layout
- Shows page header with title and "Back to Dashboard" button
- Shows filter form:
  - Date Range
  - Report Type (daily, monthly, yearly)
- Shows statistics cards:
  - Total Income
  - Total Expenses
  - Net Balance
  - Fee Collection
  - Other Income
  - Total Expenses
  - Net Profit/Loss
- Shows charts:
  - Income vs Expenses (bar chart)
  - Income by category (pie chart)
  - Expenses by category (pie chart)
  - Cash flow trend (line chart)
- Shows income table:
  - Category
  - Amount
  - Percentage
- Shows expenses table:
  - Category
  - Amount
  - Percentage
- Shows "Export Report" button (PDF/Excel)
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses AccountingController report method
- Queries Income and Expense models
- Calculates statistics
- Shows charts
- Links to export/print
- Used by accountant role

**Execute**: Create accounting report view with statistics, charts, table, and responsive design.

---

### Prompt 165: Create Balance Sheet View

**Purpose**: Create balance sheet view with income, expenses, and net balance.

**Functionality**: Shows comprehensive balance sheet with financial summary.

**How it Works**:
- Creates `resources/views/accountant/balance-sheet.blade.php`
- Extends app layout
- Shows page header with title and "Back to Dashboard" button
- Shows filter form:
  - Academic Session (select)
  - Date Range
- Shows income section:
  - Total Income card
  - Income by category table
  - Income trend chart
- Shows expenses section:
  - Total Expenses card
  - Expenses by category table
  - Expenses trend chart
- Shows balance section:
  - Net Balance card
  - Profit/Loss card
  - Balance trend chart
- Shows "Export Balance Sheet" button (PDF/Excel)
- Shows "Print Balance Sheet" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses AccountingController balanceSheet method
- Queries Income and Expense models
- Calculates balance
- Shows charts
- Links to export/print
- Used by accountant role

**Execute**: Create balance sheet view with income, expenses, balance, and responsive design.

---

## 🎨 Phase 14: Reports Views (10 Prompts)

### Prompt 166: Create Reports Dashboard View

**Purpose**: Create reports dashboard with quick access to all reports.

**Functionality**: Provides centralized access to all system reports.

**How it Works**:
- Creates `resources/views/admin/reports.blade.php`
- Extends app layout
- Shows page header with title
- Shows report categories in grid:
  - **Student Reports**
    - Student List
    - Student Details
    - Student Attendance
    - Student Results
    - Student Fees
    - Student Transport
    - Student Hostel
    - Student Library
  - **Academic Reports**
    - Class List
    - Section List
    - Subject List
    - Class Timetable
    - Class Statistics
  - **Attendance Reports**
    - Daily Attendance
    - Monthly Attendance
    - Yearly Attendance
    - Attendance Summary
  - **Examination Reports**
    - Exam Schedule
    - Exam Results
    - Report Cards
    - Grade Distribution
    - Exam Statistics
  - **Fees Reports**
    - Fee Collection
    - Fee Summary
    - Pending Fees
    - Fee Collection Trend
    - Fee Defaulters
  - **Library Reports**
    - Book List
    - Issue History
    - Return History
    - Library Statistics
  - **Transport Reports**
    - Route List
    - Vehicle List
    - Transport Students
    - Transport Statistics
  - **Hostel Reports**
    - Hostel List
    - Room List
    - Hostel Students
    - Hostel Statistics
  - **Accounting Reports**
    - Income Report
    - Expense Report
    - Balance Sheet
    - Financial Summary
- Each report card shows:
  - Report name
  - Description
  - Icon
  - Generate Report button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Links to all report generation views
- Provides centralized report access
- Used by admin role

**Execute**: Create reports dashboard view with report categories and quick access.

---

### Prompt 167: Create Student Report View

**Purpose**: Create student report generation view with filters and export.

**Functionality**: Provides interface to generate student reports.

**How it Works**:
- Creates `resources/views/admin/reports/student.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Report Type (list, details, attendance, results, fees, transport, hostel, library)
  - Include Photo (checkbox)
  - Include Documents (checkbox)
  - Include Siblings (checkbox)
- Shows report preview:
  - Student table with selected fields
  - Summary statistics
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
  - Include Charts (checkbox)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController studentReport method
- Queries Student model with filters
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create student report view with filters, preview, and export options.

---

### Prompt 168: Create Attendance Report View

**Purpose**: Create attendance report generation view with filters and export.

**Functionality**: Provides interface to generate attendance reports.

**How it Works**:
- Creates `resources/views/admin/reports/attendance.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Date Range
  - Report Type (daily, monthly, yearly, summary)
  - Include Charts (checkbox)
- Shows report preview:
  - Attendance table
  - Summary statistics
  - Attendance percentage
- Shows charts:
  - Attendance trend (line chart)
  - Attendance by type (pie chart)
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController attendanceReport method
- Queries Attendance model with filters
- Calculates statistics
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create attendance report view with filters, charts, and export options.

---

### Prompt 169: Create Exam Report View

**Purpose**: Create exam report generation view with filters and export.

**Functionality**: Provides interface to generate exam reports.

**How it Works**:
- Creates `resources/views/admin/reports/exam.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Academic Session (select)
  - Exam Type (select)
  - Exam (select)
  - Class (select)
  - Section (select)
  - Subject (select, optional)
  - Report Type (results, report cards, grade distribution, statistics)
  - Include Charts (checkbox)
- Shows report preview:
  - Results table
  - Summary statistics
  - Grade distribution
- Shows charts:
  - Grade distribution (pie chart)
  - Subject-wise performance (bar chart)
  - Pass/Fail percentage (pie chart)
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController examReport method
- Queries ExamMark model with filters
- Calculates statistics
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create exam report view with filters, charts, and export options.

---

### Prompt 170: Create Fees Report View

**Purpose**: Create fees report generation view with filters and export.

**Functionality**: Provides interface to generate fees reports.

**How it Works**:
- Creates `resources/views/admin/reports/fees.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Academic Session (select)
  - Class (select)
  - Section (select)
  - Fee Type (select)
  - Date Range
  - Report Type (collection, summary, pending, defaulters, trend)
  - Include Charts (checkbox)
- Shows report preview:
  - Fees table
  - Summary statistics
  - Collection percentage
- Shows charts:
  - Fee collection trend (line chart)
  - Collection by class (bar chart)
  - Collection by fee type (pie chart)
  - Pending fees by class (bar chart)
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController feesReport method
- Queries FeesTransaction and FeesAllotment models with filters
- Calculates statistics
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create fees report view with filters, charts, and export options.

---

### Prompt 171: Create Library Report View

**Purpose**: Create library report generation view with filters and export.

**Functionality**: Provides interface to generate library reports.

**How it Works**:
- Creates `resources/views/admin/reports/library.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Date Range
  - Report Type (books, issues, returns, statistics, popular books)
  - Include Charts (checkbox)
- Shows report preview:
  - Books table
  - Issue/Return table
  - Summary statistics
- Shows charts:
  - Books by category (pie chart)
  - Issue trend (line chart)
  - Popular books (bar chart)
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController libraryReport method
- Queries LibraryBook and LibraryIssue models with filters
- Calculates statistics
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create library report view with filters, charts, and export options.

---

### Prompt 172: Create Transport Report View

**Purpose**: Create transport report generation view with filters and export.

**Functionality**: Provides interface to generate transport reports.

**How it Works**:
- Creates `resources/views/admin/reports/transport.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Academic Session (select)
  - Route (select)
  - Vehicle (select)
  - Date Range
  - Report Type (students, routes, vehicles, statistics)
  - Include Charts (checkbox)
- Shows report preview:
  - Students table
  - Routes table
  - Vehicles table
  - Summary statistics
- Shows charts:
  - Students by route (pie chart)
  - Vehicle capacity utilization (bar chart)
  - Fee collection trend (line chart)
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController transportReport method
- Queries TransportRoute, TransportVehicle, TransportStudent models with filters
- Calculates statistics
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create transport report view with filters, charts, and export options.

---

### Prompt 173: Create Hostel Report View

**Purpose**: Create hostel report generation view with filters and export.

**Functionality**: Provides interface to generate hostel reports.

**How it Works**:
- Creates `resources/views/admin/reports/hostel.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Academic Session (select)
  - Hostel (select)
  - Room Type (select)
  - Date Range
  - Report Type (students, rooms, occupancy, statistics)
  - Include Charts (checkbox)
- Shows report preview:
  - Students table
  - Rooms table
  - Occupancy statistics
- Shows charts:
  - Students by hostel (pie chart)
  - Room occupancy by floor (bar chart)
  - Occupancy trend (line chart)
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController hostelReport method
- Queries Hostel, HostelRoom, HostelAssignment models with filters
- Calculates statistics
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create hostel report view with filters, charts, and export options.

---

### Prompt 174: Create Accounting Report View

**Purpose**: Create accounting report generation view with filters and export.

**Functionality**: Provides interface to generate accounting reports.

**How it Works**:
- Creates `resources/views/admin/reports/accounting.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows filter form:
  - Date Range
  - Report Type (income, expenses, balance sheet, financial summary)
  - Include Charts (checkbox)
- Shows report preview:
  - Income table
  - Expenses table
  - Balance sheet
  - Summary statistics
- Shows charts:
  - Income vs Expenses (bar chart)
  - Income by category (pie chart)
  - Expenses by category (pie chart)
  - Cash flow trend (line chart)
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Chart.js for visualizations
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController accountingReport method
- Queries Income and Expense models with filters
- Calculates statistics
- Generates report
- Exports to Excel/PDF/CSV
- Used by admin role

**Execute**: Create accounting report view with filters, charts, and export options.

---

### Prompt 175: Create Custom Report View

**Purpose**: Create custom report builder view with field selection.

**Functionality**: Provides interface to build custom reports.

**How it Works**:
- Creates `resources/views/admin/reports/custom.blade.php`
- Extends app layout
- Shows page header with title and "Back to Reports" button
- Shows report builder form:
  - Report Name (required)
  - Report Type (select: student, attendance, exam, fees, library, transport, hostel, accounting)
  - Data Source (select based on report type)
  - Filters (add/remove filters)
    - Field
    - Operator
    - Value
  - Fields Selection (checkboxes for fields to include)
  - Field Ordering (drag-and-drop)
  - Group By (select)
  - Sort By (select)
  - Sort Order (asc/desc)
- Shows preview of selected fields
- Shows "Save Report Template" button
- Shows "Generate Report" button with loading state
- Shows export options:
  - Export Format (Excel, PDF, CSV)
- Shows "Export Report" button
- Shows "Print Report" button
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state

**Integration**:
- Uses ReportController customReport method
- Queries models based on report type
- Applies filters and ordering
- Generates report
- Exports to Excel/PDF/CSV
- Saves report template
- Used by admin role

**Execute**: Create custom report view with field selection, filters, and export options.

---

## 🎨 Phase 15: Settings Views (10 Prompts)

### Prompt 176: Create General Settings View

**Purpose**: Create general settings view for school information.

**Functionality**: Provides interface to manage school settings.

**How it Works**:
- Creates `resources/views/admin/settings/general.blade.php`
- Extends app layout
- Shows page header with title
- Shows settings form with sections:
  - **School Information**
    - School Name (required)
    - School Logo (file upload with preview)
    - School Tagline
    - School Address (textarea)
    - City
    - State
    - Country
    - Postal Code
    - Phone
    - Email
    - Website
    - School Code
  - **Academic Settings**
    - Current Academic Session (select)
    - Academic Year Start Date
    - Academic Year End Date
    - Number of Working Days
  - **Time Settings**
    - School Start Time (time picker)
    - School End Time (time picker)
    - Time Zone (select)
  - **Contact Settings**
    - Principal Name
    - Principal Email
    - Principal Phone
    - Admin Name
    - Admin Email
    - Admin Phone
- Shows logo preview
- Shows validation errors
- Shows "Save Settings" button with loading state
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during saving
- Shows success message on successful save

**Integration**:
- Uses SettingsController updateGeneral method
- Validates form fields
- Uploads logo to storage
- Updates settings in settings table
- Used by admin role

**Execute**: Create general settings view with school information, academic settings, and responsive design.

---

### Prompt 177: Create System Settings View

**Purpose**: Create system settings view for system configuration.

**Functionality**: Provides interface to manage system settings.

**How it Works**:
- Creates `resources/views/admin/settings/system.blade.php`
- Extends app layout
- Shows page header with title
- Shows settings form with sections:
  - **Application Settings**
    - Application Name
    - Application URL
    - Debug Mode (checkbox)
    - Maintenance Mode (checkbox)
  - **Security Settings**
    - Session Timeout (number)
    - Password Min Length (number)
    - Require Strong Password (checkbox)
    - Two-Factor Authentication (checkbox)
  - **Backup Settings**
    - Auto Backup (checkbox)
    - Backup Frequency (daily, weekly, monthly)
    - Backup Time (time picker)
    - Keep Backups For (number)
  - **Email Settings**
    - Mail Driver (select)
    - Mail Host
    - Mail Port
    - Mail Username
    - Mail Password
    - Mail Encryption
    - From Email
    - From Name
    - Test Email button
  - **SMS Settings**
    - SMS Gateway (select)
    - API Key
    - Sender ID
    - Test SMS button
- Shows validation errors
- Shows "Save Settings" button with loading state
- Shows "Test Email" button
- Shows "Test SMS" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during saving
- Shows success message on successful save

**Integration**:
- Uses SettingsController updateSystem method
- Validates form fields
- Updates settings in settings table and .env file
- Tests email/SMS settings
- Used by admin role

**Execute**: Create system settings view with application, security, backup, email, SMS settings.

---

### Prompt 178: Create Language Settings View

**Purpose**: Create language settings view for multi-language configuration.

**Functionality**: Provides interface to manage languages and translations.

**How it Works**:
- Creates `resources/views/admin/settings/languages.blade.php`
- Extends app layout
- Shows page header with title and "Add Language" button
- Shows languages table with columns:
  - Language Name
  - Language Code
  - Native Name
  - Direction (ltr/rtl)
  - Is Default (badge)
  - Status (active/inactive)
  - Actions (edit, delete, set default, manage translations)
- Shows "Add Language" button
- Shows modal for adding language:
  - Language Name (required)
  - Language Code (required, unique)
  - Native Name
  - Direction (ltr/rtl)
  - Is Default (checkbox)
  - Status (active/inactive)
  - Add button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no languages

**Integration**:
- Uses LanguageController index method
- Queries Language model
- Links to add, edit, delete, set default, manage translations
- Used by admin role

**Execute**: Create language settings view with languages table and add modal.

---

### Prompt 179: Create Translations Management View

**Purpose**: Create translations management view for editing translations.

**Functionality**: Provides interface to manage translations for all languages.

**How it Works**:
- Creates `resources/views/admin/settings/translations.blade.php`
- Extends app layout
- Shows page header with language name and "Back to Languages" button
- Shows filter form:
  - Search by translation key
  - Filter by module
- Shows translations table with columns:
  - Translation Key
  - Default Language Value
  - Target Language Value (editable)
  - Module
  - Actions (save)
- Shows "Save All" button with loading state
- Shows "Import Translations" button (JSON file)
- Shows "Export Translations" button (JSON file)
- Shows "Auto-Translate" button (uses translation API)
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no translations

**Integration**:
- Uses TranslationController index method
- Queries Translation model
- Updates translations in translations table
- Imports/exports translations
- Auto-translates using API
- Links to language settings
- Used by admin role

**Execute**: Create translations management view with table, edit, and import/export.

---

### Prompt 180: Create Theme Settings View

**Purpose**: Create theme settings view for UI customization.

**Functionality**: Provides interface to customize application theme.

**How it Works**:
- Creates `resources/views/admin/settings/theme.blade.php`
- Extends app layout
- Shows page header with title
- Shows theme settings form with sections:
  - **Color Settings**
    - Primary Color (color picker)
    - Secondary Color (color picker)
    - Success Color (color picker)
    - Danger Color (color picker)
    - Warning Color (color picker)
    - Info Color (color picker)
  - **Typography Settings**
    - Font Family (select)
    - Font Size (number)
    - Heading Font Size (number)
  - **Layout Settings**
    - Sidebar Position (left/right)
    - Sidebar Style (dark/light)
    - Header Style (dark/light)
    - Footer Style (dark/light)
  - **Theme Mode**
    - Default Theme (light/dark/auto)
    - Allow Theme Toggle (checkbox)
  - **Logo Settings**
    - Light Logo (file upload with preview)
    - Dark Logo (file upload with preview)
    - Favicon (file upload with preview)
- Shows color previews
- Shows logo previews
- Shows validation errors
- Shows "Save Settings" button with loading state
- Shows "Reset to Default" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during saving
- Shows success message on successful save

**Integration**:
- Uses SettingsController updateTheme method
- Validates form fields
- Uploads logos/favicon to storage
- Updates settings in settings table
- Applies theme to application
- Used by admin role

**Execute**: Create theme settings view with colors, typography, layout, and responsive design.

---

### Prompt 181: Create Notification Settings View

**Purpose**: Create notification settings view for configuring notifications.

**Functionality**: Provides interface to manage notification preferences.

**How it Works**:
- Creates `resources/views/admin/settings/notifications.blade.php`
- Extends app layout
- Shows page header with title
- Shows notification settings form with sections:
  - **Attendance Notifications**
    - Send SMS to Parents (checkbox)
    - Send Email to Parents (checkbox)
    - SMS Template (textarea)
    - Email Template (textarea)
  - **Exam Notifications**
    - Send SMS to Parents (checkbox)
    - Send Email to Parents (checkbox)
    - SMS Template (textarea)
    - Email Template (textarea)
  - **Fee Notifications**
    - Send SMS to Parents (checkbox)
    - Send Email to Parents (checkbox)
    - Due Reminder (days before due, number)
    - SMS Template (textarea)
    - Email Template (textarea)
  - **Notice Notifications**
    - Send SMS (checkbox)
    - Send Email (checkbox)
    - SMS Template (textarea)
    - Email Template (textarea)
  - **Message Notifications**
    - Email Notification (checkbox)
    - SMS Notification (checkbox)
- Shows template previews
- Shows validation errors
- Shows "Save Settings" button with loading state
- Shows "Test SMS" button
- Shows "Test Email" button
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during saving
- Shows success message on successful save

**Integration**:
- Uses SettingsController updateNotifications method
- Validates form fields
- Updates settings in settings table
- Tests SMS/email templates
- Used by admin role

**Execute**: Create notification settings view with templates and test functionality.

---

### Prompt 182: Create Backup Settings View

**Purpose**: Create backup settings view for managing backups.

**Functionality**: Provides interface to manage system backups.

**How it Works**:
- Creates `resources/views/admin/settings/backups.blade.php`
- Extends app layout
- Shows page header with title and "Create Backup" button
- Shows backup settings form:
  - Backup Type (full, database, files)
  - Backup Name
  - Description
- Shows backups table with columns:
  - Backup Name
  - Type (full/database/files)
  - File Size
  - Created At
  - Status (pending/completed/failed)
  - Actions (download, restore, delete)
- Shows "Create Backup" button with loading state
- Shows "Auto Backup" section:
  - Enable Auto Backup (checkbox)
  - Backup Frequency (daily, weekly, monthly)
  - Backup Time (time picker)
  - Keep Backups For (number)
- Shows storage usage card
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Shows empty state if no backups

**Integration**:
- Uses BackupController index method
- Queries Backup model
- Creates backup
- Downloads backup file
- Restores from backup
- Deletes backup
- Used by admin role

**Execute**: Create backup settings view with backups table and create functionality.

---

### Prompt 183: Create Role Permissions View

**Purpose**: Create role permissions management view for assigning permissions to roles.

**Functionality**: Provides interface to manage role-based permissions.

**How it Works**:
- Creates `resources/views/admin/settings/permissions.blade.php`
- Extends app layout
- Shows page header with title
- Shows role selector (tabs)
- For each role shows permissions table with:
  - Module (grouped)
  - Permissions (checkboxes: view, create, edit, delete)
  - Select All checkbox for each module
- Shows "Save Permissions" button with loading state
- Shows "Reset to Default" button
- Shows validation errors
- Uses Bootstrap 5 grid layout
- Responsive design
- Supports RTL languages
- Shows loading state
- Tab-based navigation for roles

**Integration**:
- Uses PermissionController update method
- Updates role_has_permissions table
- Uses Spatie Permission package
- Links to settings
- Used by admin role

**Execute**: Create role permissions view with role tabs and permission checkboxes.

---

### Prompt 184: Create User Management View

**Purpose**: Create user management view for managing system users.

**Functionality**: Provides interface to manage all system users.

**How it Works**:
- Creates `resources/views/admin/settings/users.blade.php`
- Extends app layout
- Shows page header with title and "Add User" button
- Shows search filter component with:
  - Search by name, email, phone
  - Filter by role
  - Filter by status (active/inactive)
- Shows table with columns:
  - Photo (avatar)
  - Name
  - Email
  - Phone
  - Role (badge)
  - Last Login
  - Status (active/inactive)
  - Actions (view, edit, delete, reset password)
- Shows bulk actions:
  - Activate selected
  - Deactivate selected
  - Delete selected
- Shows pagination component
- Shows records per page selector
- Shows "Export All" button
- Uses Bootstrap 5 grid layout
- Responsive design (table scrolls on mobile)
- Supports RTL languages
- Shows loading state
- Shows empty state if no users

**Integration**:
- Uses UserController index method
- Queries User model with filters
- Links to add user, edit, delete, reset password
- Links to export functionality
- Used by admin role

**Execute**: Create user management view with search, filters, table, and responsive design.

---

### Prompt 185: Create Profile Settings View

**Purpose**: Create profile settings view for managing user profile.

**Functionality**: Provides interface for users to manage their profile.

**How it Works**:
- Creates `resources/views/admin/settings/profile.blade.php`
- Extends app layout
- Shows page header with title
- Shows profile form with sections:
  - **Personal Information**
    - First Name (required)
    - Last Name (required)
    - Email (required, readonly)
    - Phone
    - Date of Birth (date picker)
    - Gender (select)
    - Address (textarea)
    - City
    - State
    - Country
    - Postal Code
  - **Profile Photo**
    - Current Photo (avatar)
    - Upload New Photo (file upload with preview)
  - **Change Password**
    - Current Password
    - New Password
    - Confirm Password
- Shows photo preview
- Shows validation errors
- Shows "Update Profile" button with loading state
- Shows "Change Password" button with loading state
- Uses Bootstrap 5 form styling
- Responsive design
- Supports RTL languages
- Shows loading spinner during saving
- Shows success message on successful update

**Integration**:
- Uses ProfileController update method
- Validates form fields
- Uploads photo to storage
- Updates user in users table
- Updates password if provided
- Used by all roles

**Execute**: Create profile settings view with personal info, photo, and password change.

---

## 📊 Summary

**Total Frontend Prompts in Part 4: 45**

**Phases Covered in Part 4:**
12. **Communication Views** (15 prompts)
13. **Accounting Views** (10 prompts)
14. **Reports Views** (10 prompts)
15. **Settings Views** (10 prompts)

**Total Frontend Prompts (Part 1 + Part 2 + Part 3 + Part 4): 185**

**Features Implemented in Part 4:**
- Notices with targeting and publishing
- Messages with inbox, sent, compose, reply
- SMS logs and sending with templates
- Email logs and sending with templates
- Downloads management
- Homework management
- Income and expense tracking
- Accounting reports with charts
- Balance sheet with financial summary
- Comprehensive reports for all modules
- Custom report builder
- General settings (school info, academic, time)
- System settings (security, backup, email, SMS)
- Language settings with translations
- Theme settings with colors and layout
- Notification settings with templates
- Backup management with restore
- Role permissions management
- User management
- Profile settings

---

## 🎉 Complete Frontend Implementation

**Total Frontend Prompts: 185**

**All Phases Completed:**
1. Layout & Components (20 prompts)
2. Authentication Views (5 prompts)
3. Dashboard Views (10 prompts)
4. Student Management Views (15 prompts)
5. Academic Management Views (20 prompts)
6. Attendance Management Views (10 prompts)
7. Examination Management Views (15 prompts)
8. Fees Management Views (15 prompts)
9. Library Management Views (10 prompts)
10. Transport Management Views (10 prompts)
11. Hostel Management Views (10 prompts)
12. Communication Views (15 prompts)
13. Accounting Views (10 prompts)
14. Reports Views (10 prompts)
15. Settings Views (10 prompts)

**Complete System Coverage:**
- All 16 modules fully implemented
- All 6 user roles supported
- Responsive design for all views
- RTL language support
- Interactive components with Alpine.js
- Charts with Chart.js
- Multi-step forms
- Drag-and-drop functionality
- Real-time search and filtering
- Pagination
- File upload with preview
- Modal dialogs
- Loading states
- Empty states
- Validation feedback
- Accessibility features

---

## 🚀 Ready for Implementation

The Smart School Management System frontend is now fully planned with 185 detailed prompts covering every aspect of the UI implementation.

**Happy Building with DevIn AI!** 🚀
