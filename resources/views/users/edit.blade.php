@extends('layouts.app')

@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('users.edit')
        <a style="margin-left: 15px;" href="{!! route('users.index') !!}" class="btn btn-info install pull-right">
            <span>@lang('users.list')</span>
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
                        <form action="{{ action('UserController@update', ['id' => $user->id]) }}" method="POST"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('put') }}
                            <div class="form-group">
                                <label for="name">@lang('users.table.name')</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="{{ $user->name }}">
                            </div>
                            <div class="form-group">
                                <label for="email">@lang('users.table.email')</label>
                                <input type="text" class="form-control" id="email" name="email" disabled
                                       value="{{ $user->email }}">
                            </div>
                            <div class="form-group">
                                <label for="password">@lang('users.form.password')</label>
                                <br>
                                <small>@lang('users.form.passwordHint')</small>
                                <input type="password" id="password" name="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="role">@lang('users.form.role')</label>
                                <select class="form-control text-uppercase" name="role_id">
                                    <option value="">Wybierz</option>
                                    @if ($roles->count())
                                        @foreach($roles as $role)
                                            @php
                                                if(Auth::user()->role_id > 1){
                                                    if($role->id == 1){
                                                        unset($role);
                                                        continue;
                                                    }
                                                }
                                            @endphp
                                            <option
                                                value="{{ $role->id }}" {{ $selectedRole == $role->id ? 'selected="selected"' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="warehouse_id">@lang('users.form.warehouse')</label>
                                <select class="form-control text-uppercase" name="warehouse_id">
                                    <option value="">Wybierz</option>
                                    @if ($warehouses->count())
                                        @foreach($warehouses as $warehouse)
                                            <option
                                                {{ $user->warehouse_id == $warehouse->id ? 'selected="selected"' : '' }} value="{{ $warehouse->id }}">{{ $warehouse->symbol }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="firstname">@lang('users.form.first_name')</label>
                                <input type="text" id="firstname" name="firstname" class="form-control"
                                       value="{{ $user->firstname }}">
                            </div>
                            <div class="form-group">
                                <label for="lastname">@lang('users.form.last_name')</label>
                                <input type="text" id="lastname" name="lastname" class="form-control"
                                       value="{{ $user->lastname}}">
                            </div>
                            <div class="form-group">
                                <label for="phone">@lang('users.form.phone')</label>
                                <input type="phone" id="phone" name="phone" class="form-control"
                                       value="{{ $user->phone }}">
                            </div>
                            <div class="form-group">
                                <label for="phone2">@lang('users.form.phone2')</label>
                                <input type="phone2" id="phone2" name="phone2" class="form-control"
                                       value="{{ $user->phone2 }}">
                            </div>
                            <div class="form-group">
                                <label for="email-username">@lang('users.form.email-username')</label>
                                <input type="text" id="email-username" name="email-username" class="form-control"
                                       value="{{ $userEmail->username ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="host">@lang('users.form.host')</label>
                                <input type="text" id="host" name="host" class="form-control"
                                       value="{{ $userEmail->host ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="port">@lang('users.form.port')</label>
                                <input type="text" id="port" name="port" class="form-control"
                                       value="{{ $userEmail->port ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="email-password">@lang('users.form.password')</label>
                                <input type="password" id="email-password" name="email-password" class="form-control"
                                       value="{{ $userEmail->password ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label for="encryption">@lang('users.form.encryption')</label>
                                <select class="form-control text-uppercase" name="encryption">
                                    @if($userEmail != null)
                                        @if($userEmail->encryption == 'SSL')
                                            <option value="SSL" selected>SSL</option>
                                            <option value="TLS">TLS</option>
                                            <option value="NONE">Brak</option>
                                        @elseif($userEmail->encryption == 'TLS')
                                            <option value="SSL">SSL</option>
                                            <option value="TLS" selected>TLS</option>
                                            <option value="NONE">Brak</option>
                                        @else
                                            <option value="SSL">SSL</option>
                                            <option value="TLS">TLS</option>
                                            <option value="NONE" selected>Brak</option>
                                        @endif
                                    @else
                                        <option value="SSL">SSL</option>
                                        <option value="TLS">TLS</option>
                                        <option value="NONE" selected>Brak</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rate_hour">@lang('users.form.rate_hour')</label>
                                <input type="number" id="rate_hour" name="rate_hour" class="form-control"
                                       value="{{ $user->rate_hour }}">
                            </div>
                            <div class="form-group">
                                <label for="status">@lang('users.form.status')</label>
                                <select name="status" class="form-control text-uppercase"
                                        id="status" {{$selectedRole == 2 ? 'disabled' : ''}}>
                                    <option
                                        value="ACTIVE" {{ $user->status === 'ACTIVE' ? 'selected="selected"' : '' }}>@lang('users.form.active')</option>
                                    <option
                                        value="PENDING" {{ $user->status === 'PENDING' ? 'selected="selected"' : '' }}>@lang('users.form.pending')</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">@lang('voyager.generic.save')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('javascript')
    <script>
        var breadcrumb = $('.breadcrumb:nth-child(2)');
        breadcrumb.children().remove();
        breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
        breadcrumb.append("<li class='active'><a href='/admin/users/'>Użytkownicy</a></li>");
        breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
    </script>
@endsection
