@extends('layouts.datatable')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('employees.edit')
        <a style="margin-left: 15px;" href="{{ route('firms.edit', ['firm' => $employee->firm_id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('firms.back_to_edit')</span>
        </a>
    </h1>
@endsection

@section('table')
    @if($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ action('EmployeesController@update', ['id' => $employee->id])}}" method="POST">
        {{ csrf_field() }}
        {{ method_field('put') }}
        <div class="panel-body">
            <div class="employees-general" id="general">
                <div>
                    <label>@lang('employees.form.firm_visibilty')</label>
                    <input type="checkbox" id="firm_visibility" name="firm_visibility"
                           val="0"{{$employee->firm_visibility == 1 ? '' : 'checked' }}>
                </div>
                <div class="form-group">
                    <label for="firstname">@lang('employees.form.firstname')</label>
                    <input type="text" class="form-control" id="firstname" name="firstname"
                           value="{{ $employee->firstname }}">
                    <label>@lang('employees.form.visibility')</label>
                    <input type="checkbox" id="firstname_visibility" name="firstname_visibility"
                           val="0"{{$employee->firstname_visibility == 1 ? '' : 'checked' }}>
                </div>
                <div class="form-group">
                    <label for="lastname">@lang('employees.form.lastname')</label>
                    <input type="text" class="form-control" id="lastname" name="lastname"
                           value="{{ $employee->lastname }}">
                    <label>@lang('employees.form.visibility')</label>
                    <input type="checkbox" id="lastname_visibility" name="lastname_visibility"
                           val="0"{{$employee->lastname_visibility == 1 ? '' : 'checked' }}>
                </div>
                <div class="form-group">
                    <label for="email">@lang('employees.form.email')</label>
                    <input type="email" class="form-control" id="email" name="email"
                           value="{{ $employee->email }}">
                    <label>@lang('employees.form.visibility')</label>
                    <input type="checkbox" id="email_visibility" name="email_visibility"
                           val="0" {{$employee->email_visibility == 1 ? '' : 'checked' }}>
                </div>
                <div class="form-group">
                    <label for="phone">@lang('employees.form.phone')</label>
                    <input type="text" class="form-control" id="phone" name="phone"
                           value="{{ $employee->phone }}">
                    <label>@lang('employees.form.visibility')</label>
                    <input type="checkbox" id="phone_visibility" name="phone_visibility"
                           val="0"{{$employee->phone_visibility == 1 ? '' : 'checked' }}>
                </div>
                <div class="form-group">
                    <label for="job_position">@lang('employees.form.job_position')</label>
                    @if (!empty($roles))
                        @php
                            $roles;
                            $count=1;
                            $attachedRoles;
                        @endphp
                        @foreach($roles as $role)
                            @php
                                $checked=0;
                            @endphp
                            @foreach($attachedRoles as $attachedRole)
                                @if ($attachedRole->id == $role->id)
                                    @php
                                        $checked=2;
                                    @endphp
                                @endif
                            @endforeach

                            <div>
                                <label>{{$role->name}}</label>
                                @if ($checked == 0)
                                    <input type="checkbox" id="role{{$count}}" name="role{{$count}}"
                                           value="{{$role->id}}">
                                @elseif ($checked == 2)
                                    <input type="checkbox" id="role{{$count}}" name="role{{$count}}"
                                           value="{{$role->id}}" checked>
                                @endif
                            </div>
                            @php
                                $count++;
                            @endphp
                        @endforeach
                        <input type="hidden" id="rolecount" name="rolecount" value="{{$count-1}}">
                    @endif
                </div>
                <div class="form-group">
                    <label for="person_number">@lang('employees.form.numer')</label>
                    <input type="number" class="form-control" id="person_number" name="person_number"
                           value="{{ $employee->person_number }}">
                </div>
                <div class="form-group">
                    <label for="job_position">@lang('employees.form.magazines')</label>
                    @if (!empty($warehouses))
                        @php
                            $warehouses;
                            $count=1;
                            $attachedWarehouses;
                        @endphp
                        @foreach($warehouses as $warehouse)
                            @php
                                $checked=0;
                            @endphp
                            @foreach($attachedWarehouses as $attachedWarehouse)
                                @if ($attachedWarehouse->id == $warehouse->id)
                                    @php
                                        $checked=2;
                                    @endphp
                                @endif
                            @endforeach
                            <div>
                                <label>{{$warehouse->symbol}}</label>
                                @if ($checked == 0)
                                    <input type="checkbox" id="warehouse{{$count}}" name="warehouse{{$count}}"
                                           value="{{$warehouse->id}}">
                                @elseif ($checked == 2)
                                    <input type="checkbox" id="warehouse{{$count}}" name="warehouse{{$count}}"
                                           value="{{$warehouse->id}}" checked>
                                @endif
                            </div>
                            @php
                                $count++;
                            @endphp
                        @endforeach
                        <input type="hidden" id="magazinecount" name="magazinecount" value="{{$count-1}}">
                    @endif
                </div>
                <div class="form-group">
                    <label for="comments">@lang('employees.form.comments')</label>
                    <textarea rows="4" cols="50" class="form-control" id="comments"
                              name="comments"
                    >{{ $employee->comments}}</textarea>
                    <label>@lang('employees.form.visibility')</label>
                    <input type="checkbox" id="comments_visibility" name="comments_visibility"
                           val="0"{{$employee->comments_visibility == 1 ? '' : 'checked' }}>
                </div>
                <div class="form-group">
                    <label for="additional_comments">@lang('employees.form.additional_comments')</label>
                    <textarea rows="4" cols="50" class="form-control" id="additional_comments"
                              name="additional_comments"
                    >{{ $employee->additional_comments}}</textarea>
                </div>
                <div class="form-group">
                    <label for="faq">@lang('employees.form.faq')</label>
                    <textarea rows="4" cols="50" class="form-control" id="faq"
                              name="faq"
                    >{{ $employee->faq }}</textarea>
                </div>
                <div class="form-group">
                    <label for="postal_code">@lang('employees.form.postal_code')</label>
                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                           value="{{ $employee->postal_code }}">
                    <label>@lang('employees.form.visibility')</label>
                    <input type="checkbox" id="postal_code_visibility" name="postal_code_visibility"
                           val="0"{{$employee->postal_code_visibility == 1 ? '' : 'checked' }}>
                </div>
                <div class="form-group">
                    <label for="latitude">@lang('firms.form.address.latitude')</label>
                    <input type="text" class="form-control disabled" id="latitude" name="latitude"
                           value="{{ $employee->latitude }}" disabled>
                </div>
                <div class="form-group">
                    <label for="longitude">@lang('firms.form.address.longitude')</label>
                    <input type="text" class="form-control" id="longitude" name="longitude"
                           value="{{ $employee->longitude }}" disabled>
                </div>
                <div class="form-group">
                    <label for="radius">@lang('employees.form.radius')</label>
                    <input type="text" class="form-control" id="radius" name="radius"
                           value="{{ $employee->radius }}">
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
    </form>
@endsection

@section('javascript')
    <script>
        $(document).ready(function () {
            var breadcrumb = $('.breadcrumb:nth-child(2)');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/{{$employee->firm_id}}/edit'>Firmy</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/firms/{{$employee->firm_id}}/edit#employees'>Pracownicy</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");
        })
    </script>

@endsection
