@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Edit Product Charge', ['resource' => 'Edit Product Charge'])
    @slot('subtitle', $product_charge->charge)

    <li><a href="{{ route('admin.product.charge') }}">{{ __('Product Charge') }}</a></li>
    <li class="active">{{ __('Edit Product Charge') }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.product.charge.update', $product_charge) }}" class="form-horizontal"
        id="product-charge-edit-form" novalidate>
        {{ csrf_field() }}
        {{ method_field('put') }}

        <div class="accordion-content clearfix">
            <div class="col-lg-3 col-md-4">
                <div class="accordion-box">
                    <div class="panel-group" id="FlashSaleTabs">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <p class="panel-title">
                                    <a>
                                        Product Charge Information
                                    </a>
                                </p>
                            </div>
                            <div id="product_charge_information" class="panel-collapse collapse in">
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
                                                    value="{{ $product_charge->weight }}">
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="charge"
                                                class="col-md-3 control-label text-left">Amount<span
                                                    class="m-l-5 text-red">*</span></label>
                                            <div class="col-md-9">
                                                <input type="number" name="charge" class="form-control" id="charge"
                                                    value="{{ $product_charge->charge }}">
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="status"
                                                class="col-md-3 control-label text-left">Status</label>
                                            <div class="col-md-9">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="status" class="" id="status"
                                                        value="1" {{ $product_charge->status ? 'checked' : '' }}>
                                                    <label for="status">Enable the product charge</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-10 col-md-offset-2">
                                    <button type="submit" class="btn btn-primary"
                                        data-loading="">{{ __('Update') }}</button>
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
