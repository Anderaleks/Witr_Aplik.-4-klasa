let audioCtx = new (window.AudioContext || window.webkitAudioContext)();

function playSound() {
    let freq = document.getElementById("freq").value;

    let osc = audioCtx.createOscillator();
    osc.type = "sine";
    osc.frequency.value = freq;

    osc.connect(audioCtx.destination);
    osc.start();
    osc.stop(audioCtx.currentTime + 1);

    drawWave(freq);
}

function drawWave(freq) {
    let canvas = document.getElementById("wave");
    let ctx = canvas.getContext("2d");

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.beginPath();

    for (let x = 0; x < canvas.width; x++) {
        let y = canvas.height / 2 + Math.sin(x * freq * 0.01) * 60;
        ctx.lineTo(x, y);
    }

    ctx.strokeStyle = "#e2dfed";
    ctx.lineWidth = 2;
    ctx.stroke();
}

function playOff() {
    playFreq(430);
}

function playOn() {
    playFreq(440);
}

function playFreq(f) {
    let osc = audioCtx.createOscillator();
    osc.type = "sine";
    osc.frequency.value = f;
    osc.connect(audioCtx.destination);
    osc.start();
    osc.stop(audioCtx.currentTime + 1);
}