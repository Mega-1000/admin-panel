{{--@extends('layouts.app')--}}
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
      integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
<div class="container">
    <div class="row justify-content-end">
        <form method="POST" action="{{ route('accept-deny') }}">
            @csrf
            <input type="hidden" value="{{$skip}}" name="skip">
            <input type="hidden" value="{{$package_type}}" name="package_type">
            <input type="hidden" value="{{$user_id}}" name="user_id">

            <button type="submit" name="action" value="accept" class="btn btn-success btn-lg">Akceptuj</button>
            <button type="submit" name="action" value="next" class="btn btn-danger btn-lg">Odrzuć</button>
        </form>
    </div>
</div>
