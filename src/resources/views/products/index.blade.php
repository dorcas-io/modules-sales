@extends('layouts.tabler')
@section('body_content_header_extras')
<div class="dropdown">
      <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">
         Manage Products
      </button>
      <div class="dropdown-menu">
        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#add-product">Add Product</a>
        @if(!empty($subdomain))
        <a class="dropdown-item" href="{{ $subdomain . '/store' }}" target="_blank">Open Web Store</a>
        @endif
      </div>
    </div>
    <br>

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

			
<div class="row">	
	@include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
        <div class="row row-cards row-deck" id="products-list" v-on:click="clickAction($event)">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="bootstrap-table responsive-table" v-if="productsCount > 0"
                   data-url="{{ route('product-lists') }}"
                   data-page-list="[10,25,50,100,200,300,500]"
                   data-row-attributes="formatProducts"
                   data-side-pagination="server"
                   data-show-refresh="true"
                   data-sort-class="sortable"
                   data-pagination="true"
                   data-search="true"
                   data-unique-id="id"
                   data-search-on-enter-key="true"
                   id="products-table">
                <thead>
                <tr>
                    <th data-field="name" data-width="25%">Product</th>
                    <th data-field="inventory" data-width="10%">Stock</th>
                    <th data-field="unit_prices" data-width="15%">Unit Price(s)</th>
                    <th data-field="created_at" data-width="10%">Added On</th>
                    <th data-field="buttons" data-width="15%">Action</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
                @include('modules-sales::products.modals.new')
            </div>
        </div>

    </div>
    


</div>
@endsection


@section('help_modal_content')
    @component('layouts.slots.video-embed')
        //www.youtube.com/embed/UdXldTFenQk?rel=0
    @endcomponent
@endsection


@section('body_js')
<script type="text/javascript" src="{{ cdn('vendors/bootstrap-table/bootstrap-table-materialui.js') }}">
</script>
    <script type="text/javascript">
        $(function() {
            $('input[type=checkbox].check-all').on('change', function () {
                var className = $(this).parent('div').first().data('item-class') || '';
                if (className.length > 0) {
                    $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
                }
            });
        });
        new Vue({
            el: '#products-list',
            data: {
                productsCount: {{ $productsCount }}
            },
            methods: {
                clickAction: function (event) {
                    console.log(event.target);
                    var target = event.target.tagName.toLowerCase() === 'i' ? event.target.parentNode : event.target;
                    var attrs = Hub.utilities.getElementAttributes(target);
                    // get the attributes
                    var classList = target.classList;
                    if (classList.contains('view')) {
                        return true;
                    } else if (classList.contains('remove')) {
                        this.delete(attrs);
                    }
                },
                delete: function (attributes) {
                    console.log(attributes);
                    var name = attributes['data-name'] || '';
                    var id = attributes['data-id'] || null;
                    if (id === null) {
                        return false;
                    }
                    var context = this;
                    swal({
                        title: "Are you sure?",
                        text: "You are about to delete product " + name + " from your inventory.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    }, function() {
                        axios.delete("/xhr/inventory/products/" + id)
                            .then(function (response) {
                                console.log(response);
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
                                    var e = error.response.data.errors[0];
                                    message = e.title;
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
                    });
                }
            }
        });

function formatProducts (row) {
            row.description_info = '<span class="truncate">'+row.description+'</span>';
            var unit_prices = [];
            if (typeof row.prices !== 'undefined') {
                var price = null;
                for (var i = 0; i < row.prices.data.length; i++) {
                    if (i > 0) {
                        unit_prices.push('<div>+ ' +(row.prices.data.length - 1)+ ' more</div>');
                        break;
                    }
                    price = row.prices.data[i];
                    unit_prices.push( price.currency+ ' ' + price.unit_price.formatted);
                }
                row.unit_prices = unit_prices.join('');
            }
            row.created_at = moment(row.created_at).format('DD MMM, YYYY');

            

            row.buttons = '<a href="/msl/sales-product/' + row.id + '" type="button" class="btn btn-primary sm"><i class="fe fe-eye mr-2"></i></a>' + 
                '<a href="#" type="button" data-id="'+row.id+'" data-name="'+row.name+'" class="btn btn-danger sm"><i class="fe fe-trash mr-2"></i></a>';

            return row;
        }
    </script>
@endsection
