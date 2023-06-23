<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use App\Models\Order;
use App\Models\Store;
use App\Models\OrderDetail;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\CentralLogics\ProductLogic;
use App\Mail\PlaceOrder;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

class POSController extends Controller
{
    public function index(Request $request)
    {
        // dd(session('cart'));
        $category = $request->query('category_id', 0);
        // $sub_category = $request->query('sub_category', 0);
        $categories = Category::active()->module(Helpers::get_store_data()->module_id)->get();
        $keyword = $request->query('keyword', false);
        $store = Store::find(Helpers::get_store_data()->module_id);
        $key = explode(' ', $keyword);
        $products = Item::active()
        ->when($category, function($query)use($category){
            $query->whereHas('category',function($q)use($category){
                return $q->whereId($category)->orWhere('parent_id', $category);
            });
        })
        ->when($keyword, function($query)use($key){
            return $query->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
        })
        ->latest()->paginate(10);
        return view('vendor-views.pos.index', compact('categories', 'products','store','category', 'keyword'));
    }

    public function quick_view(Request $request)
    {
        $product = Item::findOrFail($request->product_id);

        return response()->json([
            'success' => 1,
            'view' => view('vendor-views.pos._quick-view-data', compact('product'))->render(),
        ]);
    }

    public function quick_view_card_item(Request $request)
    {
        $product = Item::findOrFail($request->product_id);
        $item_key = $request->item_key;
        $cart_item = session()->get('cart')[$item_key];

        return response()->json([
            'success' => 1,
            'view' => view('vendor-views.pos._quick-view-cart-item', compact('product', 'cart_item', 'item_key'))->render(),
        ]);
    }

    public function variant_price(Request $request)
    {
        $product = Item::find($request->id);
        if($product->module->module_type == 'food' && $product->food_variations){
            $price = $product->price;
            $addon_price = 0;
            if ($request['addon_id']) {
                foreach ($request['addon_id'] as $id) {
                    $addon_price += $request['addon-price' . $id] * $request['addon-quantity' . $id];
                }
            }
            $product_variations = json_decode($product->food_variations, true);
            if ($request->variations && count($product_variations)) {

                $price_total =  $price + Helpers::food_variation_price($product_variations, $request->variations);
                $price= $price_total - Helpers::product_discount_calculate($product, $price_total, $product->store);
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price, $product->store);
            }
        }else{

            $str = '';
            $quantity = 0;
            $price = 0;
            $addon_price = 0;

            foreach (json_decode($product->choice_options) as $key => $choice) {
                if ($str != null) {
                    $str .= '-' . str_replace(' ', '', $request[$choice->name]);
                } else {
                    $str .= str_replace(' ', '', $request[$choice->name]);
                }
            }

            if($request['addon_id'])
            {
                foreach($request['addon_id'] as $id)
                {
                    $addon_price+= $request['addon-price'.$id]*$request['addon-quantity'.$id];
                }
            }

            if ($str != null) {
                $count = count(json_decode($product->variations));
                for ($i = 0; $i < $count; $i++) {
                    if (json_decode($product->variations)[$i]->type == $str) {
                        $price = json_decode($product->variations)[$i]->price - Helpers::product_discount_calculate($product, json_decode($product->variations)[$i]->price,Helpers::get_store_data());
                    }
                }
            } else {
                $price = $product->price - Helpers::product_discount_calculate($product, $product->price,Helpers::get_store_data());
            }
        }

        return array('price' => Helpers::format_currency(($price * $request->quantity)+$addon_price));
    }

    public function addDeliveryInfo(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'floor' => 'required',
            'road' => 'required',
            'house' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address_type' => 'delivery',
            'address' => $request->address,
            'floor' => $request->floor,
            'road' => $request->road,
            'house' => $request->house,
            'delivery_fee' => $request->delivery_fee?:0,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];

        $request->session()->put('address', $address);

        return response()->json([
            'data' => $address,
            'view' => view('vendor-views.pos._address', compact('address'))->render(),
        ]);
    }

    public function addToCart(Request $request)
    {
        $product = Item::find($request->id);

        if($product->module->module_type == 'food' && $product->food_variations){
        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $addon_price = 0;
        $variation_price=0;

        $product_variations = json_decode($product->food_variations, true);
        if ($request->variations && count($product_variations)) {
            foreach($request->variations  as $key=> $value ){

                if($value['required'] == 'on' &&  isset($value['values']) == false){
                    return response()->json([
                        'data' => 'variation_error',
                        'message' => translate('Please select items from') . ' ' . $value['name'],
                    ]);
                }
                if(isset($value['values'])  && $value['min'] != 0 && $value['min'] > count($value['values']['label'])){
                    return response()->json([
                        'data' => 'variation_error',
                        'message' => translate('Please select minimum ').$value['min'].translate(' For ').$value['name'].'.',
                    ]);
                }
                if(isset($value['values']) && $value['max'] != 0 && $value['max'] < count($value['values']['label'])){
                    return response()->json([
                        'data' => 'variation_error',
                        'message' => translate('Please select maximum ').$value['max'].translate(' For ').$value['name'].'.',
                    ]);
                }
            }
            $variation_data = Helpers::get_varient($product_variations, $request->variations);
            $variation_price = $variation_data['price'];
            $variations = $request->variations;
        }

        $data['variations'] = $variations;
        $data['variant'] = $str;

        $price = $product->price + $variation_price;
        $data['variation_price'] = $variation_price;

        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = Helpers::product_discount_calculate($product, $price,Helpers::get_store_data());
        $data['image'] = $product->image;
        $data['add_ons'] = [];
        $data['add_on_qtys'] = [];

        if($request['addon_id'])
        {
            foreach($request['addon_id'] as $id)
            {
                $addon_price+= $request['addon-price'.$id]*$request['addon-quantity'.$id];
                $data['add_on_qtys'][]=$request['addon-quantity'.$id];
            }
            $data['add_ons'] = $request['addon_id'];
        }

        $data['addon_price'] = $addon_price;

        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            if(isset($request->cart_item_key))
            {
                $cart[$request->cart_item_key] = $data;
                $data = 2;
            }
            else
            {
                $cart->push($data);
            }

        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }
    }else{

        $data = array();
        $data['id'] = $product->id;
        $str = '';
        $variations = [];
        $price = 0;
        $addon_price = 0;
    
        //Gets all the choice values of customer choice option and generate a string like Black-S-Cotton
        foreach (json_decode($product->choice_options) as $key => $choice) {
            $data[$choice->name] = $request[$choice->name];
            $variations[$choice->title] = $request[$choice->name];
            if ($str != null) {
                $str .= '-' . str_replace(' ', '', $request[$choice->name]);
            } else {
                $str .= str_replace(' ', '', $request[$choice->name]);
            }
        }
        $data['variations'] = $variations;
        $data['variant'] = $str;
        if ($request->session()->has('cart') && !isset($request->cart_item_key)) {
            if (count($request->session()->get('cart')) > 0) {
                foreach ($request->session()->get('cart') as $key => $cartItem) {
                    if (is_array($cartItem) && $cartItem['id'] == $request['id'] && $cartItem['variant'] == $str) {
                        return response()->json([
                            'data' => 1
                        ]);
                    }
                }
    
            }
        }
        //Check the string and decreases quantity for the stock
        if ($str != null) {
            $count = count(json_decode($product->variations));
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variations)[$i]->type == $str) {
                    $price = json_decode($product->variations)[$i]->price;
                    $data['variations'] = json_decode($product->variations, true)[$i];
                }
            }
        } else {
            $price = $product->price;
        }
    
        $data['quantity'] = $request['quantity'];
        $data['price'] = $price;
        $data['name'] = $product->name;
        $data['discount'] = Helpers::product_discount_calculate($product, $price,Helpers::get_store_data());
        $data['image'] = $product->image;
        $data['add_ons'] = [];
        $data['add_on_qtys'] = [];
    
        if($request['addon_id'])
        {
            foreach($request['addon_id'] as $id)
            {
                $addon_price+= $request['addon-price'.$id]*$request['addon-quantity'.$id];
                $data['add_on_qtys'][]=$request['addon-quantity'.$id];
            }
            $data['add_ons'] = $request['addon_id'];
        }
    
        $data['addon_price'] = $addon_price;
    
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            if(isset($request->cart_item_key))
            {
                $cart[$request->cart_item_key] = $data;
                $data = 2;
            }
            else
            {
                $cart->push($data);
            }
    
        } else {
            $cart = collect([$data]);
            $request->session()->put('cart', $cart);
        }
    }


        return response()->json([
            'data' => $data
        ]);
    }

    public function cart_items()
    {
        return view('vendor-views.pos._cart');
    }

    //removes from Cart
    public function removeFromCart(Request $request)
    {
        if ($request->session()->has('cart')) {
            $cart = $request->session()->get('cart', collect([]));
            $cart->forget($request->key);
            $request->session()->put('cart', $cart);
        }

        return response()->json([],200);
    }

    //updated the quantity for a cart item
    public function updateQuantity(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart = $cart->map(function ($object, $key) use ($request) {
            if ($key == $request->key) {
                $object['quantity'] = $request->quantity;
            }
            return $object;
        });
        $request->session()->put('cart', $cart);
        return response()->json([],200);
    }

    //empty Cart
    public function emptyCart(Request $request)
    {
        session()->forget('cart');
        session()->forget('address');
        return response()->json([],200);
    }

    public function update_tax(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['tax'] = $request->tax;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function update_discount(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['discount'] = $request->discount;
        $cart['discount_type'] = $request->type;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function update_paid(Request $request)
    {
        $cart = $request->session()->get('cart', collect([]));
        $cart['paid'] = $request->paid;
        $request->session()->put('cart', $cart);
        return back();
    }

    public function get_customers(Request $request){
        $key = explode(' ', $request['q']);
        $data = User::
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                ->orWhere('l_name', 'like', "%{$value}%")
                ->orWhere('phone', 'like', "%{$value}%");
            }
        })
        ->limit(8)
        ->get([DB::raw('id, CONCAT(f_name, " ", l_name, " (", phone ,")") as text')]);

        $data[]=(object)['id'=>false, 'text'=>translate('messages.walk_in_customer')];

        $reversed = $data->toArray();

        $data = array_reverse($reversed);


        return response()->json($data);
    }

    public function place_order(Request $request)
    {
        if(!$request->type){
            Toastr::error(translate('No payment method selected'));
            return back();
        }

        if($request->session()->has('cart'))
        {
            if(count($request->session()->get('cart')) < 1)
            {
                Toastr::error(translate('messages.cart_empty_warning'));
                return back();
            }
        }
        else
        {
            Toastr::error(translate('messages.cart_empty_warning'));
            return back();
        }
        if ($request->session()->has('address')) {
            if(!$request->user_id){
                Toastr::error(translate('messages.no_customer_selected'));
                return back();
            }
            $address = $request->session()->get('address');
        }

        $store = Helpers::get_store_data();
        $cart = $request->session()->get('cart');

        $total_addon_price = 0;
        $product_price = 0;
        $store_discount_amount = 0;

        $order_details = [];
        $product_data = [];
        $order = new Order();
        $order->id = 100000 + Order::all()->count() + 1;
        if (Order::find($order->id)) {
            $order->id = Order::latest()->first()->id + 1;
        }
        $order->payment_status = isset($address)?'unpaid':'paid';
        if($request->user_id){

            $order->order_status = isset($address)?'confirmed':'delivered';
            $order->order_type = isset($address)?'delivery':'take_away';
        }else{
            $order->order_status = 'delivered';
            $order->order_type = 'take_away';
        }
        if($order->order_type == 'take_away'){
            $order->delivered = now();
        }
        $order->payment_method = $request->type;
        $order->store_id = $store->id;
        $order->module_id = Helpers::get_store_data()->module_id;
        $order->user_id = $request->user_id;
        $order->delivery_charge = isset($address)?$address['delivery_fee']:0;
        $order->original_delivery_charge = isset($address)?$address['delivery_fee']:0;
        $order->delivery_address = isset($address)?json_encode($address):null;
        $order->checked = 1;
        $order->created_at = now();
        $order->schedule_at = now();
        $order->updated_at = now();
        $order->otp = rand(1000, 9999);
        foreach ($cart as $c) {
            if(is_array($c))
            {
                $product = Item::withoutGlobalScope(StoreScope::class)->find($c['id']);
                if ($product) {
                    if($product->module->module_type == 'food'){
                        if ($product->food_variations) {
                            $variation_data = Helpers::get_varient(json_decode($product->food_variations, true), $c['variations']);
                            $variations = $variation_data['variations'];
                        }else{
                            $variations = [];
                        }
                        $price = $c['price'];
                        $product->tax = $product->store->tax;
                        $product = Helpers::product_data_formatting($product);
                        $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::withOutGlobalScope(StoreScope::class)->whereIn('id', $c['add_ons'])->get(), $c['add_on_qtys']);
                        $or_d = [
                            'item_id' => $c['id'],
                            'item_campaign_id' => null,
                            'item_details' => json_encode($product),
                            'quantity' => $c['quantity'],
                            'price' => $price,
                            'tax_amount' => Helpers::tax_calculate($product, $price),
                            'discount_on_item' => Helpers::product_discount_calculate($product, $price, $product->store),
                            'discount_type' => 'discount_on_product',
                            'variant' => '',
                            'variation' => isset($variations)?json_encode($variations):json_encode([]),
                            // 'variation' => json_encode(count($c['variations']) ? $c['variations'] : []),
                            'add_ons' => json_encode($addon_data['addons']),
                            'total_add_on_price' => $addon_data['total_add_on_price'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $total_addon_price += $or_d['total_add_on_price'];
                        $product_price += $price * $or_d['quantity'];
                        $store_discount_amount += $or_d['discount_on_item'] * $or_d['quantity'];
                        $order_details[] = $or_d;
                    }else{

                        if (count(json_decode($product['variations'], true)) > 0) {
                            $variant_data = Helpers::variation_price($product, json_encode([$c['variations']]));
                            $price = $variant_data['price'];
                            $stock = $variant_data['stock'];
                        } else {
                            $price = $product['price'];
                            $stock = $product->stock;
                        }
    
                        if(config('module.'.$product->module->module_type)['stock'])
                        {
                            if($c['quantity']>$stock)
                            {
                                Toastr::error(translate('messages.product_out_of_stock_warning',['item'=>$product->name]));
                                return back();
                            }
    
                            $product_data[]=[
                                'item'=>clone $product,
                                'quantity'=>$c['quantity'],
                                'variant'=>count($c['variations'])>0?$c['variations']['type']:null
                            ];
                        }
    
                        $price = $c['price'];
                        $product->tax = $store->tax;
                        $product = Helpers::product_data_formatting($product);
                        $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id',$c['add_ons'])->get(), $c['add_on_qtys']);
                        $or_d = [
                            'item_id' => $c['id'],
                            'item_campaign_id' => null,
                            'item_details' => json_encode($product),
                            'quantity' => $c['quantity'],
                            'price' => $price,
                            'tax_amount' => Helpers::tax_calculate($product, $price),
                            'discount_on_item' => Helpers::product_discount_calculate($product, $price, $store),
                            'discount_type' => 'discount_on_product',
                            'variant' => json_encode($c['variant']),
                            'variation' => json_encode(count($c['variations']) ? [$c['variations']] : []),
                            'add_ons' => json_encode($addon_data['addons']),
                            'total_add_on_price' => $addon_data['total_add_on_price'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $total_addon_price += $or_d['total_add_on_price'];
                        $product_price += $price*$or_d['quantity'];
                        $store_discount_amount += $or_d['discount_on_item']*$or_d['quantity'];
                        $order_details[] = $or_d;
                    }
                }
            }
        }


        if(isset($cart['discount']))
        {
            $store_discount_amount += $cart['discount_type']=='percent'&&$cart['discount']>0?((($product_price + $total_addon_price - $store_discount_amount) * $cart['discount'])/100):$cart['discount'];
        }

        $total_price = $product_price + $total_addon_price - $store_discount_amount;
        $tax = isset($cart['tax'])?$cart['tax']:$store->tax;
        // $total_tax_amount= ($tax > 0)?(($total_price * $tax)/100):0;

        $order->tax_status = 'excluded';

        $tax_included =BusinessSetting::where(['key'=>'tax_included'])->first() ?  BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;
        if ($tax_included ==  1){
            $order->tax_status = 'included';
        }

        $total_tax_amount=Helpers::product_tax($total_price,$tax,$order->tax_status =='included');
        $tax_a=$order->tax_status =='included'?0:$total_tax_amount;
        try {
            // $order->tax_status = 'excluded' ;
            // $tax_included =BusinessSetting::where(['key'=>'tax_included'])->first() ?  BusinessSetting::where(['key'=>'tax_included'])->first()->value : 0;
            // if ($tax_included ==  1){
            //     $total_tax_amount=0;
            //     $order->tax_status = 'included';
            // }
            $order->store_discount_amount= $store_discount_amount;
            $order->total_tax_amount= $total_tax_amount;
            $order->order_amount = $total_price + $tax_a + $order->delivery_charge;
            if($request->type == 'card'){

                $order->adjusment = 0;
            }else{
                $order->adjusment = $request->amount - ($total_price + $total_tax_amount + $order->delivery_charge);

            }
            $order->payment_method = $request->type;
            $order->save();
            foreach ($order_details as $key => $item) {
                $order_details[$key]['order_id'] = $order->id;
            }
            OrderDetail::insert($order_details);
            if(count($product_data)>0)
            {
                foreach($product_data as $item)
                {
                    ProductLogic::update_stock($item['item'], $item['quantity'], $item['variant'])->save();
                }
            }
            session()->forget('cart');
            session()->forget('address');
            session(['last_order' => $order->id]);
            if($order->order_status=='confirmed' && $order->user){
                Helpers::send_order_notification($order);
                //PlaceOrderMail
                try{
                    if($order->order_status == 'pending' && config('mail.status'))
                    {
                        Mail::to($order->customer->email)->send(new PlaceOrder($order->id));
                    }
                }catch (\Exception $ex) {
                    info($ex);
                }
            }
            Toastr::success(translate('messages.order_placed_successfully'));
            return back();
        } catch (\Exception $e) {
            info($e->getMessage());
        }
        Toastr::warning(translate('messages.failed_to_place_order'));
        return back();
    }

    public function order_list()
    {
        $orders = Order::with(['customer'])
        ->where('order_type', 'pos')
        ->where('store_id',\App\CentralLogics\Helpers::get_store_id())
        ->latest()
        ->paginate(config('default_pagination'));

        return view('vendor-views.pos.order.list', compact('orders'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $orders=Order::where(['store_id'=>Helpers::get_store_id()])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                    ->orWhere('order_status', 'like', "%{$value}%")
                    ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->pos()->limit(100)->get();
        return response()->json([
            'view'=>view('vendor-views.pos.order.partials._table',compact('orders'))->render()
        ]);
    }

    public function order_details($id)
    {
        $order = Order::with('details')->where(['id' => $id, 'store_id' => Helpers::get_store_id()])->first();
        if (isset($order)) {
            return view('vendor-views.pos.order.order-view', compact('order'));
        } else {
            Toastr::info(translate('No more orders!'));
            return back();
        }
    }

    public function generate_invoice($id)
    {
        $order = Order::where('id', $id)->first();

        return response()->json([
            'success' => 1,
            'view' => view('vendor-views.pos.order.invoice', compact('order'))->render(),
        ]);
    }

    public function customer_store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|unique:users',
        ]);
        User::create([
            'f_name' => $request['f_name'],
            'l_name' => $request['l_name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'password' => bcrypt('password')
        ]);
        try {
            if (config('mail.status')) {
                Mail::to($request->email)->send(new \App\Mail\CustomerRegistration($request->f_name . ' ' . $request->l_name,true));
            }
        } catch (\Exception $ex) {
            info($ex->getMessage());
        }
        Toastr::success(translate('customer_added_successfully'));
        return back();
    }
}
