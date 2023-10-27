<?php

namespace App\Http\Controllers\Api;

use App\Actions\Companies\CreateCompany;
use App\Actions\Companies\DeleteCompany;
use App\Actions\Companies\GenerateCompanyShowCache;
use App\Actions\Companies\UpdateCompany;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\Companies\CreateCompanyRequest;
use App\Http\Requests\Companies\DeleteCompanyRequest;
use App\Http\Requests\Companies\ListCompanyRequest;
use App\Http\Requests\Companies\ShowCompanyRequest;
use App\Http\Requests\Companies\UpdateCompanyRequest;
use App\Models\Company;
use App\Support\QueryBuilder;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Company
 *
 * APIs for managing companies
 */
class CompanyController extends Controller
{
    use ApiResponse;

    /**
     * index()
     *
     * Reads and returns a collection of all companies.
     * <aside><ul><li>list-company</li></ul></aside>
     */
    public function index(ListCompanyRequest $request)
    {
        return QueryBuilder::for(Company::class)->list();
    }

    /**
     * show()
     *
     * Reads and returns a company.
     * <aside><ul><li>show-company</li></ul></aside>
     * @urlParam company integer Company ID. Example: 1
     */
    public function show(ShowCompanyRequest $request, int $id, GenerateCompanyShowCache $generateCompanyShowCache)
    {
        return $generateCompanyShowCache->execute($id);
    }

    /**
     * store()
     *
     * Creates, saves and returns a company.
     * <aside><ul><li>create-company</li></ul></aside>
     */
    public function store(CreateCompanyRequest $request, CreateCompany $createCompany)
    {
        return $this->respond($createCompany->execute($request->all()));
    }

    /**
     * update()
     *
     * Updates, saves and returns a company.
     * <aside><ul><li>update-company</li></ul></aside>
     * @urlParam company integer Company ID. Example: 1
     */
    public function update(UpdateCompanyRequest $request, int $id, UpdateCompany $updateCompany)
    {
        return $this->respond($updateCompany->execute($id, $request->all()));
    }

    /**
     * destroy()
     *
     * Deletes a company.
     * <aside><ul><li>delete-company</li></ul></aside>
     * @urlParam company integer Company ID. Example: 1
     */
    public function destroy(DeleteCompanyRequest $request, int $id, DeleteCompany $deleteCompany)
    {
        return $this->respondDeleted($deleteCompany->execute($id));
    }

	/**
	* userCompanies()
	*
	* Lists companies based on their user.
	* <aside><ul><li>list-company</li></ul></aside>
	* @urlParam users integer[] User IDs Example: [1,2,3]
	*/
	public function userCompanies(string $userIDs)
	{
		$userIDs = explode(',', $userIDs);
		abort(501, 'User companies not implemented');
		//return $this->respond(QueryBuilder::for(Company::class)->whereHas('users', function ($q) use ($userIDs) {$q->whereIn('id', $userIDs);})->list());
	}

	/**
	* serverCompanies()
	*
	* Lists companies based on their server.
	* <aside><ul><li>list-company</li></ul></aside>
	* @urlParam servers integer[] Server IDs Example: [1,2,3]
	*/
	public function serverCompanies(string $serverIDs)
	{
		$serverIDs = explode(',', $serverIDs);
		abort(501, 'Server companies not implemented');
		//return $this->respond(QueryBuilder::for(Company::class)->whereHas('servers', function ($q) use ($serverIDs) {$q->whereIn('id', $serverIDs);})->list());
	}

}

//Generated 27-10-2023 10:55:44
