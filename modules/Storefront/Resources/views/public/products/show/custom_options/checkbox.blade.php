<div class="form-group variant-check">
    <div class="row">
        <div class="col-lg-18">
            <label>
                {!!
                    $option->name .
                    ($option->is_required ? '<span>*</span>' : '')
                !!}
            </label>
        </div>

        <div class="col-lg-18">
            @foreach ($option->values as $value)
                <div class="form-check">
                    <input
                        type="checkbox"
                        name="options[{{ $option->id }}][]"
                        id="option-{{ $option->id }}-value-{{ $value->id }}"
                        value="{{ $value->id }}"
                        @change="updateCheckboxTypeOptionValue({{ $option->id }}, $event)"
                    >

                    <label for="option-{{ $option->id }}-value-{{ $value->id }}">
                        {!!
                            $value->label .
                            $value->formattedPriceForProduct($product)
                        !!}
                    </label>
                </div>
            @endforeach
            
            <template x-if="errors.has('{{ "options.{$option->id}" }}')">
                <span class="error-message" x-text="errors.get('{{ "options.{$option->id}" }}')"></span>
            </template>
        </div>
    </div>
</div>
