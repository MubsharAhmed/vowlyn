# Content video administration

Open **Admin → Website content → Content videos → Upload video**.

1. Choose an MP4 and wait for the upload progress to finish.
2. Optionally choose a cover; otherwise the first frame becomes an optimized JPEG cover.
3. Confirm permission to use the video, music and likenesses. Enter its title, client, category and description. Add a transcript/visual description when useful; export captions into videos containing speech.
4. Save as a draft. Review the current video on the edit screen.
5. Turn on **Show on the content page** and save. **Use in the hero** replaces the featured video. Only one video can be featured.
6. Change Display order (lower first), or drag rows in reorder mode. Search, publication filters and Trash help manage the library.

Moving a video to Trash hides it but keeps its files so it can be restored. Permanent deletion removes managed uploads when no other record references them. The five original bundled videos are retained on disk. A replacement is validated before the old media is removed.

## Limits and performance

- MP4, H.264 8-bit / AAC audio; up to **50 MiB**, **3 minutes**, **1080p** (1920 × 1080 or 1080 × 1920), **60 fps**, **12 Mbps**. Export from your editor with these settings; MOV, ProRes, HEVC and 4K must be converted before upload.
- Optional cover: JPEG/PNG/WebP, 2 MiB, maximum 4096 × 4096. Covers are normalized to JPEG, at most 720 × 1120.
- Managed media allowance: **2 GiB**, including Trash. Change `CONTENT_VIDEO_STORAGE_MB` deliberately when more disk capacity is available. Bundled original media and temporary uploads are separate from this quota; leave room for backups and temporary files.
- Signed temporary uploads require a verified admin and are rate-limited to 20 requests/minute. Temporary files are private, expire after 10 minutes for form submission, and Livewire cleans old temporary files during subsequent uploads (not an independent cron cleanup). No original filenames or arbitrary URL downloads are used.
- Video inspection and stream-copy optimization use bounded FFprobe/FFmpeg processes. There is no expensive video transcoding on the web server. A lock serializes processing to enforce the disk quota.
- Nine cards per page, lazy covers, no autoplay or video requests until Play, native seek/volume/fullscreen controls, one player at a time, pause offscreen or in hidden tabs. Uploaded media is served statically through `/storage`, not streamed through PHP.
- **Draft means unlisted, not private.** Anyone with the asset URL can access it. Do not upload confidential or unreleased material requiring access control.

## Deployment prerequisites (do not reset the database)

1. Install FFmpeg/FFprobe on the VPS (Ubuntu: `sudo apt-get install ffmpeg`). The defaults are `/usr/bin/ffmpeg` and `/usr/bin/ffprobe`; override `FFMPEG_BINARY` / `FFPROBE_BINARY` if necessary. Keep the OS packages security-updated.
2. For this website's PHP-FPM configuration, allow `upload_max_filesize = 50M`, `post_max_size = 64M`, `max_execution_time = 120`, and `max_input_time = 600`. Reload the applicable PHP-FPM service after changing its configuration. CLI PHP settings do not prove the FPM limits.
3. In the **vowlyn.com server block only**, set `client_max_body_size 64M;` and allow enough request time (e.g. `fastcgi_read_timeout 150s;`). Test with `sudo nginx -t` before reloading Nginx. Do not modify other sites' server blocks.
4. Use the project's atomic deployment script. The new migration creates only `content_videos` and imports the original five entries. It does not reset or replace existing database tables. Never use `migrate:fresh` on production.
5. Ensure `public/storage` points to shared `storage/app/public`, writable by PHP-FPM. Shared storage must persist across releases, be included in backups, and serve MP4/JPEG with correct MIME types and byte-range support. Continue denying PHP/script execution under uploads.
6. Verify in the production admin: upload a real MP4 larger than 2 MB, preview it, publish it, check playback/seeking from a phone, then remove the test record. Check DevTools Network: zero MP4 requests on initial public-page load, and a successful media request after Play.

Application code does not silently change VPS/Nginx configuration. If you see HTTP 413, check Nginx's body limit; if PHP rejects uploads, check FPM's upload and POST limits. A failed media-processing message means FFmpeg/FFprobe, disk space, permissions or the source file needs checking.

## Local verification

`php artisan test` includes real FFmpeg-backed upload tests. FFmpeg/FFprobe must be installed locally. `npm run build` builds the click-to-play behavior. For local uploads, run PHP with the upload/POST limits above; stock PHP often defaults to only 2 MB.
