<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class DataTableLonsumService
{
    public function getJsonResponse(Request $request, $query, $bindings = null)
    {
        $params = $bindings ?? [];
        // Apply pagination
        $perPage = $request->input('limit', 10);
        $currentPage = ceil(($request->input('page') - 1) / $perPage + 1); // Corrected calculation

        if (is_string($query)) {
            try {
                // Clone the original query to get total records count
                $originalRecord = DB::select($query, $params);

                // Apply filter
                $this->applyFilter($query, $params, $request);

                // Apply sorting
                $this->applySorting($query, $request);

                $data = DB::select($this->addLimitOffset($query, $perPage, $currentPage), $params);
                $totalRecords = count($originalRecord);

                // Convert the data to array
                $dataArray = array_map(function ($item) {
                    return (array) $item;
                }, $data);

                return [
                    // 'totalRecord' => $totalFiltered,
                    'recordsTotal' => $totalRecords,
                    'data' => $dataArray,
                    'pagination' => [
                        'currentPage' => $currentPage,
                        'perPage' => $perPage,
                        'lastPage' => ceil($totalRecords / $perPage),
                        'recordPerPage' => count($dataArray),
                    ],
                ];
            } catch (Exception $ex) {
                return $ex;
            }
        } elseif ($query instanceof Builder) {
            try {
                // Clone the original query to get total records count
                $totalRecordsQuery = clone $query;
                $totalRecords = $totalRecordsQuery->count();

                // Apply searching if any
                $this->applySearch($query, $request);

                // Get the total count after applying searching
                $totalFiltered = $query->count();

                // Apply ordering
                $this->applyOrdering($query, $request);

                // Apply pagination
                $perPage = $request->input('displayLength', 10);
                // $currentPage = ($request->input('page') - 1) / $perPage + 1;
                $currentPage = ceil(($request->input('page') - 1) / $perPage + 1); // Corrected calculation
                // dd($currentPage);

                $data = $query->paginate($perPage, ['*'], 'page', $currentPage);

                // Extract the columns from the payload
                // $requestedColumns = collect($request->input('columns'))->pluck('data')->toArray();
                $requestedColumns = $request->input('columns');

                // Filter the data to keep only the requested columns
                $filteredData = $data->map(function ($item) use ($requestedColumns) {
                    return collect($item)->only($requestedColumns)->toArray();
                });

                return [
                    'recordsTotal' => $totalRecords,
                    'recordsFiltered' => $totalFiltered,
                    'data' => $filteredData->toArray(),
                    'pagination' => [
                        'currentPage' => $data->currentPage(),
                        'perPage' => $data->perPage(),
                        'lastPage' => $data->lastPage(),
                        'total' => $data->total(),
                    ],
                ];
            } catch (Exception $ex) {
                return $ex;
            }    
        } else {

        }
    }

    protected function applyFilter(&$query, &$params, Request $request)
    {
        $hasWhere = false;
        if ($request->has('filterParam')) {
            $filterConditions = $request->input('filterParam');
            foreach ($filterConditions as $filterCondition) {
                $column = $filterCondition['column']; // Assuming this is the column name
                $value = $filterCondition['value'];
                if (is_array($value)) {
                    $operator = $filterCondition['operator'] ?? "BETWEEN"; 

                    // Adjust the WHERE clause based on the filter condition
                    if (!empty($column) && !empty($operator) && count($value) > 0) {
                        if (!$hasWhere) {
                            $query .= " WHERE $column $operator ?";
                        } else {
                            $query .= " AND $column $operator ?";
                        }
                        // Add the value to the bindings array
                        $params[] = $value;
                    }
                } else {
                    $operator = $filterCondition['operator'] ?? "="; // Assuming this is the operator
    
                    // Adjust the WHERE clause based on the filter condition
                    if (!empty($column) && !empty($operator) && !empty($value)) {
                        if (!$hasWhere) {
                            $query .= " WHERE $column $operator ?";
                        } else {
                            $query .= " AND $column $operator ?";
                        }
                        // Add the value to the bindings array
                        $params[] = $value;
                    }
                }
            }
        }
    }

    protected function applySorting(&$query, Request $request)
    {
        $hasOrder = false;
        if ($request->has('sortParam')) {
            $sorts = $request->input('sortParam');

            foreach ($sorts as $sort) {
                $sortColumn = $sort['sortBy'];
                $sortDirection = $sort['sortType'];

                // Adjust the ORDER BY clause based on the ordering criteria
                if (!empty($sortColumn) && !empty($sortDirection)) {
                    if (!$hasOrder) {
                        $query .= " ORDER BY $sortColumn $sortDirection";
                        $hasOrder = true;
                    } else {
                        $query .= ", $sortColumn $sortDirection";
                    }
                }
            }
        }
    }

    protected function addLimitOffset($query, $perPage, $currentPage)
    {
        return "$query LIMIT $perPage OFFSET " . (($currentPage - 1) * $perPage);
    }

    protected function applySearch(Builder $query, Request $request)
    {
        if ($request->has('search')) {
            $searchConditions = $request->input('search');
            if (is_string($request->has('search'))) {
                $column = $request->input('columns');
            } else {
                foreach ($searchConditions as $searchCondition) {
                    $columnIndex = $searchCondition['column']; // Adjust to zero-based index
                    $searchValue = $searchCondition['value'];
    
                    if ($columnIndex >= 0 && $columnIndex < count($request->input('columns'))) {
                        // $column = $request->input('columns')[$columnIndex];
    
                        // // Apply the search condition to the specified column
                        // $query->orWhere($column, 'like', '%' . $searchValue . '%');
                        $columns = $request->input('columns');

                        // Apply the search condition to the specified column
                        $query->orWhere(function ($query) use ($columns, $columnIndex, $searchValue) {
                            foreach ($columns as $i => $column) {
                                if ($i === $columnIndex) {
                                    $query->where($column, 'like', '%' . $searchValue . '%');
                                }
                            }
                        });
                    }
                }
            }
        }
    }

    protected function applyOrdering(Builder $query, Request $request)
    {
        if ($request->has('orders')) {
            $orders = $request->input('orders');

            foreach ($orders as $order) {
                $orderColumnIndex = $order['column'];
                $orderColumn = $request->input('columns')[$orderColumnIndex];
                $orderDirection = $order['dir'];

                $query->orderBy($orderColumn, $orderDirection);
            }
        }
    }
}
