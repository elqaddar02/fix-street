<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ReportImageValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_image_must_not_exceed_6_megapixels(): void
    {
        $user = User::factory()->create();
        Category::create([
            'name' => 'Roads',
            'name_ar' => 'الطرق',
        ]);

        $response = $this->actingAs($user)->post('/reports', [
            'title' => 'Broken road',
            'category_id' => Category::first()->id,
            'latitude' => 33.9716,
            'longitude' => -6.8498,
            'image' => UploadedFile::fake()->create('report.jpg', 7000, 'image/jpeg'),
        ]);

        $response->assertSessionHasErrors('image');
    }
}
