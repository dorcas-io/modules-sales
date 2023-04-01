<div class="modal fade" id="product-category-mapping-modal" tabindex="-1" role="dialog" aria-labelledby="product-edit-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="product-edit-modalLabel">Map Category To Parent Category</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
              <form method="post" id="form-map-product-category"  action="{{ url('msl/map-category/') }}">
                        {{ csrf_field() }}
                  <div class="row">
                    <div class="form-group col-md-12">
                    <div style="display: flex;justify-content:space-evenly;">
                        <div>
                              <label>Parent Category</label>
                              <select name="parent_category" class="form-control">
                                   @foreach($parent_categories as $index => $category) 
                                        <option value="{{ $category }}">
                                              {{ $category }}
                                        </option>
                                    @endforeach
                              </select>
                         </div>
                          <div>
                              <label>Business Defined Category</label>
                              <select name="business_category" class="form-control"  multiple>
                                        <option v-for="(category, index) in product.categories.data"
                                                :key="category.id"
                                                :value="category.id">
                                                        @{{ category.name }}
                                        </option>
                              </select>
                         </div>
                         <input type="hidden" value="{{ $product->id }}" name="product_id"/>
                    </div>
                </div>
                </div>
              </form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="image_submit"
                                                type="submit" 
                                                form="form-map-product-category" 
                                                class="btn btn-primary" name="action"
                                                value="add_product_image" v-if="!updating">Save Changes</button>
			</div>
		</div>
	</div>
</div>
