<div class="modal fade" id="product-image-edit-modal" tabindex="-1" role="dialog" aria-labelledby="product-image-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="product-image-modalLabel">Update Product Image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('sales-product-single-image-update',[$product->id]) }}" id="form-product-image-update" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <fieldset class="form-fieldset">
                        <div class="form-group row">
                            <div class="form-label">Product Image</div>
                            <div class="custom-file">
                                <input type="file" name="image" accept="image/*" v-on:change="productImageUpdateCheck" id="imageEdit" ref="image" class="custom-file-input" required>
                                <label id="image_update_label" class="custom-file-label">Select Image</label>
                                <input type="hidden" v-model="productImageId.id" name="product_image_id"/>
                                <output id="output"></output>
                            </div>
                            <small id="image_edit_message">Any attachment must not exceed 100KB in size</small>

                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="image_edit_submit" type="submit" form="form-product-image-update" class="btn btn-primary" name="action"
                        value="update_product_image">Upload Image</button>
            </div>
        </div>
    </div>
</div>