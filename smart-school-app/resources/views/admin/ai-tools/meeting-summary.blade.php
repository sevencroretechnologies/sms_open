@extends('layouts.app')

@section('title', 'Meeting Summary Generator')

@section('content')
<div class="container-fluid" x-data="meetingSummary()">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h3 mb-1">Meeting Summary Generator</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.ai-tools.index') }}">AI Tools</a></li>
                    <li class="breadcrumb-item active">Meeting Summary</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-journal-text me-2"></i>Meeting Details
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Meeting Title</label>
                        <input type="text" class="form-control" x-model="meetingTitle" placeholder="e.g., Parent-Teacher Conference">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attendees (comma separated)</label>
                        <input type="text" class="form-control" x-model="attendeesInput" placeholder="e.g., Mr. Smith, Mrs. Johnson, Principal">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meeting Notes</label>
                        <textarea class="form-control" x-model="meetingNotes" rows="8" placeholder="Enter the raw meeting notes here..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Action Items (one per line)</label>
                        <textarea class="form-control" x-model="actionItemsInput" rows="4" placeholder="- Follow up on homework completion&#10;- Schedule next meeting&#10;- Send progress report"></textarea>
                    </div>
                    <button class="btn btn-info w-100" @click="generate()" :disabled="!isValid() || isLoading">
                        <span x-show="!isLoading"><i class="bi bi-magic me-2"></i>Generate Summary</span>
                        <span x-show="isLoading"><i class="bi bi-hourglass-split me-2"></i>Generating...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow-sm" x-show="result">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-file-text me-2"></i>Meeting Summary</span>
                    <button class="btn btn-sm btn-outline-primary" @click="copyToClipboard()">
                        <i class="bi bi-clipboard me-1"></i>Copy
                    </button>
                </div>
                <div class="card-body">
                    <template x-if="result">
                        <div>
                            <div class="mb-4">
                                <h6 class="text-primary" x-text="result.title"></h6>
                                <small class="text-muted">Date: <span x-text="result.date"></span></small>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-people me-2"></i>Attendees</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <template x-for="attendee in result.attendees || []">
                                        <span class="badge bg-secondary" x-text="attendee"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-chat-left-text me-2"></i>Summary</h6>
                                <p class="text-muted" x-text="result.summary" style="white-space: pre-wrap;"></p>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-key me-2"></i>Key Points</h6>
                                <ul class="list-group list-group-flush">
                                    <template x-for="point in result.key_points || []">
                                        <li class="list-group-item"><i class="bi bi-check-circle text-success me-2"></i><span x-text="point"></span></li>
                                    </template>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <h6><i class="bi bi-list-task me-2"></i>Action Items</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Task</th>
                                                <th>Assigned To</th>
                                                <th>Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="item in result.action_items || []">
                                                <tr>
                                                    <td x-text="item.task"></td>
                                                    <td><span class="badge bg-primary" x-text="item.assigned_to"></span></td>
                                                    <td x-text="item.due_date"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="mb-3" x-show="result.follow_ups && result.follow_ups.length > 0">
                                <h6><i class="bi bi-arrow-repeat me-2"></i>Follow-ups</h6>
                                <ul class="list-unstyled">
                                    <template x-for="followup in result.follow_ups || []">
                                        <li class="mb-1"><i class="bi bi-arrow-right text-info me-2"></i><span x-text="followup"></span></li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="card border-0 shadow-sm" x-show="!result && !isLoading">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-text text-muted" style="font-size: 4rem;"></i>
                    <h5 class="mt-3">Generate Meeting Summary</h5>
                    <p class="text-muted">Enter the meeting details and notes to generate a structured summary with key points and action items.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function meetingSummary() {
    return {
        meetingTitle: '',
        attendeesInput: '',
        meetingNotes: '',
        actionItemsInput: '',
        isLoading: false,
        result: null,

        isValid() {
            return this.meetingTitle && this.attendeesInput && this.meetingNotes;
        },

        async generate() {
            if (!this.isValid()) return;
            
            this.isLoading = true;
            this.result = null;

            const attendees = this.attendeesInput.split(',').map(s => s.trim()).filter(s => s);
            const actionItems = this.actionItemsInput.split('\n').map(s => s.replace(/^-\s*/, '').trim()).filter(s => s);

            try {
                const response = await fetch('/api/v1/ai/meeting-summary', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        meeting_title: this.meetingTitle,
                        attendees: attendees,
                        meeting_notes: this.meetingNotes,
                        action_items: actionItems
                    })
                });

                const data = await response.json();
                if (data.status === 'success') {
                    this.result = data.data;
                } else {
                    alert('Error: ' + (data.message || 'Failed to generate summary'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while generating summary');
            } finally {
                this.isLoading = false;
            }
        },

        copyToClipboard() {
            const text = `Meeting Summary: ${this.result.title}\n\nAttendees: ${this.result.attendees?.join(', ')}\n\nSummary:\n${this.result.summary}\n\nKey Points:\n${this.result.key_points?.map(p => '- ' + p).join('\n')}\n\nAction Items:\n${this.result.action_items?.map(i => '- ' + i.task + ' (Assigned to: ' + i.assigned_to + ')').join('\n')}`;
            navigator.clipboard.writeText(text);
            alert('Summary copied to clipboard!');
        }
    };
}
</script>
@endpush
