@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9">
        <div class="row row-cards row-deck" id="shipping-routes-list">
            <div class="col-sm-12">
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                           data-pagination="true"
                           data-search="true"
                           data-side-pagination="server"
                           data-show-refresh="true"
                           data-unique-id="id"
                           data-id-field="id"
                           data-row-attributes="formatRoutes"
                           data-url="{{ route('sales-product-search') }}?type=shipping"
                           data-page-list="[10,25,50,100,200,300,500]"
                           data-sort-class="sortable"
                           data-search-on-enter-key="true"
                           v-if="productsCount > 0"
                            id="shipping-routes-table"
                        v-on:click="clickAction($event)">
                        <thead>
                        <tr>
                            <th data-field="name">Route</th>
                            <th data-field="unit_prices">Unit Price(s)</th>
                            <th data-field="created_at">Added On</th>
                            <th data-field="buttons">Action(s)</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <div class="col s12" v-if="productsCount === 0">
                        @component('layouts.blocks.tabler.empty-fullpage')
                            @slot('title')
                                No Shipping Routes
                            @endslot
                            Add shipping routes so customers can choose during checkout.
                            @slot('buttons')
                                <a href="#" v-on:click.prevent="addRoute" class="btn btn-primary btn-sm">Add Route</a>
                            @endslot
                        @endcomponent
                    </div>

                </div>
            </div>
            @include('modules-sales::modals.shipping-route')
        </div>

    </div>

</div>
@endsection

@section('body_js')
<script type="text/javascript">
    /*$(function() {
        $('input[type=checkbox].check-all').on('change', function () {
            var className = $(this).parent('div').first().data('item-class') || '';
            if (className.length > 0) {
                $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
            }
        });
    });*/
    var vmSR = new  Vue({
        el: '#shipping-routes-list',
        data: {
            productsCount: {{ $productsCount }},
            shippingRoutes: {!! json_encode(!empty($shippingRoutes) ? $shippingRoutes : []) !!},
            shippingRoute: { index: '', name:'', description:'', prices: '', currency: '' },
            routeName: '',
            // routeType: '',
            // routeCurrency: '',
            // routePrice: ''
        },
        mounted: function() {
            //console.log(this.productsCount)
            //console.log(this.shippingRoutes)
        },
        computed: {
            routeName: {
                get: function () {
                    let original = this.shippingRoute.name;
                    let ind = this.shippingRoute.name.indexOf("(");
                    return ind === -1 ? this.shippingRoute.name : this.shippingRoute.name.split(' (')[0];
                },
                set: function (newValue) {
                    this.routeName = newValue;
                    // do some action
                }
            },
            // routeName: function() {
            //     let original = this.shippingRoute.name;
            //     let ind = this.shippingRoute.name.indexOf("(");
            //     return ind === -1 ? this.shippingRoute.name : this.shippingRoute.name.split(' (')[0];
            // },
            routeType: function() {
                let original = this.shippingRoute.name;
                let ind = this.shippingRoute.name.indexOf("(");
                let rtype = ind === -1 ? "Inter-State)" : this.shippingRoute.name.split(' (')[1];
                return rtype.substring(0, rtype.length - 1);
            },
            routeCurrency: function() {
                let index = this.shippingRoute.index;
                let shippingRoute = this.shippingRoute.index !== '' ? this.shippingRoute : null;
                //console.log(this.shippingRoute)
                return shippingRoute !== null ? this.shippingRoute.prices.data[0].currency : 'NGN';
            },
            routePrice: function() {
                let index = this.shippingRoute.index;
                let shippingRoute = this.shippingRoute.index !== '' ? this.shippingRoute : null;
                //console.log(this.shippingRoute)
                return shippingRoute !== null ? this.shippingRoute.prices.data[0].unit_price.raw : '0.00';
            },
            routeID: function() {
                let index = this.shippingRoute.index;
                let shippingRoute = this.shippingRoute.index !== '' ? this.shippingRoute : null;
                return shippingRoute !== null ? this.shippingRoute.id : '';
            }
        },
        methods: {
            // updateRouteName: function (newValue) {
            //     this.routeName = newValue;
            // },
            // updateRouteType: function (newValue) {
            //     this.routeType = newValue;
            // },
            // updateRouteCurrency: function (newValue) {
            //     this.routeCurrency = newValue;
            // },
            // updateRoutePrice: function (newValue) {
            //     this.routePrice = newValue;
            // },
            clickAction: function (event) {
                //console.log(event.target);

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
                } else if (action === 'edit_route') {
                    this.editRoute(id,index,name);
                } else if (action === 'delete_route') {
                    this.deleteRoute(id,index,name);
                } else {
                    return true;
                }

            },
            addRoute: function() {
                this.shippingRoute = { index: '', name:'', description:'', prices: '', currency: '' };
                $('#shipping-route-modal').modal('show');
            },
            editRoute: function(id,index,name) {
                console.log(index)
                console.log(this.shippingRoutes)
                let shippingRoute = typeof this.shippingRoutes[index] !== 'undefined' ? this.shippingRoutes[index] : null;
                console.log(shippingRoute)
                this.shippingRoute = shippingRoute;
                this.shippingRoute.index = index;
                $('#shipping-route-modal').modal('show');
                //console.log(shippingRoute)
            },
            deleteRoute: function (id,index,name) {
                var context = this;
                Swal.fire({
                    title: "Are you sure?",
                    text: "You are about to delete the " + name + " shipping route.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    showLoaderOnConfirm: true,
                    preConfirm: (delete_route) => {
                    this.deleting = true;
                        return axios.delete("/msl/sales-product/" + id)
                            .then(function (response) {
                                //console.log(response);
                                context.visible = false;
                                context.contactsCount -= 1;
                                $('#shipping-routes-table').bootstrapTable('removeByUniqueId', response.data.id);
                                return swal("Deleted!", "The route was successfully deleted.", "success");
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
    new Vue({
        el: '#sub-menu-action',
        methods: {
            newRoute: function () {
                vmSR.addRoute();
            }
        }
    });
    function formatRoutes(row,index) {
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
        row.buttons = '<a class="btn btn-warning btn-sm" data-index="'+index+'" data-action="edit_route" data-id="'+row.id+'" href="#" data-name="'+row.name+'">Edit</a> &nbsp;'+
            '<a class="btn btn-danger btn-sm" data-index="'+index+'" data-action="delete_route" data-id="'+row.id+'" href="#" data-name="'+row.name+'">Delete</a>';
        return row;
    }
</script>
@endsection
