<div class="modal fade" id="reminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reminderModalLabel">Đặt thời gian nhắc nhở</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('setReminder') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="reminderTime" class="form-label">Thời gian nhắc nhở</label>
                        <input type="time" name="reminder_time" id="reminderTime" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>
            </div>
        </div>
    </div>
</div>
