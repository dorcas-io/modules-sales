@extends('layouts.tabler')
@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-9">
		<div class="row">

			<div class="col-md-6 col-lg-4">
				<div class="card">
					<div class="card-status bg-blue"></div>
					<div class="card-header">
						<h3 class="card-title">Manual Shipping</h3>
						<div class="card-options">
							<a href="{{ route('sales-shipping-routes') }}" class="btn btn-primary btn-sm">Manage</a>
						</div>
					</div>
					<div class="card-body">
						If you wish to handle shipping by yourself (deliver goods to customers), set your <strong>Routes</strong> &amp; <strong>Pricing</strong> here
					</div>
				</div>
			</div>

			<div class="col-md-6 col-lg-4">
				<div class="card">
					<div class="card-status bg-green"></div>
					<div class="card-header">
						<h3 class="card-title">Provider Shipping</h3>
						<div class="card-options">
							<a href="{{ route('sales-logistics-provider') }}" class="btn btn-success btn-sm">Manage</a>
						</div>
					</div>
					<div class="card-body">
                        If you wish to have a commercial provider handle shipping & delivery, you can manage those preferences here
					</div>
				</div>
			</div>

			<div class="col-md-6 col-lg-4">
				<div class="card">
					<div class="card-status bg-red"></div>
					<div class="card-header">
						<h3 class="card-title">Fulfilment</h3>
						<div class="card-options">
							<a href="{{ route('sales-logistics-fulfilment') }}" class="btn btn-danger btn-sm">Manage</a>
						</div>
					</div>
					<div class="card-body">
                        You can choose if you want to store your products and dispatch them from your own location or at a fulfilment centre.
					</div>
				</div>
			</div>
			
		</div>
	
	</div>

</div>

@endsection
@section('body_js')
    
@endsection
