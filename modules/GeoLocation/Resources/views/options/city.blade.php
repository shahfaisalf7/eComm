<select name="city_id" class="form-control" id="city_id">
    <option value="">{{ __('Select City') }}</option>
    @foreach ($cities as $city)
        <option value="{{ $city->id }}" {{ old('division_id', $city_id) == $city->id ? 'selected' : '' }}>
            {{ $city->name }}
        </option>
    @endforeach
</select>
