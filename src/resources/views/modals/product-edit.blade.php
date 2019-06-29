<div class="modal fade" id="product-edit-modal" tabindex="-1" role="dialog" aria-labelledby="product-edit-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="product-edit-modalLabel">Edit Product</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

                <form method="post" id="form-product-edit" action="">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input class="form-control" id="name" type="text" v-model="product.name" name="name" maxlength="80" required>
                            <label class="form-label" for="name">Product Name</label>
                        </div>
                        <div class="form-group col-md-12">
                            <textarea class="form-control" id="description" name="description" v-model="product.description">@{{ product.description }}</textarea>
                            <label class="form-label" for="description">Description</label>
                        </div>
                        <div class="form-group col-md-12" v-if="typeof product.prices.data !== 'undefined'">
                            <product-price-control v-for="(price, index) in product.prices.data" :key="price.id"
                               :id_currency="price.id" :id_price="index + price.id"
                               :opening_price="price.unit_price.raw" :opening_currency="price.currency"
                               :index="index" v-on:remove="removeEntry"></product-price-control>
                        </div>
                        <div class="form-group col-md-12">
                            <a href="#" class="btn btn-info btn-sm" v-on:click.prevent="addPriceField">Add Currency Price</a>
                        </div>

                        <div class="form-group col-md-12">
                            <input class="form-control" id="default_price" type="number" name="default_price" v-model="product.default_unit_price.raw" min="0">
                            <label class="form-label" for="default_price">Default Fallback Unit Price (for other currencies)</label>
                        </div>
                        {{ method_field('PUT') }}
                    </div>
                </form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="image_submit" type="submit" form="form-product-edit" class="btn btn-primary" name="action"
                    value="add_product_image" v-if="!updating">Save Changes</button>
			</div>
		</div>
	</div>
</div>