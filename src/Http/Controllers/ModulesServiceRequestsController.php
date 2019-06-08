<?php

namespace Dorcas\ModulesServiceRequests\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dorcas\ModulesServiceRequests\Models\ModulesServiceRequests;
use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use App\Http\Controllers\HomeController;
use Hostville\Dorcas\Sdk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ModulesServiceRequestsController extends Controller {

    public function __construct()
    {
        parent::__construct();
        $this->data = [
            'page' => ['title' => config('modules-service-requests.title')],
            'header' => ['title' => config('modules-service-requests.title')],
            'selectedMenu' => 'modules-service-requests',
            'submenuConfig' => 'navigation-menu.modules-service-requests.sub-menu',
            'submenuAction' => ''
        ];
    }

    public function index(Request  $request)
    {
        $this->setViewUiResponse($request);
    	return view('modules-service-requests::index', $this->data);
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'business_id' => 'required|string',
            'modules' => 'required|array',
            'modules.*' => 'required|string'
        ]);
        # validate the request
        try {
            $query = $sdk->createCompanyResource($request->input('business_id'));
            $data = $request->only(['modules']);
            foreach ($data as $key => $value) {
                $query->addBodyParam($key, $value);
            }
            $query = $query->send('post', ['access-grant-requests']);
            # send the request
            if (!$query->isSuccessful()) {
                $message = $response->errors[0]['title'] ?? '';
                throw new \RuntimeException('Failed while sending the request. '.$message);
            }
            $response = (tabler_ui_html_response(['Successfully sent the request.']))->setType(UiResponse::TYPE_SUCCESS);
        } catch (\Exception $e) {
            $response = (tabler_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getServiceRequests(Request $request, Sdk $sdk)
    {
        $limit = $request->query('limit', 12);
        $pageNumber = $request->query('page', 1);
        # get the request data
        $viewMode = $request->session()->get('viewMode');
        # get the view mode
        $query = $sdk->createDirectoryResource()->addQueryArgument('limit', $limit)
                                                ->addQueryArgument('page', $pageNumber);
        if ($viewMode !== 'professional') {
            $query->addQueryArgument('mode', $viewMode);
        }
        $query = $query->send('GET', ['service-requests']);
        if (!$query->isSuccessful()) {
            throw new \RuntimeException($response->errors[0]['title'] ??
                'Errors occurred while fetching service requests for your account.');
        }
        $json = json_decode($query->getRawResponse(), true);
        return response()->json($json);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     * @param string  $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateServiceRequest(Request $request, Sdk $sdk, string $id)
    {
        $this->validate($request, [
            'status' => 'required|in:accepted,rejected'
        ]);
        # validate the request
        $query = $sdk->createDirectoryResource()->addBodyParam('status', $request->input('status'))
                                                ->send('PUT', ['service-requests', $id]);
        if (!$query->isSuccessful()) {
            throw new \RuntimeException($query->errors[0]['title'] ??
                'Errors occurred while marking the service request. Please try again.');
        }
        return response()->json($query->getData());
    }

}