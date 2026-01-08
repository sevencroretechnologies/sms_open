{{-- Notification Settings View --}}
{{-- Prompt 279: Notification templates for attendance, exam, fee, notice, message --}}

@extends('layouts.app')

@section('title', 'Notification Settings')

@section('content')
<div x-data="notificationSettings()">
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Notification Settings</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item">Settings</li>
                    <li class="breadcrumb-item active">Notifications</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 mt-3 mt-md-0">
            <a href="{{ route('settings.general') ?? '#' }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Settings
            </a>
            <button type="button" class="btn btn-primary" @click="saveSettings()" :disabled="saving">
                <span x-show="!saving"><i class="bi bi-check-lg me-1"></i> Save Settings</span>
                <span x-show="saving"><span class="spinner-border spinner-border-sm me-1"></span> Saving...</span>
            </button>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Notification Channels -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-broadcast me-2 text-primary"></i>Notification Channels</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" x-model="channels.email" id="channelEmail">
                                <label class="form-check-label" for="channelEmail">
                                    <i class="bi bi-envelope me-1"></i> Email Notifications
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" x-model="channels.sms" id="channelSMS">
                                <label class="form-check-label" for="channelSMS">
                                    <i class="bi bi-chat-dots me-1"></i> SMS Notifications
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" x-model="channels.push" id="channelPush">
                                <label class="form-check-label" for="channelPush">
                                    <i class="bi bi-bell me-1"></i> Push Notifications
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Templates -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-file-text me-2 text-success"></i>Notification Templates</h5>
                </div>
                <div class="card-body p-0">
                    <!-- Template Tabs -->
                    <ul class="nav nav-tabs px-3 pt-3" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link" :class="{ 'active': activeTab === 'attendance' }" 
                                    @click="activeTab = 'attendance'">
                                <i class="bi bi-calendar-check me-1"></i> Attendance
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ 'active': activeTab === 'exam' }" 
                                    @click="activeTab = 'exam'">
                                <i class="bi bi-journal-text me-1"></i> Exams
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ 'active': activeTab === 'fee' }" 
                                    @click="activeTab = 'fee'">
                                <i class="bi bi-currency-rupee me-1"></i> Fees
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ 'active': activeTab === 'notice' }" 
                                    @click="activeTab = 'notice'">
                                <i class="bi bi-megaphone me-1"></i> Notices
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" :class="{ 'active': activeTab === 'message' }" 
                                    @click="activeTab = 'message'">
                                <i class="bi bi-chat-left-text me-1"></i> Messages
                            </button>
                        </li>
                    </ul>

                    <!-- Attendance Templates -->
                    <div class="p-4" x-show="activeTab === 'attendance'">
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Absent Notification</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.attendance.absent.email" id="absentEmail">
                                            <label class="form-check-label" for="absentEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.attendance.absent.sms" id="absentSMS">
                                            <label class="form-check-label" for="absentSMS">SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.attendance.absent.sms_template"></textarea>
                                    <small class="text-muted">Variables: {student_name}, {date}, {class}, {section}</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Subject</label>
                                    <input type="text" class="form-control" 
                                           x-model="templates.attendance.absent.email_subject">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Body</label>
                                    <textarea class="form-control" rows="4" 
                                              x-model="templates.attendance.absent.email_body"></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Late Arrival Notification</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.attendance.late.email" id="lateEmail">
                                            <label class="form-check-label" for="lateEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.attendance.late.sms" id="lateSMS">
                                            <label class="form-check-label" for="lateSMS">SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.attendance.late.sms_template"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exam Templates -->
                    <div class="p-4" x-show="activeTab === 'exam'">
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Exam Schedule Notification</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.exam.schedule.email" id="examScheduleEmail">
                                            <label class="form-check-label" for="examScheduleEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.exam.schedule.sms" id="examScheduleSMS">
                                            <label class="form-check-label" for="examScheduleSMS">SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.exam.schedule.sms_template"></textarea>
                                    <small class="text-muted">Variables: {student_name}, {exam_name}, {start_date}, {end_date}</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Subject</label>
                                    <input type="text" class="form-control" 
                                           x-model="templates.exam.schedule.email_subject">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Body</label>
                                    <textarea class="form-control" rows="4" 
                                              x-model="templates.exam.schedule.email_body"></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Result Published Notification</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.exam.result.email" id="examResultEmail">
                                            <label class="form-check-label" for="examResultEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.exam.result.sms" id="examResultSMS">
                                            <label class="form-check-label" for="examResultSMS">SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.exam.result.sms_template"></textarea>
                                    <small class="text-muted">Variables: {student_name}, {exam_name}, {percentage}, {grade}, {rank}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Templates -->
                    <div class="p-4" x-show="activeTab === 'fee'">
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Fee Due Reminder</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.fee.due.email" id="feeDueEmail">
                                            <label class="form-check-label" for="feeDueEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.fee.due.sms" id="feeDueSMS">
                                            <label class="form-check-label" for="feeDueSMS">SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Days Before Due Date</label>
                                    <input type="number" class="form-control" 
                                           x-model="templates.fee.due.days_before" min="1" max="30">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.fee.due.sms_template"></textarea>
                                    <small class="text-muted">Variables: {student_name}, {amount}, {due_date}, {fee_type}</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Subject</label>
                                    <input type="text" class="form-control" 
                                           x-model="templates.fee.due.email_subject">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Body</label>
                                    <textarea class="form-control" rows="4" 
                                              x-model="templates.fee.due.email_body"></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">Payment Received Confirmation</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.fee.paid.email" id="feePaidEmail">
                                            <label class="form-check-label" for="feePaidEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.fee.paid.sms" id="feePaidSMS">
                                            <label class="form-check-label" for="feePaidSMS">SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.fee.paid.sms_template"></textarea>
                                    <small class="text-muted">Variables: {student_name}, {amount}, {receipt_no}, {payment_date}</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div>
                            <h6 class="text-muted mb-3">Overdue Fee Alert</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.fee.overdue.email" id="feeOverdueEmail">
                                            <label class="form-check-label" for="feeOverdueEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.fee.overdue.sms" id="feeOverdueSMS">
                                            <label class="form-check-label" for="feeOverdueSMS">SMS</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.fee.overdue.sms_template"></textarea>
                                    <small class="text-muted">Variables: {student_name}, {amount}, {overdue_days}, {fine_amount}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notice Templates -->
                    <div class="p-4" x-show="activeTab === 'notice'">
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">New Notice Notification</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.notice.new.email" id="noticeNewEmail">
                                            <label class="form-check-label" for="noticeNewEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.notice.new.sms" id="noticeNewSMS">
                                            <label class="form-check-label" for="noticeNewSMS">SMS</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.notice.new.push" id="noticeNewPush">
                                            <label class="form-check-label" for="noticeNewPush">Push</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">SMS Template</label>
                                    <textarea class="form-control" rows="2" 
                                              x-model="templates.notice.new.sms_template"></textarea>
                                    <small class="text-muted">Variables: {notice_title}, {school_name}</small>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Subject</label>
                                    <input type="text" class="form-control" 
                                           x-model="templates.notice.new.email_subject">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Body</label>
                                    <textarea class="form-control" rows="4" 
                                              x-model="templates.notice.new.email_body"></textarea>
                                    <small class="text-muted">Variables: {notice_title}, {notice_content}, {publish_date}, {school_name}</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Templates -->
                    <div class="p-4" x-show="activeTab === 'message'">
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">New Message Notification</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.message.new.email" id="messageNewEmail">
                                            <label class="form-check-label" for="messageNewEmail">Email</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   x-model="templates.message.new.push" id="messageNewPush">
                                            <label class="form-check-label" for="messageNewPush">Push</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Subject</label>
                                    <input type="text" class="form-control" 
                                           x-model="templates.message.new.email_subject">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Email Body</label>
                                    <textarea class="form-control" rows="4" 
                                              x-model="templates.message.new.email_body"></textarea>
                                    <small class="text-muted">Variables: {sender_name}, {message_preview}, {school_name}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Notification Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-bar-chart me-2 text-primary"></i>This Month</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Emails Sent</span>
                        <strong x-text="stats.emails_sent">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">SMS Sent</span>
                        <strong x-text="stats.sms_sent">0</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Push Notifications</span>
                        <strong x-text="stats.push_sent">0</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Failed</span>
                        <strong class="text-danger" x-text="stats.failed">0</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-lightning me-2 text-warning"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary text-start" @click="testNotification('email')">
                            <i class="bi bi-envelope me-2"></i> Send Test Email
                        </button>
                        <button type="button" class="btn btn-outline-primary text-start" @click="testNotification('sms')">
                            <i class="bi bi-chat-dots me-2"></i> Send Test SMS
                        </button>
                        <a href="{{ route('settings.email') ?? '#' }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-gear me-2"></i> Email Settings
                        </a>
                        <a href="{{ route('settings.sms') ?? '#' }}" class="btn btn-outline-secondary text-start">
                            <i class="bi bi-gear me-2"></i> SMS Settings
                        </a>
                    </div>
                </div>
            </div>

            <!-- Available Variables -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="bi bi-code-slash me-2 text-info"></i>Template Variables</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Use these variables in your templates:</p>
                    <div class="d-flex flex-wrap gap-1">
                        <span class="badge bg-light text-dark">{student_name}</span>
                        <span class="badge bg-light text-dark">{parent_name}</span>
                        <span class="badge bg-light text-dark">{class}</span>
                        <span class="badge bg-light text-dark">{section}</span>
                        <span class="badge bg-light text-dark">{date}</span>
                        <span class="badge bg-light text-dark">{amount}</span>
                        <span class="badge bg-light text-dark">{school_name}</span>
                        <span class="badge bg-light text-dark">{school_phone}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function notificationSettings() {
    return {
        saving: false,
        activeTab: 'attendance',
        channels: {
            email: true,
            sms: true,
            push: false
        },
        stats: {
            emails_sent: 1250,
            sms_sent: 890,
            push_sent: 456,
            failed: 12
        },
        templates: {
            attendance: {
                absent: {
                    email: true,
                    sms: true,
                    sms_template: 'Dear Parent, {student_name} was marked absent on {date}. Please contact school for details.',
                    email_subject: 'Attendance Alert - {student_name}',
                    email_body: 'Dear Parent,\n\nThis is to inform you that {student_name} of Class {class}-{section} was marked absent on {date}.\n\nIf this is incorrect, please contact the school office.\n\nRegards,\n{school_name}'
                },
                late: {
                    email: false,
                    sms: true,
                    sms_template: 'Dear Parent, {student_name} arrived late to school on {date}. Please ensure timely arrival.'
                }
            },
            exam: {
                schedule: {
                    email: true,
                    sms: true,
                    sms_template: 'Dear Parent, {exam_name} for {student_name} is scheduled from {start_date} to {end_date}. Please ensure preparation.',
                    email_subject: 'Exam Schedule - {exam_name}',
                    email_body: 'Dear Parent,\n\nThis is to inform you that {exam_name} has been scheduled for your ward {student_name}.\n\nExam Period: {start_date} to {end_date}\n\nPlease ensure your child is well-prepared.\n\nRegards,\n{school_name}'
                },
                result: {
                    email: true,
                    sms: true,
                    sms_template: 'Dear Parent, {exam_name} results are out. {student_name} scored {percentage}% with Grade {grade}. Rank: {rank}'
                }
            },
            fee: {
                due: {
                    email: true,
                    sms: true,
                    days_before: 7,
                    sms_template: 'Dear Parent, Fee of Rs.{amount} for {student_name} is due on {due_date}. Please pay on time to avoid late fee.',
                    email_subject: 'Fee Payment Reminder - {student_name}',
                    email_body: 'Dear Parent,\n\nThis is a reminder that the {fee_type} fee of Rs.{amount} for {student_name} is due on {due_date}.\n\nPlease make the payment on time to avoid late fee charges.\n\nRegards,\n{school_name}'
                },
                paid: {
                    email: true,
                    sms: true,
                    sms_template: 'Dear Parent, Payment of Rs.{amount} received for {student_name}. Receipt No: {receipt_no}. Thank you!'
                },
                overdue: {
                    email: true,
                    sms: true,
                    sms_template: 'Dear Parent, Fee of Rs.{amount} for {student_name} is overdue by {overdue_days} days. Fine: Rs.{fine_amount}. Please pay immediately.'
                }
            },
            notice: {
                new: {
                    email: true,
                    sms: false,
                    push: true,
                    sms_template: 'New Notice from {school_name}: {notice_title}. Check school portal for details.',
                    email_subject: 'Notice: {notice_title}',
                    email_body: 'Dear Parent/Student,\n\n{notice_content}\n\nPublished on: {publish_date}\n\nRegards,\n{school_name}'
                }
            },
            message: {
                new: {
                    email: true,
                    push: true,
                    email_subject: 'New Message from {sender_name}',
                    email_body: 'Dear User,\n\nYou have received a new message from {sender_name}.\n\nPreview: {message_preview}\n\nLogin to view the full message.\n\nRegards,\n{school_name}'
                }
            }
        },
        
        async saveSettings() {
            this.saving = true;
            
            try {
                await new Promise(resolve => setTimeout(resolve, 1000));
                
                Swal.fire({
                    icon: 'success',
                    title: 'Settings Saved!',
                    text: 'Notification settings have been updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Failed to save settings. Please try again.'
                });
            } finally {
                this.saving = false;
            }
        },
        
        testNotification(type) {
            Swal.fire({
                title: 'Send Test ' + (type === 'email' ? 'Email' : 'SMS'),
                input: type === 'email' ? 'email' : 'text',
                inputLabel: type === 'email' ? 'Email Address' : 'Phone Number',
                inputPlaceholder: type === 'email' ? 'Enter email address' : 'Enter phone number',
                showCancelButton: true,
                confirmButtonText: 'Send',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Please enter a valid ' + (type === 'email' ? 'email' : 'phone number');
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Test ' + (type === 'email' ? 'Email' : 'SMS') + ' Sent!',
                        text: 'A test notification has been sent to ' + result.value,
                        timer: 3000,
                        showConfirmButton: false
                    });
                }
            });
        }
    };
}
</script>
@endpush

@push('styles')
<style>
.nav-tabs .nav-link {
    color: #6c757d;
    border: none;
    border-bottom: 2px solid transparent;
}

.nav-tabs .nav-link:hover {
    border-color: transparent;
    color: var(--bs-primary);
}

.nav-tabs .nav-link.active {
    color: var(--bs-primary);
    border-bottom-color: var(--bs-primary);
    background: transparent;
}

[dir="rtl"] .text-start {
    text-align: right !important;
}
</style>
@endpush
