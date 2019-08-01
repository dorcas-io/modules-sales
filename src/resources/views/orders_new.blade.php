@extends('layouts.tabler')
@section('head_css')
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9" id="sales-order">

        <form action="" method="post">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-md-6">
                <fieldset class="form-fieldset">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <input class="form-control" id="title" name="title" type="text" maxlength="80" required>
                            <label class="form-label" for="title">Title</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <select class="form-control" name="currency" v-model="currency" required>
                                <option value="" disabled>Select Currency</option>
                                <option value="NGN">Nigerian Naira (NGN)</option>
                            </select>
                            <label class="form-label" for="currency">Currency</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" id="amount" name="amount" type="number" step="0.01" min="0" v-model="total_amount" required>
                            <label class="form-label" for="amount">Total Amount</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group" v-bind:class="{'col-md-6': due_date !== null && due_date.length > 0}">
                            <input type="text" class="custom-datepicker" name="due_at" id="due_at" v-model="due_date">
                            <label for="due_at">Due Date</label>
                        </div>
                        <div class="col-md-6 form-group" v-if="due_date !== null && due_date.length > 0">
                            <p>Invoice Reminders</p>
                            <div class="switch">
                                <label>
                                    Off
                                    <input type="checkbox" name="reminders_on">
                                    <span class="lever"></span>
                                    On
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <textarea class="form-control" id="description" name="description" v-model="description"></textarea>
                            <label class="form-label" for="description">Description</label>
                        </div>
                    </div>
                    <div class="row" v-if="customer_mode === 'add_new'">
                        <div class="col-md-6 form-group">
                            <input class="form-control" id="customer_firstname" name="customer_firstname" type="text" maxlength="30">
                            <label class="form-label" for="customer_firstname">Customer's Firstname</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" id="customer_lastname" name="customer_lastname" type="text" maxlength="30">
                            <label class="form-label" for="customer_lastname">Customer's Lastname</label>
                        </div>
                    </div>
                    <div class="row" v-if="customer_mode === 'add_new'">
                        <div class="col-md-6 form-group">
                            <input class="form-control" id="customer_email" name="customer_email" type="email" maxlength="80">
                            <label class="form-label" for="customer_email">Customer's Email</label>
                        </div>
                        <div class="col-md-6 form-group">
                            <input class="form-control" id="customer_phone" name="customer_phone" type="text" maxlength="30">
                            <label class="form-label" for="customer_phone">Customer's Phone</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <select class="form-control" name="customer" id="customer"
                                    v-model="customer_mode" required>
                                <option value="" disabled selected>Add a customer to this invoice</option>
                                <option value="add_new">Add a new customer</option>
                                <optgroup label="Select an existing customer">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->firstname . ' ' . $customer->lastname }}</option>
                                    @endforeach
                                </optgroup>
                            </select>
                            <label class="form-label" for="customer">Customer Mode</label>
                        </div>
                    </div>
                </fieldset>
                </div>
                <div class="col-md-6">
                <fieldset class="form-fieldset">
                    <div class="form-group col-md-12">
                        <select v-model="product_style" class="form-control" v-on:change="checkCurrency">
                            <option value="" disabled>How do you want to add products?</option>
                            <option value="inline">Add a product here directly</option>
                            <option value="select">Select from your products list</option>
                        </select>
                        <label class="form-label" for="product_style">Product Name</label>
                    </div>
                    <div class="row" v-if="product_style === 'inline'">
                        <div class="col s12">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <input class="form-control" id="product_name" name="product_name" type="text" maxlength="80">
                                    <label class="form-label" for="product_name">Product Name</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <input class="form-control" id="product_quantity" name="product_quantity"
                                           type="number" min="1" value="1" v-model="inline.qty"
                                           v-on:keyup="updateInlineTotal"
                                           v-on:change="updateInlineTotal">
                                    <label class="form-label" for="product_quantity">Quantity</label>
                                </div>
                                <div class="form-group col-md-6">
                                    <input class="form-control" id="product_price" name="product_price" type="number" step="1" min="0" v-model="inline.unit_price" v-on:keyup="updateInlineTotal" v-on:change="updateInlineTotal">
                                    <label class="form-label" for="product_price">Unit Price</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12">
                                    <p>Is this a Proforma/Quote</p>
                                    <div class="switch">
                                        <label>
                                            No
                                            <input type="checkbox" name="is_quote" value="1">
                                            <span class="lever"></span>
                                            Yes
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="product_style === 'select' && currency.length === 3">
                        <div class="col-md-12">
                            <cart-item v-for="(item, index) in cart" :key="index"
                                       :quantity_id="'quantity' + index"
                                       :unit_price_id="'price' + index"
                                       :index="index"
                                       v-on:sync-cart="syncCart"
                                       v-on:remove-item="removeItem"></cart-item>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col s12">
                        	<a href="#" class="btn btn-primary btn-sm" v-on:click.prevent="addProductField">Add Product</a>
                        </div>
                    </div>
                </fieldset>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="submit" name="action">Save Order</button>
                    </div>
                </div>
            </div>
        </form>

    </div>

</div>

@endsection
@section('body_js')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        $('.custom-datepicker').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd mmmm, yyyy',
            close: function (e) {
                /*let target = e.target;
                if (!target.hasAttribute('data-action')) {
                    target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
                }
                //console.log(target, target.getAttribute('data-action'));
                let action = target.getAttribute('data-action').toLowerCase();
                let name = target.getAttribute('data-name');
                let id = target.getAttribute('data-id');
                let index = parseInt(target.getAttribute('data-index'), 10);
                console.log(target);
                console.log($(target).val())*/
                vm.due_date = e.target.value;
            }
        });
    });

    var vm = new Vue({
        el: '#sales-order',
        data: {
            products: {!! !empty($products) ? json_encode($products) : '[]' !!},
            currency: '{{ old('currency', '') }}',
            total_amount: {{ old('amount', 0) }},
            due_date: '{{ old('due_at', '') }}',
            description: '{{ old('description') }}',
            product_style: 'inline',
            inline: {qty: 0, unit_price: 0},
            cart: [],
            customer_mode: ''
        },
        computed: {

        },
        mounted: function()  {
            console.log(this.products)
        },
        methods: {
            checkCurrency: function () {
                if (this.product_style === 'select' && this.currency.length !== 3) {
                    //Materialize.toast('Please select a currency for the order to continue...', 4000);
                    swal("Order Status", "Please select a currency for the order to continue...", "warning");
                }
                if (this.product_style === 'select') {
                    this.updateCartTotal();
                } else if (this.product_style === 'inline') {
                    this.updateInlineTotal();
                }
            },
            addProductField: function () {
                if (this.products.length === 0) {
                    Swal.fire({
                        title: "Add a Product",
                        text: "You have no product in your inventory, would you like to add one now?",
                        type: "info",
                        showCancelButton: true,
                        confirmButtonText: "Yes, add one.",
                        confirmButtonColor: "#DD6B55",
		                showLoaderOnConfirm: true,
		                preConfirm: (add_product) => {
		                    window.location = '/msl/sales-products';
		                },
		                allowOutsideClick: () => !Swal.isLoading()
		            });
                    return;
                }
                var item = {quantity: 0, id: app.utilities.randomId(10), unit_price: 0};
                this.cart.push(item);
            },
            updateInlineTotal: function () {
                this.total_amount = parseInt(this.inline.qty, 10) * parseFloat(this.inline.unit_price);
            },
            updateCartTotal: function () {
                var total = 0;
                for (var i = 0; i < this.cart.length; i++) {
                    total += (isNaN(this.cart[i].quantity) ? 0 : this.cart[i].quantity) * (isNaN(this.cart[i].unit_price) ? 0 : this.cart[i].unit_price);
                }
                this.total_amount = total;
            },
            syncCart: function (index, quantity, unit_price, id) {
                this.cart.splice(index, 1, {quantity: parseInt(quantity, 10), unit_price: parseFloat(unit_price), id: id});
                this.updateCartTotal();
            },
            removeItem: function (index) {
                this.cart.splice(index, 1);
                this.updateCartTotal();
            }
        }
    });
</script>
@endsection