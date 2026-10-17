<div class="form-group">
    <label for="role_name">Role name</label>
    <input id="role_name" name="role_name" type="text" maxlength="50" required value="{{ old('role_name', $role->role_name ?? '') }}" class="form-control @error('role_name') is-invalid @enderror">
    @error('role_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="form-group">
    <label for="description">Description</label>
    <textarea id="description" name="description" maxlength="255" rows="3" required class="form-control @error('description') is-invalid @enderror">{{ old('description', $role->description ?? '') }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
