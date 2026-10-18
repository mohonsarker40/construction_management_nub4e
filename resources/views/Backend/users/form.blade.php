<div class="form-row">
    <div class="form-group col-md-6">
        <label for="full_name">Full name</label>
        <input id="full_name" name="full_name" type="text" maxlength="100" required value="{{ old('full_name', $user->full_name ?? '') }}" class="form-control @error('full_name') is-invalid @enderror">
        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
        <label for="role_id">Role</label>
        <select id="role_id" name="role_id" required class="form-control @error('role_id') is-invalid @enderror">
            <option value="">Choose a role</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}" {{ (string) old('role_id', $user->role_id ?? '') === (string) $role->id ? 'selected' : '' }}>{{ $role->role_name }}</option>
            @endforeach
        </select>
        @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" maxlength="100" required value="{{ old('email', $user->email ?? '') }}" class="form-control @error('email') is-invalid @enderror">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
        <label for="phone">Phone <span class="text-muted">(optional)</span></label>
        <input id="phone" name="phone" type="text" maxlength="20" value="{{ old('phone', $user->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="password">Password @isset($user)<span class="text-muted">(leave blank to keep current)</span>@endisset</label>
        <input id="password" name="password" type="password" minlength="8" @empty($user) required @endempty autocomplete="new-password" class="form-control @error('password') is-invalid @enderror">
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-6">
        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" minlength="8" @empty($user) required @endempty autocomplete="new-password" class="form-control">
    </div>
</div>
<div class="form-group">
    <label for="status">Status</label>
    <select id="status" name="status" required class="form-control @error('status') is-invalid @enderror">
        <option value="1" {{ (string) old('status', $user->status ?? 1) === '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ (string) old('status', $user->status ?? 1) === '0' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
