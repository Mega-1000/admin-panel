<tr>
    <th colspan="2" style="height: {{ $users->isEmpty() ? '40px' : '20px'}}; padding-top: 15px;">{{ $title }}
        @if ($users->isEmpty())
            <p colspan="2" style="font-weight: 400;">
                {{ $isEmptyMsg }}
            </p>
        @endif
    </th>
</tr>
@foreach ($users as $user)
    <tr>
        <th class="{{ $class }}" style="height: 90px;">
            @if($title === "Pracownicy firm uczestniczących w przetargu:")
                Odległość: {{ $user->radius }} km
            @endif
            {!! ChatHelper::formatChatUser($user, $userType) !!}
        </th>
        @if ($currentUserType == MessagesHelper::TYPE_USER && $userType != MessagesHelper::TYPE_USER || $title === 'Pracownicy firm uczestniczących w przetargu:')
            @if ($arePossibleUsers)
                <th>
                    <button name="{{ get_class($user) }}" class="btn btn-success add-user"
                        value="{{ $user->id }}">Dodaj
                    </button>
                </th>
            @else
                <th>
                    <button class="btn btn-danger remove-user" value="{{ $user->id }}"
                        name="{{ get_class($user) }}">Usuń
                    </button>
                </th>
            @endif
        @endif
    </tr>
@endforeach
