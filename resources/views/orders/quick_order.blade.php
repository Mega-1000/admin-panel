@extends('layouts.datatable')

@section('app-header')
    <h1 class="page-title">
        <i class="fas fa-plus-circle"></i> Szybkie zadanie
    </h1>
@endsection

@section('table')
    <div class="row">
        <div class="col-lg-12">
            <form action="/admin/store-quick-order">
                <div class="form-group">
                    <textarea name="content" id="" cols="30" rows="10" class="form-control"
                              placeholder="Treść zadania"></textarea>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input id="1" name="master" class="form-check-input" type="checkbox">
                        <label for="1" class="form-check-label">Informacja dla mastera</label>
                    </div>
                    <div class="form-check">
                        <input id="2" name="consultant" class="form-check-input" type="checkbox">
                        <label for="2" class="form-check-label">Informacja dla konsultanta</label>
                    </div>
                    <div class="form-check">
                        <input id="3" name="warehouse" class="form-check-input" type="checkbox">
                        <label for="3" class="form-check-label">Informacja dla magazynu</label>
                    </div>
                    <div class="form-check">
                        <input id="4" name="accountant" class="form-check-input" type="checkbox">
                        <label for="4" class="form-check-label">Informacja dla księgowości</label>
                    </div>
                </div>

                <div class="form-group">
                    <button class="btn btn-primary">Dodaj</button>
                </div>
            </form>
        </div>
    </div>
@endsection
