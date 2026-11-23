@extends('frontend.master')

@section('title', 'ফ্ল্যাট কিনতে অনুরোধ | Buildwise')
@section('styles')
    .request-wrap { width:min(1000px,calc(100% - 36px)); margin:50px auto 70px; }
    .request-card { padding:clamp(22px,5vw,42px); border:1px solid var(--line); border-radius:16px; background:#fff; box-shadow:0 14px 44px rgba(24,61,50,.08); }
    .request-card h1 { margin:0 0 8px; color:var(--green); font-size:clamp(2rem,4vw,3rem); line-height:1.1; }
    .request-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; margin-top:26px; }
    .request-grid .wide { grid-column:1/-1; }
    .request-grid label { display:block; margin-bottom:6px; font-weight:700; }
    .request-grid input,.request-grid select,.request-grid textarea { width:100%; padding:12px; border:1px solid #cbd4ce; border-radius:7px; font:inherit; }
    .request-note { color:var(--muted); }
    .form-error { color:#a42424; font-size:.88rem; }
    .choice-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); gap:12px; margin:10px 0 20px; }
    .choice-card { position:relative; display:block; padding:16px; border:1px solid var(--line); border-radius:12px; background:#fff; cursor:pointer; }
    .choice-card:has(input:checked) { border:2px solid var(--green); background:#f1f6eb; }
    .choice-card input { width:auto; margin-right:7px; }
    .choice-card strong,.choice-card span { display:block; }
    .choice-card span { margin-top:5px; color:var(--muted); font-size:.9rem; }
    .unit-group { display:none; }
    .unit-group.active { display:block; }
    .selected-flat { padding:12px 15px; border-radius:8px; background:#f1f6eb; color:var(--green); font-weight:700; }
    @media(max-width:640px) { .request-grid { grid-template-columns:1fr; } .request-grid .wide { grid-column:auto; } }
@endsection

@section('content')
<section class="request-wrap"><div class="request-card">
    <p class="eyebrow">ক্লায়েন্ট বুকিং</p><h1>ফ্ল্যাট বাছাই করুন</h1>
    <p class="request-note">শুধু সম্পন্ন প্রজেক্টের খালি ফ্ল্যাট বুকিংয়ের জন্য দেখানো হচ্ছে। আগে প্রজেক্ট, তারপর ফ্ল্যাট কার্ডে চাপ দিয়ে বাছুন।</p>
    @if($errors->any())<div class="form-error" role="alert">{{ $errors->first() }}</div>@endif
    @if($projects->isEmpty())
        <div class="request-note">এখন সম্পন্ন প্রজেক্টে কোনো খালি ফ্ল্যাট নেই।</div>
    @else
    <form action="{{ route('booking.request.submit') }}" method="post">@csrf
        <div class="request-grid">
            <div class="wide"><label for="project_id">সম্পন্ন প্রজেক্ট বাছুন</label><select id="project_id" name="project_id" required><option value="">প্রজেক্ট নির্বাচন করুন</option>@foreach($projects as $project)<option value="{{ $project->id }}" data-name="{{ $project->project_name }}" data-location="{{ $project->location }}" data-start="{{ $project->start_date?->format('d M Y') ?? '—' }}" data-end="{{ $project->actual_end_date?->format('d M Y') ?? '—' }}" data-manager="{{ $project->manager->full_name ?? 'নির্ধারিত নেই' }}" data-designer="{{ $project->designer->full_name ?? 'নির্ধারিত নেই' }}" {{ (string) old('project_id', $selectedProjectId) === (string) $project->id ? 'selected' : '' }}>{{ $project->project_name }} — {{ $project->location }}</option>@endforeach</select></div>
            <div><label for="project_name">প্রজেক্টের নাম</label><input id="project_name" readonly></div><div><label for="project_location">ঠিকানা</label><input id="project_location" readonly></div>
            <div><label for="project_start">শুরুর তারিখ</label><input id="project_start" readonly></div><div><label for="project_end">সম্পন্নের তারিখ</label><input id="project_end" readonly></div>
            <div><label for="project_manager">Project Manager</label><input id="project_manager" readonly></div><div><label for="project_designer">Designer / Architect</label><input id="project_designer" readonly></div>
        </div>
        @error('project_id')<div class="form-error">{{ $message }}</div>@enderror

        <label>আপনি যে ফ্ল্যাটটি কিনবেন সেটি বাছুন</label>
        @foreach($projects as $project)
            <div class="unit-group" data-project="{{ $project->id }}">
                <div class="choice-grid">
                    @foreach($project->frontend_units as $unit)
                        <label class="choice-card"><input type="radio" name="unit_label" value="{{ $unit['label'] }}" data-price="{{ $unit['price'] }}" {{ old('unit_label') === $unit['label'] ? 'checked' : '' }}><strong>ফ্ল্যাট {{ $unit['label'] }}</strong><span>দাম: ৳ {{ number_format($unit['price'], 2) }}</span></label>
                    @endforeach
                </div>
            </div>
        @endforeach
        @error('unit_label')<div class="form-error">{{ $message }}</div>@enderror
        <div class="selected-flat mb-3" id="selected-flat">প্রজেক্ট ও ফ্ল্যাট বাছলে এখানে নির্বাচিত ফ্ল্যাট দেখাবে।</div>

        <div class="request-grid">
            <div class="wide selected-flat">ক্রেতা: {{ $client->client_name }} · {{ $client->email }} · {{ $client->phone }}</div>
            <div><label for="agreed_amount">ফ্ল্যাটের মোট দাম (৳)</label><input id="agreed_amount" name="agreed_amount" type="number" min="0.01" step="0.01" value="{{ old('agreed_amount') }}" readonly required></div>
            <div><label for="initial_payment_amount">অনুমোদনের জন্য পূর্ণ মূল্য (৳)</label><input id="initial_payment_amount" name="initial_payment_amount" type="number" min="0.01" step="0.01" value="{{ old('initial_payment_amount') }}" readonly required></div>
            <div><label for="payment_method">পেমেন্টের মাধ্যম</label><select id="payment_method" name="payment_method" required><option value="bank">ব্যাংক</option><option value="mobile_banking">মোবাইল ব্যাংকিং</option><option value="cheque">চেক</option><option value="cash">নগদ</option></select></div>
            <div><label for="payment_reference">লেনদেন/চেক নম্বর (ঐচ্ছিক)</label><input id="payment_reference" name="payment_reference" value="{{ old('payment_reference') }}" maxlength="100"></div>
            <div class="wide"><label for="client_message">অতিরিক্ত তথ্য (ঐচ্ছিক)</label><textarea id="client_message" name="client_message" rows="3" maxlength="2000">{{ old('client_message') }}</textarea></div>
            <div class="wide"><p class="request-note">ফ্ল্যাট বুকিং অনুমোদনের জন্য পুরো মূল্য একবারে জমা দিতে হবে। ম্যানেজার এবং হিসাবরক্ষকের অনুমোদনের পর বুকিং নিশ্চিত হবে।</p><button class="button button-primary" type="submit">বুকিং অনুরোধ পাঠান</button></div>
        </div>
    </form>
    @endif
</div></section>
@endsection

@section('script')
<script>
    const projectSelect = document.getElementById('project_id');
    const unitGroups = document.querySelectorAll('.unit-group');
    const priceField = document.getElementById('agreed_amount');
    const paymentField = document.getElementById('initial_payment_amount');
    const selectedFlat = document.getElementById('selected-flat');
    function updateProject() {
        const chosenProject = projectSelect.value;
        const option = projectSelect.selectedOptions[0];
        document.getElementById('project_name').value = option?.dataset.name || '';
        document.getElementById('project_location').value = option?.dataset.location || '';
        document.getElementById('project_start').value = option?.dataset.start || '';
        document.getElementById('project_end').value = option?.dataset.end || '';
        document.getElementById('project_manager').value = option?.dataset.manager || '';
        document.getElementById('project_designer').value = option?.dataset.designer || '';
        unitGroups.forEach(group => {
            const active = group.dataset.project === chosenProject;
            group.classList.toggle('active', active);
            group.querySelectorAll('input[name="unit_label"]').forEach(radio => radio.disabled = !active);
        });
        updateUnit();
    }
    function updateUnit() {
        const chosen = document.querySelector('input[name="unit_label"]:checked:not(:disabled)');
        priceField.value = chosen ? chosen.dataset.price : '';
        paymentField.value = chosen ? chosen.dataset.price : '';
        selectedFlat.textContent = chosen ? `আপনি বেছেছেন: ফ্ল্যাট ${chosen.value} — ৳ ${Number(chosen.dataset.price).toLocaleString('en-BD')}` : 'এই প্রজেক্ট থেকে একটি ফ্ল্যাট বাছুন।';
    }
    projectSelect.addEventListener('change', updateProject);
    document.querySelectorAll('input[name="unit_label"]').forEach(radio => radio.addEventListener('change', updateUnit));
    updateProject();
</script>
@endsection
