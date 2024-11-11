<form action="{{ route($type == 'in' ? 'checkin' : 'checkout') }}" method="POST">
    @csrf
    <div>
        <label for="checkTime">Giờ {{ $type == 'in' ? 'Check In' : 'Check Out' }}:</label>
        <input type="text" id="checkTime" value="{{ $checkTime->format('H:i') }}" disabled>
    </div>
    
    <div>
        <label for="justification">Lý do giải trình:</label>
        <textarea id="justification" name="justification" required></textarea>
    </div>

    <button type="submit">Gửi lý do giải trình</button>
</form>
