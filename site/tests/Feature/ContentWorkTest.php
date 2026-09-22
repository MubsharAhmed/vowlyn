<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\ContentWorks\Pages\CreateContentWork;
use App\Filament\Resources\ContentWorks\Pages\EditContentWork;
use App\Models\ContentWork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

final class ContentWorkTest extends TestCase
{
    use RefreshDatabase;

    private function data(array $extra = []): array
    {
        return array_replace([
            'discipline' => 'photography', 'title' => 'Campaign portrait', 'client' => 'Example client',
            'description' => 'A considered portrait from the campaign production.',
            'image_alt' => 'Portrait of a campaign subject against a blue studio background',
            'link_url' => null, 'sort_order' => 40, 'is_published' => false, 'rights_confirmed' => true,
        ], $extra);
    }

    private function imageUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'content-work-test-');
        (new Process(['/usr/bin/magick', '-size', '1200x800', 'xc:#18d2ff', $path.'.png']))->mustRun();
        $file = UploadedFile::fake()->createWithContent('portrait.png', file_get_contents($path.'.png'));
        unlink($path);
        unlink($path.'.png');

        return $file;
    }

    public function test_seeded_photography_and_design_work_is_visible(): void
    {
        $this->assertSame(3, ContentWork::where('discipline', 'photography')->count());
        $this->assertSame(3, ContentWork::where('discipline', 'design')->count());
        $this->get('/content-creation')->assertOk()->assertSee('Above the Fairway')->assertSee('IT Bridges')->assertSee('View live project');
    }

    public function test_admin_upload_is_optimized_and_draft_can_be_published(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        $upload = $this->imageUpload();

        Livewire::test(CreateContentWork::class)
            ->fillForm($this->data(['image_upload' => [$upload]]))
            ->call('create')->assertHasNoFormErrors()->assertNotified();

        $work = ContentWork::where('title', 'Campaign portrait')->firstOrFail();
        $this->assertStringEndsWith('-large.webp', $work->image_path);
        $this->assertStringEndsWith('-card.webp', $work->thumbnail_path);
        Storage::disk('public')->assertExists([$work->image_path, $work->thumbnail_path]);
        $this->get('/content-creation')->assertDontSee('Campaign portrait');

        Livewire::test(EditContentWork::class, ['record' => $work->id])->fillForm(['is_published' => true])->call('save')->assertHasNoFormErrors();
        $this->get('/content-creation')->assertSee('Campaign portrait');
    }

    public function test_only_verified_admins_can_manage_creative_work(): void
    {
        $this->get('/admin/content-works')->assertRedirect();
        $user = User::factory()->unverified()->create();
        $this->assertFalse(Gate::forUser($user)->allows('create', ContentWork::class));
        $this->actingAs($user)->get('/admin/content-works')->assertForbidden();
    }
}
