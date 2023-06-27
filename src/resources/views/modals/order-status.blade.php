<div class="modal fade" id="order-status-modal" tabindex="-1" role="dialog" aria-labelledby="order-edit-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="order-edit-modalLabel">Update Status</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

                <form method="post" id="form-order-update-status" action="">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-control" name="status" > 
                              <option value="pending">Pending</option>
                              <option value="delivered">Delivered</option>
                              <option value="completed">Completed</option>
							  <option value="ready to ship">Ready to Ship</option>
							  <option value="shipped">Shipped</option>
                            </select>
                        </div>
                       
                       
                    </div>
                </form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" form="form-order-update-status" class="btn btn-primary" :class="{'btn-loading':updating}" name="action" v-on:click.prevent="updateOrderStatus">Save Changes</button>
			</div>
		</div>
	</div>
</div>