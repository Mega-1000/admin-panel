@extends('layouts.app')
@section('app-header')
    <h1 class="page-title">
        <i class="voyager-person"></i> @lang('column_visibilities.modules.edit')
        <a style="margin-left:15px" href="{{ action('ColumnVisibilitiesController@moduleIndex') }}"
           class="btn btn-info install pull-right">
            <span>@lang('column_visibilities.modules.lists')</span>
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

                        <form action="{{ action('ColumnVisibilitiesController@moduleUpdate', ['id' => $module->id]) }}"
                              method="POST">
                            {{ csrf_field() }}
                            {{ method_field('put') }}
                            <div class="customer-general" id="general">
                                <div class="form-group">
                                    <label for="name">@lang('column_visibilities.modules.form.name')</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ $module->name  }}">
                                </div>
                                <div class="form-group">
                                    <label for="table_name">@lang('column_visibilities.modules.form.table_name')</label>
                                    <input type="text" class="form-control" id="table_name" name="table_name"
                                           value="{{$module->table_name}}">
                                </div>


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
            breadcrumb.append("<li class='active'><a href='/admin/columnVisibilities/modules'>Widoczność kolumn moduły</a></li>");
            breadcrumb.append("<li class='disable'><a href='javascript:void()'>Edytuj</a></li>");


        });
    </script>
@endsection
