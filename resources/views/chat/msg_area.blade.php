@php use App\Enums\UserRole; @endphp

<select name="{{ $msgAreaId }}" id="{{ $msgAreaId }}">
    <option value="{{ UserRole::Main }}" {{ $area == UserRole::Main ? 'selected' : '' }}>
        Główny
    </option>
    <option value="{{ UserRole::Storekeeper }}" {{ $area == UserRole::Storekeeper ? 'selected' : '' }}>
        Magazyn
    </option>
    <option value="{{ UserRole::SuperAdministrator }}" {{ $area == UserRole::SuperAdministrator ? 'selected' : '' }}>
        Administrator
    </option>
    <option value="{{ UserRole::Consultant }}" {{ $area == UserRole::Consultant ? 'selected' : '' }}>
        Konsultant
    </option>
    <option value="{{ UserRole::Accountant }}" {{ $area == UserRole::Accountant ? 'selected' : '' }}>
        Księgowość
    </option>
</select>
