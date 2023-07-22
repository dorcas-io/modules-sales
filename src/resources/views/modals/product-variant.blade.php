

<div class="modal fade" id="product-variant-modal" tabindex="-1" role="dialog" aria-labelledby="product-variant-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="product-variant-modalLabel">@{{ typeof variant.id !== 'undefined' ? 'Edit' : 'Add' }} Variant of @{{ product.name }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="{{ route('sales-variant-post') }}" id="form-product-variant" method="post">
					{{ csrf_field() }}
					<fieldset class="form-fieldset">
						<div class="row">
		                    <div class="form-group col-md-6">
		                        <input class="form-control" id="product_variant" type="text" name="product_variant" maxlength="15" v-model="variant.product_variant" required>
		                        <label class="form-label" for="product_variant">Variant Title (e.g. Blue)</label>
		                    </div>
		                    <div class="form-group col-md-6">
									
								<select class="form-control" id="product_variant_type" name="product_variant_type" v-model="variant.product_variant_type" onChange="checkVaraiant()" required>
									<option value="">Select Variant Type</option>
								  	<option v-for="variantType in variantTypes" v-bind:value="variantType">
								    	@{{ variantType }}
								  	</option>
								</select>
		                        <label class="form-label" for="product_variant_type">Variant Type (e.g. Color)</label>
		                    </div>
		                </div>
						<div class="row">
		                    <div class="form-group col-md-6">
		                        <select class="form-control" id="currency" name="currency" required><!-- v-if="typeof variant.prices !== 'undefined'" v-model="variant.prices.data[0].currency"-->
		                            <option value="NGN">NGN</option>
		                        </select>
		                        <label class="form-label" for="currency">Currency</label>
		                    </div>
		                    <div class="form-group col-md-6">
		                        <input class="form-control" id="price" type="number" name="price" maxlength="10" min="0" required><!-- v-if="typeof variant.prices !== 'undefined'" v-model="variant.prices.data[0].unit_price.raw"-->
		                        <label class="form-label" for="price">Unit Price</label>
		                    </div>
		                </div>
{{--							 <div class="row" id="variant_quantity">--}}
{{--								<div class="form-group col-md-12">--}}
{{--									 <input class="form-control" id="quantity" type="number" name="quantity" maxlength="10" min="0" ><!-- v-if="typeof variant.prices !== 'undefined'" v-model="variant.prices.data[0].unit_price.raw"-->--}}
{{--									 <label class="form-label" for="price">Quantity</label>--}}
{{--								</div>--}}
{{--						  </div>--}}
						<div class="row" >
						<div class="form-group col-md-12">
							<input class="form-control"  id="quantity" type="number" name="quantity" value="1"  maxlength="10" min="0">
							<label class="form-label" for="quantity">Stock Available</label>
						</div>
						</div>
						<div class="row">
		                    <div class="form-group col-md-12">
		                        <textarea class="form-control" id="description" name="description" v-model="variant.description"></textarea>
		                        <label class="form-label" for="description">Variant Description</label>
		                    </div>
		                </div>
					</fieldset>
					<input type="hidden" id="product_parent" name="product_parent" v-model="product.id">
					<input type="hidden" id="product_type" name="product_type" value="variant">
					<input type="hidden" id="name" name="name" v-model="product.name">
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit"value="1" name="save_product" value="1" form="form-product-variant" class="btn btn-primary">Save Product Variant</button>
			</div>
		</div>
	</div>
</div>