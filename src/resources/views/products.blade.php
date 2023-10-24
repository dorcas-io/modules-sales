@extends('layouts.tabler')
@section('body_content_header_extras')
<style>
    .file-upload {
        position: relative;
        display: inline-block;
    }

    .file-upload-label {
        display: inline-block;
        padding: 10px 20px;
        background-color:#467fcf;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
    }

    .file-upload-input {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        cursor: pointer;
    }
    output{
        width: 30%;
        /*min-height: 150px;*/
        display: flex;
        justify-content: flex-start;
        flex-wrap: wrap;
        gap: 15px;
        position: relative;
        border-radius: 5px;
    }

    output .image{
        /*height: 150px;*/
        border-radius: 5px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        position: relative;
    }

    output .image img{
        height: 100%;
        width: 100%;
    }

</style>
@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">
    @include('layouts.blocks.tabler.sub-menu')
   
    <div class="col-md-9 col-xl-9">
       
        <div class="row row-cards row-deck" id="products-list">
{{--            <div v-if="productsCount > 0">--}}
{{--             <button type="submit" onclick="masDeleteFunc()" class="btn btn-danger">--}}
{{--                Bulk Delete--}}
{{--              </button>--}}
{{--            </div>--}}

            
            <div class="col-sm-12">

                <div style="display: flex;justify-content: flex-end;margin-bottom:3%;"  v-if="productsCount > 0">
                    <a href=""  data-toggle="modal" data-target="#product-new-modal"   class="btn btn-primary btn-sm">Add Product</a>
                </div>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap bootstrap-table"
                           data-pagination="true"
                           data-search="false"
                           data-side-pagination="server"
                           data-show-refresh="false"
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
                            {{-- <th data-field="checkbox">#</th> --}}
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
    function addCategory() {

        Swal.fire({
            title: 'New Category',
            text: "Enter the name for the category:",
            input: "text",
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Save',
            animation: "slide-from-top",
            showLoaderOnConfirm: true,
            inputPlaceholder: "e.g. Stationery",
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to write something!'
                }
            },
            preConfirm: (value) => {
                return axios.post("/msl/sales-categories", {
                    name: value
                }).then(function (response) {
                    console.log(response);
                    // $('#product-new-modal').show();
                    return swal("Success", "The product category was successfully created.", "success");
                })
                    .catch(function (error) {
                        console.log(error)
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
                        return swal("Oops!", message, "warning");
                    });
            }

        });
        $('#product-new-modal').hide();
    }


    var vm = new Vue({
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
            newCategory: function () {
                addCategory();
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
        
        // row.checkbox = "<input type=\"checkbox\" data-index=" + index +" onChange=\"massDelete()\" class=\"productId\" value=" + row.id +" >";
      
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


const inputFile = document.getElementById('upload');
const outputFile = document.getElementById("outputUpload")


let images = []
inputFile.addEventListener("change", () => {
    const file = inputFile.files
    images = [];
    images.push(file[0])
    displayImagesOnUpload()
})

function displayImagesOnUpload() {
    let img = ""

    images.forEach((image, index) => {
        img += `<div class="image">
            <img src="${URL.createObjectURL(image)}" alt="image">
            <span onclick="deleteImage(${index})">&times;</span>
            </div>`
    })
    outputFile.innerHTML = img
}


</script>
@endsection
