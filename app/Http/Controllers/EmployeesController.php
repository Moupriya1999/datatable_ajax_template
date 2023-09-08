<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employees;

class EmployeesController extends Controller
{
    public function index()
    {
        // Load index view
        return view('index');
    }

    public function getEmployees(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get("start");
        $rowperpage = $request->get("length"); // Rows display per page
    
        $columnIndex_arr = $request->get('order');
        $columnName_arr = $request->get('columns');
        $order_arr = $request->get('order');
        $search_arr = $request->get('search');
    
        $columnIndex = $columnIndex_arr[0]['column']; // Column index
        $columnName = $columnName_arr[$columnIndex]['data']; // Column name
        $columnSortOrder = $order_arr[0]['dir']; // asc or desc
        $searchValue = $search_arr['value']; // Search value
        $status = $request->get('status'); // Get the selected status filter
    
        // Total records
        $totalRecords = Employees::select('count(*) as allcount')->count();
    
        // Fetch records with status filter
        $query = Employees::orderBy($columnName, $columnSortOrder)
            ->where('name', 'like', '%' . $searchValue . '%');
    
        if ($status !== null) {
            $query->where('status', $status); // Apply status filter if a status is selected
        }
    
        // Total records with filter
        $totalRecordswithFilter = $query->count();
    
        // Fetch records
        $records = $query->skip($start)
            ->take($rowperpage)
            ->get();
    
        $data_arr = array();
    
        foreach ($records as $record) {
            $id = $record->id;
            $username = $record->username;
            $name = $record->name;
            $email = $record->email;
            $date = $record->date; // Add date field
    
            $data_arr[] = array(
                "id" => $id,
                "username" => $username,
                "name" => $name,
                "email" => $email,
                "date" => $date, // Include date in the response
            );
        }
    
        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            "aaData" => $data_arr,
        );
    
        return response()->json($response);
    }


    // Fetch records
    // public function getEmployees(Request $request)
    // {
    //     $draw = $request->get('draw');
    //     $start = $request->get("start");
    //     $rowperpage = $request->get("length"); // Rows display per page

    //     $columnIndex_arr = $request->get('order');
    //     $columnName_arr = $request->get('columns');
    //     $order_arr = $request->get('order');
    //     $search_arr = $request->get('search');

    //     $columnIndex = $columnIndex_arr[0]['column']; // Column index
    //     $columnName = $columnName_arr[$columnIndex]['data']; // Column name
    //     $columnSortOrder = $order_arr[0]['dir']; // asc or desc
    //     $searchValue = $search_arr['value']; // Search value

    //     // Total records
    //     $totalRecords = Employees::select('count(*) as allcount')->count();
    //     $totalRecordswithFilter = Employees::select('count(*) as allcount')->where('name', 'like', '%' . $searchValue . '%')->count();

    //     // Fetch records
    //     $records = Employees::orderBy($columnName, $columnSortOrder)
    //         ->where('name', 'like', '%' . $searchValue . '%')
    //         ->select('id', 'username', 'name', 'email', 'date') // Include the date column
    //         ->skip($start)
    //         ->take($rowperpage)
    //         ->get();

    //     $data_arr = array();

    //     foreach ($records as $record) {
    //         $id = $record->id;
    //         $username = $record->username;
    //         $name = $record->name;
    //         $email = $record->email;
    //         $date = $record->date; // Add date field

    //         $data_arr[] = array(
    //             "id" => $id,
    //             "username" => $username,
    //             "name" => $name,
    //             "email" => $email,
    //             "date" => $date, // Include date in the response
    //         );
    //     }

    //     $response = array(
    //         "draw" => intval($draw),
    //         "iTotalRecords" => $totalRecords,
    //         "iTotalDisplayRecords" => $totalRecordswithFilter,
    //         "aaData" => $data_arr,
    //     );

    //     return response()->json($response);
    // }

    public function getFilteredEmployees(Request $request)
    {
        try {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length"); // Rows display per page
    
            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');
            $startDate = $request->get('startDate'); // Get the start date
            $endDate = $request->get('endDate'); // Get the end date
    
            $columnIndex = $columnIndex_arr[0]['column']; // Column index
            $columnName = $columnName_arr[$columnIndex]['data']; // Column name
            $columnSortOrder = $order_arr[0]['dir']; // asc or desc
            $searchValue = $search_arr['value']; // Search value

            // $status = $request->input('status');
            

            // Start building the query
            
    
            // Start building the query
            $query = Employees::orderBy($columnName, $columnSortOrder)
                ->where('name', 'like', '%' . $searchValue . '%')
                ->whereBetween('date', [$startDate, $endDate]); // Filter by date range

            // Filter by status
            // if ($status !== null) {
            //     $query->where('status', $status);
            // }
    
            // Total records with filter
            $totalRecordswithFilter = $query->count();
    
            // Fetch filtered records
            $records = $query->skip($start)
                ->take($rowperpage)
                ->get();
    
            $data_arr = array();
    
            foreach ($records as $record) {
                $id = $record->id;
                $username = $record->username;
                $name = $record->name;
                $email = $record->email;
                $date = $record->date;
    
                $data_arr[] = array(
                    "id" => $id,
                    "username" => $username,
                    "name" => $name,
                    "email" => $email,
                    "date" => $date,
                );
            }
    
            $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecordswithFilter,
                "iTotalDisplayRecords" => $totalRecordswithFilter,
                "aaData" => $data_arr,
            );
    
            return response()->json($response);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error while fetching filtered employees data: ' . $e->getMessage());
    
            // Return an error response
            return response()->json(['error' => 'Error fetching filtered data.'], 500);
        }
    }    
    
    public function getEmployee($id)
    {
        try {
            $employee = Employees::findOrFail($id);
            return response()->json($employee);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error while fetching employee data for edit: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Error fetching employee data.'], 500);
        }
    }

    // Update Function
    public function updateEmployee(Request $request, $id)
    {
        try {
            $employee = Employees::findOrFail($id);
            $employee->update($request->all());
    
            return response()->json(['message' => 'Employee updated successfully']);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error while updating employee data: ' . $e->getMessage());
    
            // Return an error response
            return response()->json(['error' => 'Error updating employee data.'], 500);
        }
    }    

    // Delete Function
    public function deleteEmployee($id)
    {
        try {
            $employee = Employees::findOrFail($id);
            $employee->delete();

            return response()->json(['message' => 'Employee deleted successfully']);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error while deleting employee: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Error deleting employee.'], 500);
        }
    }

    public function deleteSelectedEmployees(Request $request)
    {
        try {
            $ids = $request->input('ids');
            Employees::whereIn('id', $ids)->delete();

            return response()->json(['message' => 'Selected employees deleted successfully']);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error while deleting selected employees: ' . $e->getMessage());

            // Return an error response
            return response()->json(['error' => 'Error deleting selected employees.'], 500);
        }
    }

    

    public function processSelectedEmployees(Request $request)
    {
        try {
            $selectedIds = $request->input('ids');
    
            // You can perform any desired action with the selected IDs here
            // For example, you can update the status of selected employees or perform other operations
            
            // For demonstration purposes, let's assume you want to retrieve the usernames of the selected employees
            $selectedUsernames = Employees::whereIn('id', $selectedIds)->pluck('username')->toArray();
    
            // Return a JSON response with only the selected usernames
            return response()->json([
                'selectedUsernames' => $selectedUsernames,
            ]);
        } catch (\Exception $e) {
            // Log the error message
            Log::error('Error while processing selected employees: ' . $e->getMessage());
    
            // Return an error response
            return response()->json(['error' => 'Error processing selected employees.'], 500);
        }
    }
}

