@php use App\Enums\UserRole; @endphp

<select name="{{ $msgAreaId }}" id="{{ $msgAreaId }}">
    <option value="{{ UserRole::Main }}">
        Główny
    </option>
    <option value="{{ UserRole::Storekeeper }}">
        Magazyn
    </option>
    <option value="{{ UserRole::SuperAdministrator }}">
        Administrator
    </option>
    <option value="{{ UserRole::Consultant }}">
        Konsultant
    </option>
    <option value="{{ UserRole::Accountant }}">
        Księgowość
    </option>
</select>
