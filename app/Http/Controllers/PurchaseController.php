<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Purchase;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd(Order::paginate(50));

        // 合計
        $orders = Order::groupBy('id')
            ->selectRaw('id, customer_name,sum(subtotal) as total, status, created_at' )->paginate(50);

        return Inertia::render('Purchases/Index', [
            'orders' => $orders
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // $customers = Customer::select('id', 'name', 'kana')->get();
        $items = Item::select('id', 'name', 'price')->where('is_selling', true)->get();
        return Inertia::render('Purchases/Create', [
            'items' => $items
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePurchaseRequest $request)
    {
        DB::beginTransaction();
        try {
            $purchase = Purchase::create([
                'customer_id' => $request->customer_id,
                'status' => $request->status,
            ]);

            foreach($request->items as $item){
                // 第一引数に外部キー
                $purchase->items()->attach($purchase->id, [
                    'item_id' => $item['id'],
                    'quantity' => $item['quantity']
                ]);
            }
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
        }


        return to_route('dashboard');
    }

    /**
     * Display the specified resource.
     */
    public function show(Purchase $purchase)
    {
        // 小計
        $items = Order::where('id', $purchase->id)->get();

        // 合計
        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id, customer_name,sum(subtotal) as total, status, created_at' )
        ->get();

        // dd($items, $order);

        return Inertia::render('Purchases/Show', [
            'items' => $items,
            'order' => $order
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Purchase $purchase)
    {
        $all_items = Item::select('id', 'name', 'price')->get();
        $items_aray = [];

        foreach ($all_items as $items) {
            $quantity = 0;
            foreach ($purchase->items as $purchased_item) {
                if ($purchased_item->id === $items->id) {
                    $quantity = $purchased_item->pivot->quantity;
                }
            }

            array_push($items_aray, [
                'id' => $items->id,
                'name' => $items->name,
                'price' => $items->price,
                'quantity' => $quantity
            ]);
        }
        // dd($items_aray);

        // 合計
        $order = Order::groupBy('id')
        ->where('id', $purchase->id)
        ->selectRaw('id, customer_id, customer_name, status, created_at')
        ->get();

        return Inertia::render('Purchases/Edit', [
            'items' => $items_aray,
            'order' => $order
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePurchaseRequest $request, Purchase $purchase)
    {

        DB::beginTransaction();
        try {
            $purchase->update([
                'status' => $request->status,
            ]);

            $items = [];
            foreach($request->items as $item){
                $items = $items + [
                    $item['id'] => [
                        'quantity' => $item['quantity']
                    ],
                ];
            }

            $purchase->items()->sync($items);
            DB::commit();
            return to_route('dashboard');
        } catch(\Exception $e) {
            DB::rollback();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Purchase $purchase)
    {
        //
    }
}
