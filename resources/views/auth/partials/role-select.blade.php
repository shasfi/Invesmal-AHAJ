<div class="auth-form-group">
    <label for="role">{{ $label ?? 'Role' }}</label>
    <select id="role" name="role" required>
        <option value="">Select your role</option>
        <option value="student_founder" @selected(old('role') === 'student_founder')>Student Founder</option>
        <option value="investor" @selected(old('role') === 'investor')>Investor</option>
        <option value="mentor" @selected(old('role') === 'mentor')>Mentor</option>
        @if($showAdmin ?? false)
            <option value="admin" @selected(old('role') === 'admin')>Admin</option>
        @endif
    </select>
</div>
