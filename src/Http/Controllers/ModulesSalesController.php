<?php

namespace Dorcas\ModulesSales\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\Contactform\Models\ModulesSales;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use App\Exceptions\RecordNotFoundException;

class ModulesSalesController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-sales.title')],
            'header' => ['title' => ''],

            'selectedMenu' => 'modules-sales',
            'submenuConfig' => 'navigation-menu.modules-sales.sub-menu',
            'submenuAction' => ''
        ];
        $this->data['page']['header'] = ['title' => 'Sales'];
    }

    public function index()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-sales::index', $this->data);
    }

    public function categories_index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $this->data['categories'] = $this->getProductCategories($sdk);
        return view('modules-sales::categories.index', $this->data);
    }

    public function categories_create(Request $request, Sdk $sdk)
    {
        $name = $request->input('name', null);
        $response = $sdk->createProductCategoryResource()->addBodyParam('name', $name)->send('POST');
        # send the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while creating the product category.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.product-categories.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    public function products_index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $subdomain = get_dorcas_subdomain();
        if (!empty($subdomain)) {
            $this->data['page']['header']['title'] .= ' (Store: '.$subdomain.'/store)';
        }
        $productCount = 0;
        $query = $sdk->createProductResource()->addQueryArgument('limit', 1)->send('get');
        if ($query->isSuccessful()) {
            $productCount = $query->meta['pagination']['total'] ?? 0;
        }
        $this->data['categories'] = $this->getProductCategories($sdk);
        $this->data['subdomain'] = get_dorcas_subdomain($sdk);
        # set the subdomain
        $this->data['productsCount'] = $productCount;
        return view('modules-sales::products.index', $this->data);
    }

    public function product_create(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'name' => 'required|string|max:80',
            'currency' => 'required|string|size:3',
            'price' => 'required|numeric',
            'description' => 'nullable'
        ]);
        # validate the request
        try {
            $price = ['currency' => $request->currency, 'price' => $request->price];
            # create the price payload
            $resource = $sdk->createProductResource();
            $resource = $resource->addBodyParam('name', $request->name)
                                    ->addBodyParam('description', $request->description)
                                    ->addBodyParam('prices', [$price]);
            # the resource
            $response = $resource->send('post');
            # send the request
            if (!$response->isSuccessful()) {
                # it failed
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while adding the product. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully added product.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    public function product_lists(Request $request, Sdk $sdk)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $query = $sdk->createProductResource();
        $query = $query->addQueryArgument('limit', $limit)
                        ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $query = $query->addQueryArgument('search', $search);
        }
        $response = $query->send('get');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching products.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }

    public function product_index(Request $request, Sdk $sdk, string $id)
    {
        $this->setViewUiResponse($request);
        $response = $sdk->createProductResource($id)->addQueryArgument('include', 'stocks:limit(1|0),orders:limit(1|0)')
                                                    ->send('get');
        $subdomain = get_dorcas_subdomain();
        if (!empty($subdomain)) {
            $this->data['page']['header']['title'] .= ' (Store: '.$subdomain.'/store)';
        }
        $this->data['subdomains'] = $this->getSubDomains($sdk);
        # get the subdomains issued to this customer
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the product at this URL.');
        }
        $this->data['categories'] = $this->getProductCategories($sdk);
        $this->data['product'] = $product = $response->getData(true);
        $this->data['page']['title'] .= ' - ' . $product->name;
        return view('modules-sales::products.product', $this->data);
    }
    public function product_update(Request $request, Sdk $sdk, string $id)
    {
        try {
            $prices = [];
            if ($request->has('prices')) {
                foreach ($request->currencies as $index => $currency) {
                    $price = (float) $request->prices[$index] ?? 0;
                    $prices[] = ['currency' => $currency, 'price' => $price];
                }
            }
            $query = $sdk->createProductResource($id)->addBodyParam('name', $request->name)
                                                    ->addBodyParam('description', $request->description)
                                                    ->addBodyParam('default_price', $request->default_price)
                                                    ->addBodyParam('prices', $prices)
                                                    ->send('post');
            # send the request
            if (!$query->isSuccessful()) {
                # it failed
                $message = $query->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating the product. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully updated product information.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }



    public function invoices_index()
    {
        return view('modules-sales::invoices.index', $this->data);
    }

    public function orders_index()
    {
        return view('modules-sales::orders.index', $this->data);
    }



}