<div class="modal fade" id="shipping-route-modal" tabindex="-1" role="dialog" aria-labelledby="shipping-route-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="shipping-route-modalLabel">@{{ typeof shippingRoute.id !== 'undefined' ? 'Edit Shipping Route' : 'Add Shipping Route' }}</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="{{ route('sales-shipping-routes-post') }}" id="form-shipping-route" method="post">
					{{ csrf_field() }}
					<fieldset class="form-fieldset">
						<div class="row">
		                    <div class="form-group col-md-6">
		                        <input class="form-control" id="name" type="text" name="name" maxlength="80" v-model="shippingRoute.name" required>
		                        <label class="form-label" for="name">Route Title (e.g. InterCity)</label>
		                    </div>
		                    <div class="form-group col-md-6">
		                        <textarea class="form-control" id="description" name="description" v-model="shippingRoute.description"></textarea>
		                        <label class="form-label" for="description">Route Description</label>
		                    </div>
		                </div>
						<div class="row">
		                    <div class="form-group col-md-6">
		                        <select class="form-control" id="currency" name="currency" v-model="routeCurrency" required>
		                            <option value="NGN">NGN</option>
		                        </select>
		                        <label class="form-label" for="currency">Currency</label>
		                    </div>
		                    <div class="form-group col-md-6">
		                        <input class="form-control" id="price" type="number" name="price" maxlength="10" min="0" v-model="routePrice" required>
		                        <label class="form-label" for="price">Unit Price</label>
		                    </div>
		                </div>
						<div class="row">
		                </div>
					</fieldset>
					<input type="hidden" id="product_type" name="product_type" value="shipping">
					<input type="hidden" id="product_id" name="product_id" v-model="routeID">
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit"value="1" name="save_product" value="1" form="form-shipping-route" class="btn btn-primary">Save Route</button>
			</div>
		</div>
	</div>
</div>