@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')
   
    <div class="col-md-9 col-xl-9">
       
        <div class="row row-cards row-deck" id="products-list">
            <div v-if="productsCount > 0">
             <button type="submit" onclick="masDeleteFunc()" class="btn btn-danger">
                Bulk Delete
              </button>
            </div>
            
            <div class="col-sm-12">
                
                
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                           data-pagination="true"
                           data-search="true"
                           data-side-pagination="server"
                           data-show-refresh="true"
                           data-unique-id="id"
                           data-id-field="id"
                           data-row-attributes="formatProducts"
                           data-url="{{ route('sales-product-search') }}"
                           data-page-list="[10,25,50,100,200,300,500]"
                           data-sort-class="sortable"
                           data-search-on-enter-key="false"
                           v-if="productsCount > 0"
                            id="products-table"
                        v-on:click="clickAction($event)">
                        <thead>
                        <tr>
                            <th data-field="checkbox">#</th>
                            <th data-field="name">Product</th>
                            <th data-field="inventory">Stock</th>
                            {{-- <th data-field="updated_at">Stock Level</th> --}}
                            {{-- <th data-field="barcode_img">Barcode</th> --}}
                            <th data-field="unit_prices">Unit Price(s)</th>
                            <th data-field="created_at">Added On</th>
                            <th data-field="buttons">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <div class="col s12" v-if="productsCount === 0">
                        @component('layouts.blocks.tabler.empty-fullpage')
                            @slot('title')
                                No Products
                            @endslot
                            Add products to be able to manage stock levels, and orders with ease.
                            @slot('buttons')
                                <a href="#" data-toggle="modal" data-target="#product-new-modal" class="btn btn-primary btn-sm">Add Product</a>
                            @endslot
                        @endcomponent
                    </div>

                </div>
            </div>
            @include('modules-sales::modals.product-new')
        </div>

    </div>

</div>
@endsection

@section('body_js')
<script type="text/javascript">
    // $(function() {
    //     $('input[type=checkbox].check-all').on('change', function () {
    //         var className = $(this).parent('div').first().data('item-class') || '';
    //         if (className.length > 0) {
    //             $('input[type=checkbox].'+className).prop('checked', $(this).prop('checked'));
    //         }
    //     });
    // });
    new Vue({
        el: '#products-list',
        data: {
            productsCount: {{ $productsCount }},
            productIds : [],
        },
        computed: {
             // a computed getter
            //  console.log(this.productIds)
            count: function() {
                return 'The shop number is ' + this.productIds
         }
         },
        
        methods: {
            clickAction: function (event) {
                //console.log(event.target);

                let target = event.target;
                if (!target.hasAttribute('data-action')) {
                    target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
                   
                }
                //console.log(target, target.getAttribute('data-action'));
                if(target.getAttribute('data-action') !== null){

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
                    } else if (action === 'delete') {
                        this.deleteItem(id,index,name);
                    } else {
                        return true;
                    }
                }
              

            },
            deleteItem: function (id,index,name) {
                /*console.log(attributes);
                var name = attributes['data-name'] || '';
                var id = attributes['data-id'] || null;
                if (id === null) {
                    return false;
                }*/
                var context = this;
                Swal.fire({
                    title: "Are you sure?",
                    text: "You are about to delete product " + name + " from your inventory.",
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

    function formatProducts(row,index) {
        
        row.checkbox = "<input type=\"checkbox\" data-index=" + index +" onChange=\"massDelete()\" class=\"productId\" value=" + row.id +" >";
      
        // onChange=\"massDelete()\" 

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
        row.buttons = '<a class="btn btn-primary btn-sm" data-index="'+index+'" data-action="view" data-id="'+row.id+'" href="/msl/sales-product/' + row.id + '" data-name="'+row.name+'">View</a> &nbsp;'+
            '<a class="btn btn-danger btn-sm" data-index="'+index+'" data-action="delete" data-id="'+row.id+'" href="#" data-name="'+row.name+'">Delete</a>';
        return row;
    }
</script>


<script type="text/javascript">

 let productIds = [];
 var uniqueIds = []

 function massDelete(){
   
    var select = document.getElementsByClassName('productId');
     
    for (var i = 0; i < select.length; i++) {
            if (select[i].checked) {
                productIds.push(select[i].value)
            }
        }
        uniqueIds = [...new Set(productIds)];
     
        sessionStorage.setItem("unique_ids", uniqueIds.length);
        sessionStorage.getItem("unique_ids")
     

 }

 function masDeleteFunc(){

        if(uniqueIds.length < 1){
            message = "No item to delete"
            return swal("Delete Failed", message, "warning");
        }

        var result = confirm("Want to delete?");
        if (result) {
                for(var i = 0;  i < uniqueIds.length ; i++){
                        var context = this;
                        let res = axios.delete("/msl/sales-product/" + uniqueIds[i])
                        .then(function (response) {
                            //console.log(response);
                            context.visible = false;
                            context.contactsCount -= 1;
                            sessionStorage.removeItem('unique_ids');
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
            }
        }
    }

 


</script>
@endsection
