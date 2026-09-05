// No video bytes are requested before a visitor presses Play.
export default () => ({
    started: false,
    playing: false,
    loading: false,
    error: '',
    observer: null,
    onVisibility: null,
    onOtherVideo: null,
    init() {
        this.onVisibility = () => { if (document.hidden) this.$refs.film.pause(); };
        this.onOtherVideo = (event) => { if (event.detail !== this.$refs.film) this.$refs.film.pause(); };
        document.addEventListener('visibilitychange', this.onVisibility);
        document.addEventListener('content-video-play', this.onOtherVideo);
        this.observer = new IntersectionObserver(([entry]) => {
            if (!entry.isIntersecting) this.$refs.film.pause();
        });
        this.observer.observe(this.$root);
    },
    async play() {
        this.error = '';
        this.loading = true;
        const video = this.$refs.film;
        if (!video.getAttribute('src')) video.src = this.$root.dataset.videoUrl;
        this.started = true;
        try {
            await this.$nextTick();
            await video.play();
        } catch (error) {
            if (error.name !== 'AbortError') this.onError();
        } finally {
            this.loading = false;
        }
    },
    onPlay() {
        this.playing = true;
        document.dispatchEvent(new CustomEvent('content-video-play', { detail: this.$refs.film }));
    },
    onError() {
        this.error = 'Video could not load. Check your connection and press Play to retry.';
        this.started = false;
        this.loading = false;
        this.$refs.film.removeAttribute('src');
        this.$refs.film.load();
    },
    destroy() {
        this.observer?.disconnect();
        document.removeEventListener('visibilitychange', this.onVisibility);
        document.removeEventListener('content-video-play', this.onOtherVideo);
        this.$refs.film.pause();
    },
});
