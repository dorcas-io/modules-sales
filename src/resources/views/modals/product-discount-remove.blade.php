<div class="modal fade" id="product-discount-remove-modal" tabindex="-1" role="dialog" aria-labelledby="product-discount-remove-modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="product-inventory-remove-modalLabel">Add / Reduce / Remove Discount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="alert alert-primary" role="alert">
                    Please note ! once you uncheck the box discount will be removed, but you can as well change the value without unchecking the box if you only want to update discount value
                </div>
                <form action="{{ route('sales-product-remove-discount', [!empty($product) ? $product->id : '']) }}" id="form-product-discount-remove" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input class="form-control" id="discount_value" type="number" name="discount_value"  value="{{$product->discount_value}}">
                            <label class="form-label" for="discount_value">Discount</label>
                        </div>
                        <div class="form-group col-md-12">
                            <input type="checkbox" id="myCheckbox" name="isDiscount" checked>
                            <label for="myCheckbox" class="custom-checkbox"></label>
                            <p>Apply Discount / Remove Discount</p>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" form="form-product-discount-remove" class="btn btn-primary" name="save_action" value="1">Add / Reduce / Remove  Discount</button>
            </div>
        </div>
    </div>
</div>
