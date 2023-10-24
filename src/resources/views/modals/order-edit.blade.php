<div class="modal fade" id="order-edit-modal" tabindex="-1" role="dialog" aria-labelledby="order-edit-modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="order-edit-modalLabel">Edit Order</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">

                <form method="post" id="form-order-edit" action="">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <input class="form-control" id="name" type="text" v-model="order.title" name="name" maxlength="80" required>
                            <label class="form-label" for="name">Title</label>
                        </div>
                        <div class="form-group col-md-12">
                            <textarea class="form-control" id="description" name="description" v-model="order.description">@{{ order.description }}</textarea>
                            <label class="form-label" for="description">Description</label>
                        </div>
                        <div class="form-group col-md-12">
                            <input type="text" class="custom-datepicker" name="due_at" id="due_at" v-model="order.due_at">
                            <label class="form-label" for="due_at">Due Date</label>
                        </div>
                        <div class="form-group col-md-12" v-if="typeof order.due_at !== 'undefined' && order.due_at !== null">
                            <p>Invoice Reminders</p>
                            <div class="switch">
                                <label>
                                    Off
                                    <input type="checkbox" name="reminders_on" v-model="order.has_reminders">
                                    <span class="lever"></span>
                                    On
                                </label>
                            </div>
                        </div>
                        <div class="col s12 mb-3">
                            {{ method_field('PUT') }}
                        </div>
                    </div>
                </form>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="submit" form="form-order-edit" class="btn btn-primary" :class="{'btn-loading':updating}" name="action" v-on:click.prevent="updateDetails">Save Changes</button>
			</div>
		</div>
	</div>
</div>