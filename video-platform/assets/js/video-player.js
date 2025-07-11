/**
 * DOBİEN Video Platform - Video Player JavaScript
 * Geliştirici: DOBİEN
 * Modern Video Oynatıcı İşlevleri
 */

class DOBIENVideoPlayer {
    constructor() {
        this.video = null;
        this.container = null;
        this.controls = null;
        this.isPlaying = false;
        this.isDragging = false;
        this.currentQuality = '720p';
        this.currentSpeed = 1;
        this.volume = 1;
        this.isMuted = false;
        this.isFullscreen = false;
        this.controlsTimeout = null;
        this.qualitySources = [];
        
        this.init();
    }
    
    init() {
        this.video = document.getElementById('mainVideo');
        this.container = document.getElementById('videoPlayer');
        this.controls = document.getElementById('videoControls');
        
        if (!this.video || !this.container || !this.controls) {
            console.warn('Video player elements not found');
            return;
        }
        
        this.setupEventListeners();
        this.loadQualitySources();
        this.setupKeyboardControls();
        this.hideCursor();
    }
    
    setupEventListeners() {
        // Video events
        this.video.addEventListener('loadstart', () => this.showLoading());
        this.video.addEventListener('loadeddata', () => this.hideLoading());
        this.video.addEventListener('loadedmetadata', () => this.updateDuration());
        this.video.addEventListener('timeupdate', () => this.updateProgress());
        this.video.addEventListener('ended', () => this.onVideoEnded());
        this.video.addEventListener('play', () => this.onPlay());
        this.video.addEventListener('pause', () => this.onPause());
        this.video.addEventListener('volumechange', () => this.updateVolumeIcon());
        this.video.addEventListener('waiting', () => this.showLoading());
        this.video.addEventListener('canplay', () => this.hideLoading());
        
        // Container events
        this.container.addEventListener('click', (e) => this.handleContainerClick(e));
        this.container.addEventListener('mousemove', () => this.showControls());
        this.container.addEventListener('mouseleave', () => this.hideControls());
        
        // Control button events
        document.getElementById('playPauseBtn')?.addEventListener('click', () => this.togglePlayPause());
        document.getElementById('volumeBtn')?.addEventListener('click', () => this.toggleMute());
        document.getElementById('qualityBtn')?.addEventListener('click', () => this.toggleQualityMenu());
        document.getElementById('speedBtn')?.addEventListener('click', () => this.toggleSpeedMenu());
        document.getElementById('pipBtn')?.addEventListener('click', () => this.togglePiP());
        document.getElementById('fullscreenBtn')?.addEventListener('click', () => this.toggleFullscreen());
        document.getElementById('bigPlayButton')?.addEventListener('click', () => this.play());
        
        // Progress bar events
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            progressBar.addEventListener('click', (e) => this.seek(e));
            progressBar.addEventListener('mousemove', (e) => this.showTimeTooltip(e));
            progressBar.addEventListener('mouseleave', () => this.hideTimeTooltip());
        }
        
        // Volume slider
        const volumeSlider = document.getElementById('volumeSlider');
        if (volumeSlider) {
            volumeSlider.addEventListener('input', (e) => this.setVolume(e.target.value / 100));
        }
        
        // Speed options
        document.querySelectorAll('.speed-option').forEach(option => {
            option.addEventListener('click', (e) => this.setSpeed(parseFloat(e.target.dataset.speed)));
        });
        
        // Outside click to close menus
        document.addEventListener('click', (e) => this.handleOutsideClick(e));
        
        // Fullscreen change
        document.addEventListener('fullscreenchange', () => this.onFullscreenChange());
        document.addEventListener('webkitfullscreenchange', () => this.onFullscreenChange());
        document.addEventListener('mozfullscreenchange', () => this.onFullscreenChange());
        document.addEventListener('MSFullscreenChange', () => this.onFullscreenChange());
    }
    
    loadQualitySources() {
        const sources = this.video.querySelectorAll('source');
        this.qualitySources = Array.from(sources).map(source => ({
            src: source.src,
            quality: source.dataset.quality,
            resolution: source.dataset.res
        }));
        
        this.populateQualityMenu();
        
        // Set initial quality
        if (this.qualitySources.length > 0) {
            this.currentQuality = this.qualitySources[0].quality;
            this.updateQualityText();
        }
    }
    
    populateQualityMenu() {
        const qualityOptions = document.getElementById('qualityOptions');
        if (!qualityOptions) return;
        
        qualityOptions.innerHTML = '';
        
        this.qualitySources.forEach(source => {
            const option = document.createElement('div');
            option.className = 'quality-option';
            option.textContent = source.quality;
            option.dataset.quality = source.quality;
            option.dataset.src = source.src;
            
            if (source.quality === this.currentQuality) {
                option.classList.add('active');
            }
            
            option.addEventListener('click', () => this.changeQuality(source));
            qualityOptions.appendChild(option);
        });
    }
    
    setupKeyboardControls() {
        document.addEventListener('keydown', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            
            switch (e.code) {
                case 'Space':
                    e.preventDefault();
                    this.togglePlayPause();
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    this.seek(null, this.video.currentTime - 10);
                    break;
                case 'ArrowRight':
                    e.preventDefault();
                    this.seek(null, this.video.currentTime + 10);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    this.changeVolume(0.1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    this.changeVolume(-0.1);
                    break;
                case 'KeyM':
                    e.preventDefault();
                    this.toggleMute();
                    break;
                case 'KeyF':
                    e.preventDefault();
                    this.toggleFullscreen();
                    break;
                case 'Escape':
                    this.exitFullscreen();
                    break;
            }
        });
    }
    
    hideCursor() {
        let cursorTimeout;
        
        this.container.addEventListener('mousemove', () => {
            this.container.style.cursor = 'default';
            clearTimeout(cursorTimeout);
            
            cursorTimeout = setTimeout(() => {
                if (this.isPlaying && !this.controls.classList.contains('show')) {
                    this.container.style.cursor = 'none';
                }
            }, 2000);
        });
    }
    
    handleContainerClick(e) {
        if (e.target === this.container || e.target === this.video) {
            this.togglePlayPause();
        }
    }
    
    handleOutsideClick(e) {
        if (!e.target.closest('.quality-menu') && !e.target.closest('#qualityBtn')) {
            this.hideQualityMenu();
        }
        if (!e.target.closest('.speed-menu') && !e.target.closest('#speedBtn')) {
            this.hideSpeedMenu();
        }
    }
    
    togglePlayPause() {
        if (this.video.paused) {
            this.play();
        } else {
            this.pause();
        }
    }
    
    play() {
        const playPromise = this.video.play();
        
        if (playPromise !== undefined) {
            playPromise.then(() => {
                this.isPlaying = true;
                this.updatePlayButton();
                this.hideBigPlayButton();
            }).catch(error => {
                console.error('Error playing video:', error);
            });
        }
    }
    
    pause() {
        this.video.pause();
        this.isPlaying = false;
        this.updatePlayButton();
        this.showBigPlayButton();
    }
    
    onPlay() {
        this.isPlaying = true;
        this.updatePlayButton();
        this.hideBigPlayButton();
    }
    
    onPause() {
        this.isPlaying = false;
        this.updatePlayButton();
        this.showBigPlayButton();
    }
    
    onVideoEnded() {
        this.isPlaying = false;
        this.updatePlayButton();
        this.showBigPlayButton();
        this.showControls();
    }
    
    updatePlayButton() {
        const playPauseBtn = document.getElementById('playPauseBtn');
        if (!playPauseBtn) return;
        
        const icon = playPauseBtn.querySelector('i');
        if (icon) {
            icon.className = this.isPlaying ? 'fas fa-pause' : 'fas fa-play';
        }
    }
    
    showBigPlayButton() {
        const bigPlayButton = document.getElementById('bigPlayButton');
        if (bigPlayButton) {
            bigPlayButton.classList.remove('hide');
        }
    }
    
    hideBigPlayButton() {
        const bigPlayButton = document.getElementById('bigPlayButton');
        if (bigPlayButton) {
            bigPlayButton.classList.add('hide');
        }
    }
    
    seek(event, time) {
        if (time !== undefined) {
            this.video.currentTime = Math.max(0, Math.min(time, this.video.duration));
            return;
        }
        
        if (!event) return;
        
        const progressBar = event.currentTarget;
        const rect = progressBar.getBoundingClientRect();
        const percent = (event.clientX - rect.left) / rect.width;
        const time = percent * this.video.duration;
        
        this.video.currentTime = time;
    }
    
    updateProgress() {
        if (!this.video.duration) return;
        
        const progress = (this.video.currentTime / this.video.duration) * 100;
        const progressElement = document.getElementById('progress');
        
        if (progressElement) {
            progressElement.style.width = progress + '%';
        }
        
        this.updateCurrentTime();
    }
    
    updateCurrentTime() {
        const currentTimeElement = document.getElementById('currentTime');
        if (currentTimeElement) {
            currentTimeElement.textContent = this.formatTime(this.video.currentTime);
        }
    }
    
    updateDuration() {
        const durationElement = document.getElementById('duration');
        if (durationElement) {
            durationElement.textContent = this.formatTime(this.video.duration);
        }
    }
    
    formatTime(seconds) {
        if (!seconds || !isFinite(seconds)) return '00:00';
        
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60);
        
        if (hours > 0) {
            return `${hours}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
        
        return `${minutes}:${secs.toString().padStart(2, '0')}`;
    }
    
    showTimeTooltip(event) {
        const progressBar = event.currentTarget;
        const tooltip = document.getElementById('timeTooltip');
        if (!tooltip || !this.video.duration) return;
        
        const rect = progressBar.getBoundingClientRect();
        const percent = (event.clientX - rect.left) / rect.width;
        const time = percent * this.video.duration;
        
        tooltip.textContent = this.formatTime(time);
        tooltip.style.left = (event.clientX - rect.left) + 'px';
        tooltip.style.opacity = '1';
    }
    
    hideTimeTooltip() {
        const tooltip = document.getElementById('timeTooltip');
        if (tooltip) {
            tooltip.style.opacity = '0';
        }
    }
    
    toggleMute() {
        if (this.video.muted) {
            this.video.muted = false;
            this.video.volume = this.volume;
        } else {
            this.video.muted = true;
        }
        
        this.updateVolumeIcon();
    }
    
    setVolume(volume) {
        this.volume = Math.max(0, Math.min(1, volume));
        this.video.volume = this.volume;
        this.video.muted = false;
        this.updateVolumeIcon();
    }
    
    changeVolume(delta) {
        this.setVolume(this.video.volume + delta);
        
        // Update volume slider
        const volumeSlider = document.getElementById('volumeSlider');
        if (volumeSlider) {
            volumeSlider.value = this.video.volume * 100;
        }
    }
    
    updateVolumeIcon() {
        const volumeBtn = document.getElementById('volumeBtn');
        if (!volumeBtn) return;
        
        const icon = volumeBtn.querySelector('i');
        if (!icon) return;
        
        if (this.video.muted || this.video.volume === 0) {
            icon.className = 'fas fa-volume-mute';
        } else if (this.video.volume < 0.5) {
            icon.className = 'fas fa-volume-down';
        } else {
            icon.className = 'fas fa-volume-up';
        }
    }
    
    toggleQualityMenu() {
        const qualityMenu = document.getElementById('qualityMenu');
        if (!qualityMenu) return;
        
        qualityMenu.classList.toggle('show');
        this.hideSpeedMenu();
    }
    
    hideQualityMenu() {
        const qualityMenu = document.getElementById('qualityMenu');
        if (qualityMenu) {
            qualityMenu.classList.remove('show');
        }
    }
    
    changeQuality(source) {
        const currentTime = this.video.currentTime;
        const wasPlaying = !this.video.paused;
        
        this.video.src = source.src;
        this.currentQuality = source.quality;
        
        this.video.addEventListener('loadedmetadata', () => {
            this.video.currentTime = currentTime;
            if (wasPlaying) {
                this.video.play();
            }
        }, { once: true });
        
        this.updateQualityText();
        this.updateQualityOptions();
        this.hideQualityMenu();
    }
    
    updateQualityText() {
        const qualityText = document.querySelector('.quality-text');
        if (qualityText) {
            qualityText.textContent = this.currentQuality;
        }
    }
    
    updateQualityOptions() {
        document.querySelectorAll('.quality-option').forEach(option => {
            option.classList.remove('active');
            if (option.dataset.quality === this.currentQuality) {
                option.classList.add('active');
            }
        });
    }
    
    toggleSpeedMenu() {
        const speedMenu = document.getElementById('speedMenu');
        if (!speedMenu) return;
        
        speedMenu.classList.toggle('show');
        this.hideQualityMenu();
    }
    
    hideSpeedMenu() {
        const speedMenu = document.getElementById('speedMenu');
        if (speedMenu) {
            speedMenu.classList.remove('show');
        }
    }
    
    setSpeed(speed) {
        this.video.playbackRate = speed;
        this.currentSpeed = speed;
        this.updateSpeedText();
        this.updateSpeedOptions();
        this.hideSpeedMenu();
    }
    
    updateSpeedText() {
        const speedText = document.querySelector('.speed-text');
        if (speedText) {
            speedText.textContent = this.currentSpeed + 'x';
        }
    }
    
    updateSpeedOptions() {
        document.querySelectorAll('.speed-option').forEach(option => {
            option.classList.remove('active');
            if (parseFloat(option.dataset.speed) === this.currentSpeed) {
                option.classList.add('active');
            }
        });
    }
    
    togglePiP() {
        if (!document.pictureInPictureEnabled) {
            alert('Picture-in-Picture modu bu tarayıcıda desteklenmiyor.');
            return;
        }
        
        if (document.pictureInPictureElement) {
            document.exitPictureInPicture();
        } else {
            this.video.requestPictureInPicture().catch(error => {
                console.error('PiP error:', error);
            });
        }
    }
    
    toggleFullscreen() {
        if (this.isFullscreen) {
            this.exitFullscreen();
        } else {
            this.enterFullscreen();
        }
    }
    
    enterFullscreen() {
        const element = this.container;
        
        if (element.requestFullscreen) {
            element.requestFullscreen();
        } else if (element.webkitRequestFullscreen) {
            element.webkitRequestFullscreen();
        } else if (element.mozRequestFullScreen) {
            element.mozRequestFullScreen();
        } else if (element.msRequestFullscreen) {
            element.msRequestFullscreen();
        }
    }
    
    exitFullscreen() {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
    
    onFullscreenChange() {
        this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement || 
                              document.mozFullScreenElement || document.msFullscreenElement);
        
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        if (fullscreenBtn) {
            const icon = fullscreenBtn.querySelector('i');
            if (icon) {
                icon.className = this.isFullscreen ? 'fas fa-compress' : 'fas fa-expand';
            }
        }
        
        // Adjust container for fullscreen
        if (this.isFullscreen) {
            this.container.classList.add('fullscreen');
        } else {
            this.container.classList.remove('fullscreen');
        }
    }
    
    showControls() {
        this.controls.classList.add('show');
        this.container.style.cursor = 'default';
        
        clearTimeout(this.controlsTimeout);
        
        if (this.isPlaying) {
            this.controlsTimeout = setTimeout(() => {
                this.hideControls();
            }, 3000);
        }
    }
    
    hideControls() {
        if (!this.isPlaying) return;
        
        this.controls.classList.remove('show');
        this.container.style.cursor = 'none';
    }
    
    showLoading() {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.classList.add('show');
        }
    }
    
    hideLoading() {
        const spinner = document.getElementById('loadingSpinner');
        if (spinner) {
            spinner.classList.remove('show');
        }
    }
}

// Global functions for external access
let videoPlayer;

function initVideoPlayer() {
    videoPlayer = new DOBIENVideoPlayer();
}

function setupVideoEvents() {
    // Additional setup if needed
}

function loadQualityOptions() {
    // Legacy function for compatibility
    if (videoPlayer) {
        videoPlayer.loadQualitySources();
    }
}

// Initialize on DOM content loaded
document.addEventListener('DOMContentLoaded', function() {
    // Auto-initialize if video player elements exist
    if (document.getElementById('mainVideo')) {
        initVideoPlayer();
    }
});

// Export for module usage if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = DOBIENVideoPlayer;
}