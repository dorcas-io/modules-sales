<div class="modal fade" id="product-image-modal" tabindex="-1" role="dialog" aria-labelledby="product-image-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="product-image-modalLabel">Add Product Image</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="{{ route('sales-product-single-images', [$product->id]) }}" id="form-product-image" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<fieldset class="form-fieldset">
						<div class="form-group row">
							<div class="form-label">Product Image  	<small id="image_message">Any attachment must not exceed 100KB in size</small> </div>
								<div class="custom-file">
									<input type="file" name="image" accept="image/*" v-on:change="productImageCheck" id="imageUpload" ref="image" class="custom-file-input" required>
									<label id="image_label" class="custom-file-label">Select Image</label>
								</div>
							<br><br>
							<output id="outputProductImage"></output>
						</div>
					</fieldset>
				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="image_submit" type="submit" form="form-product-image" class="btn btn-primary" name="action"
                    value="add_product_image">Upload Image</button>
			</div>
		</div>
	</div>
</div>