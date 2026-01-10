@extends('layouts.app')

@section('title', 'Parent Communication Generator')

@section('content')
<div class="container-fluid" x-data="parentCommunication()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Parent Communication Generator</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Parent Communication</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-envelope me-2"></i>Message Settings
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Message Type</label>
                        <select class="form-select" x-model="messageType">
                            <option value="fee_reminder">Fee Reminder</option>
                            <option value="meeting">Meeting Invitation</option>
                            <option value="progress">Progress Update</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Student Name</label>
                        <input type="text" class="form-control" x-model="studentName" placeholder="Enter student name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Parent Name</label>
                        <input type="text" class="form-control" x-model="parentName" placeholder="Enter parent name">
                    </div>

                    <template x-if="messageType === 'fee_reminder'">
                        <div>
                            <div class="mb-3">
                                <label class="form-label">Amount Due</label>
                                <input type="number" class="form-control" x-model="amountDue" placeholder="5000">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Due Date</label>
                                <input type="date" class="form-control" x-model="dueDate">
                            </div>
                        </div>
                    </template>

                    <template x-if="messageType === 'meeting'">
                        <div>
                            <div class="mb-3">
                                <label class="form-label">Meeting Date</label>
                                <input type="date" class="form-control" x-model="meetingDate">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Meeting Time</label>
                                <input type="time" class="form-control" x-model="meetingTime">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Venue</label>
                                <input type="text" class="form-control" x-model="venue" placeholder="School Conference Room">
                            </div>
                        </div>
                    </template>

                    <div class="mb-3">
                        <label class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" x-model="customNotes" rows="3" placeholder="Any specific details to include..."></textarea>
                    </div>

                    <button class="btn btn-info w-100" @click="generate()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-magic me-2"></i>Generate Message</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Generating...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" x-show="message">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-envelope-paper me-2"></i>Generated Message</span>
                    <button class="btn btn-sm btn-outline-primary" @click="copyToClipboard()">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="card-body">
                    <div class="p-3 bg-light rounded">
                        <p class="mb-0" x-text="message" style="white-space: pre-wrap;"></p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!message && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-envelope-paper text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Generate Parent Communication</h5>
                    <p class="text-muted">Select a message type, fill in the details, and click "Generate Message" to create a professional parent communication.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function parentCommunication() {
    return {
        messageType: 'fee_reminder',
        studentName: '',
        parentName: '',
        amountDue: '',
        dueDate: '',
        meetingDate: '',
        meetingTime: '',
        venue: '',
        customNotes: '',
        isLoading: false,
        message: '',

        isValid() {
            if (!this.studentName || !this.parentName) return false;
            if (this.messageType === 'fee_reminder' && (!this.amountDue || !this.dueDate)) return false;
            if (this.messageType === 'meeting' && (!this.meetingDate || !this.meetingTime || !this.venue)) return false;
            return true;
        },

        async generate() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.message = '';

            const payload = {
                type: this.messageType,
                student_name: this.studentName,
                parent_name: this.parentName,
                custom_notes: this.customNotes
            };

            if (this.messageType === 'fee_reminder') {
                payload.amount_due = parseFloat(this.amountDue);
                payload.due_date = this.dueDate;
            }

            if (this.messageType === 'meeting') {
                payload.meeting_date = this.meetingDate;
                payload.meeting_time = this.meetingTime;
                payload.venue = this.venue;
            }

            try {
                const response = await fetch('/api/v1/ai/parent-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.message = data.data.message;
                } else {
                    alert('Error: ' + (data.message || 'Failed to generate message'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while generating message');
            } finally {
                this.isLoading = false;
            }
        },

        copyToClipboard() {
            navigator.clipboard.writeText(this.message);
            alert('Message copied to clipboard!');
        }
    };
}
</script>
@endpush
