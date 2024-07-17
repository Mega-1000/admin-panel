<!-- resources/views/aproaches-index.blade.php -->

@extends('layouts.datatable')

@section('table')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Prośby o kontakt</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Numer telefonu</th>
                                <th>Email referenta</th>
                                <th>Email prospekta</th>
                                <th>Notatki</th>
                                <th>Akcje</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($items as $approach)
                                <tr>
                                    <td>{{ $approach->phone_number }}</td>
                                    <td>
                                        @if ($approach->referredByUser)
                                            {{ $approach->referredByUser->login }}
                                        @else
                                            -
                                        @endif

                                        @php
                                            $order = App\Entities\Order::where('customer.addresses.0.phone', $approach->phone_number)->first();
                                        @endphp
                                        @if ($order)
                                            <div style="color: red">
                                                Ten użytkmownik ma już zapytanie na swoim koncie
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $approach->prospect_email }}
                                    </td>
                                    <td>
                                        {{ $approach->notes }}
                                    </td>
                                    <td>
                                        <a href="{{ route('set-approach-as-non-interested', $approach->id) }}" class="btn btn-danger">
                                            Niezainteresowany
                                        </a>
                                        <a href="{{ route('set-approach-as-interested', $approach->id) }}" class="btn btn-success">
                                            Zainteresowany
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">Brak wpisów do wyświetlenia.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
