<?php

namespace Database\Factories;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Transaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title'      => 'Payment to ' . $this->faker->name,
            'amount'     => rand(10, 500),
            'status'     => Arr::random(['success', 'processing', 'failed']),
            'created_at' => Carbon::now()->subDays(rand(1, 365)),
        ];
    }
}
