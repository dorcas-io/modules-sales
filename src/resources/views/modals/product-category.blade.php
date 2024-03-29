<div class="modal fade" id="product-new-category-modal" tabindex="-1" role="dialog" aria-labelledby="product-new-modalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="product-new-modalLabel">Add Product Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{url('/msl/sales-categories')}}"  method="post">
                    {{ csrf_field() }}
                    <fieldset class="form-fieldset">
                        <div class="row">
                            <div class="form-group col-md-12">
                                @if($is_partner)
                                    <select name="parent_category" class="form-control" id="parent_category" v-on:change="fetchSubcategory" required>
                                        <option value=""> ---- Select Parent Category ---- </option>
                                        @foreach($parent_categories as $index => $category)
                                            <option value="{{ $category }}" >
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="form-label" for="parent_category">Parent category</label>
                                @endif
                            </div>
                            <div class="form-group col-md-12">
                                    <select name="parent_sub_category" class="form-control" id="parent_sub_category" >
                                            <option v-for="(sub_cat,index) in subCategories" :key="index">
                                                @{{ sub_cat }}
                                            </option>

                                    </select>
                                    <label class="form-label" for="parent_sub_category">Parent Sub category</label>

                            </div>
                            <div class="form-group col-md-12">
                                <input class="form-control" id="name" type="text" name="name" maxlength="80" required>
                                <label class="form-label" for="name">Category Name</label>
                            </div>

                        </div>
                    </fieldset>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit"  class="btn btn-primary">Map Category</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>