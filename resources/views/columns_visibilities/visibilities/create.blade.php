@extends('layouts.app')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('column_visibilities.visibilities.creates') {{$roleName}} @lang('column_visibilities.visibilities.creates2') {{$moduleName}}
        <a style="margin-left:15px" href="{{ route('columnVisibilities.modules.roles.visibilities.index',['role_id'=>$role_id,'module_id'=>$module_id]) }}"
           class="btn btn-info install pull-right">
            <span>@lang('column_visibilities.visibilities.lists')</span>
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

                        <form action="{{ action('ColumnVisibilitiesController@visibilitiesStore',['module_id'=>$module_id,'role_id'=>$role_id]) }}"
                              method="POST">
                            {{ csrf_field() }}
                            {{ method_field('post') }}
                            <div class="customer-general" id="general">
                                <div class="form-group">
                                    <label for="display_name">@lang('column_visibilities.visibilities.form.name')</label>
                                    <input type="text" class="form-control" id="display_name" name="display_name">
                                </div>
                                
                                Zaznacz kolumny które mają być widoczne
                                @if($isNumberColumns)
                                     @foreach($columns as $key => $row)
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="columnName[{{$row}}]" id="columnName[{{$row}}]">
                                            <label class="form-check-label" for="columnName[{{$row}}]">{{$key}}</label>
                                            </div>
                                    @endforeach
                                @else
                                    @foreach($columns as $key => $row)
                                        @if(\Lang::has($lang.'.table.'.$row))
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" name="columnName[{{$row}}]" id="columnName[{{$row}}]">
                                                <label class="form-check-label" for="columnName[{{$row}}]">@lang($lang.'.table.'.$row)</label>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif


                            </div>
                            {{--@if(App\Helpers\Helper::checkRole('customers', 'standard_firstname') === true)--}}
                            {{--<div class="form-group">--}}
                            {{--<label for="standard_firstname">@lang('customers.form.standard_firstname')</label>--}}
                            {{--<input type="text" class="form-control" id="standard_firstname"--}}
                            {{--name="standard_firstname"--}}
                            {{--value="{{ $customerAddressStandard->first->id->firstname  }}">--}}
                            {{--</div>--}}
                            {{--@endif--}}


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
        $(document).ready(function () {
            var breadcrumb = $('.breadcrumb:nth-child(2)');

            breadcrumb.children().remove();
            breadcrumb.append("<li class='active'><a href='/admin/'><i class='voyager-boat'></i>Panel</a></li>");
            breadcrumb.append("<li class='active'><a href='/admin/columnVisibilities/modules'><i class='voyager-boat'></i>widoczność kolumn Moduły </a></li>");
            breadcrumb.append("<li class='active'><a href='{{route('columnVisibilities.modules.roles.index',['role_id'=>$role_id])}}'><i class='voyager-boat'></i>Role</a></li>");
            breadcrumb.append("<li class='active'><a href='{{route('columnVisibilities.modules.roles.visibilities.index',['module_id'=>$module_id,'role_id'=>$role_id])}}'>Widoczności</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Utwórz</a></li>");


        });
    </script>
@endsection
