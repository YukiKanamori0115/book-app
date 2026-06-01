<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    protected $model = Book::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(), // 適当なタイトル
            'author' => $this->faker->name(),    // 適当な著者名
            'isbn13' => $this->faker->isbn13(),  // 適当なISBN
            // 他に必須カラムがあればここに追加してください
        ];
    }
}
