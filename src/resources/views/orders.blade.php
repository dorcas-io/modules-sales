@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
        <div class="row row-cards row-deck" id="orders-list">

            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter bootstrap-table"
                           data-pagination="true"
                           data-search="false"
                           data-side-pagination="server"
                           data-show-refresh="false"
                           data-unique-id="id"
                           data-id-field="id"
                           data-row-attributes="formatOrders"
                           data-url="{{ route('sales-orders-search') }}"
                           data-page-list="[10,25,50,100,200,300,500]"
                           data-sort-class="sortable"
                           data-search-on-enter-key="false"
                            id="orders-table"
                             v-if="ordersCount > 0"
                       v-on:click="clickAction($event)">
                        <thead>
                        <tr>

		                    <th data-field="invoice_number">Invoice #</th>
		                    <th data-field="title">Title</th>
{{--                            <th data-field="customer">Customer</th>--}}
{{--		                    <th data-field="description">Description</th>--}}
		                    <th data-field="currency">Currency</th>
		                    <th data-field="amount.formatted">Amount</th>
		                    <th data-field="quantity">Quantity(s)</th>
                            <th data-field="status">Status</th>
		                    <th data-field="reminder_on">Reminder?</th>
		                    <th data-field="due_at">Due At</th>
		                    <th data-field="created_at">Created</th>
		                    <th data-field="buttons">&nbsp;</th>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
		            <div class="col s12" v-if="ordersCount === 0">
                        @component('layouts.blocks.tabler.empty-fullpage')
                            @slot('title')
                                No Orders or Invoices
                            @endslot
                            Create Orders to automatically generate invoices for your customers.
                            @slot('buttons')
                                <a href="{{ route('sales-orders-new') }}" class="btn btn-primary btn-sm">New Orders</a>
                            @endslot
                           
                        @endcomponent
		            </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection

@section('body_js')
<script>

	String.prototype.title_case = function () {
	    this.toLowerCase();
	    var components = this.split(' ');
	    return components.map(function (component) {
	        return component.charAt(0).toUpperCase() + component.substr(1).toLowerCase();
	    }).join(' ');
	};

        /*$(function() {
            $('input[type=checkbox].check-all').on('change', function () {
                var className = $(this).parent('div').first().data('item-class') || '';
                if (className.length > 0) {
                    $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
                }
            });
        });*/
        new Vue({
            el: '#orders-list',
            data: {
                ordersCount: {{ $ordersCount }}
            },
            methods: {
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
                    }*/
		            let target = event.target;
		            if (!target.hasAttribute('data-action')) {
		                target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
		            }
		            //console.log(target, target.getAttribute('data-action'));
		            let action = target.getAttribute('data-action').toLowerCase();
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
		            } else {
		                return true;
		            }


                },
                delete: function (id,index,name) {
                    /*console.log(attributes);
                    var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;*/
                    if (id === null) {
                        return false;
                    }
                    context = this;
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You are about to delete product " + name + " from your inventory.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
		                showLoaderOnConfirm: true,
		                preConfirm: (delete_order) => {
	                        return axios.delete("/xhr/inventory/products/" + id)
	                            .then(function (response) {
	                                //console.log(response);
	                                context.visible = false;
	                                context.contactsCount -= 1;
	                                $('#products-table').bootstrapTable('removeByUniqueId', response.data.id);
	                                return swal("Deleted!", "The product was successfully deleted.", "success");
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
    function formatOrders (row, index) {
		row.customers[0];

        if (typeof row.products !== 'undefined' && typeof row.products.data !== 'undefined' && row.products.data.length > 0) {
            row.cart_content = row.products.data.length;

        } else if (typeof row.inline_product !== 'undefined') {
            // should be an inline product
            row.cart_content = 1;
        }



		row.reminder_on = '<div class="tag">' + (row.has_reminders ? 'Yes' : 'No') + '</div>';
        row.due_at = typeof row.due_at !== 'undefined' && row.due_at !== null ? moment(row.due_at).format('DD MMM, YYYY') : '';
        row.created_at = moment(row.created_at).format('DD MMM, YYYY');
        row.buttons = '<a class="btn btn-success btn-sm" data-index="' + index + '" data-id="' + row.id + '" data-action="view" href="/msl/sales-order/' + row.id + '">View</a>';
        return row;
    }
</script>
@endsection