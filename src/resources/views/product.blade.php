@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9" id="product_profile">

        <div class="row">

            <div class="col-md-4">
                <div class="card card-profile">
                    <div class="card-header" v-bind:style="{ 'background-image': 'url(' + backgroundImage + ')' }"></div>
                    <div class="card-body text-center">
                        <img class="card-profile-img" v-bind:src="photo">
                        <h3 class="mb-3">@{{ product.name }}</h3>
                        <p class="mb-4">
                            @{{ product.description }}
                        </p>
                        <div class="alert alert-primary" role="alert" v-if="isVariant">
                            Variant of <a href="{{ route('sales-products-single', [$product->product_parent]) }}"><strong>@{{ variantParent.name }}</strong></a>
                        </div>
                        <div v-if="product.categories.data.length > 0">
                            <strong>Categories</strong>
                            <div class="tag" v-for="(category, index) in product.categories.data" :key="category.id">
                              @{{ category.name }}
                              <a data-ignore-click="true" href="#" v-bind:data-index="index" v-on:click.prevent="removeCategory(index)" class="tag-addon tag-danger"><i class="fe fe-delete"></i></a>
                            </div>
                        </div>
                        <div>&nbsp;</div>
                        <button v-on:click.prevent="editProduct" class="btn btn-outline-primary btn-sm text-center">
                            <span class="fa fa-sliders"></span> Edit Product
                        </button>
                    </div>
                    @include('modules-sales::modals.product-edit')
                </div>

                <div class="card">
                    <div class="card-status bg-green"></div>
                    <div class="card-header">
                        <h3 class="card-title">Add Category</h3>
                    </div>
                    <div class="card-body">

                        <form method="post" action="{{ route('sales-product-single-categories', [$product->id]) }}">
                            {{ csrf_field() }}
                            <fieldset class="form-fieldset">
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <select class="form-control" name="categories[]" id="categories" multiple>
                                            <option disabled="">Select one or more Categories</option>
                                            <option v-for="category in categories" :key="category.id"
                                                    v-if="productCategories.indexOf(category.id) === -1"
                                                    v-bind:value="category.id">@{{ category.name }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <button class="btn btn-primary" type="submit" name="action"
                                                v-if="!updating">Add Categories</button>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </div>

                <div class="card" v-if="!isVariant">
                    <div class="card-status bg-green"></div>
                    <div class="card-header">
                        <h3 class="card-title">Variant Types</h3>
                    </div>
                    <div class="card-body">
                        <div class="tag" v-for="variantType in variantTypes" :key="variantType">
                            @{{ variantType }}
                            <a href="#" v-on:click.prevent="deleteVariantType(variantType)" class="tag-addon tag-danger">
                                <i class="fe fe-trash-2" data-ignore-click="true"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>


            <div class="col-md-8">
                <div class="card">
                    <div class="card-status bg-blue"></div>
                    <div class="card-header">
                        <h3 class="card-title">Activity</h3>
                    </div>
                    <div class="card-body">
                        Manage <strong>images</strong>, <strong>sales</strong> &amp; <strong>stock</strong> activities for this product:
                        <ul class="nav nav-tabs nav-justified">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#product_images">Images</a>
                            </li>
                            <li class="nav-item" v-if="!isVariant">
                                <a class="nav-link" data-toggle="tab" href="#product_variants">Variants</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#product_sales">Sales</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#product_stock">Stock</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane container active o-auto" id="product_images">
                                <br/>

                                <div class="row section" id="images-list">
                                    <table class="bootstrap-table responsive-table" v-if="product.images.data.length > 0"
                                           data-page-list="[10,25,50,100,200,300,500]"
                                           data-show-refresh="true"
                                           data-sort-class="sortable"
                                           data-pagination="true"
                                           data-search="false"
                                           data-search-on-enter-key="true"
                                           id="images-table" v-on:click="clickAction($event)">
                                        <thead>
                                        <tr>
                                            <th data-field="title">ID</th>
                                            <th data-field="image">Image</th>
                                            <th data-field="created_at">Date</th>
                                            <th data-field="menu">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @if (count($product->images['data']) > 0)
                                            @foreach ($product->images['data'] as $image)
                                                <tr>
                                                    <td>Image #{{ $loop->iteration }}</td>
                                                    <td>
                                                        <img src="{{ $image['url'] }}" title="Image #{{ $product->id }}" width="400" />
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($image['created_at'])->format('D jS M, Y') }}</td>
                                                    <td>
                                                        <a href="#" class="btn btn-danger btn-sm" data-action="delete_image" data-id="{{ $image['id'] }}" data-index="{{ $loop->index }}" data-name="Image #{{ $loop->iteration }}">Delete</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>
                                    <div class="col s12" v-if="product.images.data.length === 0">
                                        @component('layouts.blocks.tabler.empty-fullpage')
                                            @slot('title')
                                                No Products Images
                                            @endslot
                                            {{ $subdomains->count() === 0 ? 'To add product images, you need to enable your Dorcas store, visit Domains and reserve your subdomain' : 'Upload images for your product to be displayed in your store.' }}
                                            @slot('buttons')
                                                @if ($subdomains->count() === 0)
                                                    <a class="btn btn-primary btn-sm"
                                                       href="{{ route('ecommerce-domains') }}">
                                                        Reserve your Domain
                                                    </a>
                                                @else
                                                    <a class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#product-image-modal">
                                                        Add an Image
                                                    </a>
                                                @endif
                                            @endslot
                                        @endcomponent
                                    </div>
                                </div>


                            </div>
                            <div class="tab-pane container o-auto" id="product_variants" v-if="!isVariant">
                                <br/>

                                <div class="row section" id="variants-list">
                                    <table class="bootstrap-table responsive-table"
                                           data-url="{{ route('sales-product-search') }}?type=variant&parent={{ $product->id }}"
                                           data-page-list="[10,25,50,100,200,300,500]"
                                           data-row-attributes="formatVariant"
                                           data-side-pagination="server"
                                           data-show-refresh="true"
                                           data-sort-class="sortable"
                                           data-pagination="true"
                                           data-search="false"
                                           data-unique-id="id"
                                           data-search-on-enter-key="true"
                                           id="variants-table" v-on:click="clickAction($event)">
                                        <thead>
                                        <tr>
                                            <th data-field="name">Product</th>
                                            <th data-field="description">Description</th>
                                            <th data-field="inventory">Stock</th>
                                            <th data-field="unit_prices">Unit Price(s)</th>
                                            <th data-field="created_at">Added On</th>
                                            <th data-field="buttons">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                                <div class="row mt-2" v-if="!isVariant">
                                    <a class="btn btn-primary btn-sm" href="#" v-on:click.prevent="addVariant">New Variant</a>
                                </div>

                            </div>
                            <div class="tab-pane container o-auto" id="product_sales">
                                <br/>

                                <div class="row section col-md-12" id="orders-list">
                                    <table class="table bootstrap-table responsive-table" width="100%" v-if="showOrders"
                                           data-url="{{ url('/msl/sales-orders-search') }}?product={{ $product->id }}"
                                           data-page-list="[10,25,50,100,200,300,500]"
                                           data-row-attributes="formatOrders"
                                           data-side-pagination="server"
                                           data-show-refresh="true"
                                           data-sort-class="sortable"
                                           data-pagination="true"
                                           data-search="false"
                                           data-unique-id="id"
                                           data-search-on-enter-key="true"
                                           id="orders-table" v-on:click="clickAction($event)">
                                        <thead>
                                        <tr>
                                            <th data-field="title">Title</th>
                                            <th data-field="description">Description</th>
                                            <th data-field="currency">Currency</th>
                                            <th data-field="amount.formatted">Amount</th>
                                            <th data-field="due_at">Due At</th>
                                            <th data-field="reminder_on">Reminder On?</th>
                                            <th data-field="created_at">Created</th>
                                            <th data-field="buttons">&nbsp;</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div class="col s12" v-if="!showOrders">
                                        @component('layouts.blocks.tabler.empty-fullpage')
                                            @slot('title')
                                                No Orders
                                            @endslot
                                            Add customer orders to generate invoices, and keep track of your sales.
                                            @slot('buttons')
                                                <a class="btn btn-primary btn-sm" href="{{ route('sales-orders-new') }}">New Order</a>
                                            @endslot
                                        @endcomponent
                                    </div>
                                </div>

                            </div>
                            <div class="tab-pane container o-auto" id="product_stock">
                                <br/>

                                <div class="row section" id="stock-list">
                                    <table class="bootstrap-table responsive-table" v-if="product.stocks.data.length > 0"
                                           data-url="{{ url('/msl/sales-product', [$product->id, 'stocks']) }}"
                                           data-page-list="[10,25,50,100,200,300,500]"
                                           data-row-attributes="formatStocks"
                                           data-side-pagination="server"
                                           data-show-refresh="true"
                                           data-sort-class="sortable"
                                           data-pagination="true"
                                           data-search="false"
                                           data-unique-id="id"
                                           data-search-on-enter-key="true"
                                           id="stocks-table" v-on:click="clickAction($event)">
                                        <thead>
                                        <tr>
                                            <th data-field="activity">Activity</th>
                                            <th data-field="quantity">Quantity</th>
                                            <th data-field="comment">Comment</th>
                                            <th data-field="date">Date</th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                    <div class="col s12" v-if="product.stocks.data.length === 0">
                                        @component('layouts.blocks.tabler.empty-fullpage')
                                            @slot('title')
                                                No Inventory
                                            @endslot
                                            Manage product stock, and see the log of all these activities here.
                                            @slot('buttons')
                                                <a class="btn btn-primary btn-sm" href="#" data-toggle="modal" data-target="#product-inventory-modal">Manage Inventory</a>
                                            @endslot
                                        @endcomponent
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                @include('modules-sales::modals.product-inventory')
                @include('modules-sales::modals.product-image')
                @include('modules-sales::modals.product-variant')
            </div>
        </div>
    </div>
</div>
@endsection

@section('body_js')
<script type="text/javascript">
    var proVm = new Vue({
        el: '#product_profile',
        data: {
            product: {!! json_encode($product) !!},
            subdomains: {!! json_encode($subdomains ?: []) !!},
            updating: false,
            categories: {!! json_encode($categories ?: []) !!},
            defaultPhoto: "{{ cdn('images/avatar/avatar-9.png') }}",
            backgroundImage: "{{ cdn('images/gallery/imani-clovis-547617-unsplash.jpg') }}",
            productImage: { file: '' },
            variantTypes: {!! json_encode($variantTypes ?: []) !!},
            variantType: '',
            variant: { name:'', description:'', product_type:'', product_parent:'', prices: '', currency: '', product_variant_type: '' },
            variantProducts: {!! json_encode(!empty($variantProducts) ? $variantProducts : []) !!},
            variantParent: {!! json_encode(!empty($variantParent) ? $variantParent : []) !!}
        },
        computed: {
            productCategories: function () {
                var selected = [];
                for (var i = 0; i < this.product.categories.data.length; i++) {
                    selected.push(this.product.categories.data[i].id);
                }
                return selected;
            },
            showOrders: function () {
                return typeof this.product.orders !== 'undefined' && typeof this.product.orders.data !== 'undefined' &&
                        this.product.orders.data.length > 0;
            },

            photo: function () {
                //return this.employee.photo.length > 0 ? this.employee.photo : this.defaultPhoto;
                return this.defaultPhoto;
            },

            isVariant: function () {
                return this.product.product_type === 'variant';
            },
        },
        methods: {
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
            editProduct: function (index) {
                $('#product-edit-modal').modal('show');
            },
            clickAction: function (event) {
                //console.log(event.target);
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
                } else if (action === 'delete_image') {
                    this.deleteImage(id,index,name);
                } else if (action === 'delete_variant') {
                    this.deleteVariant(id,index,name);
                } else if (action === 'edit_variant') {
                    this.editVariant(id,index,name);
                } else {
                    return true;
                }
            },
            deleteImage: function (id,index,name) {

                if (id === null) {
                    return false;
                }
                context = this;
                Swal.fire({
                    title: "Are you sure?",
                    text: "You are about to delete image " + name + " from this product.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    showLoaderOnConfirm: true,
                    preConfirm: (delete_image) => {
                    this.deleting = true;
                        return axios.delete("/msl/sales-product/" + context.product.id + "/images", {
                            params: {id: id}
                        }).then(function (response) {
                                //console.log(response);
                                context.visible = false;
                                context.contactsCount -= 1;
                                $('#images-table').bootstrapTable('remove', {field: 'title', values: [name]});
                                context.product.images.data.splice(index, 1);
                                // remove the image that was just deleted
                                return swal("Deleted!", "The product was successfully deleted.", "success");
                            }).catch(function (error) {
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
            addVariant: function() {
                this.variant = { name:'', description:'', product_type:'', product_parent:'', prices: '', currency: '', product_variant_type: '' };
                $('#product-variant-modal').modal('show');
            },
            editVariant: function (id,index,name) {
                let variant = typeof this.variantProducts[index] !== 'undefined' ? this.variantProducts[index] : null;
                this.variant = variant;
                //console.log(variant)
                $('#product-variant-modal').modal('show');
            },
            deleteVariant: function (id,index,name) {
                /*console.log(attributes);
                var name = attributes['data-name'] || '';
                var id = attributes['data-id'] || null;
                if (id === null) {
                    return false;
                }*/
                var context = this;
                Swal.fire({
                    title: "Are you sure?",
                    text: "You are about to delete Product Variant " + name + " from your inventory.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    showLoaderOnConfirm: true,
                    preConfirm: (delete_item) => {
                    this.deleting = true;
                        return axios.delete("/msl/sales-product/" + id)
                            .then(function (response) {
                                //console.log(response);
                                context.visible = false;
                                context.contactsCount -= 1;
                                $('#variants-table').bootstrapTable('removeByUniqueId', response.data.id);
                                return swal("Deleted!", "The product was successfully deleted.", "success");
                            })
                            .catch(function (error) {
                                var message = '';
                                console.log(error);
                                if (error.response) {
                                    // The request was made and the server responded with a status code
                                    // that falls out of the range of 2xx
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
            addPriceField: function () {
                var price = {currency: 'NGN', id: app.utilities.randomId(10), unit_price: {formatted: 0, raw: 0}};
                if (typeof this.product.prices === 'undefined' || typeof this.product.prices.data === 'undefined') {
                    this.product.prices = {data: []};
                }
                this.product.prices.data.push(price);
            },
            removeEntry: function (index) {
                this.product.prices.data.splice(index, 1);
            },
            removeCategory: function (index) {
               var category = this.product.categories.data[index];
               // get the category to be removed
                if (this.updating) {
                    //Materialize.toast('Please wait till the current activity completes...', 4000);
                    return;
                }
                this.updating = true;
                var context = this;
                axios.delete("/msl/sales-product/" + context.product.id + "/categories", {
                    data: {categories: [category.id]}
                }).then(function (response) {
                    //console.log(response);
                    console.log(index);
                    if (index !== null) {
                        context.product.categories.data.splice(index, 1);
                    }
                    context.updating = false;
                    //Materialize.toast('Category '+category.name+' removed.', 2000);
                    return swal("Delete Success", 'Category '+category.name+' removed.', "success");
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
                        context.updating = false;
                        return swal("Delete Failed", message, "warning");
                    });
            },


            titleCase: function (string) {
                return string.title_case();
            },
            addVariantType: function () {
                var context = this;
                Swal.fire({
                        title: "New Variant Type",
                        text: "Enter the name for the variant Type:",
                        input: "text",
                        showCancelButton: true,
                        animation: "slide-from-top",
                        showLoaderOnConfirm: true,
                        inputPlaceholder: "Custom Variant Type",
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You need to write something!'
                            }
                        },
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Add Variant Type",
                        showLoaderOnConfirm: true,
                        preConfirm: (variant_type) => {
                            //console.log(variant_name);
                        return axios.post("/msl/sales-variant-type", {
                                variant_type: variant_type
                            }).then(function (response) {
                                //vm.fields.push({id: response.data.id, name: response.data.name});
                                context.variantTypes = response.data;
                                return swal("Success", "The variant type was successfully created.", "success");
                            })
                                .catch(function (error) {
                                    var message = '';
                                    if (error.response) {
                                        // The request was made and the server responded with a status code
                                        // that falls out of the range of 2xx
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
                                    return swal("Oops!", message, "warning");
                                });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    });
            },
            deleteVariantType: function (variant_name) {
                var context = this;
                Swal.fire({
                    title: "Are you sure?",
                    text: "You are about to delete the \"" + variant_name + "\" Variant Type",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    showLoaderOnConfirm: true,
                    preConfirm: (delete_custom) => {
                    return axios.post("/msl/sales-variant-type-remove/", {
                        variant_name: variant_name
                    })
                        .then(function (response) {
                            //console.log(response);
                            context.variantTypes = response.data;
                            return swal("Deleted!", "The variant type was successfully deleted.", "success");
                        })
                        .catch(function (error) {
                            var message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
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

        },
        mounted: function () {
            var context = this;
            //console.log(this.variantProducts)
            
        }
    });

    new Vue({
        el: '#sub-menu-action',
        methods: {
            newVariantType: function () {
                console.log('nv')
                proVm.addVariantType();
            }
        }
    });


    function formatVariant (row, index) {
        row.description_info = '<span class="truncate">'+row.description+'</span>';
        var unit_prices = [];
        if (typeof row.prices !== 'undefined') {
            var price = null;
            for (var i = 0; i < row.prices.data.length; i++) {
                if (i > 0) {
                    unit_prices.push('<div class="chip">+ ' + (row.prices.data.length - 1)+ ' more</div>');
                    break;
                }
                price = row.prices.data[i];
                unit_prices.push('<div class="chip">' + price.currency+ ' ' + price.unit_price.formatted + '</div>');
            }
            row.unit_prices = unit_prices.join('');
        }
        row.created_at = moment(row.created_at).format('DD MMM, YYYY');
        row.buttons = '<a class="btn btn-icon" href="/msl/sales-product/' + row.id + '"><i data-index="'+index+'" data-action="view" data-id="'+row.id+'" data-name="'+row.name+'" class="fe fe-eye"></i></a> &nbsp;'+
            '<!--<a class="btn btn-icon" href="#"><i data-index="'+index+'" data-action="edit_variant" data-id="'+row.id+'" data-name="'+row.name+'" class="fe fe-edit"></i></a>-->' +
            '<a class="btn btn-icon" href="#"><i data-index="'+index+'" data-action="delete_variant" data-id="'+row.id+'" data-name="'+row.name+'" class="fe fe-trash-2"></i></a>';
        return row;
    }

    function formatOrders (row, index) {
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

    function formatStocks (row, index) {
        row.activity = row.action.title_case() + 'ed'; // converts add => Added; subtract => Subtracted
        row.date = moment(row.created_at).format('DD MMM, YYYY HH:mm');
        return row;
    }

</script>
@endsection