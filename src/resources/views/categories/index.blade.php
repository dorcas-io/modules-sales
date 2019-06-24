@extends('layouts.tabler')
@section('body_content_header_extras')
<br>
<a href="#" v-on:click.prevent="newField" class="btn btn-primary" style="float: right;">Add New Category</a>
        <br><br><br>
@endsection
@section('body_content_main')
@include('layouts.blocks.tabler.alert')

			
<div class="row" id="personal-profile">	
	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-7">
		<div class="row" id="product-categories">

				<product-category v-for="(category, index) in categories" :key="category.id"
                              :index="index" :category="category"
                           v-bind:show-delete="true" v-on:update="update"
                           v-on:remove="decrement">
                </product-category>
                
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
                preConfirm: (inputValue) => {


                    if (inputValue === false) return false;
                    if (inputValue === "") {
                        swal.showInputError("You need to write something!");
                        return false
                    }
                    axios.post("/xhr/inventory/categories", {
                        name: inputValue
                    }).then(function (response) {
                        console.log(response);
                        vm.categories.push(response.data);
                        return swal("Success", "The product category was successfully created.", "success");
                    })
                        .catch(function (error) {
                            var message = '';
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
                            return swal("Oops!", message, "warning");
                        });



                  },






                });
        }

        var vm = new Vue({
            el: '#product-categories',
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
            el: '#breadcrumbs-wrapper',
            methods: {
                newField: function () {
                    addCategory();
                }
            }
        });
    </script>


@endsection
