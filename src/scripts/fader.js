// main function to process the fade request //
export function colorFade(id, element, start, end, stepsArg, speedArg) {
    const target = document.getElementById(id);
    const steps = stepsArg || 20;
    const speed = speedArg || 20;
    clearInterval(target.timer);
    const endrgb = colorConv(end);
    const er = endrgb[0];
    const eg = endrgb[1];
    const eb = endrgb[2];
    if (!target.r) {
        const startrgb = colorConv(start);
        const r = startrgb[0];
        const g = startrgb[1];
        const b = startrgb[2];
        target.r = r;
        target.g = g;
        target.b = b;
    }
    let rint = Math.round(Math.abs(target.r - er) / steps);
    let gint = Math.round(Math.abs(target.g - eg) / steps);
    let bint = Math.round(Math.abs(target.b - eb) / steps);
    if (rint === 0) { rint = 1; }
    if (gint === 0) { gint = 1; }
    if (bint === 0) { bint = 1; }
    target.step = 1;
    target.timer = setInterval(() => { animateColor(id, element, steps, er, eg, eb, rint, gint, bint); }, speed);
}

// incrementally close the gap between the two colors //
function animateColor(id, element, steps, er, eg, eb, rint, gint, bint) {
    const target = document.getElementById(id);
    let color;
    if (target.step <= steps) {
        let r = target.r;
        let g = target.g;
        let b = target.b;
        if (r >= er) {
            r = r - rint;
        } else {
            r = parseInt(r, 10) + parseInt(rint, 10);
        }
        if (g >= eg) {
            g = g - gint;
        } else {
            g = parseInt(g, 10) + parseInt(gint, 10);
        }
        if (b >= eb) {
            b = b - bint;
        } else {
            b = parseInt(b, 10) + parseInt(bint, 10);
        }
        color = `rgb(${r},${g},${b})`;
        if (element === 'background') {
            target.style.backgroundColor = color;
        } else if (element === 'border') {
            target.style.borderColor = color;
        } else {
            target.style.color = color;
        }
        target.r = r;
        target.g = g;
        target.b = b;
        target.step = target.step + 1;
    } else {
        clearInterval(target.timer);
        color = `rgb(${er},${eg},${eb})`;
        if (element === 'background') {
            target.style.backgroundColor = color;
        } else if (element === 'border') {
            target.style.borderColor = color;
        } else {
            target.style.color = color;
        }
    }
}

// convert the color to rgb from hex //
function colorConv(color) {
    const rgb = [parseInt(color.substring(0, 2), 16),
        parseInt(color.substring(2, 4), 16),
        parseInt(color.substring(4, 6), 16)];
    return rgb;
}
