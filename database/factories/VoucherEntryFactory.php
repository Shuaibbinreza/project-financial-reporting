<?php

namespace Database\Factories;

use App\Models\VoucherEntry;
use App\Models\Voucher;
use App\Models\Category;
use App\Models\EconomicCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VoucherEntry>
 */
class VoucherEntryFactory extends Factory
{
    protected $model = VoucherEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();
        $economicCode = EconomicCode::where('category_id', $category->id)->inRandomOrder()->first() 
            ?? EconomicCode::factory(['category_id' => $category->id])->create();

        return [
            'voucher_id' => Voucher::factory(),
            'category_id' => $category->id,
            'economic_code_id' => $economicCode->id,
            'amount' => $this->faker->randomFloat(2, 100, 10000),
        ];
    }
}