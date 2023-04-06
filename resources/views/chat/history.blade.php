<button class="btn btn-success show-all">
    Pokaż wszystkich
</button>
<h5>Klienci:</h5>
@foreach ($usersHistory['customers'] as $user)
    <label>{{ $user->customer->addresses->first()?->email }}
        <input type="checkbox" checked class="filter-users-history" value="{{ $user->id }}" />
    </label>
@endforeach
<h5>Pracownicy:</h5>
@foreach ($usersHistory['employees'] as $user)
    <label>{{ $user->employee->email }}
        <input type="checkbox" checked class="filter-users-history" value="{{ $user->id }}" />
    </label>
@endforeach
<h5>Konsultanci:</h5>
@foreach ($usersHistory['consultants'] as $user)
    <label>{{ $user->user->email }}
        <input type="checkbox" checked class="filter-users-history" value="{{ $user->id }}" />
    </label>
@endforeach
