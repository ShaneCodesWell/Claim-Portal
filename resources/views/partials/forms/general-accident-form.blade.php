@php
    $f = $formData ?? [];
    $isStaff = ($context ?? 'customer') === 'staff';
    $isEdit = !is_null($claim ?? null);

@endphp