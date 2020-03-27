@extends('layouts.app')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('employees.create')
        <a style="margin-left:15px" href="{{ action('FirmsController@edit', ['id' => $id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('firms.back_to_edit')</span>
        </a>
    </h1>
@endsection

@section('app-content')
    <div class="browse container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    @if($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="panel-body">
                        <form action="{{ action('EmployeesController@store', ['firm_id' => $id]) }}" method="POST">
                            {{ csrf_field() }}
                            <div class="employees-general" id="general">
                                <div class="form-group">
                                    <label for="firstname">@lang('employees.form.firstname')</label>
                                    <input type="text" class="form-control" id="firstname" name="firstname"
                                           value="{{ old('firstname') }}">
                                </div>
                                <div class="form-group">
                                    <label for="lastname">@lang('employees.form.lastname')</label>
                                    <input type="text" class="form-control" id="lastname" name="lastname"
                                           value="{{ old('lastname') }}">
                                </div>
                                <div class="form-group">
                                    <label for="email">@lang('employees.form.email')</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email') }}">
                                </div>
                                <div class="form-group">
                                    <label for="phone">@lang('employees.form.phone')</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                           value="{{ old('phone') }}">
                                </div>
                                <div class="form-group">
                                    <label for="job_position">@lang('employees.form.job_position')</label>
                                    @php
                                    $roles;
                                    $count=1;
                                    @endphp
                                    @foreach($roles as $role)
                                    <div>
                                        <label>{{$role->name}}</label>
                                        <input type="checkbox" id="role{{$count}}" name="role{{$count}}" value="{{$role->id}}">
                                    </div>
                                    @php        
                                    $count++;
                                    @endphp
                                    @endforeach
                                    <input type="hidden" id="rolecount" name="rolecount" value="{{$count-1}}">
                                </div>
                                <div class="form-group">
                                    <label for="person_number">@lang('employees.form.numer')</label>
                                    <input type="number" class="form-control" id="person_number" name="person_number"
                                           value="{{ old('person_number') }}">
                                </div>
                                <div class="form-group">
                                    <label for="job_position">@lang('employees.form.magazines')</label>
                                    @php
                                    $warehouses;
                                    $count=1;
                                    @endphp
                                    @foreach($warehouses as $warehouse)
                                    <div>
                                        <label>{{$warehouse->symbol}}</label>
                                        <input type="checkbox" id="magazine{{$count}}" name="magazine{{$count}}" value="{{$warehouse->id}}">
                                    </div>
                                    @php        
                                    $count++;
                                    @endphp
                                    @endforeach 
                                    <input type="hidden" id="magazinecount" name="magazinecount" value="{{$count-1}}">
                                </div>
                                <div class="form-group">
                                    <label for="comments">@lang('employees.form.comments')</label>
                                    <textarea rows="4" cols="50" class="form-control" id="comments"
                                              name="comments"
                                    >{{ old('comments')}}</textarea>
                                    <div class="form-group">
                                        <label for="additional_comments">@lang('employees.form.additional_comments')</label>
                                        <textarea rows="4" cols="50" class="form-control" id="additional_comments"
                                                  name="additional_comments"
                                        >{{ old('additional_comments')}}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="postal_code">@lang('employees.form.postal_code')</label>
                                        <input type="text" class="form-control" id="postal_code" name="postal_code"
                                               value="{{ old('postal_code') }}">
                                    </div>
                                    <div class="form-group">
                                    <label for="latitude">@lang('firms.form.address.latitude')</label>
                                    <input type="text" class="form-control disabled" id="latitude" name="latitude"
                                            value="{{ old('latitude') }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="longitude">@lang('firms.form.address.longitude')</label>
                                        <input type="text" class="form-control" id="longitude" name="longitude"
                                                value="{{ old('longitude') }}" disabled>
                                    </div>
                                    <div class="form-group">
                                        <label for="status">@lang('employees.form.status')</label>
                                        <select class="form-control text-uppercase" name="status">
                                            <option value="ACTIVE">@lang('employees.form.active')</option>
                                            <option value="PENDING">@lang('employees.form.pending')</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        $(document).ready(function () {
            var breadcrumb = $('.breadcrumb:nth-child(2)');
            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/{{$id}}/edit'>Firmy</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/{{$id}}/edit#employees'>Pracownicy</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Dodaj</a></li>");
        });
    </script>
@endsection