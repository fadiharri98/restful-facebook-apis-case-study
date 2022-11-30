<?php

namespace Helpers;

use Exception;
use CustomExceptions\ResourceNotFoundException;
use CustomExceptions\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Models\User;

class ResourceHelper
{
    /**
     * @return Model
     * @throws ResourceNotFoundException if no match resource by id
     */
    public static function findResource($model, $resource_id, $with=[], $resource_name=null)
    {
        /**
         * @var Builder $query
         */
        $query = $model::query();

        if($with)
        {
            $query = $query->with($with);
        }

        $resource = $query->find($resource_id);

        if(! $resource)
        {
            $_ = explode("\\", $model);
            throw new ResourceNotFoundException($resource_name ?: array_pop($_));
        }

        return $resource;
    }

    /**
     * @return LengthAwarePaginator|Collection
     */
    public static function getResources($model, $filters=[], $with=[], $paginated=false)
    {
        /**
         * @var Builder $query
         */
        $query = $model::query();

        foreach ($filters as $column => $filter)
        {
            if (key_exists('operator', $filter)) {

                $query->where($column, $filter['operator'], $filter['value']);
                continue;
            }

            $query->where($column, $filter['value']);
        }

        if (is_array($with) && ! empty($with))
        {
            $query->with($with);
        }

        if ($paginated)
        {
            $perPage = $_GET['limit'] ?? 15;
            $currentPage = $_GET['page'] ?? 1;

            return $query->paginate($perPage, ['*'], 'page', $currentPage);
        }

        return $query->get();
    }

    /**
     * @return LengthAwarePaginator
     */
    public static function getResourcesPaginated($model, $filters=[], $with=[])
    {
        return self::getResources($model, $filters, $with, true);
    }

    /**
     * @param User $user
     * @return void
     * @throws AuthorizationException if admin isn't admin
     */
    public static function validateUserIsAdmin($user)
    {
        if(! $user->is_admin) {

            throw new AuthorizationException("this API isn't allowed for none admin users.");
        }

    }

    /**
     * @param User $user
     * @param Model $model
     * @param bool $skip_admin
     * @return void
     * @throws AuthorizationException
     * @throws Exception if passed model hasn't user_id
     */
    public static function validateIfUserAllowedTo($user, $model, $skip_admin=true)
    {
        /**
         * bad use of helper method.
         */
        if ($model->user_id === null)
        {
            throw new Exception("to validate if user allowed, the model should has user_id.");
        }

        if ($skip_admin && $user->is_admin)
        {
            return;
        } else if ($user->id != $model->user_id) {

            throw new AuthorizationException("not allowed.");
        }

    }
}