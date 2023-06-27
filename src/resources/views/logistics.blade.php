@extends('layouts.tabler')

@section('head_css')
<style type="text/css">
	.card-stamp {
		--stamp-size: 7rem;
		position: absolute;
		top: 0;
		right: 0;
		width: calc(var(--stamp-size) * 1);
		height: calc(var(--stamp-size) * 1);
		max-height: 100%;
		border-top-right-radius: 4px;
		opacity: .2;
		overflow: hidden;
		pointer-events: none;
	}
	.bg-primary-lt {
		color: #206bc4!important;
		background: rgba(32,107,196,.05)!important;
	}

	.text-primary {
		--tblr-text-opacity: 1;
		color: rgba(var(--tblr-primary-rgb),var(--tblr-text-opacity))!important;
	}

	.text-primary {
		color: #206bc4!important;
	}

	.card-stamp-icon {
		background: #626976;
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 100rem;
		width: calc(var(--stamp-size) * 1);
		height: calc(var(--stamp-size) * 1);
		position: relative;
		top: calc(var(--stamp-size) * -.25);
		right: calc(var(--stamp-size) * -.25);
		font-size: calc(var(--stamp-size) * .75);
		transform: rotate(10deg);
	}
	.bg-white {
		--tblr-bg-opacity: 1;
		background-color: rgba(var(--tblr-white-rgb),var(--tblr-bg-opacity))!important;
	}

</style>
@endsection

@section('body_content_header_extras')

@endsection

@section('body_content_main')

@include('layouts.blocks.tabler.alert')

<div class="row">

	@include('layouts.blocks.tabler.sub-menu')
	<div class="col-md-9">
		<div class="row">
			<div class="col-md-12 col-lg-12">
				<div class="card bg-primary-lt">
					<div class="card-stamp">
						<div class="card-stamp-icon bg-white text-primary">
							<!-- Download SVG icon from http://tabler-icons.io/i/truck-delivery -->
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck-delivery" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
								<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
								<path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
								<path d="M5 17h-2v-4m-1 -8h11v12m-4 0h6m4 0h2v-6h-8m0 -5h5l3 5"></path>
								<path d="M3 9l4 0"></path>
							</svg>
						</div>
					</div>
					<div class="card-body">
						<h3 class="card-title">Logistics</h3>
						<p class="text-muted">{{ $shippingSelectionMessage }}</p>
					</div>
				</div>
			</div>
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
							<a href="#" class="btn btn-success btn-sm">Manage</a>
							<!-- {{ route('sales-logistics-provider') }} -->
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
							<a href="#" class="btn btn-danger btn-sm">Manage</a>
							<!-- {{ route('sales-logistics-fulfilment') }} -->
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
