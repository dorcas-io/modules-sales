<div class="modal fade" id="product-inventory-modal" tabindex="-1" role="dialog" aria-labelledby="product-inventory-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="product-inventory-modalLabel">Add or Remove Stock</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

				<form action="{{ route('sales-product-single-stocks', [!empty($product) ? $product->id : '']) }}" id="form-product-inventory" method="post">
					{{ csrf_field() }}
					<div class="row">
	                    <div class="form-group col-md-6">
	                        <select class="form-control" id="action" name="action">
	                            <option value="add">Add/Increase Stock</option>
	                            <option value="subtract">Remove/Reduce Stock</option>
	                        </select>
	                        <label class="form-label" for="action">Action</label>
	                    </div>
	                    <div class="form-group col-md-6">
	                        <input class="form-control" id="quantity" type="number" name="quantity" maxlength="10" min="1">
	                        <label class="form-label" for="quantity">Quantity</label>
	                    </div>
	                    <div class="form-group col-md-12">
	                        <textarea class="form-control" id="description" name="description"></textarea>
	                        <label class="form-label" for="description">Describe the Activity</label>
	                    </div>
					</div>
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" form="form-product-inventory" class="btn btn-primary" name="save_action" value="1">Save Activity</button>
			</div>
		</div>
	</div>
</div>