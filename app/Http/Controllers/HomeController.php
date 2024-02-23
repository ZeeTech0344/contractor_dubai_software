<?php

namespace App\Http\Controllers;

use App\Models\BuyerPurchaserDetail;
use App\Models\contractor_information;
use App\Models\ExpenseDetail;
use App\Models\partnership_detail;
use App\Models\supplierData;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class HomeController extends Controller
{


    function viewContractorFinalReceipt(Request $req, $client_id, $invoice_no)
    {


        $client_id = $client_id;
        $get_invoice = $invoice_no;

        $firstArray = supplierData::with("getOneRecordClient")->where("invoice_no", $invoice_no)->where("supplier_id", $client_id)->get()->toArray();
        $secondArray = supplierData::select(DB::raw('SUM(total) as grand_total, SUM(quantity) as grand_quantity , SUM(amount) as grand_amount , scope, invoice_no, supplier_id'))
            ->where("invoice_no", $invoice_no)
            ->where("supplier_id", $client_id)
            ->groupBy('scope', 'invoice_no', 'supplier_id') // Group by the 'scope' column
            ->get()->toArray();


        $data = [];
        foreach ($firstArray as $firstItem) {
            foreach ($secondArray as $secondItem) {
                if (
                    $firstItem['scope'] === $secondItem['scope'] &&
                    $firstItem['invoice_no'] === $secondItem['invoice_no'] &&
                    $firstItem['supplier_id'] === $secondItem['supplier_id']
                ) {
                    $data[] = array_merge($firstItem, $secondItem);
                }
            }
        }



        $expense_record = ExpenseDetail::with("getClientData")->where("client_id", $client_id)->get();

        $contractor_info = partnership_detail::with("getContractor")->where("client_id", $client_id)->where("invoice_no", $invoice_no)->get();

        $invoice_data_for_approval = $client_id . "," . $get_invoice;



        // $html = [];
        // $html["title"] = "Contractor Detail <button class='btn btn-sm btn-warning' id='back_to_contractor' >Back</button>";
        // $html["view"] = view("old_design.all_view.view-contractor-final-receipt", compact("contractor_info", "expense_record", "data", "client_id", "get_invoice", "invoice_data_for_approval"))->render();
        // return response()->json($html, 200);


        return view("old_design.all_view.final-receipt-new", compact("contractor_info", "expense_record", "data", "client_id", "get_invoice", "invoice_data_for_approval"));
    }


    function viewContractorDetail(Request $req, $contractor_id, $contractor_name)
    {

        if ($req->ajax()) {

            $contractors = partnership_detail::with([
                'getClientData.getInvoiceData',
                'getClientData.getExpense'
            ])
                ->where("contractor_id", $contractor_id)
                ->get();

            $html = [];
            $html["title"] = "Contractor Detail (" . $contractor_name . ")";
            $html["view"] = view("old_design.all_view.view-contractor-detail", compact("contractors"))->render();
            return response()->json($html, 200);
        }

        // $contractor_id = 3;

        // return view("old_design.all_view.view-contractor-detail", compact("contractors"));
    }

    function listOfContractorForDetailView(Request $req)
    {

        return view("old_design.all_view.list-of-contractor-for-detail");
    }

    function listOfContractorForDetail(Request $req)
    {

        if ($req->ajax()) {


            if ($req->search_data_value) {

                $search = $req->search_data_value;

                $all_data_count = contractor_information::where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone_no', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                })->count();

                $data = contractor_information::where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('phone_no', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                })->limit(10)->orderBy("id", "desc");
            } else {
                $all_data_count = contractor_information::count();
                $data = contractor_information::offset($req->start)->limit(10)->orderBy("id", "desc");
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('phone_no', function ($row) {
                    return $row->phone_no;
                })
                ->addColumn('account_no', function ($row) {
                    return $row->account_no;
                })
                ->addColumn('address', function ($row) {
                    return $row->address;
                })
                ->addColumn('status', function ($row) {
                    return "<label class='text-center d-block'>" . $row->status . "</label>";
                })

                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                    <button class="btn btn-sm btn-block btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                         <a class="contractor_grand_detail dropdown-item" data-id="' . $row->id . "," . $row->name . '" href="#">View</a>
                    </div>
                    </div>';
                    return $btn;
                })

                ->setFilteredRecords($all_data_count)
                ->setTotalRecords($data->count())
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }



    function finalReceiptForClient(Request $req, $client_id, $invoice_no)
    {


        $client_id = $client_id;
        $get_invoice = $invoice_no;

        $firstArray = supplierData::with("getOneRecordClient")->where("invoice_no", $invoice_no)->where("supplier_id", $client_id)->get()->toArray();
        $secondArray = supplierData::select(DB::raw('SUM(total) as grand_total, SUM(quantity) as grand_quantity , SUM(amount) as grand_amount , scope, invoice_no, supplier_id'))
            ->where("invoice_no", $invoice_no)
            ->where("supplier_id", $client_id)
            ->groupBy('scope', 'invoice_no', 'supplier_id') // Group by the 'scope' column
            ->get()->toArray();


        $data = [];
        foreach ($firstArray as $firstItem) {
            foreach ($secondArray as $secondItem) {
                if (
                    $firstItem['scope'] === $secondItem['scope'] &&
                    $firstItem['invoice_no'] === $secondItem['invoice_no'] &&
                    $firstItem['supplier_id'] === $secondItem['supplier_id']
                ) {
                    $data[] = array_merge($firstItem, $secondItem);
                }
            }
        }




        $expense_record = ExpenseDetail::with("getClientData")->where("client_id", $client_id)->get();

        $contractor_info = partnership_detail::with("getContractor")->where("client_id", $client_id)->where("invoice_no", $invoice_no)->get();

        $invoice_data_for_approval = $client_id . "," . $get_invoice;

        return view("old_design.all_view.final-receipt-for-client", compact("contractor_info", "expense_record", "data", "client_id", "get_invoice", "invoice_data_for_approval"));
    }

    function updateUserRole(Request $req)
    {

        $user_id = $req->user_id;
        $role = $req->role;

        $user = User::find($user_id);
        $user->role = $role;
        $user->save();
        return response()->json("saved", 200);
    }


    function usersListView()
    {

        return view("old_design.all_view.user-detail");
    }

    function usersList(Request $req)
    {


        if ($req->ajax()) {



            $all_data_count = User::count();


            $data = User::offset($req->start)->limit(10)->orderBy("id", "desc");



            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('email', function ($row) {
                    return $row->email;
                })
                ->addColumn('date', function ($row) {
                    return "<label class='text-center d-block'>" . date_format(date_create($row->created_at), "d-m-Y h:m:i") . "</label>";
                })
                ->addColumn('role', function ($row) {
                    return '<label class="text-center d-block">' . $row->role . '</label>';
                })

                ->addColumn('action', function ($row) {
                    $btn = '<div class="text-center">
                <input type="radio" ' . ($row->role == "Admin" ? "checked" : "") . ' name="role_id' . $row->id . '" class="role_id" value="Admin,' . $row->id . '">Admin
                <input type="radio" ' . ($row->role == "User" ? "checked" : "") . ' name="role_id' . $row->id . '" class="role_id" value="User,' . $row->id . '">User
            </div>';
                    $btn .= '</div></div>';
                    return $btn;
                })

                ->setFilteredRecords($all_data_count)
                ->setTotalRecords($data->count())
                ->rawColumns(['action', 'date', 'role'])
                ->make(true);
        }
    }

    function logout()
    {

        Auth::logout();
        return redirect('/login');
    }

    function invoicePDF(Request $req, $invoice_no, $client_id)
    {

        // set_time_limit(120);

        $client_id = $client_id;
        $get_invoice = $invoice_no;
        $data = supplierData::with("getOneRecordClient")->where("invoice_no", $invoice_no)->where("supplier_id", $client_id)->get();


        $invoice_data_for_approval = $client_id . "," . $get_invoice;



        $pdf = PDF::loadView("old_design.all_view.quotation-view-pdf", compact("data", "client_id", "get_invoice", "invoice_data_for_approval"));
        $file = $pdf->download('supplier_pdf.pdf');

        // // Return the file with appropriate headers
        return response()->json([base64_encode($file)], 200);

        // return view("old_design.all_view.quotation-view-pdf", compact("data", "client_id", "get_invoice", "invoice_data_for_approval"));

    }

    function deleteItem(Request $req)
    {

        if ($req->ajax()) {
            $quotation = supplierData::find($req->id);
            $quotation->delete();
            return response()->json("success", 200);
        }
    }
    function finalReceipt(Request $req, $client_id, $invoice_no)
    {

        $client_id = $client_id;
        $get_invoice = $invoice_no;

        $firstArray = supplierData::with("getOneRecordClient")->where("invoice_no", $invoice_no)->where("supplier_id", $client_id)->get()->toArray();
        $secondArray = supplierData::select(DB::raw('SUM(total) as grand_total, SUM(quantity) as grand_quantity , SUM(amount) as grand_amount , scope, invoice_no, supplier_id'))
            ->where("invoice_no", $invoice_no)
            ->where("supplier_id", $client_id)
            ->groupBy('scope', 'invoice_no', 'supplier_id') // Group by the 'scope' column
            ->get()->toArray();


        $data = [];
        foreach ($firstArray as $firstItem) {
            foreach ($secondArray as $secondItem) {
                if (
                    $firstItem['scope'] === $secondItem['scope'] &&
                    $firstItem['invoice_no'] === $secondItem['invoice_no'] &&
                    $firstItem['supplier_id'] === $secondItem['supplier_id']
                ) {
                    $data[] = array_merge($firstItem, $secondItem);
                }
            }
        }




        $expense_record = ExpenseDetail::with("getClientData")->where("client_id", $client_id)->get();

        $contractor_info = partnership_detail::with("getContractor")->where("client_id", $client_id)->where("invoice_no", $invoice_no)->get();

        $invoice_data_for_approval = $client_id . "," . $get_invoice;

        return view("old_design.all_view.final-receipt-new", compact("contractor_info", "expense_record", "data", "client_id", "get_invoice", "invoice_data_for_approval"));
    }

    function editPartnershipDetail(Request $req)
    {

        return partnership_detail::find($req->id);
    }


    function insertContractorPercentage(Request $req)
    {

        // Define validation rules
        $validationRules = [
            'client_id' => [
                'required',
                'numeric',
                Rule::unique('partnership_details')
                    ->where('contractor_id', $req->contractor_id)
                    ->ignore($req->hidden_buyer_purchaser_id),
            ],
            'contractor_id' => [
                'required',
                'numeric',
                Rule::unique('partnership_details')
                    ->where('client_id', $req->client_id)
                    ->ignore($req->hidden_buyer_purchaser_id),
            ],
            'invoice_no' => 'required|string',
            'percentage' => 'required|numeric',
        ];

        // Validate the request
        $validator = Validator::make($req->all(), $validationRules);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }


        // Proceed with insert or update
        if ($req->hidden_buyer_purchaser_id) {
            // Update existing record
            $percentage = partnership_detail::find($req->hidden_buyer_purchaser_id);
        } else {
            // Insert new record
            $percentage = new partnership_detail();
        }

        $percentage->client_id = $req->client_id;
        $percentage->contractor_id = $req->contractor_id;
        $percentage->invoice_no = $req->invoice_no;
        $percentage->percentage = $req->percentage;
        $percentage->save();

        return response()->json("Saved", 200);
    }


    function getContractorPercentageList(Request $req, $client_id, $invoice_no)
    {


        if ($req->ajax()) {

            if ($req->search) {
                $query = partnership_detail::with('getContractor')
                    ->where("name", "like", '%' . $req->search . '%');
            } else {
                $query = partnership_detail::with('getContractor')
                    ->where('client_id', $client_id)
                    ->where('invoice_no', $invoice_no);
            }

            $total_count = $query->count();

            $data = $query->offset($req->start)
                ->limit(10)
                ->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('contractor', function ($row) {
                    return $row->getContractor->name;
                })
                ->addColumn('percentage', function ($row) {
                    return $row->percentage;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                    <button class="btn btn-sm btn-block btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                         <a class="edit_partnership_detail dropdown-item" data-id="' . $row->id . '" href="#">Edit</a>
                    </div>
                    </div>';
                    return $btn;
                })
                ->setFilteredRecords($total_count)
                ->setTotalRecords($total_count) // Use the total count directly instead of counting again
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }
    function addContractorPercentage(Request $req, $client_id, $invoice_no)
    {


        if ($req->ajax()) {

            $contractors = contractor_information::all();
            $html = [];
            $html["title"] = "Add Contractor Percentage";
            $html["view"] = view("old_design.all_view.add-contractor-percentaga", compact("contractors", "client_id", "invoice_no"))->render();
            return response()->json($html, 200);
        }

        // return view("old_design.all_view.add-contractor-percentaga");

    }


    function contractorInfoView(Request $req, $id)
    {

        if ($req->ajax()) {

            $supplier = contractor_information::find($id);
            $html = [];
            $html["title"] = "Contractor Information";
            $html["view"] = view("new_design.new_design_view.supplier-info-view", compact("supplier"))->render();
            return response()->json($html, 200);
        }
    }

    function updateContractorStatus(Request $req)
    {

        $id = $req->id;
        $buyer_purchaser_record = contractor_information::find($id);
        $buyer_purchaser_record->status == "On" ? $buyer_purchaser_record->status = "Off" : $buyer_purchaser_record->status = "On";
        $buyer_purchaser_record->save();
        return response()->json("update", 200);
    }

    function getContractorList(Request $req)
    {


        if ($req->ajax()) {

            if ($req->search) {
                $query = contractor_information::where("name", "like", '%' . $req->search . '%');
            } else {
                $query = contractor_information::query();
            }

            $total_count = $query->count();

            $data = $query->offset($req->start)
                ->limit(10)
                ->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('phone_no', function ($row) {
                    return $row->phone_no;
                })
                ->addColumn('status', function ($row) {
                    $statusClass = $row->status == "On" ? 'btn-success' : 'btn-danger';
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="update_status_buyer_purchaser_detail btn-block btn btn-sm ' . $statusClass . '">' . $row->status . '</a>';
                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                    <button class="btn btn-sm btn-block btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                         <a class="edit_buyer_purchaser_detail dropdown-item" data-id="' . $row->id . '" href="#">Edit</a>
                        <a class="view_buyer_purchaser_detail dropdown-item" data-id="' . $row->id . '" href="#">View</a>
                        
                    </div>
                    </div>';
                    return $btn;
                })
                ->setFilteredRecords($total_count)
                ->setTotalRecords($total_count) // Use the total count directly instead of counting again
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }


    function insertContractorInfo(Request $req)
    {

        $validation = [
            'name' => 'required',
        ];

        // if ($req->has('hidden_buyer_purchaser_id')) {
        //     $validation['phone_no'] = [
        //         'required',
        //         Rule::unique('buyer_purchaser_details', 'phone_no')->ignore($req->hidden_buyer_purchaser_id),
        //     ];
        // } else {
        //     $validation['phone_no'] = 'required|unique:buyer_purchaser_details,phone_no';
        // }


        $validator = Validator::make($req->all(), $validation);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if ($req->hidden_buyer_purchaser_id) {
            $buyer_purchaser = contractor_information::find($req->hidden_buyer_purchaser_id);
        } else {
            $buyer_purchaser = new contractor_information();
        }
        $buyer_purchaser->name = $req->name;
        $buyer_purchaser->phone_no = $req->phone_no;
        $buyer_purchaser->account_no = $req->account_no;
        $buyer_purchaser->cnic = $req->cnic;
        $buyer_purchaser->address = $req->address;
        $buyer_purchaser->save();
        return response()->json("saved", 200);
    }



    function test()
    {
        return view("old_design.all_view.test");
    }

    function insertLastReceipt(Request $req)
    {

        $data = $req->all();
        $withHidden = [];
        $withoutHidden = [];

        $created_at = Carbon::now();
        $updated_at = Carbon::now();




        foreach ($data["supplier_data"] as $item) {
            if ($item['hidden'] == "") {
                unset($item["hidden"]);
                $item['client_id'] = $req->client_id;
                $item['created_at'] = $created_at;
                $item['updated_at'] = $updated_at;
                $withoutHidden[] = $item;
            } else {
                $withHidden[] = $item;
            }
        }


        $update_array = $withHidden;
        if (count($withHidden) > 0) {

            $updateQuery = 'UPDATE expense_details SET ' . implode(', ', array_map(function ($data) {
                return 'head = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['head'] . '" ELSE head END, ' .
                    'quantity = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['quantity'] . '" ELSE quantity END, ' .
                    'amount = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['amount'] . '" ELSE amount END, ' .
                    'total = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['total'] . '" ELSE total END';
            }, $update_array));

            $updateQuery .= ' WHERE id IN (' . implode(',', array_column($update_array, 'hidden')) . ')';

            DB::statement($updateQuery);
        }


        ExpenseDetail::insert($withoutHidden);
        return response()->json("saved");

        return false;
    }

    function lastReceipt(Request $req, $client_id, $invoice_no)
    {
        $expense = ExpenseDetail::where("client_id", $client_id)->get();

        return view("old_design.all_view.last-receipt", compact("client_id", "invoice_no", "expense"));
    }

    function contractorInfo()
    {

        // $html = [];
        // $html["title"] = "Contractor Information Form";
        // $html["view"] = view("old_design.all_view.contractor-infor")->render();
        // return response()->json($html, 200);
        return  view("old_design.all_view.contractor-infor");
    }

    function deleteInvoice(Request $req)
    {

        if ($req->ajax()) {
            $invoices = SupplierData::where('invoice_no', $req->invoice_no)->get(); // Retrieve all invoices matching the criteria
            foreach ($invoices as $invoice) {
                // Delete related records (assuming you have defined the relationships)
                $invoice->getMultipleClient()->delete();
                // Then delete the invoice itself
                $invoice->delete();
            }
            return response()->json("deleted", 200);
        }
        
    }

    function editQuotation(Request $req, $invoice_no)
    {

        $invoice_data =  supplierData::with("getOneRecordOfClient")->where("invoice_no", $invoice_no)->get();
        return view("old_design.all_view.quotation-old", compact("invoice_data"));
    }

    function quotationOld()
    {

        return view("old_design.all_view.quotation-old");
    }


    function clientRegisterationOld()
    {


        $html = [];
        $html["title"] = "Client Information Form";
        $html["view"] = view("old_design.all_view.client-registeration")->render();
        return response()->json($html, 200);

        // return view("old_design.all_view.client-registeration");
    }


    function oldHome()
    {

        return view("old_design.all_view.old-home");
    }


    function supplierInfoView(Request $req, $id)
    {

        if ($req->ajax()) {

            $supplier = BuyerPurchaserDetail::find($id);
            $html = [];
            $html["title"] = "Client Information";
            $html["view"] = view("new_design.new_design_view.supplier-info-view", compact("supplier"))->render();
            return response()->json($html, 200);
        }
    }

    function buyerPurchaserRecordStatusUpdate(Request $req)
    {

        $id = $req->id;
        $buyer_purchaser_record = BuyerPurchaserDetail::find($id);
        return response()->json($buyer_purchaser_record, 200);
    }


    function updateStatusBuyerPurchaserDetail(Request $req)
    {

        $id = $req->id;
        $buyer_purchaser_record = BuyerPurchaserDetail::find($id);
        $buyer_purchaser_record->status == "On" ? $buyer_purchaser_record->status = "Off" : $buyer_purchaser_record->status = "On";
        $buyer_purchaser_record->save();
        return response()->json("update", 200);
    }


    function insertBuyerPurchaserRecord(Request $req)
    {

        $validation = [
            'name' => 'required',
        ];

        // if ($req->has('hidden_buyer_purchaser_id')) {
        //     $validation['phone_no'] = [
        //         'required',
        //         Rule::unique('buyer_purchaser_details', 'phone_no')->ignore($req->hidden_buyer_purchaser_id),
        //     ];
        // } else {
        //     $validation['phone_no'] = 'required|unique:buyer_purchaser_details,phone_no';
        // }


        $validator = Validator::make($req->all(), $validation);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if ($req->hidden_buyer_purchaser_id) {
            $buyer_purchaser = BuyerPurchaserDetail::find($req->hidden_buyer_purchaser_id);
        } else {
            $buyer_purchaser = new BuyerPurchaserDetail();
        }

        $buyer_purchaser->name = $req->name;
        $buyer_purchaser->phone_no = $req->phone_no;
        $buyer_purchaser->account_no = $req->account_no;
        $buyer_purchaser->cnic = $req->cnic;
        $buyer_purchaser->address = $req->address;
        $buyer_purchaser->opening_amount = $req->opening_amount;
        $buyer_purchaser->save();
        return response()->json("saved", 200);
    }

    function getSupplierList(Request $req)
    {

        if ($req->ajax()) {

            if ($req->search) {
                $query = BuyerPurchaserDetail::where("name", "like", '%' . $req->search . '%');
            } else {
                $query = BuyerPurchaserDetail::query();
            }

            $total_count = $query->count();

            $data = $query->offset($req->start)
                ->limit(10)
                ->orderBy("id", "desc");

            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('phone_no', function ($row) {
                    return $row->phone_no;
                })
                ->addColumn('status', function ($row) {
                    $statusClass = $row->status == "On" ? 'btn-success' : 'btn-danger';
                    $btn = '<a href="javascript:void(0)" data-id="' . $row->id . '" class="update_status_buyer_purchaser_detail btn-block btn btn-sm ' . $statusClass . '">' . $row->status . '</a>';
                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                    <button class="btn btn-sm btn-block btn-danger dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                         <a class="edit_buyer_purchaser_detail dropdown-item" data-id="' . $row->id . '" href="#">Edit</a>
                        <a class="view_buyer_purchaser_detail dropdown-item" data-id="' . $row->id . '" href="#">View</a>
                        
                    </div>
                    </div>';
                    return $btn;
                })
                ->setFilteredRecords($total_count)
                ->setTotalRecords($total_count) // Use the total count directly instead of counting again
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }


    function clientRegisteration()
    {


        return view("new_design.client-registeration");
        $html = [];
        $html["title"] =  "Client Registeration";
        $html["view"] = view("academic.view-qurbani-data", compact("get_qurbani_data"))->render();
        return response()->json($html, 200);
    }



    function updateQuotationStatus(Request $req)
    {

        $string = $req->data["data_for_update"];

        $array_for_approval = explode(",", $string);

        //supplier id is actually client id
        $supplier_id = $array_for_approval[0];
        $invoice_no = $array_for_approval[1];
        $status = $req->data["status"];

        if ($status == 0) {
            $status = 1;
        } elseif ($status == 1) {
            $status = 0;
        }
        supplierData::where('supplier_id', '=', $supplier_id)
            ->where('invoice_no', '=', $invoice_no)
            ->update(['status' => $status]);

        return response()->json($status, "200");
    }


    function insertSupplierData(Request $req)
    {



        $data = $req->all();

        $created_at = Carbon::now();
        $updated_at = Carbon::now();

        if ($req->invoice_no) {


            $withHidden = [];
            $withoutHidden = [];

            $check = 0;


            // echo "<pre>";
            // print_r($data["supplier_data"]);
            // echo "</pre>";

            // return false;

            foreach ($data["supplier_data"] as $item) {

                if ($item['hidden'] == "") {

                    unset($item["hidden"]);
                    $item['invoice_no'] = $req->invoice_no;
                    $item['created_at'] = $created_at;
                    $item['updated_at'] = $updated_at;
                    $item['supplier_id'] = $data["hidden_supplier_id"];

                    $check++;
                    if ($check == 1) {
                        $check_status = supplierData::where("invoice_no", $data["invoice_no"])->latest()->first();

                        if ($check_status) {
                            $status = $check_status->status;
                        } else {
                            $status = 0;
                        }
                    }
                    $item['status'] = $status;
                    $withoutHidden[] = $item;
                } else {

                    $withHidden[] = $item;
                }
            }



            $update_array = $withHidden;
            if (count($withHidden) > 0) {

                $updateQuery = 'UPDATE supplier_data SET ' . implode(', ', array_map(function ($data) {
                    $escapedHead = addslashes($data['head']);
                    return 'head = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $escapedHead . '" ELSE head END, ' .
                        'quantity = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['quantity'] . '" ELSE quantity END, ' .
                        'amount = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['amount'] . '" ELSE amount END, ' .
                        'include_or_exclude = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['include_or_exclude'] . '" ELSE include_or_exclude END, ' .
                        'total = CASE WHEN id = ' . $data['hidden'] . ' THEN "' . $data['total'] . '" ELSE total END';
                }, $update_array));
                
                $updateQuery .= ' WHERE id IN (' . implode(',', array_column($update_array, 'hidden')) . ')';
                
                DB::statement($updateQuery);
            }

            if (count($withoutHidden) > 0) {
                supplierData::insert($withoutHidden);
            }


            $data["buyer_purchaser_data"]["name"];
            $hidden_client_id =  $data["hidden_supplier_id"];

            $client_search = BuyerPurchaserDetail::find($hidden_client_id);
            $client_search->name = $data["buyer_purchaser_data"]["name"];
            $client_search->phone_no = $data["buyer_purchaser_data"]["phone_no"];
            $client_search->address = $data["buyer_purchaser_data"]["address"];
            $client_search->trn_no = $data["buyer_purchaser_data"]["trn_no"];
            if (isset($data["buyer_purchaser_data"]["tax"])) {
                $client_search->tax = $data["buyer_purchaser_data"]["tax"];
            }
            if (isset($data["buyer_purchaser_data"]["recieved_payment"])) {
                $client_search->recieved_payment = $data["buyer_purchaser_data"]["recieved_payment"];
            }
            $client_search->save();

            return response()->json("saved");
        }


        $client_data = new BuyerPurchaserDetail();
        $client_data->name = $data["buyer_purchaser_data"]["name"];
        $client_data->phone_no = $data["buyer_purchaser_data"]["phone_no"];
        $client_data->address = $data["buyer_purchaser_data"]["address"];
        $client_data->trn_no = $data["buyer_purchaser_data"]["trn_no"];
        if (isset($data["buyer_purchaser_data"]["tax"])) {
            $client_data->tax = $data["buyer_purchaser_data"]["tax"];
        }
        if (isset($data["buyer_purchaser_data"]["recieved_payment"])) {
            $client_data->recieved_payment = $data["buyer_purchaser_data"]["recieved_payment"];
        }
        $client_data->save();

        $last_invoice_no = supplierData::latest()->value('invoice_no');

        if (!$last_invoice_no) {
            $last_invoice_no = 1000;
        } else {
            $last_invoice_no = $last_invoice_no + 1;
        }


        foreach ($data["supplier_data"] as $key => $get_data) {

            // $data["supplier_data"][$key]["date"] = $date;
            $data["supplier_data"][$key]["invoice_no"] = $last_invoice_no;
            $data["supplier_data"][$key]["supplier_id"] = $client_data->id;
            $data["supplier_data"][$key]["head"] = $get_data["head"];
            $data["supplier_data"][$key]["quantity"] = $get_data["quantity"];
            $data["supplier_data"][$key]["amount"] = $get_data["amount"];
            $data["supplier_data"][$key]["total"] = $get_data["total"];
            $data["supplier_data"][$key]["created_at"] = $created_at;
            $data["supplier_data"][$key]["updated_at"] = $updated_at;
            unset($data["supplier_data"][$key]["hidden"]);
        }

        $final_array_insert =  $data["supplier_data"];


        supplierData::insert($final_array_insert);
        return response()->json("saved");
    }


    function buyerPurchaserList(Request $req)
    {

        $data = BuyerPurchaserDetail::where("status", "On")->orderBy("id", "DESC")->get();

        return response()->json($data, 200);
    }

    function getListofQuotation(Request $req)
    {

        if ($req->ajax()) {

            if (($req->status == "0" || $req->status == "1") && $req->from_date && $req->to_date && $req->search_data_value) {

                $search = $req->search_data_value;

                $all_data_count = supplierData::leftJoin('buyer_purchaser_details', 'buyer_purchaser_details.id', '=', 'supplier_data.supplier_id')
                    ->where('supplier_data.status', $req->status)
                    ->whereDate('supplier_data.created_at', ">=", $req->from_date)
                    ->whereDate('supplier_data.created_at', "<=", $req->to_date)
                    ->where('buyer_purchaser_details.name', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.phone_no', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.address', 'like', '%' . $search . '%')
                    ->select('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->groupBy('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->get()->count();

                $data = supplierData::leftJoin('buyer_purchaser_details', 'buyer_purchaser_details.id', '=', 'supplier_data.supplier_id')
                    ->where('supplier_data.status', $req->status)
                    ->whereDate('supplier_data.created_at', ">=", $req->from_date)
                    ->whereDate('supplier_data.created_at', "<=", $req->to_date)
                    ->where('buyer_purchaser_details.name', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.phone_no', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.address', 'like', '%' . $search . '%')
                    ->select('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->groupBy('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->offset($req->start)->limit(10)->orderBy("supplier_data.id", "desc");
            } elseif ($req->search_data_value) {

                $search = $req->search_data_value;

                $all_data_count = supplierData::leftJoin('buyer_purchaser_details', 'buyer_purchaser_details.id', '=', 'supplier_data.supplier_id')
                    ->where('buyer_purchaser_details.name', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.phone_no', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.address', 'like', '%' . $search . '%')
                    ->select('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->groupBy('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->get()->count();

                $data = supplierData::leftJoin('buyer_purchaser_details', 'buyer_purchaser_details.id', '=', 'supplier_data.supplier_id')
                    ->where('buyer_purchaser_details.name', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.phone_no', 'like', '%' . $search . '%')
                    ->orwhere('buyer_purchaser_details.address', 'like', '%' . $search . '%')
                    ->select('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->groupBy('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->offset($req->start)->limit(10)->orderBy("supplier_data.id", "desc");
            } elseif (($req->status == "0" || $req->status == "1") && $req->from_date && $req->to_date) {


                $all_data_count = supplierData::where('supplier_data.status', $req->status)
                    ->whereDate('supplier_data.created_at', ">=", $req->from_date)
                    ->whereDate('supplier_data.created_at', "<=", $req->to_date)
                    ->select('supplier_data.supplier_id')
                    ->groupBy('supplier_data.supplier_id')
                    ->get()->count();

                $data = supplierData::leftJoin('buyer_purchaser_details', 'buyer_purchaser_details.id', '=', 'supplier_data.supplier_id')
                    ->where('supplier_data.status', $req->status)
                    ->whereDate('supplier_data.created_at', ">=", $req->from_date)
                    ->whereDate('supplier_data.created_at', "<=", $req->to_date)
                    ->select('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->groupBy('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->offset($req->start)->limit(10)->orderBy("supplier_data.id", "desc");
            } elseif ($req->status == "0" || $req->status == "1") {

                $all_data_count = supplierData::where('supplier_data.status', $req->status)
                    ->select('supplier_data.invoice_no')
                    ->groupBy('supplier_data.invoice_no')
                    ->get()->count();

                $data = supplierData::leftJoin('buyer_purchaser_details', 'buyer_purchaser_details.id', '=', 'supplier_data.supplier_id')
                    ->where('supplier_data.status', $req->status)
                    ->select('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->groupBy('supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->offset($req->start)->limit(10)->orderBy("supplier_data.id", "desc");
            } else {

                $all_data_count = BuyerPurchaserDetail::count();

                $data = supplierData::leftJoin('buyer_purchaser_details', 'buyer_purchaser_details.id', '=', 'supplier_data.supplier_id')
                    ->select('buyer_purchaser_details.id as client_id', 'supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->groupBy('buyer_purchaser_details.id', 'supplier_data.invoice_no', 'buyer_purchaser_details.name', 'buyer_purchaser_details.phone_no', 'buyer_purchaser_details.address', 'supplier_data.status', 'buyer_purchaser_details.id')
                    ->offset($req->start)->limit(10)->orderBy("supplier_data.id", "desc");
            }


            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('invoice_no', function ($row) {
                    return $row->invoice_no;
                })
                ->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->addColumn('phone_no', function ($row) {
                    return $row->phone_no;
                })
                ->addColumn('address', function ($row) {
                    return $row->address;
                })
                ->addColumn('date', function ($row) {
                    return date_format(date_create($row->created_at), "d-m-Y h:m:i");
                })

                ->addColumn('status', function ($row) {
                    return $row->status == 0 ? "<label style='color:red; text-align:center; display:block;'>Not Approved</label>" : "<label style='color:green;text-align:center;display:block;'>Approved</label>";
                })

                ->addColumn('action', function ($row) {

                    $btn = '<div class="btn-group btn-sm">
                    <button type="button" class="btn btn-sm btn-warning dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Action
                    </button>
                    <div class="dropdown-menu">
                        <a href="' . url('view-invoice/') . '/' . $row->invoice_no . '/' . $row->id . '" class="dropdown-item"><i class="fas fa-eye"></i> View</a>
                        <a href="' . url('edit-quotation/') . '/' . $row->invoice_no . '"  class="dropdown-item" ><i class="fas fa-pencil-alt"></i> Edit</a>
                        <a href="javascript:void(0)" class="dropdown-item delete_invoice" data-id="' . $row->invoice_no . '"><i class="fas fa-trash-alt"></i>
                         Delete</a>';
                    if ($row->status == 1) {
                        $btn .= '<a href="' . url('last-receipt/') . '/' . $row->id . '/' . $row->invoice_no . '" class="dropdown-item"><i class="fas fa-coins"></i> Add Expense</a>';
                        $btn .= '<a href="' . url('final-receipt-for-client/') . '/' . $row->id . '/' . $row->invoice_no . '" class="dropdown-item"><i class="fas fa-receipt"></i> Client Receipt</a>';
                        $btn .= '<a href="' . url('final-receipt/') . '/' . $row->id . '/' . $row->invoice_no . '" class="dropdown-item"><i class="fas fa-receipt"></i> Final Receipt</a>';
                    }

                    $btn .= '</div>
                    </div></div>
                    </div>';
                    return $btn;
                })
                ->setFilteredRecords($all_data_count)
                ->setTotalRecords($data->count())
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    }


    function quotationList(Request $req)
    {

        return view("new_design.new_design_view.quotation-list");
    }


    function viewInvoice(Request $req, $invoice_no = null, $client_id = null)
    {


        $client_id = $client_id;
        $get_invoice = $invoice_no;
        $firstArray = supplierData::with("getOneRecordClient")->where("invoice_no", $invoice_no)->where("supplier_id", $client_id)
            ->orderBy("scope", "desc")->get()->toArray();



        $secondArray = supplierData::select(
            DB::raw('SUM(CASE WHEN include_or_exclude = 1 THEN total ELSE total END) as grand_total'),
            DB::raw('SUM(CASE WHEN include_or_exclude = 1 THEN quantity ELSE quantity END) as grand_quantity'),
            DB::raw('SUM(CASE WHEN include_or_exclude = 1 THEN amount ELSE amount END) as grand_amount'),
            'scope',
            'invoice_no',
            'supplier_id'
        )
            ->where("invoice_no", $invoice_no)
            ->where("supplier_id", $client_id)
            ->groupBy('scope', 'invoice_no', 'supplier_id') // Group by the 'scope' column
            ->orderBy("scope", "desc")
            ->get()
            ->toArray();



        $data = [];
        foreach ($firstArray as $firstItem) {
            foreach ($secondArray as $secondItem) {
                if (
                    $firstItem['scope'] === $secondItem['scope'] &&
                    $firstItem['invoice_no'] === $secondItem['invoice_no'] &&
                    $firstItem['supplier_id'] === $secondItem['supplier_id']
                ) {
                    $data[] = array_merge($firstItem, $secondItem);
                }
            }
        }


        $invoice_data_for_approval = $client_id . "," . $get_invoice;

        return view("old_design.all_view.quotation-view", compact("data", "client_id", "get_invoice", "invoice_data_for_approval"));

        // return view("new_design.new_design_view.invoice", compact("data", "client_id", "get_invoice", "invoice_data_for_approval"));
    }

    function quotation(Request $req)
    {

        return view("new_design.new_design_view.quotation");
    }




    function NewHome()
    {

        return view("new_design.home");
    }
}
