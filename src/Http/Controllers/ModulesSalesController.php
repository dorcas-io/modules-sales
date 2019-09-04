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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;
use Illuminate\Auth\Access\AuthorizationException;
use Carbon\Carbon;

class ModulesSalesController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-sales.title')],
            'header' => ['title' => config('modules-sales.title')],
            'selectedMenu' => 'modules-sales',
            'submenuConfig' => 'navigation-menu.modules-sales.sub-menu',
            'submenuAction' => ''
        ];
    }

    public function index()
    {
    	$this->data['availableModules'] = HomeController::SETUP_UI_COMPONENTS;
    	return view('modules-sales::index', $this->data);
    }


    public function categories_index(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Product Categories';
        $this->data['header']['title'] .= ' &rsaquo; Product Categories';
        $this->data['selectedSubMenu'] = 'sales-categories';
        $this->data['submenuAction'] = '<a href="#" v-on:click.prevent="newField" class="btn btn-primary btn-block">Add Product Category</a>';

        $this->setViewUiResponse($request);
        $this->data['categories'] = $this->getProductCategories($sdk);
        return view('modules-sales::categories', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function categories_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createProductCategoryResource($id);
        $response = $model->send('delete');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while deleting the category.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.product-categories.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function categories_update(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createProductCategoryResource($id);
        $response = $model->addBodyParam('name', $request->input('name'))
                            ->addBodyParam('slug', $request->input('slug'))
                            ->addBodyParam('description', $request->input('description'))
                            ->addBodyParam('update_slug', $request->input('update_slug'))
                            ->send('PUT');
        # make the request
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Failed while updating the category.');
        }
        $company = $request->user()->company(true, true);
        Cache::forget('business.product-categories.'.$company->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    public function products_index(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Products';
        $this->data['header']['title'] .= ' &rsaquo; Products';
        $this->data['selectedSubMenu'] = 'sales-products';
        $this->data['submenuAction'] = '';

        $this->setViewUiResponse($request);
        $subdomain = get_dorcas_subdomain();
        if (!empty($subdomain)) {
            $this->data['header']['title'] .= ' (Store: '.$subdomain.'/store)';
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

        $this->data['submenuAction'] .= '
            <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                <div class="dropdown-menu">
                <a href="#" data-toggle="modal" data-target="#product-new-modal" class="dropdown-item">Add Product</a>
        ';
        if (!empty($subdomain)) {
            $this->data['submenuAction'] .= '
                <a href="'.$subdomain . '/store" target="_blank" class="dropdown-item">Online Store</a>
            ';
        }
        $this->data['submenuAction'] .= '
                </div>
            </div>
        ';
        return view('modules-sales::products', $this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function products_search(Request $request, Sdk $sdk)
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



    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function product_index(Request $request, Sdk $sdk, string $id)
    {
        $this->data['page']['title'] .= ' &rsaquo; Products';
        $this->data['header']['title'] = 'Products';
        $this->data['selectedSubMenu'] = 'sales-products';
        $this->data['submenuAction'] = '';

        $this->setViewUiResponse($request);
        $response = $sdk->createProductResource($id)->addQueryArgument('include', 'stocks:limit(1|0),orders:limit(1|0)')
                                                    ->send('get');
        $subdomain = get_dorcas_subdomain();
        if (!empty($subdomain)) {
            $this->data['header']['title'] .= ' (Store: '.$subdomain.'/store)';
        }
        $this->data['subdomains'] = $subdomains = $this->getSubDomains($sdk);
        # get the subdomains issued to this customer
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the product at this URL.');
        }
        $this->data['categories'] = $this->getProductCategories($sdk);
        $this->data['product'] = $product = $response->getData(true);
        $this->data['page']['title'] .= ' &rsaquo; ' . $product->name;
        $this->data['header']['title'] .= ' &rsaquo; ' . $product->name;

        $this->data['submenuAction'] .= '
            <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                <div class="dropdown-menu">
        ';
        if ($subdomains->count() > 0) {
            $this->data['submenuAction'] .= '
                <a href="#" data-toggle="modal" data-target="#product-image-modal" class="dropdown-item">Add Image</a>
                <a href="'.$subdomain . '/store" target="_blank" class="dropdown-item">View Store Online</a>
                <!--<a href="'.$subdomain . '/store" target="_blank" class="dropdown-item">View Product Online</a>-->
            ';
        } else {
            $this->data['submenuAction'] .= '
                <a href="' . route('ecommerce-domains') . '" target="_blank" class="dropdown-item">Setup Store</a>
            ';
        }
        $this->data['submenuAction'] .= '
                <a href="#" data-toggle="modal" data-target="#product-inventory-modal" class="dropdown-item">Manage Stock</a>
                </div>
            </div>
        ';

        return view('modules-sales::product', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * @param string $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function product_redirect(string $id)
    {
        return redirect()->route('sales-products-single', [$id]);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function product_addCategories(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'categories' => 'required|array',
        ]);
        # validate the request
        try {
            $query = $sdk->createProductResource($id)->addBodyParam('ids', $request->input('categories', []))
                                                    ->send('POST', ['categories']);
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException('Failed while adding the selected categories. Please try again.');
            }
            Cache::forget('business.product-categories.'.$this->getCompany()->id);
            $message = ['Successfully added the selected categories.'];
            $response = (tabler_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect()->route('sales-products-single', [$id])->with('UiResponse', $response);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function product_addImage(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'image' => 'required_if:action,add_product_image|image',
        ]);
        # validate the request
        try {
            if ($request->action === 'add_product_image') {
                # update the business information
                $file = $request->file('image');
                $query = $sdk->createProductResource($id)->addMultipartParam('image', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                                                            ->send('post', ['images']);
                # send the request
                if (!$query->isSuccessful()) {
                    throw new \RuntimeException('Failed while uploading the product image. Please try again.');
                }
                $message = ['Successfully added new product image.'];
            }
            $response = (tabler_ui_html_response($message))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect()->route('sales-products-single', [$id])->with('UiResponse', $response);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function product_updateStocks(Request $request, Sdk $sdk, string $id)
    {
        try {
            $query = $sdk->createProductResource($id)->addBodyParam('action', $request->action)
                                                    ->addBodyParam('quantity', $request->quantity)
                                                    ->addBodyParam('comment', $request->description)
                                                    ->send('post', ['stocks']);
            # send the request
            if (!$query->isSuccessful()) {
                # it failed
                $message = $query->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while updating product stocks. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully updated product stock and inventory.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect()->route('sales-products-single', [$id])->with('UiResponse', $response);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function product_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createProductResource($id);
        $response = $model->send('delete');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the product.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function product_deleteCategory(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'categories' => 'required|array',
            'categories.*' => 'required|string'
        ]);
        # validate the request
        $model = $sdk->createProductResource($id)->addBodyParam('ids', $request->input('categories'));
        $response = $model->send('delete', ['categories']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the selected categories.');
        }
        Cache::forget('business.product-categories.'.$this->getCompany()->id);
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function product_deleteImage(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createProductResource($id)->addBodyParam('id', $request->id);
        $response = $model->send('delete', ['images']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the product image.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function product_stocks(Request $request, Sdk $sdk, string $id)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        # get the request parameters
        $model = $sdk->createProductResource($id)->addQueryArgument('limit', $limit)
                                                    ->addQueryArgument('page', get_page_number($offset, $limit));
        if (!empty($search)) {
            $model = $model->addQueryArgument('search', $search);
        }
        $response = $model->send('get', ['stocks']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching stock entries.');
        }
        $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
        # set the total
        $this->data['rows'] = $response->data;
        # set the data
        return response()->json($this->data);
    }




    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function orders_index(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Invoices';
        $this->data['header']['title'] .= ' &rsaquo; Invoices';
        $this->data['selectedSubMenu'] = 'sales-orders';
        $this->data['submenuAction'] = '<a href="'.route('sales-orders-new').'" class="btn btn-primary btn-block">Add Invoice</a>';

        $this->setViewUiResponse($request);
        $ordersCount = 0;
        $query = $sdk->createOrderResource()->addQueryArgument('limit', 1)->send('get');
        if ($query->isSuccessful()) {
            $ordersCount = $query->meta['pagination']['total'] ?? 0;
        }
        $this->data['ordersCount'] = $ordersCount;
        return view('modules-sales::orders', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order_new(Request $request, Sdk $sdk)
    {
        $this->data['page']['title'] .= ' &rsaquo; Add Order';
        $this->data['header']['title'] .= ' &rsaquo; Add Order';
        $this->data['selectedSubMenu'] = 'sales-orders';
        $this->data['submenuAction'] = '';

        $this->setViewUiResponse($request);
        $this->data['products'] = $this->getProducts($sdk);
        $this->data['customers'] = $this->getCustomers($sdk);
        return view('modules-sales::orders_new', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function order_create(Request $request, Sdk $sdk)
    {
        $company = $this->getCompany();
        # get the company
        $this->validate($request, [
            'title' => 'required|string|max:80',
            'description' => 'nullable|string',
            'currency' => 'required|string|size:3',
            'amount' => 'required|numeric|min:0',
            'due_at' => 'nullable|date_format:"d F, Y"',
            'reminders_on' => 'nullable',
            'is_quote' => 'nullable',
            'customer' => 'required|string',
            'product_name' => 'required_without:products|string|max:80',
            'product_quantity' => 'required_without:products|numeric|min:1',
            'product_price' => 'required_without:products|numeric|min:0',
            'products' => 'required_without:product_name|array',
            'products.*' => 'string',
            'quantities' => 'required_with:products|array',
            'quantities.*' => 'numeric|min:1',
            'unit_prices' => 'required_with:products|array',
            'unit_prices.*' => 'numeric|min:0',
            'customer_email' => 'required_if:customer,add_new|email',
            'customer_firstname' => 'required_if:customer,add_new|string|max:30',
            'customer_lastname' => 'required_if:customer,add_new|string|max:30',
            'customer_phone' => 'required_if:customer,add_new|string|max:30',
        ]);
        # validate the request
        try {
            $customerId = $request->customer;
            # the default customer ID
            if (strtolower($customerId) === 'add_new') {
                # check the customer entry mode
                $storeService = $sdk->createStoreService();
                # create the store service
                $customer = (clone $storeService)->addBodyParam('firstname', $request->customer_firstname)
                                                ->addBodyParam('lastname', $request->customer_lastname)
                                                ->addBodyParam('email', $request->customer_email)
                                                ->addBodyParam('phone', $request->customer_phone)
                                                ->send('POST', [$company->id, 'customers']);
                # we put step 1 & 2 in one call
                if (!$customer->isSuccessful()) {
                    throw new \RuntimeException('Failed while creating the new customer account...Please try again later.');
                }
                $customerId = $customer->getData()['id'];
                # set the new customer ID
                Cache::forget('crm.customers.'.$company->id);
                # clear the cache
            }
            $query = $sdk->createOrderResource()->addBodyParam('title', $request->title)
                                                ->addBodyParam('description', $request->description ?: '')
                                                ->addBodyParam('currency', $request->currency)
                                                ->addBodyParam('amount', $request->amount)
                                                ->addBodyParam('customers', [$customerId]);
            if ($request->has('due_at')) {
                $date = Carbon::createFromFormat('d F, Y', $request->due_at);
                if (!empty($date)) {
                    $query = $query->addBodyParam('due_date', $date->format('Y-m-d'))
                                    ->addBodyParam('enable_reminder', (int) $request->has('reminders_on'));
                }
            }
            if ($request->has('is_quote')) {
                $query = $query->addBodyParam('is_quote', (int) $request->has('is_quote'));
            }
            if ($request->has('products') && !empty($request->products)) {
                $products = [];
                foreach ($request->products as $index => $productId) {
                    $quantity = $request->quantities[$index] ?? 0;
                    $price = $request->unit_prices[$index] ?? -1;
                    # set the values
                    if ($quantity === 0 || $price === -1) {
                        throw new \UnexpectedValueException(
                            'There is a problem in your form, one of your quantities or prices is invalid.'
                        );
                    }
                    $products[] = ['id' => $productId, 'quantity' => $quantity, 'price' => $price];
                }
                $query = $query->addBodyParam('products', $products);
            } else {
                $product = [
                    'name' => $request->product_name,
                    'quantity' => $request->product_quantity,
                    'price' => $request->product_price
                ];
                $query = $query->addBodyParam('product', $product);
            }
            $query = $query->send('post');
            # send the request
            if (!$query->isSuccessful()) {
                $message = $query->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while creating the order. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully created invoice.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(route('sales-orders'))->with('UiResponse', $response);
        //return redirect(url()->current())->with('UiResponse', $response);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function orders_search(Request $request, Sdk $sdk)
    {
        $search = $request->query('search', '');
        $sort = $request->query('sort', '');
        $order = $request->query('order', 'asc');
        $offset = (int) $request->query('offset', 0);
        $limit = (int) $request->query('limit', 10);
        $product = $request->query('product');
        # get the request parameters
        if (!empty($product)) {
            $query = $sdk->createProductResource($product)->addQueryArgument('include', 'orders:limit(10000|0)')
                                                            ->send('get');
            if (!$query->isSuccessful()) {
                // do something here
                throw new RecordNotFoundException($query->errors[0]['title'] ?? 'Could not find any matching orders.');
            }
            $this->data['rows'] = $data = $query->getData(true)->orders['data'];
            # set the data
            $this->data['total'] = count($data);
            # set the total
        } else {
            $query = $sdk->createOrderResource()->addQueryArgument('limit', $limit)
                                                ->addQueryArgument('page', get_page_number($offset, $limit));
            if (!empty($search)) {
                $query = $query->addQueryArgument('search', $search);
            }
            $response = $query->send('get');
            if (!$response->isSuccessful()) {
                // do something here
                throw new RecordNotFoundException($response->errors[0]['title'] ?? 'Could not find any matching orders.');
            }
            $this->data['total'] = $response->meta['pagination']['total'] ?? 0;
            # set the total
            $this->data['rows'] = $response->data;
            # set the data
        }
        return response()->json($this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function order_index(Request $request, Sdk $sdk, string $id)
    {
        $this->data['page']['title'] .= ' &rsaquo; Orders';
        $this->data['header']['title'] .= ' &rsaquo; Orders';
        $this->data['selectedSubMenu'] = 'sales-orders';


        $this->setViewUiResponse($request);
        $response = $sdk->createOrderResource($id)->addQueryArgument('include', 'customers:limit(10000|0)')
                                                    ->send('get');
        if (!$response->isSuccessful()) {
            abort(404, 'Could not find the order at this URL.');
        }
        $this->data['dorcasUrlGenerator'] = $sdk->getUrlRegistry();
        $this->data['order'] = $order = $response->getData(true);
        $this->data['header']['title'] .= ' - Invoice #' . $order->invoice_number;
        $this->data['page']['title'] .= ' - Invoice #' . $order->invoice_number;

        $this->data['submenuAction'] .= '
            <div class="dropdown"><button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Actions</button>
                <div class="dropdown-menu">
                    <a href="'.route('sales-orders-new').'" class="dropdown-item">New Order</a>
                    <a href="#!" v-on:click.prevent="deleteOrder(\''.$order->id.'\')" class="dropdown-item">Delete Order</a>
                </div>
            </div>
        ';
        return view('modules-sales::order', $this->data);
    }


    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_delete(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createOrderResource($id);
        $response = $model->send('delete');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the order.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_update(Request $request, Sdk $sdk, string $id)
    {
        $dueDate = !empty($request->due_at) ? Carbon::parse($request->due_at) : null;
        $model = $sdk->createOrderResource($id)->addBodyParam('title', $request->title)
                                                ->addBodyParam('description', $request->description)
                                                ->addBodyParam('enable_reminder', (int) $request->input('reminders_on'));
        if (!empty($dueDate)) {
            $model = $model->addBodyParam('due_at', $dueDate->format('Y-m-d'));
        }
        $response = $model->send('put');
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while updating the order.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_deleteCustomer(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createOrderResource($id)->addBodyParam('id', $request->input('id'));
        $response = $model->send('delete',  ['customers']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while deleting the order.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_updateCustomerOrder(Request $request, Sdk $sdk, string $id)
    {
        $model = $sdk->createOrderResource($id)->addBodyParam('id', $request->input('id'))
                                                ->addBodyParam('is_paid', $request->input('is_paid'));
        $response = $model->send('put',  ['customers']);
        if (!$response->isSuccessful()) {
            // do something here
            throw new \RuntimeException($response->errors[0]['title'] ?? 'Failed while updating the customer order information.');
        }
        $this->data = $response->getData();
        return response()->json($this->data);
    }



}