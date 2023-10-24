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
               <div class="card">
                  <div class="card-body">
                  
                    <p>Generate transaction report by spooling form start date to end date</p>
                     {{-- <div style="display:flex;justify-content:space-evenly;width:60%;flex-wrap:wrap;">
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                        <div>
                           <input type="checkbox" name="reference">
                           Reference
                        </div>
                     </div> --}}
                     <form action="{{ route('sales-report-generate') }}" method="POST">
                        @csrf
                        <div>
                           <label>Start Date</label>
                           <input type="date" name="start_date" class="form-control"/>
                        </div>
                        <div>
                           <label>End Date</label>
                           <input type="date" name="end_date" class="form-control"/>
                        </div>
                        <br>
                        <button class="btn btn-primary form-control">Generate Report</button>
                     </form>
                  </div>
                </div>
              </div>
          </div>
{{--          @include('modules-sales::modals.shipping-route')--}}
      </div>

  </div>

   

</div>
@endsection



