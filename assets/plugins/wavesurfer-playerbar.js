// WaveSurfer player bar for Music Cloud
// This script replaces the progress bar in the player bar with a waveform
// and aligns the cover image, title, and artist to the left.

let wavePlayer = null;
let waveReady = false;

function initWaveSurferPlayerBar(audioSelector, waveContainerSelector) {
    // Lấy lại các thành phần player (cover, title, artist) nếu bị mất
    function restorePlayerInfo() {
        if (!document.getElementById('player-cover')) {
            // Nếu mất, reload lại giao diện player (có thể reload lại trang hoặc append lại DOM)
            // Ở đây chỉ cảnh báo, tuỳ dự án có thể custom thêm
            console.warn('Player cover bị mất, cần kiểm tra lại DOM render.');
        }
    }
    restorePlayerInfo();

    // Đảm bảo chỉ có 1 instance và reset đúng khi đổi bài
    if (wavePlayer) {
        wavePlayer.destroy();
        wavePlayer = null;
    }
    const audio = document.querySelector(audioSelector);
    if (!audio) return;
    // Luôn clear sóng cũ trước khi tạo mới
    if (document.querySelector(waveContainerSelector)) {
        document.querySelector(waveContainerSelector).innerHTML = '';
    }
    wavePlayer = WaveSurfer.create({
        container: waveContainerSelector,
        waveColor: '#ffb86c',
        progressColor: '#ff5500',
        height: 48,
        barWidth: 3,
        barGap: 1,
        barRadius: 4,
        responsive: true,
        cursorColor: '#fff',
        normalize: true,
        partialRender: true,
    });
    window._wavePlayerBarInstance = wavePlayer;

    // Luôn load lại sóng khi audio thay đổi source
    function reloadWave() {
        let src = audio.querySelector('source') ? audio.querySelector('source').src : audio.src;
        if (!src) return;
        wavePlayer.empty();
        wavePlayer.load(src);
        if (audio) audio.currentTime = 0;
        wavePlayer.once('ready', function() {
            if (!audio.paused) wavePlayer.play();
        });
    }
    audio.addEventListener('loadedmetadata', reloadWave);
    // Nếu source thay đổi qua DOM
    const observer = new MutationObserver(reloadWave);
    observer.observe(audio, { childList: true, subtree: true });
    // Khởi tạo sóng lần đầu
    reloadWave();

    // 2. Khi play/pause thì play/pause sóng, nhưng không dùng backend MediaElement nữa
    // 3. Khi seek sóng thì cập nhật audio.currentTime và ngược lại
    wavePlayer.on('seek', (progress) => {
        if (audio.duration) audio.currentTime = progress * audio.duration;
    });
    audio.addEventListener('timeupdate', function() {
        if (wavePlayer && wavePlayer.getDuration() > 0) {
            let percent = audio.currentTime / audio.duration;
            let wavePercent = wavePlayer.getCurrentTime() / wavePlayer.getDuration();
            if (Math.abs(percent - wavePercent) > 0.02) {
                wavePlayer.seekTo(percent);
            }
        }
    });
    // 4. Khi sóng play thì audio play, khi sóng pause thì audio pause
    wavePlayer.on('play', function() { if(audio.paused) audio.play(); });
    wavePlayer.on('pause', function() { if(!audio.paused) audio.pause(); });
    audio.addEventListener('play', function() { if(wavePlayer) wavePlayer.play(); });
    audio.addEventListener('pause', function() { if(wavePlayer) wavePlayer.pause(); });
    // 5. Đảm bảo màu sóng
    wavePlayer.on('ready', function() {
        if (wavePlayer.drawer && wavePlayer.drawer.progressWave) {
            wavePlayer.drawer.progressWave.style.backgroundColor = '#ff5500';
        }
        if (wavePlayer.drawer && wavePlayer.drawer.wave) {
            wavePlayer.drawer.wave.style.backgroundColor = '#ffb86c';
        }
    });
    // Force redraw and color update on play for some browsers
    audio.addEventListener('play', () => {
        // WaveSurfer v7 không còn drawBuffer, chỉ cập nhật màu nếu có
        setTimeout(() => {
            if (wavePlayer.drawer && wavePlayer.drawer.progressWave) {
                wavePlayer.drawer.progressWave.style.backgroundColor = '#ff5500';
            }
        }, 200);
    });
    // Đồng bộ thanh tiến độ và thời gian
    function updateProgress() {
        if (!audio.duration) return;
        var cur = audio.currentTime;
        var dur = audio.duration;
        var percent = (cur/dur)*100;
        var curMin = Math.floor(cur/60), curSec = Math.floor(cur%60);
        var durMin = Math.floor(dur/60), durSec = Math.floor(dur%60);
        document.getElementById('player-current-time').textContent = curMin+":"+(curSec<10?"0":"")+curSec;
        document.getElementById('player-duration').textContent = durMin+":"+(durSec<10?"0":"")+durSec;
        document.getElementById('player-progress').value = percent;
    }
    audio.addEventListener('timeupdate', updateProgress);
    audio.addEventListener('loadedmetadata', updateProgress);
    document.getElementById('player-progress').addEventListener('input', function(e){
        if(audio.duration) audio.currentTime = (e.target.value/100)*audio.duration;
    });
    // Volume control fix: luôn đồng bộ muted và volume, không bị lệch khi kéo hoặc bấm nút
    var volSlider = document.getElementById('player-volume');
    function setVolumeAndMute(val) {
        var v = Math.max(0, Math.min(1, parseFloat(val)));
        // Đảm bảo volume luôn là số hợp lệ
        if (wavePlayer && wavePlayer.setVolume) {
            wavePlayer.setVolume(v);
        }
        // Nếu có audio element, đồng bộ volume và muted
        if (audio) {
            audio.volume = v;
            if (volSlider) volSlider.value = v;
            audio.muted = (v === 0);
        }
    }
    if (volSlider) {
        volSlider.addEventListener('input', function(e){
            setVolumeAndMute(e.target.value);
        });
        // Đồng bộ lại volume khi load bài mới
        setTimeout(function(){
            setVolumeAndMute(volSlider.value);
        }, 200);
    }
    var btnDown = document.getElementById('player-volume-down');
    if (volSlider && btnDown) {
        btnDown.onclick = function(){
            setVolumeAndMute(parseFloat(volSlider.value)-0.1);
        };
    }
    var btnUp = document.getElementById('player-volume-up');
    if (volSlider && btnUp) {
        btnUp.onclick = function(){
            setVolumeAndMute(parseFloat(volSlider.value)+0.1);
        };
    }
    setTimeout(function(){
        if (volSlider) setVolumeAndMute(volSlider.value);
    }, 200);
    // Sửa lỗi sóng nhạc bị xám: ép style cho bar và progress bằng CSS
    var style = document.createElement('style');
    style.innerHTML = `
      #waveform-playerbar .wavesurfer-bar {
        background: #ffb86c !important;
        border-radius: 4px 4px 0 0;
      }
      #waveform-playerbar .wavesurfer-progress {
        background: #ff5500 !important;
      }
      #waveform-playerbar .wavesurfer-cursor {
        background: #fff !important;
        width: 2px !important;
      }
    `;
    document.head.appendChild(style);
    // Đồng bộ sóng nhạc và thanh tiến độ
    function syncProgressFromAudio() {
        if (!audio.duration) return;
        var percent = (audio.currentTime / audio.duration) * 100;
        if (wavePlayer && wavePlayer.getDuration() > 0) {
            var wavePercent = wavePlayer.getCurrentTime() / wavePlayer.getDuration() * 100;
            // Nếu lệch quá 1% thì seek sóng về đúng audio
            if (Math.abs(percent - wavePercent) > 1) {
                wavePlayer.seekTo(audio.currentTime / audio.duration);
            }
        }
    }
    function syncAudioFromWave() {
        if (!audio.duration || !wavePlayer) return;
        var wavePercent = wavePlayer.getCurrentTime() / wavePlayer.getDuration();
        var audioPercent = audio.currentTime / audio.duration;
        // Nếu lệch quá 1% thì seek audio về đúng sóng
        if (Math.abs(wavePercent - audioPercent) > 0.01) {
            audio.currentTime = wavePlayer.getCurrentTime();
        }
    }
    // Khi audio chạy thì cập nhật sóng
    audio.addEventListener('timeupdate', syncProgressFromAudio);
    // Khi sóng seek thì cập nhật audio
    wavePlayer.on('seek', function() {
        syncAudioFromWave();
    });
    // Khi play/pause thì đồng bộ trạng thái
    audio.addEventListener('play', function() { if(wavePlayer) wavePlayer.play(); });
    audio.addEventListener('pause', function() { if(wavePlayer) wavePlayer.pause(); });
    wavePlayer.on('play', function() { if(audio.paused) audio.play(); });
    wavePlayer.on('pause', function() { if(!audio.paused) audio.pause(); });
}
window.initWaveSurferPlayerBar = initWaveSurferPlayerBar;
