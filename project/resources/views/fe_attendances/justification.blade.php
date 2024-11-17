<div class="modal fade" id="justificationModal" tabindex="-1" aria-labelledby="justificationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="justificationModalLabel">Giải trình</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('attendance.addJustification') }}" method="POST">
                    @csrf
                    <input type="hidden" name="attendance_id" id="attendanceId" value="{{ $attendance->id ?? '' }}">
                    <div class="mb-3">
                        <label for="justificationText" class="form-label">Lý do giải trình</label>
                        <textarea name="justification_text" id="justificationText" class="form-control" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Gửi</button>
                </form>
            </div>
        </div>
    </div>
</div>