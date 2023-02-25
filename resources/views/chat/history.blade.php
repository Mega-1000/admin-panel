@foreach ($usersHistory['customers'] as $user)
    <h5>Klienci:</h5>
    <label>{{ $user->customer->addresses->first()->email }}
        <input type="checkbox" checked class="filter-users-history" value="{{ $user->id }}" />
    </label>
@endforeach
@foreach ($usersHistory['employees'] as $user)
    <h5>Pracownicy:</h5>
    <label>{{ $user->employee->email }}
        <input type="checkbox" checked class="filter-users-history" value="{{ $user->id }}" />
    </label>
@endforeach
@foreach ($usersHistory['consultants'] as $user)
    <h5>Konsultanci:</h5>
    <label>{{ $user->user->email }}
        <input type="checkbox" checked class="filter-users-history" value="{{ $user->id }}" />
    </label>
@endforeach
