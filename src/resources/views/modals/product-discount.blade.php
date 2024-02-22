<div class="modal fade" id="product-discount-modal" tabindex="-1" role="dialog" aria-labelledby="product-discount-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="product-inventory-modalLabel">Add Discount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form action="{{ route('sales-product-add-discount', [!empty($product) ? $product->id : '']) }}" id="form-product-discount" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input class="form-control" id="discount_value" type="number" name="discount_value" >
                            <label class="form-label" for="discount_value">Discount</label>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="form-product-discount" class="btn btn-primary" name="save_action" value="1">Add Discount</button>
            </div>
        </div>
    </div>
</div>
