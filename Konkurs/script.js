let audioCtx = new (window.AudioContext || window.webkitAudioContext)();

function updateFreqLabel(val) {
    document.getElementById("freqVal").innerText = val + " Hz";
}

function playSound() {
    let freq = parseFloat(document.getElementById("freq").value);
    playFreq(freq, "sine", 1);
    drawWave(freq);
}

function drawWave(freq) {
    let canvas = document.getElementById("wave");
    let ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();
    ctx.lineWidth = 2;
    ctx.strokeStyle = "#e2dfed";

    for (let x = 0; x < canvas.width; x++) {
        let y = canvas.height / 2 + Math.sin(x * freq * 0.005) * 60;
        ctx.lineTo(x, y);
    }
    ctx.stroke();
}


function playFreq(f, type = "sine", duration = 1) {
    let osc = audioCtx.createOscillator();
    let gainNode = audioCtx.createGain();

    osc.type = type;
    osc.frequency.value = f;


    gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + duration);

    osc.connect(gainNode);
    gainNode.connect(audioCtx.destination);

    osc.start();
    osc.stop(audioCtx.currentTime + duration);
}


function playHarmonics() {
    let base = parseFloat(document.getElementById("freq").value);

    [1, 2, 3, 4].forEach(n => {
        playFreq(base * n, "sine", 1.5);
    });
}

function playInterval(ratio) {
    let base = 440;
    playFreq(base, "sine", 1);
    playFreq(base * ratio, "sine", 1);
}

function playScale() {
    let base = 261.63; // C4
    let factor = Math.pow(2, 1 / 12);
    for (let i = 0; i <= 12; i++) {
        setTimeout(() => {
            let f = base * Math.pow(factor, i);
            playFreq(f, "sine", 0.5);
            drawWave(f);
        }, i * 500);
    }
}

function playOff() { playFreq(430, "sine", 1); } //
function playOn() { playFreq(440, "sine", 1); }  //


function changeAndPlay(type) {
    let freq = parseFloat(document.getElementById("freq").value);

    let osc = audioCtx.createOscillator();
    let gainNode = audioCtx.createGain();

    osc.type = type;
    osc.frequency.value = freq;

    gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
    gainNode.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + 1);

    osc.connect(gainNode);
    gainNode.connect(audioCtx.destination);

    osc.start();
    osc.stop(audioCtx.currentTime + 1);

    drawCustomWave(type, freq);
}

function drawCustomWave(type, freq) {
    let canvas = document.getElementById("wave");
    let ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();
    ctx.strokeStyle = "#a3a0e6";

    for (let x = 0; x < canvas.width; x++) {
        let t = x * freq * 0.00005;
        let y = 0;

        if (type === 'sine') {
            y = Math.sin(x * freq * 0.005);
        } else if (type === 'square') {
            y = Math.sin(x * freq * 0.005) >= 0 ? 1 : -1;
        } else if (type === 'sawtooth') {
            y = 2 * (t - Math.floor(t + 0.5));
        } else if (type === 'triangle') {
            y = Math.asin(Math.sin(x * freq * 0.005)) * (2 / Math.PI);
        }

        ctx.lineTo(x, canvas.height / 2 + y * 60);
    }
    ctx.stroke();
}