<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ReportCreatePageAssetsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The create page shipped without image-compress.js while create.js called
     * window.madinupImage.compress(). The call threw on every photo, the catch
     * reported it as an unreadable image, and the input was cleared — so no
     * report could ever be submitted with a picture.
     */
    public function test_create_page_loads_the_image_compression_helper(): void
    {
        $user = User::factory()->create();

        $content = $this->actingAs($user)
            ->get(route('reports.create'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('js/reports/image-compress.js', $content);
        $this->assertStringContainsString('js/reports/create.js', $content);

        $this->assertLessThan(
            strpos($content, 'js/reports/create.js'),
            strpos($content, 'js/reports/image-compress.js'),
            'image-compress.js must load before create.js, which depends on window.madinupImage.'
        );
    }

    public function test_edit_page_also_loads_the_helper_it_depends_on(): void
    {
        $content = file_get_contents(resource_path('views/reports/edit.blade.php'));

        $this->assertStringContainsString('js/reports/image-compress.js', $content);
    }

    #[DataProvider('acceptedImageTypes')]
    public function test_server_accepts_ordinary_photo_formats(string $filename, string $mime): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Roads', 'name_ar' => 'الطرق']);
        $city = City::create(['name' => 'Casablanca', 'active' => true]);

        $this->actingAs($user)
            ->post(route('reports.store'), [
                'title' => 'Nid-de-poule',
                'category_id' => $category->id,
                'city_id' => $city->id,
                'latitude' => 33.5731,
                'longitude' => -7.5898,
                'image' => UploadedFile::fake()->create($filename, 512, $mime),
            ])
            ->assertSessionDoesntHaveErrors('image');
    }

    public static function acceptedImageTypes(): array
    {
        return [
            'jpeg' => ['photo.jpg', 'image/jpeg'],
            'png' => ['photo.png', 'image/png'],
        ];
    }
}
