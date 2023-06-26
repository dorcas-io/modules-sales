<div class="modal fade" id="product-new-modal" tabindex="-1" role="dialog" aria-labelledby="product-new-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="product-new-modalLabel">Add Product</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<form action="" id="form-product-new" method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<fieldset class="form-fieldset">
						<div class="row">
		                    <div class="form-group col-md-6">
		                        <input class="form-control" id="name" type="text" name="name" maxlength="80" required>
		                        <label class="form-label" for="name">Product Name</label>
		                    </div>
		                    <div class="form-group col-md-6">
		                        <textarea class="form-control" id="description" name="description"></textarea>
		                        <label class="form-label" for="description">Product Description</label>
		                    </div>
		                </div>
						<div class="row">
		                    <div class="form-group col-md-6">
		                        <input class="form-control" id="price" type="number" name="price" maxlength="10" min="0">
		                        <label class="form-label" for="price">Unit Price</label>
		                    </div>
		                    <div class="form-group col-md-6">
		                        <select class="form-control" id="currency" name="currency">
		                            <option value="{{ env('SETTINGS_CURRENCY', 'NGN') }}">{{ env('SETTINGS_CURRENCY', 'NGN') }}</option>
		                        </select>
		                        <label class="form-label" for="currency">Currency</label>
		                    </div>
						</div>
						<div class="row">
							<div class="form-group col-md-6">
								 <select name="category" id="category" class="form-control">
									 @if(count($categories) > 0)
										 @foreach($categories as $index => $category)
										 <option value="{{$category->id}}">{{$category->name}}</option>
										 @endforeach
									 @endif
								 </select>
								<label class="form-label" for="category">Product Category</label>
							</div>
		                    <div class="form-group col-md-6">
		                        <input class="form-control" id="stock" type="number" value="1" name="stock" maxlength="10" min="0">
		                        <label class="form-label" for="stock">Stock Available</label>
		                    </div>
						</div>
						<div class="row">
							<div class="form-group col-md-12">
{{--								<input type="file" name="image" accept="image/*"  id="image"  class="custom-file-input form-control" required>--}}
{{--								<input class="form-control" id="image" type="file" name="image"  >--}}
								<label class="form-label" for="image">Upload product image</label>
								<div class="file-upload">
									<label for="upload" class="file-upload-label">Choose a product image (optional)</label>
									<input type="file" id="upload" class="file-upload-input" name="image">
								</div>
							</div>
						</div>
					</fieldset>
				</form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit"value="1" name="save_product" value="1" form="form-product-new" class="btn btn-primary">Save Product</button>
			</div>
		</div>
	</div>
</div>