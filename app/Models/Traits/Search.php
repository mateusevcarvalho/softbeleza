<?php

namespace App\Models\Traits;

use App\Managers\TenantManager;
use Illuminate\Database\Eloquent\Builder;

trait Search
{
    public static function getResults($request)
    {
        $query = self::query();

        if (isset($request['whereLike']) && $request['whereLike']) {
            $keys = array_keys($request['whereLike']);
            foreach ($keys as $key) {
                $value = $request['whereLike'][$key];
                $query->where($key, 'like', "%{$value}%");
            }
        }

        if (isset($request['whereLg']) && $request['whereLg']) {
            $keys = array_keys($request['whereLg']);
            foreach ($keys as $key) {
                $value = $request['whereLg'][$key];
                $query->where($key, '>', "{$value}");
            }
        }

        if (isset($request['whereSm']) && $request['whereSm']) {
            $keys = array_keys($request['whereSm']);
            foreach ($keys as $key) {
                $value = $request['whereSm'][$key];
                $query->where($key, '<', "{$value}");
            }
        }

        if (isset($request['whereLike']) && $request['whereLike']) {
            $keys = array_keys($request['whereLike']);
            foreach ($keys as $key) {
                $value = $request['whereLike'][$key];
                $query->where($key, 'like', "%{$value}%");
            }
        }

        if (isset($request['orWhereLike']) && $request['orWhereLike']) {
            $keys = array_keys($request['orWhereLike']);
            foreach ($keys as $key) {
                $value = $request['orWhereLike'][$key];
                $query->orWhere($key, 'like', "%{$value}%");
            }
        }

        if (isset($request['orWhere']) && $request['orWhere']) {
            $keys = array_keys($request['orWhere']);
            foreach ($keys as $key) {
                $value = $request['orWhere'][$key];
                $query->orWhere($key, $value);
            }
        }

        if (isset($request['where']) && $request['where']) {
            $keys = array_keys($request['where']);
            foreach ($keys as $key) {
                $value = $request['where'][$key];
                $query->where($key, $value);
            }
        }

        if (isset($request['orderBy']) && $request['orderBy']) {
            $keys = array_keys($request['orderBy']);
            foreach ($keys as $key) {
                $value = $request['orderBy'][$key];
                $query->orderBy($key, $value);
            }
        }

        if (isset($request['whereLikeHas']) && $request['whereLikeHas']) {
            $keys = array_keys($request['whereLikeHas']);
            foreach ($keys as $key) {
                $value = $request['whereLikeHas'][$key];
                $dataValue = explode(',', $value);
                $query->whereHas($key, function (Builder $query) use ($dataValue) {
                    $query->where($dataValue[0], 'like', '%' . $dataValue[1] . '%');
                });
            }
        }

        if (isset($request['whereHas']) && $request['whereHas']) {
            $keys = array_keys($request['whereHas']);
            foreach ($keys as $key) {
                $value = $request['whereHas'][$key];
                $dataValue = explode(',', $value);
                $query->whereHas($key, function (Builder $query) use ($dataValue) {
                    $query->where($dataValue[0], $dataValue[1]);
                });
            }
        }

        if (isset($request['whereMonthHas']) && $request['whereMonthHas']) {
            $keys = array_keys($request['whereMonthHas']);
            foreach ($keys as $key) {
                $value = $request['whereMonthHas'][$key];
                $dataValue = explode(',', $value);
                $query->whereHas($key, function (Builder $query) use ($dataValue) {
                    $query->whereMonth($dataValue[0], $dataValue[1]);
                });
            }
        }

        if (isset($request['whereBetween']) && $request['whereBetween']) {
            $keys = array_keys($request['whereBetween']);
            foreach ($keys as $key) {
                $value = $request['whereBetween'][$key];
                $dataValue = explode(',', $value);
                $query->whereBetween($key, $dataValue);
            }
        }

        if (isset($request['with']) && $request['with']) {
            foreach ($request['with'] as $with) {
                $query->with($with);
            }
        }

        if (isset($request['usuarioLogado'])) {
            $query->where('usuario_id', TenantManager::get()->id);
        }

        if (isset($request['first'])) {
            return $query->first();
        }

        if (isset($request['page'])) {
            return $query->paginate(15);
        }

        if (isset($request['groupByArray'])) {
            return collect($query->get()->toArray())->groupBy($request['groupByArray']);
        }

        return $query->get();
    }
}
