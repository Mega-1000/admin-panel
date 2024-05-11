<!-- resources/views/aproaches-index.blade.php -->

@extends('layouts.datatable')

@section('table')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Contact Approaches</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Phone Number</th>
                                <th>Referred By</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse ($items as $approach)
                                <tr>
                                    <td>{{ $approach->phone_number }}</td>
                                    <td>
                                        @if ($approach->referredByUser)
                                            {{ $approach->referredByUser->name }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('approaches.update', $approach->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="done" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm">Mark as Done</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">No pending approaches found.</td>
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
