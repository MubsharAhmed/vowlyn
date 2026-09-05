<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Filament\Resources\ContentVideos\Pages\CreateContentVideo;
use App\Filament\Resources\ContentVideos\Pages\EditContentVideo;
use App\Filament\Resources\ContentVideos\Pages\ListContentVideos;
use App\Models\ContentVideo;
use App\Models\User;
use App\Services\ContentVideoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Symfony\Component\Process\Process;
use Tests\TestCase;

final class ContentVideoTest extends TestCase
{
    use RefreshDatabase;

    private function upload(array $options = []): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'video-test-');
        $process = new Process(['ffmpeg', '-v', 'error', '-y', '-f', 'lavfi', '-i', $options['input'] ?? 'color=c=blue:s=180x280:d=1', '-c:v', $options['codec'] ?? 'libx264', '-pix_fmt', 'yuv420p', '-f', 'mp4', $path]);
        $process->mustRun();
        $file = UploadedFile::fake()->createWithContent('sample.mp4', file_get_contents($path));
        unlink($path);

        return $file;
    }

    private function data(array $extra = []): array
    {
        return array_replace(['title' => 'New client film', 'client' => 'Our client', 'type' => 'Brand film', 'note' => 'A short brand story.', 'sort_order' => 10, 'is_published' => false, 'is_featured' => false, 'rights_confirmed' => true], $extra);
    }

    public function test_existing_five_videos_are_preserved_and_page_does_not_autoload_video(): void
    {
        $this->assertSame(5, ContentVideo::count());
        $this->get('/content-creation')->assertOk()->assertSee('The Full Round')->assertSee('preload="none"', false)->assertDontSee('<source src=', false)->assertDontSee('<video autoplay', false);
    }

    public function test_admin_can_view_reorder_and_filter_the_library(): void
    {
        $this->actingAs(User::factory()->create());
        $ids = ContentVideo::orderByDesc('id')->pluck('id')->all();
        Livewire::test(ListContentVideos::class)
            ->assertCanSeeTableRecords(ContentVideo::all())
            ->call('reorderTable', $ids)
            ->assertHasNoErrors();
        $this->assertSame($ids, ContentVideo::orderBy('sort_order')->pluck('id')->all());
        $draft = ContentVideo::firstOrFail();
        $draft->update(['is_published' => false]);
        Livewire::test(ListContentVideos::class)
            ->filterTable('is_published', true)
            ->assertCanNotSeeTableRecords([$draft]);
    }

    public function test_verified_admin_can_upload_then_publish_and_preview_a_video(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
        Livewire::test(CreateContentVideo::class)->fillForm($this->data(['video_upload' => [$this->upload()]]))->call('create')->assertHasNoFormErrors()->assertNotified();
        $video = ContentVideo::where('title', 'New client film')->firstOrFail();
        $this->assertFalse($video->is_published);
        Storage::disk('public')->assertExists([$video->video_path, $video->poster_path]);
        $this->get('/content-creation')->assertDontSee('New client film');
        Livewire::test(EditContentVideo::class, ['record' => $video->id])->assertSee('Current video')->fillForm(['is_published' => true])->call('save')->assertHasNoFormErrors();
        $this->get('/content-creation')->assertSee('New client film');
    }

    public function test_invalid_upload_does_not_create_record_or_files(): void
    {
        Storage::fake('public');
        try {
            app(ContentVideoService::class)->save(new ContentVideo, $this->data(['video_upload' => UploadedFile::fake()->createWithContent('fake.mp4', '<?php echo 1;')]));
            $this->fail('Invalid file accepted');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('data.video_upload', $e->errors());
        }
        $this->assertSame(5, ContentVideo::count());
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_duration_resolution_codec_and_file_size_limits_are_enforced(): void
    {
        Storage::fake('public');
        $uploads = [
            $this->upload(['input' => 'color=c=blue:s=64x64:r=1:d=181']),
            $this->upload(['input' => 'color=c=blue:s=2000x64:r=1:d=1']),
            $this->upload(['codec' => 'mpeg4']),
            $this->upload()->size(51201),
        ];
        foreach ($uploads as $upload) {
            try {
                app(ContentVideoService::class)->save(new ContentVideo, $this->data(['video_upload' => $upload]));
                $this->fail('An unsupported upload was accepted');
            } catch (ValidationException $e) {
                $this->assertArrayHasKey('data.video_upload', $e->errors());
            }
        }
        $this->assertSame(5, ContentVideo::count());
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_form_cannot_replace_media_with_an_arbitrary_path_and_titles_are_escaped(): void
    {
        $video = ContentVideo::firstOrFail();
        $path = $video->video_path;
        app(ContentVideoService::class)->save($video, $this->data([
            'title' => '<script>alert("x")</script>', 'is_published' => true,
            'video_path' => '../../private/file.mp4', 'poster_path' => 'https://example.test/tracker.jpg',
        ]));
        $this->assertSame($path, $video->fresh()->video_path);
        $this->get('/content-creation')->assertSee('&lt;script&gt;', false)->assertDontSee('<script>alert("x")</script>', false);
    }

    public function test_replacing_media_removes_only_the_old_managed_files(): void
    {
        Storage::fake('public');
        $service = app(ContentVideoService::class);
        $video = $service->save(new ContentVideo, $this->data(['video_upload' => $this->upload()]));
        $old = [$video->video_path, $video->poster_path];
        $service->save($video, $this->data(['video_upload' => $this->upload()]));
        Storage::disk('public')->assertExists([$video->video_path, $video->poster_path]);
        Storage::disk('public')->assertMissing($old);
    }

    public function test_temporary_upload_endpoint_requires_verified_admin(): void
    {
        $url = URL::temporarySignedRoute('livewire.upload-file', now()->addMinutes(5));
        $this->post($url, ['files' => [$this->upload()]])->assertForbidden();
        $this->actingAs(User::factory()->unverified()->create())->post($url, ['files' => [$this->upload()]])->assertForbidden();
    }

    public function test_quota_failure_preserves_existing_video(): void
    {
        Storage::fake('public');
        config(['content-videos.storage_limit_mb' => 0]);
        $video = ContentVideo::firstOrFail();
        $path = $video->video_path;
        try {
            app(ContentVideoService::class)->save($video, $this->data(['video_upload' => $this->upload()]));
            $this->fail('Quota not enforced');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('data.video_upload', $e->errors());
        }
        $this->assertSame($path, $video->fresh()->video_path);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_pagination_drafts_and_empty_collection(): void
    {
        $original = ContentVideo::firstOrFail();
        for ($i = 0; $i < 12; $i++) {
            $copy = $original->replicate();
            $copy->title = 'Extra film '.$i;
            $copy->is_featured = false;
            $copy->save();
        }
        $this->get('/content-creation')->assertOk()->assertViewHas('films', fn ($films) => $films->count() === 9 && $films->total() === 17);
        ContentVideo::query()->update(['is_published' => false]);
        $this->get('/content-creation')->assertOk()->assertSee('New work is on the way')->assertDontSee('The Full Round');
    }

    public function test_only_verified_admins_can_manage_videos(): void
    {
        $this->get('/admin/content-videos')->assertRedirect();
        $user = User::factory()->unverified()->create();
        $this->assertFalse(Gate::forUser($user)->allows('create', ContentVideo::class));
        $this->actingAs($user)->get('/admin/content-videos')->assertForbidden();
    }

    public function test_trashed_video_can_be_restored_and_permanent_delete_releases_managed_files(): void
    {
        Storage::fake('public');
        $video = app(ContentVideoService::class)->save(new ContentVideo, $this->data(['video_upload' => $this->upload(), 'is_published' => true]));
        $paths = [$video->video_path, $video->poster_path];
        $video->delete();
        Storage::disk('public')->assertExists($paths);
        $this->get('/content-creation')->assertDontSee('New client film');
        $video->restore();
        $this->get('/content-creation')->assertSee('New client film');
        $video->forceDelete();
        Storage::disk('public')->assertMissing($paths);
    }
}
