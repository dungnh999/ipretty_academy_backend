<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCertificateAPIRequest;
use App\Http\Requests\API\UpdateCertificateAPIRequest;
use App\Models\Certificate;
use App\Repositories\CertificateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Http\Resources\CertificateResource;
use Response;

/**
 * Class CertificateController
 * @package App\Http\Controllers\API
 */

class CertificateAPIController extends AppBaseController
{
    /** @var  CertificateRepository */
    private $certificateRepository;

    public function __construct(CertificateRepository $certificateRepo)
    {
        $this->middleware(function ($request, $next) {

            $user = auth()->user();
            if ($user) {
                \App::setLocale($user->lang);
            }
            return $next($request);
        });
        $this->certificateRepository = $certificateRepo;
    }

    public function index(Request $request)
    {
        $certificates = $this->certificateRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse(
            CertificateResource::collection($certificates),
            __('messages.retrieved', ['model' => __('models/certificates.plural')])
        );
    }

    public function store(CreateCertificateAPIRequest $request)
    {
        $input = $request->all();

        $certificate = $this->certificateRepository->create($input);

        return $this->sendResponse(
            new CertificateResource($certificate),
            __('messages.saved', ['model' => __('models/certificates.singular')])
        );
    }

    public function show($id)
    {
        /** @var Certificate $certificate */
        $certificate = $this->certificateRepository->find($id);

        if (empty($certificate)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/certificates.singular')])
            );
        }

        return $this->sendResponse(
            new CertificateResource($certificate),
            __('messages.retrieved', ['model' => __('models/certificates.singular')])
        );
    }

    public function update($id, UpdateCertificateAPIRequest $request)
    {
        $input = $request->all();

        /** @var Certificate $certificate */
        $certificate = $this->certificateRepository->find($id);

        if (empty($certificate)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/certificates.singular')])
            );
        }

        $certificate = $this->certificateRepository->update($input, $id);

        return $this->sendResponse(
            new CertificateResource($certificate),
            __('messages.updated', ['model' => __('models/certificates.singular')])
        );
    }

    public function destroy($id)
    {
        /** @var Certificate $certificate */
        $certificate = $this->certificateRepository->find($id);

        if (empty($certificate)) {
            return $this->sendError(
                __('messages.not_found', ['model' => __('models/certificates.singular')])
            );
        }

        $certificate->delete();

        return $this->sendResponse(
            $id,
            __('messages.deleted', ['model' => __('models/certificates.singular')])
        );
    }
}
