@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Create Box Charge', ['resource' => 'Box Charge'])

    <li><a href="{{ route('admin.box.charge') }}">{{ __('Box Charge') }}</a></li>
    <li class="active">{{ __('Create Box Charge') }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.box.charge.store') }}" class="form-horizontal" id="box-charege-create-form"
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
                                        Box Charge Information
                                    </a>
                                </p>
                            </div>
                            <div id="box_charge_information" class="panel-collapse collapse in">
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
                                        <div class="form-group"><label for="weight"
                                                class="col-md-3 control-label text-left">Weight<span
                                                    class="m-l-5 text-red">*</span></label>
                                            <div class="col-md-9">
                                                <input type="number" name="weight" class="form-control" id="weight"
                                                    value="0">
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="charge"
                                                class="col-md-3 control-label text-left">Amount<span
                                                    class="m-l-5 text-red">*</span></label>
                                            <div class="col-md-9">
                                                <input type="number" name="charge" class="form-control" id="charge"
                                                    value="0">
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="status"
                                                class="col-md-3 control-label text-left">Status</label>
                                            <div class="col-md-9">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="status" class="" id="status"
                                                        value="1">
                                                    <label for="status">Enable the box charge</label>
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
