@extends('admin::layout')

@component('admin::components.page.header')
    @slot('title', 'Zone Edit', ['resource' => 'Zone Edit'])
    @slot('subtitle', $zone->name)

    <li><a href="{{ route('admin.geo.zones.sidebar') }}">{{ __('Zone') }}</a></li>
    <li class="active">{{ __('Zone Edit') }}</li>
@endcomponent

@section('content')
    <form method="POST" action="{{ route('admin.geo.zones.update', $zone) }}" class="form-horizontal" id="zone-edit-form"
        novalidate>
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
                                        Zone Information
                                    </a>
                                </p>
                            </div>
                            <div id="zone_information" class="panel-collapse collapse in">
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
                                                <input type="text" name="name" class="form-control" id="name"
                                                    value="{{ $zone->name }}">
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="code"
                                                class="col-md-3 control-label text-left">description<span
                                                    class="m-l-5 text-red">*</span></label>
                                            <div class="col-md-9">
                                                <select name="division_id" class="form-control" id="division_id">
                                                    <option value="">{{ __('Select Division') }}</option>
                                                    @foreach ($divisions as $division)
                                                        <option value="{{ $division->id }}"
                                                            {{ $selected_division_id == $division->id ? 'selected' : '' }}>
                                                            {{ $division->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="code" class="col-md-3 control-label text-left">City<span
                                                    class="m-l-5 text-red">*</span></label>
                                            <div class="col-md-9">
                                                <select name="city_id" class="form-control" id="city_id">
                                                    <option value="">{{ __('Select City') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group"><label for="status"
                                                class="col-md-3 control-label text-left">Status</label>
                                            <div class="col-md-9">
                                                <div class="checkbox">
                                                    <input type="checkbox" name="status" class="" id="status"
                                                        value="1" {{ $zone->status ? 'checked' : '' }}>
                                                    <label for="status">Enable the zone</label>
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
    <script>
        $(document).ready(function() {
            getCity("{{ $selected_division_id }}");

            $('#division_id').on('change', function() {
                let divisionId = $(this).val();
                getCity(divisionId);
            });
        });

        function getCity(divisionId) {
            if (divisionId) {
                $.ajax({
                    url: "{{ route('admin.geo.cities.byDivision') }}",
                    type: 'GET',
                    data: {
                        division_id: divisionId,
                        city_id: "{{ $zone->city_id }}",
                    },
                    success: function(data) {
                        $('#city_id').html(data);
                    },
                    error: function(xhr) {
                        console.error('Error loading cities:', xhr);
                    }
                });
            } else {
                $('#city_id').html('');
            }
        }
    </script>
@endpush
