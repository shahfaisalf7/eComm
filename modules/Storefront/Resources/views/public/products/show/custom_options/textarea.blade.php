<div class="form-group variant-input">
    <div class="row">
        <div class="col-lg-8">
            <label for="option-{{ $option->id }}">
                {!!
                    $option->name .
                    $option->values->first()->formattedPriceForProduct($product) .
                    ($option->is_required ? '<span>*</span>' : '')
                !!}
            </label>
        </div>

        <div class="col-lg-18">
            <div class="form-input">
                <textarea
                    class="form-control"
                    name="options[{{ $option->id }}]"
                    id="option-{{ $option->id }}"
                    x-model="cartItemForm.options[{{ $option->id }}]"
                ></textarea>
            </div>

            <template x-if="errors.has('{{ "options.{$option->id}" }}')">
                <span class="error-message" x-text="errors.get('{{ "options.{$option->id}" }}')"></span>
            </template>
        </div>
    </div>
</div>
