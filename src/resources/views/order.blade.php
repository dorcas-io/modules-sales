@extends('layouts.tabler')
@section('head_css')
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

	<div class="col-md-9" id="order-information">

	    <div class="row col-md-12 row-cards row-deck" id="order-statistics">
	    	<div class="col-sm-6 col-lg-3">
	    		<div class="card p-3">
	    			<div class="d-flex align-items-center">
	    				<span class="stamp stamp-md bg-blue mr-3">
	    					<i class="fe fe-file-text"></i>
	    				</span>
	    				<div>
	    					<h4 class="m-0">@{{ order.invoice_number }}</h4>
	    					<small class="text-muted">Invoice #</small>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    	<div class="col-sm-6 col-lg-3">
	    		<div class="card p-3">
	    			<div class="d-flex align-items-center">
	    				<span class="stamp stamp-md bg-blue mr-3">
	    					<i class="fa fa-money"></i>
	    				</span>
	    				<div>
	    					<h4 class="m-0">@{{ order.currency }} @{{ order.amount.formatted }}</h4>
	    					<small class="text-muted">Total Cost</small>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    	<div class="col-sm-6 col-lg-3">
	    		<div class="card p-3">
	    			<div class="d-flex align-items-center">
	    				<span class="stamp stamp-md bg-blue mr-3">
	    					<i class="fe fe-calendar"></i>
	    				</span>
	    				<div>
	    					<h4 class="m-0">@{{ dueDate }}</h4>
	    					<small class="text-muted">Due By</small>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    	<div class="col-sm-6 col-lg-3">
	    		<div class="card p-3">
	    			<div class="d-flex align-items-center">
	    				<span class="stamp stamp-md bg-blue mr-3">
	    					<i class="fe fe-file-text"></i>
	    				</span>
	    				<div>
	    					<h4 class="m-0">@{{ reminderIsOn }}</h4>
	    					<small class="text-muted">Reminder On?</small>
	    				</div>
	    			</div>
	    		</div>
	    	</div>
	    </div>

	    <div class="row" id="order-details">

		    <div class="col-md-4">
		        <div class="card card-profile">
{{--		            <div class="card-header" v-bind:style="{ 'background-image': 'url(' + backgroundImage + ')' }"></div>--}}
		            <div class="card-body text-center">
{{--		                <img class="card-profile-img" v-bind:src="photo">--}}
		                <h3 class="mb-3">Order # @{{ order.invoice_number }}</h3>
		                <p class="mb-4">
		                    @{{ order.description }}
		                </p>
		                <div>&nbsp;</div>
		                <button v-on:click.prevent="editOrder" class="btn btn-outline-primary btn-sm text-center">
		                    <span class="fa fa-sliders"></span> Edit Orders
		                </button>
							 <button v-on:click.prevent="updateStatus" class="btn btn-outline-primary btn-sm text-center">
								<span class="fa fa-sliders"></span> Update Status
						  </button>
		            </div>
		            @include('modules-sales::modals.order-edit')
						@include('modules-sales::modals.order-status')
		        </div>

		        <div class="card">
					<div class="card-header">
						<h3 class="card-title">Products</h3>
					</div>
					<div class="card-body o-auto" style="height: 15rem">
						<ul class="list-unstyled list-separated">
							<li class="list-separated-item" v-if="typeof order.inline_product !== 'undefined'">
								<div class="row align-items-center">
									<div class="col-auto">
										<span class="avatar avatar-md d-block"></span>
									</div>
									<div class="col">
										<div>
											<a class="text-inherit"><strong>@{{ order.inline_product.name }}</strong></a>
										</div>
										<small class="d-block item-except text-sm text-muted h-1x">@{{ order.currency }} @{{ order.inline_product.unit_price }} / unit = @{{ order.currency }} @{{ order.inline_product.unit_price * order.inline_product.quantity }}</small>
									</div>
									<div class="col-auto">
										<span class="text-right badge badge-default">x @{{ order.inline_product.quantity }}</span>
									</div>
								</div>
							</li>
							<li class="list-separated-item" v-for="product in order.products.data">
								<div class="row align-items-center" style="cursor: pointer;">
									<div class="col-auto">
										<span class="avatar avatar-md d-block"></span>
									</div>
									<div class="col">
										<div>
											<a href="#!" v-on:click.prevent="showProduct(product.id)" class="text-inherit"><strong>@{{ product.name }}</strong></a>
										</div>
										<small class="d-block item-except text-sm text-muted h-1x">@{{ order.currency }} @{{ product.sale.unit_price }} / unit = @{{ order.currency }} @{{ product.sale.unit_price * product.sale.quantity }}</small>
									</div>
									<div class="col-auto">
										<span class="text-right badge badge-default">x @{{ product.sale.quantity }}</span>
									</div>
								</div>
							</li>
						</ul>
					</div>
		        </div>

		    </div>


		    <div class="col-md-8">
		        <div class="card">
		            <div class="card-status bg-blue"></div>
		            <div class="card-header">
		                <h3 class="card-title">Details</h3>
		            </div>
		            <div class="card-body">
		                View <strong>customer</strong> &amp; <strong>transaction</strong> details for this order:
		                <ul class="nav nav-tabs nav-justified">
		                    <li class="nav-item">
		                        <a class="nav-link active" data-toggle="tab" href="#order_customers">Customers</a>
		                    </li>
		                    <li class="nav-item">
		                        <a class="nav-link" data-toggle="tab" href="#order_transactions">Transactions</a>
		                    </li>
		                </ul>

		                <div class="tab-content">
		                    <div class="tab-pane container active o-auto" id="order_customers">
		                        <br/>

		                        <div class="row section" id="images-list">
		                            <table class="bootstrap-table responsive-table"
		                                   data-page-list="[10,25,50,100,200,300,500]"
		                                   data-sort-class="sortable"
		                                   data-pagination="true"
		                                   data-search="true"
		                                   data-search-on-enter-key="true"
		                                   id="customers-table" v-on:click="clickAction($event)">
		                                <thead>
		                                <tr>
			                                <th data-sortable="true" data-field="basic_info">Customer</th>
			                                <th data-sortable="true" data-field="phone">Phone</th>
			                                <th data-sortable="true" data-field="currency">Paid?</th>
			                                <th data-sortable="true" data-field="buttons">&nbsp;</th>
		                                </tr>
		                                </thead>
		                                <tbody>
				                            @foreach ($order->customers['data'] as $customer)
				                                <tr>
				                                    <td>
				                                        <div class="row">
				                                            <div class="col s3">
				                                                <img src="{{ $customer['photo'] }}" alt="" class="avatar avatar-md d-block">
				                                            </div>
				                                            <div class="col s9 no-padding">
				                                            <span class="text-inherit">
				                                                {{ implode(' ', [$customer['firstname'], $customer['lastname']]) }}<br>
				                                                <small class="d-block item-except text-sm text-muted h-1x">{{ $customer['email'] }}</small>
				                                            </span>
				                                            </div>
				                                        </div>
				                                    </td>
				                                    <td>{{ $customer['phone'] }}</td>
				                                    <td>
				                                        <div class="tag">{{ !empty($customer['sale']) && $customer['sale']['is_paid'] ? 'Yes' : 'No' }}</div>
				                                    </td>
				                                    <td>
				                                        <a class="btn btn-secondary btn-sm" data-action="view" target="_blank"
																	 
																	 {{-- (string) $dorcasUrlGenerator->getUrl --}}
																	 {{-- {{ url('invoices/' . $order->id .'?customer_id='.$customer['id']) }} --}}
				                                           href="{{ (string) $dorcasUrlGenerator->getUrl('invoices/' . $order->id ,['query' => ['customer' => $customer['id']]]) }}">View Invoice</a>
				                                        @if (!empty($customer['customer_order']['data']) && !$customer['customer_order']['data']['is_paid'])
				                                            <a class="btn btn-success btn-sm" href="#" data-action="mark-paid" data-id="{{ $customer['id'] }}" 
				                                               data-name="{{ implode(' ', [$customer['firstname'], $customer['lastname']]) }}" data-index="{{ $loop->index }}">Mark Paid</a>
				                                        @endif
				                                        @if (!empty($customer['customer_order']['data']) && !empty($customer['customer_order']['data']['transactions']['data']))
				                                            <a class="btn btn-warning btn-sm" href="#" data-action="transactions"
				                                               data-index="{{ $loop->index }}" data-id="{{ $customer['id'] }}" 
				                                               data-name="{{ implode(' ', [$customer['firstname'], $customer['lastname']]) }}">TXNs</a>
				                                        @endif
				                                        <a class="btn btn-danger btn-sm" href="#" data-action="remove" data-id="{{ $customer['id'] }}" data-index="{{ $loop->index }}"
				                                           data-name="{{ implode(' ', [$customer['firstname'], $customer['lastname']]) }}">DELETE</a>
				                                    </td>
				                                </tr>
				                            @endforeach
		                                </tbody>
		                            </table>
		                        </div>


		                    </div>
		                    <div class="tab-pane container o-auto" id="order_transactions">
		                        <br/>

		                        <div class="card col-md-12" v-if="selectedCustomer !== null">
		                        	<div class="card-status card-status-left bg-blue"></div>
		                        	<div class="card-header">
		                        		<h3 class="card-title">@{{ selectedCustomer.firstname + ' ' + selectedCustomer.lastname }}</h3>
		                        	</div>
		                            <div class="card-body">
		                                <p>
		                                    <strong>Email: </strong> @{{ selectedCustomer.email }}<br>
		                                    <strong>Phone: </strong> @{{ selectedCustomer.phone }}
		                                </p>
		                            </div>
		                        </div>

		                        <div class="row section" id="orders-list">
		                            <table class="bootstrap-table responsive-table" v-if="selectedCustomer !== null"
		                                   data-sort-class="sortable"
		                                   data-pagination="true"
		                                   data-search="true"
		                                   data-unique-id="id">
		                                <thead>
		                                <tr>
		                                    <th>Channel</th>
		                                    <th>Ref.</th>
		                                    <th>Amount</th>
		                                    <th>Successful</th>
		                                    <th>Description</th>
		                                    <th>Date</th>
		                                </tr>
		                                </thead>
		                                <tbody>
			                                <tr v-for="txn in transactions" :key="txn.reference">
			                                    <td>@{{ txn.channel.title_case() }}</td>
			                                    <td>@{{ txn.reference }}</td>
			                                    <td>@{{ txn.currency }} @{{ txn.amount }}</td>
			                                    <td>@{{ txn.is_successful ? "Yes" : "No" }}</td>
			                                    <td>@{{ txn.response_description }}</td>
			                                    <td>@{{ moment(txn.created_at, 'DD MMM, YYYY HH:mm') }}</td>
			                                </tr>
		                                </tbody>
		                            </table>

		                            <div class="col s12" v-if="selectedCustomer === null">
		                                @component('layouts.blocks.tabler.empty-fullpage')
		                                    @slot('title')
		                                        No Transactions
		                                    @endslot
		                                    Go to the Customers tab and check if an orange <strong>TXNs</strong> button is available for the customer. If available, click and then come back here to see the details of all payment transactions through your configured Payment gateway for that customer.
		                                    @slot('buttons')
		                                        <a class="btn btn-primary btn-sm" href="#" v-on:click.prevent="openTab('order_customers')">Go to Customers Tab</a>
		                                    @endslot
		                                @endcomponent
		                            </div>
		                        </div>

		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>

		</div>


	</div>
</div>
@endsection

@section('body_js')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script type="text/javascript">
        $(function() {
            /*$('.custom-datepicker').pickadate({
                selectMonths: true, // Creates a dropdown to control month
                selectYears: 15, // Creates a dropdown of 15 years to control year,
                today: 'Today',
                clear: 'Clear',
                close: 'Ok',
                closeOnSelect: false,
                container: 'body',

                onClose: function() {
                    vm.order.due_at = this.get();
                }
            });*/
	        $('.custom-datepicker').datepicker({
	            uiLibrary: 'bootstrap4',
                format: 'yyyy-mm-dd'
	        });
        });

        new Vue({
            el: '#sub-menu-action',
            methods: {
                deleteOrder: function (orderId) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete this order.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
		                preConfirm: (delete_order) => {
	                        return axios.delete("/msl/sales-order/" + orderId)
	                            .then(function (response) {
	                                //console.log(response);
	                                window.location = "/msl/sales-orders";
	                            })
	                            .catch(function (error) {
	                                var message = '';
	                                console.log(error);
	                                if (error.response) {
	                                    // The request was made and the server responded with a status code
	                                    // that falls out of the range of 2xx
	                                    //var e = error.response.data.errors[0];
	                                    //message = e.title;
				                            var e = error.response;
				                            message = e.data.message;
	                                } else if (error.request) {
	                                    // The request was made but no response was received
	                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
	                                    // http.ClientRequest in node.js
	                                    message = 'The request was made but no response was received';
	                                } else {
	                                    // Something happened in setting up the request that triggered an Error
	                                    message = error.message;
	                                }
	                                return swal("Delete Failed", message, "warning");
	                            });
		                },
		                allowOutsideClick: () => !Swal.isLoading()
		            });

                }
            }
        });

        var vm = new Vue({
            el: '#order-information',
            data: {
                order: {!! json_encode($order) !!},
                updating: false,
                deleting: false,
                selectedCustomer: null,
                transactions: [],
                defaultPhoto: "{{ cdn('images/avatar/avatar-9.png') }}",
                backgroundImage: "{{ cdn('images/gallery/ali-yahya-435967-unsplash.jpg') }}",
                productImage: { file: '' },
				status:''
            },
            mounted: function () {
                if (typeof this.order.due_at !== 'undefined' && this.order.due_at !== null) {
                    this.order.due_at = moment(this.order.due_at).format('DD MMMM, YYYY');
                }
                //console.log(this.order)
            },
            computed: {
                reminderIsOn: function () {
                    return this.order.has_reminders ? 'Yes' : 'No';
                },
                dueDate: function () {
                    if (typeof this.order.due_at === 'undefined' || this.order.due_at === null) {
                        return 'Not Set';
                    }
                    return moment(this.order.due_at).format('DD MMM, YYYY');
                },
	            photo: function () {
	                //return this.employee.photo.length > 0 ? this.employee.photo : this.defaultPhoto;
	                return this.defaultPhoto;
	            }
            },
            methods: {
            	openTab: function (tab) {
            		$('.nav-tabs a[href="#' + tab + '"]').tab('show');
            	},
	            productImageCheck: function() {
	                this.productImage.file = this.$refs.image.files[0];
	                $("#image_label").html(this.productImage.file.name);
	                if (this.productImage.file.size > (1024 * 100)) {
	                    $("#image_submit").attr('disabled', true );
	                    $("#image_message").html('Selected file size > 100KB. Choose another');
	                    $("#image_message").css('color', 'red');
	                } else {
	                    $("#image_submit").attr('disabled', false );
	                    $("#image_message").html('Selected file OK');
	                    $("#image_message").css('color', 'green');
	                }
	            },
	            editOrder: function (index) {
	                $('#order-edit-modal').modal('show');
	            },   
					updateStatus: function (index) {
	                $('#order-status-modal').modal('show');
	            },           	
                moment: function (dateString, format) {
                    return moment(dateString).format(format);
                },
                showProduct: function (id) {
                    window.location = '/msl/sales-product/'+id;
                },
                updateDetails: function () {
                    var context = this;
                    context.updating = true;
                    axios.put("/msl/sales-order/" + context.order.id, {
                        title: context.order.title,
                        description: context.order.description,
                        due_at: context.adjusted_due_at,
                        reminders_on: context.order.has_reminders
                    }).then(function (response) {
                        //console.log(response);
                        context.updating = false;
                        //Materialize.toast("Your changes were successfully saved.", 4000);
                        swal("Success", "Your changes were successfully saved.", "success");
                        $('#order-edit-modal').modal('hide');
                        //window.location = "/msl/sales-order/"+context.order.id;

                    }).catch(function (error) {
                        var message = '';
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
			                            var e = error.response;
			                            message = e.data.message;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        context.updating = false;
                        return swal("Oops!", message, "warning");
                    });
                },

				updateOrderStatus: function () {

					var context = this;

					context.updating = true;

                    axios.put("/msl/sales-order-status/" + context.order.id, {
                        status: this.status,
                    }).then(function (response) {
                        //console.log(response);
                        context.updating = false;
                        //Materialize.toast("Your changes were successfully saved.", 4000);
                        swal("Success", "Your changes were successfully saved.", "success");
                        $('#order-status-modal').modal('hide');
                        //window.location = "/msl/sales-order/"+context.order.id;

                    }).catch(function (error) {
                        var message = '';
                        if (error.response) {
                            // The request was made and the server responded with a status code
                            // that falls out of the range of 2xx
                            //var e = error.response.data.errors[0];
                            //message = e.title;
			                            var e = error.response;
			                            message = e.data.message;
                        } else if (error.request) {
                            // The request was made but no response was received
                            // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                            // http.ClientRequest in node.js
                            message = 'The request was made but no response was received';
                        } else {
                            // Something happened in setting up the request that triggered an Error
                            message = error.message;
                        }
                        context.updating = false;
                        return swal("Oops!", message, "warning");
                    });
                },
                clickAction: function (event) {
                    /*console.log(event.target);
                    var target = event.target.tagName.toLowerCase() === 'i' ? event.target.parentNode : event.target;
                    var attrs = Hub.utilities.getElementAttributes(target);
                    // get the attributes
                    var classList = target.classList;
                    if (classList.contains('view')) {
                        return true;
                    } else if (classList.contains('remove')) {
                        this.delete(attrs);
                    } else if (classList.contains('mark-paid')) {
                        this.markPaid(attrs);
                    } else if (classList.contains('transactions')) {
                        this.selectedCustomer = null;
                        this.transactions = [];
                        this.showTransactions(attrs);
                    }*/

		            let target = event.target;
		            if (!target.hasAttribute('data-action')) {
		                target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
		            }
		            //console.log(target, target.getAttribute('data-action'));
		            let action = target.getAttribute('data-action');
		            let name = target.getAttribute('data-name');
		            let id = target.getAttribute('data-id');
		            let index = parseInt(target.getAttribute('data-index'), 10);
		            if (isNaN(index)) {
		                console.log('Index is not set.');
		                return;
		            }
		            if (action === 'view') {
		                return true;
		            } else if (action === 'remove') {
		                this.delete(id,index,name);
		            } else if (action === 'mark-paid') {
                        this.markPaid(id,index,name);
		            } else if (action === 'transactions') {
                        this.selectedCustomer = null;
                        this.transactions = [];
                        this.showTransactions(id,index,name);
		            }


                },
                markPaid: function (id,index,name) {
                    //console.log(attributes);
                    if (this.deleting) {
                        //Materialize.toast("Wait for the current activity to complete first.", 4000);
                        swal("Notice", "Wait for the current activity to complete first.", "warning");
                        return;
                    }
                    /*var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;*/
                    if (id === null) {
                        return false;
                    }
                    context = this;
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to set the order as paid for by customer " + name,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, continue.",
                        showLoaderOnConfirm: true,
		                preConfirm: (markpaid_order) => {
	                        return axios.put("/msl/sales-order/" + context.order.id + "/customers", {
	                            id: id,
	                            is_paid: 1
	                        }).then(function (response) {
	                            //console.log(response);
	                            swal("Success", "The order invoice has been marked as paid", "success");
	                            window.location = "/msl/sales-order/" + context.order.id;
	                        })
	                            .catch(function (error) {
	                                var message = '';
	                                console.log(error);
	                                if (error.response) {
	                                    // The request was made and the server responded with a status code
	                                    // that falls out of the range of 2xx
	                                    //var e = error.response.data.errors[0];
	                                    //message = e.title;
				                            var e = error.response;
				                            message = e.data.message;
	                                } else if (error.request) {
	                                    // The request was made but no response was received
	                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
	                                    // http.ClientRequest in node.js
	                                    message = 'The request was made but no response was received';
	                                } else {
	                                    // Something happened in setting up the request that triggered an Error
	                                    message = error.message;
	                                }
	                                return swal("Update Failed", message, "warning");
	                            });
		                },
		                allowOutsideClick: () => !Swal.isLoading()
		            });

                },
                delete: function (id,index,name) {
                    //console.log(attributes);
                    if (this.deleting) {
                        Materialize.toast("Wait for the current activity to complete first.", 4000);
                        return;
                    }
                    /*var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;*/
                    if (id === null) {
                        return false;
                    }
                    var customer = this.order.customers.data.find(function (customer) {
                        return customer.id === id;
                    });
                    if (typeof customer.sale !== 'undefined' && typeof customer.sale.is_paid !== 'undefined' && customer.sale.is_paid) {
                        //Materialize.toast("This customer has already paid for the order. You should delete the order instead.", 4000);
                        swal("Notice", "This customer has already paid for the order. You should delete the order instead.", "warning");
                        return;
                    }
                    if (this.order.customers.data.length === 1) {
                        //Materialize.toast("There is just one customer left on the order. You should delete the order instead.", 4000);
                        swal("Notice", "There is just one customer left on the order. You should delete the order instead.", "warning");
                        return;
                    }
                    context = this;
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete customer " + name + " from order #" + context.order.invoice_number,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        showLoaderOnConfirm: true,
		                preConfirm: (deletecustomerfrom_order) => {
	                        context.deleting = true;
	                        axios.delete("/msl/sales-order/" + context.order.id + "/customers", {
	                            data: {id: id}
	                        }).then(function (response) {
	                                //console.log(response);
	                                window.location = "/msl/sales-order/" + context.order.id;
	                            })
	                            .catch(function (error) {
	                                var message = '';
	                                console.log(error);
	                                if (error.response) {
	                                    // The request was made and the server responded with a status code
	                                    // that falls out of the range of 2xx
	                                    //var e = error.response.data.errors[0];
	                                    //message = e.title;
				                            var e = error.response;
				                            message = e.data.message;
	                                } else if (error.request) {
	                                    // The request was made but no response was received
	                                    // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
	                                    // http.ClientRequest in node.js
	                                    message = 'The request was made but no response was received';
	                                } else {
	                                    // Something happened in setting up the request that triggered an Error
	                                    message = error.message;
	                                }
	                                return swal("Delete Failed", message, "warning");
	                            });
		                },
		                allowOutsideClick: () => !Swal.isLoading()
		            });
                },
                showTransactions: function (id,index,name) {
                    //var index = attributes['data-customer-index'] || null;
                    if (index === null) {
                        return false;
                    }
                    var customer = typeof this.order.customers.data !== 'undefined' ? this.order.customers.data[index] : null;
                    if (customer === null) {
                        return false;
                    }
                    this.selectedCustomer = customer;
                    var transactions = typeof customer.customer_order.data.transactions.data !== 'undefined' ? customer.customer_order.data.transactions.data : null;
                    if (transactions === null) {
                        return false;
                    }
                    this.transactions = transactions;
                    this.openTab('order_transactions');
                }
            }
        });
    </script>
@endsection