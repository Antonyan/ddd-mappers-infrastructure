<?php

namespace Infrastructure\Models;

use Symfony\Component\HttpFoundation\Request;

interface CRUDServiceInterface
{
    /**
     * @param Request $request
     * @return GetEntityJsonResponse
     */
    public function load(Request $request) : GetEntityJsonResponse;

    /**
     * @param Request $request
     * @return CreateEntityJsonResponse
     */
    public function create(Request $request) : CreateEntityJsonResponse;

    /**
     * @param Request $request
     * @param $id
     * @return UpdateEntityJsonResponse
     */
    public function update(Request $request, $id) : UpdateEntityJsonResponse;

    /**
     * @param $id
     * @return DeleteEntityJsonResponse
     */
    public function delete($id) : DeleteEntityJsonResponse;

    /**
     * @param $id
     * @return GetEntityJsonResponse
     */
    public function get($id) : GetEntityJsonResponse;
}