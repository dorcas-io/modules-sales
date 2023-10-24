<div class="modal fade" id="product-add-barcode-modal" tabindex="-1" role="dialog" aria-labelledby="product-edit-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="product-edit-modalLabel">Add Barcode Product</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
                <form method="post" id="form-product-barcode"  action="{{ url('msl/sales-product/'.$product->id) }}">
                    
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="form-label" for="barcode">Add Barcode Number</label>
                            <textarea class="form-control" 
                            id="barcode" 
                            name="barcode" 
                           >
                           
                        </textarea>
                        <span style="color:red;">Please enter 25 letters long barcode number</span>
                        </div>
                        {{ method_field('PUT') }}
                    </div>
                </form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button id="image_submit"
                         type="submit" 
                         form="form-product-barcode" 
                         class="btn btn-primary" name="action"
                         onclick="return maxlength(getElementById('barcode'), 25);"
                    value="add_product_image" v-if="!updating">Save Changes</button>
			</div>
		</div>
	</div>
</div>
<script>
    function maxlength(element, maxvalue){
       // var q = element.value.split(/[\s]+/).length;
        var textBoxVal = element.value.length;
        console.log(textBoxVal)
        if(textBoxVal  != maxvalue){
            var lettersShort = textBoxVal  - maxvalue;
            alert("Sorry, you have inputed "+ textBoxVal +" letters  into the "+
            "text area box. Barcode number needs to be "+
            maxvalue+" letters long . Please enter the appropriate length ,"+
            "your barcode is by at least "+lettersShort+" letters short");
            return false;
        }
    }
</script>