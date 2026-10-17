<div class="form-row">
    <div class="form-group col-md-12">
        <label for="client_name">Client name</label>
        <input id="client_name" name="client_name" type="text" maxlength="120" required value="{{ old('client_name', $client->client_name ?? '') }}" class="form-control @error('client_name') is-invalid @enderror">
        @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-12">
        <label for="contact_person">Contact person</label>
        <input id="contact_person" name="contact_person" type="text" maxlength="100" value="{{ old('contact_person', $client->contact_person ?? '') }}" class="form-control @error('contact_person') is-invalid @enderror">
        @error('contact_person')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
     <div class="form-group col-md-12">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" maxlength="100" value="{{ old('email', $client->email ?? '') }}" class="form-control @error('email') is-invalid @enderror">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-12">
        <label for="phone">Phone</label>
        <input id="phone" name="phone" type="text" maxlength="20" required value="{{ old('phone', $client->phone ?? '') }}" class="form-control @error('phone') is-invalid @enderror">
        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-12">
        <label for="address">Address <span class="text-muted">(optional)</span></label>
        <textarea id="address" name="address" maxlength="255" class="form-control @error('address') is-invalid @enderror">{{ old('address', $client->address ?? '') }}</textarea>
        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group col-md-12">
        <label for="status">Status</label>
        <select id="status" name="status" required class="form-control @error('status') is-invalid @enderror">
            <option value="1" {{ (string) old('status', $client->status ?? 1) === '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ (string) old('status', $client->status ?? 1) === '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
