@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')
			
<div class="row">	
	@include('layouts.blocks.tabler.sub-menu')

    <div class="col-md-9 col-xl-9" id="sales-categories">

        <div class="container" id="product-categories">
            <div class="row mt-3" v-show="categories.length > 0">
                <product-category v-for="(category, index) in categories" class="m4 l4" :key="category.id"
                                  :index="index" :category="category"
                               v-bind:show-delete="true" v-on:update="update"
                               v-on:remove="decrement"></product-category>
            </div>
            <div class="col s12" v-if="categories.length  === 0">
                @component('layouts.blocks.tabler.empty-fullpage')
                    @slot('title')
                        No Product Categories
                    @endslot
                    Add one or more categories to classify your inventory.
                    @slot('buttons')
                        <a href="#" v-on:click.prevent="newCategory" class="btn btn-primary btn-sm">Add Product Category</a>
                    @endslot
                @endcomponent
            </div>
		</div>

	</div>

</div>
@endsection
@section('body_js')

    <script type="text/javascript">
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
                            //console.log(response);
                            vm.categories.push(response.data);
                            return swal("Success", "The product category was successfully created.", "success");
                        })
                            .catch(function (error) {
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
        }

        var vm = new Vue({
            el: '#sales-categories',
            data: {
                categories: {!! json_encode($categories ?: [])  !!}
            },
            methods: {
                decrement: function (index) {
                    console.log('Removing: ' + index);
                    this.categories.splice(index, 1);
                },
                newCategory: function () {
                    addCategory();
                },
                update: function (index, category) {
                    console.log('Updating: ' + index);
                    this.categories.splice(index, 1, category);
                }
            }
        });
        new Vue({
            el: '#sub-menu-action',
            methods: {
                newField: function () {
                    addCategory();
                }
            }
        });
    </script>


@endsection
