@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Division Create', ['resource' => 'Division Create'])

    <li><a href="{{ route('admin.geo.division.sidebar') }}">{{ __('Divisions') }}</a></li>
    <li class="active">{{ 'Divisions' }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.geo.division.store') }}" class="form-horizontal" id="division-create-form"
        novalidate>
        {{ csrf_field() }}
        <div class="accordion-content clearfix">
            <div class="col-lg-3 col-md-4">
                <div class="accordion-box">
                    <div class="panel-group" id="FlashSaleTabs">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <p class="panel-title">
                                    <a>
                                        Division Information
                                    </a>
                                </p>
                            </div>
                            <div id="division_information" class="panel-collapse collapse in">
                                <div class="panel-body">
                                    <ul class="accordion-tab nav nav-tabs">
                                        <li class="active ">
                                            <a href="#" data-toggle="tab">General</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                <div class="col-lg-9 col-md-8">
                    <div class="accordion-box-content">
                        <div class="tab-content clearfix">
                            <div class="tab-pane fade in active" id="general">
                                <p class="tab-content-title">General</p>
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group"><label for="name"
                                                class="col-md-3 control-label text-left">Name<span
                                                    class="m-l-5 text-red">*</span></label>
                                            <div class="col-md-9">
                                                <input type="text" name="name" class="form-control" id="name">
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="code"
                                                class="col-md-3 control-label text-left">description<span
                                                    class="m-l-5 text-red">*</span></label>
                                            <div class="col-md-9">
                                                <textarea name="description" class="form-control" id="description" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="status"
                                                class="col-md-3 control-label text-left">Status</label>
                                            <div class="col-md-9">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="status" class="" id="status"
                                                        value="1">
                                                    <label for="status">Enable the division</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10 col-md-offset-2">
                                    <button type="submit" class="btn btn-primary"
                                        data-loading="">{{ __('Save') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')
@endpush
