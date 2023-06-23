<?php

namespace Database\Factories;

use App\Models\OrderDetail;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\CentralLogics\Helpers;

class OrderDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderDetail::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $item_id = $this->faker->numberBetween(1,162177);
        $food = Item::find($item_id);
        if($food)
        {
            $product = Helpers::product_data_formatting($food);
            return [
                'item_id' => $product['id'],
                'order_id'=> $this->faker->numberBetween(100029,298425),
                'item_campaign_id' => null,
                'food_details' => json_encode($product),
                'quantity' => $this->faker->numberBetween(1,100),
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

    }
}
