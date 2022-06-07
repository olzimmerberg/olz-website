/* BILD FUNKTIONEN */
import {obfuscateForUpload} from '../../src/Utils/uploadUtils';

interface OlzImageEdit {
    count?: number;
    uploadqueue: [string, HTMLImageElement, boolean][];
    rotations: {[id: number]: number};
    dragindex: number;
    draggalery: number;
    dragelem?: HTMLElement;
}

const olz_images_edit: {[ident: string]: OlzImageEdit} = {};

export function olz_images_edit_rotatepreview(_index: number): void {
}

export function olz_images_edit_redraw(
    ident: string,
    dbtable: string,
    id: number,
    count: number,
): void {
    if (!olz_images_edit[ident]) {
        olz_images_edit[ident] = {
            'uploadqueue': [],
            'rotations': {},
            'dragindex': -1,
            'draggalery': -1,
            'dragelem': undefined,
        };
    }
    if (count) {
        olz_images_edit[ident].count = count;
    }
    if (!('count' in olz_images_edit[ident])) {
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.open('GET', `image_tools.php?request=info&db_table=${dbtable}&id=${id}`, false);
        xmlhttp.send();
        const info = JSON.parse(xmlhttp.responseText);
        olz_images_edit[ident].count = info.count;
    }
    window.addEventListener('dragover', (e) => {
        e.preventDefault();
    }, false);
    window.addEventListener('drop', (e) => {
        e.preventDefault();
    }, true);
    window.addEventListener('mousemove', (e) => {
        const delem = olz_images_edit[ident].dragelem;
        if (delem) {
            delem.style.left = `${e.clientX - 32}px`;
            delem.style.top = `${e.clientY + window.pageYOffset + 5}px`;
        }
    }, true);
    window.addEventListener('mouseup', (_e) => {
        const delem = olz_images_edit[ident].dragelem;
        if (delem) {
            try {
                document.getElementsByTagName('body')[0].removeChild(delem);
            } catch (err: unknown) {
                // ignore
            }
        }
        window.setTimeout(() => {
            olz_images_edit[ident].dragelem = undefined;
            olz_images_edit[ident].dragindex = -1;
            olz_images_edit[ident].draggalery = -1;
        }, 100);
    }, true);

    const cnt = olz_images_edit[ident].count;
    const elem = document.getElementById(ident);
    let htmlout = '';
    let i: number;
    for (i = 0; i < cnt; i++) {
        htmlout += `<table style='display:inline-table; width:auto; margin:0px;' cellspacing='0' id='${ident}-droptable-${i + 1}'><tr><td style='padding:5px; border:0px;'><div style='width:1px; height:134px;' id='${ident}-borderdiv-${i + 1}'></div></td></tr></table>`;
        htmlout += `<table style='display:inline-table; width:auto; margin:0px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><img src='image_tools.php?request=thumb&db_table=${dbtable}&id=${id}&index=${i + 1}&dim=110&reload=${new Date().getTime()}' style='margin:0px; border:0px;' id='${ident}-img-${i + 1}'></td></tr><tr><td style='height:24px; padding:0px; border:0px; text-align:center;'><span id='${ident}-actions-${i + 1}'><img src='icns/rot90_16.svg' alt='' title='90° im Uhrzeigersinn rotieren' style='border:0px;' id='${ident}-rotate-${i + 1}'> <img src='icns/delete_16.svg' alt='' title='löschen' style='border:0px;' id='${ident}-delete-${i + 1}'></span> &nbsp; <span style='visibility:hidden;' id='${ident}-confirm-${i + 1}'><img src='icns/save_16.svg' alt='' title='sichern' style='border:0px;' id='${ident}-submit-${i + 1}'> <img src='icns/cancel_16.svg' alt='' title='abbrechen' style='border:0px;' id='${ident}-reset-${i + 1}'></span></td></tr></table>`;
    }
    htmlout += `<table style='display:inline-table; width:auto; margin:0px;' cellspacing='0' id='${ident}-droptable-${i + 1}'><tr><td style='padding:5px; border:0px;'><div style='width:1px; height:134px;' id='${ident}-borderdiv-${i + 1}'></div></td></tr></table>`;
    const uq = olz_images_edit[ident].uploadqueue;
    for (i = 0; i < uq.length; i++) {
        htmlout += `<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><canvas width='110' height='110' style='margin:0px;' id='${ident}-uqcanvas-${uq[i][0]}'></td></tr></table>`;
    }
    htmlout += `<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><div style='width:94px; height:94px; background-color:rgb(240,240,240); border:3px dashed rgb(180,180,180); border-radius:10px; padding:5px;' id='${ident}-dropzone'>Zus&auml;tzliche Bilder per Drag&Drop hierhin ziehen</div></td></tr><tr><td style='height:24px; border:0px;'><input type='file' multiple='multiple' style='width:110px; border:0px;' id='${ident}-fileselect'></td></tr></table>`;
    elem.innerHTML = htmlout;
    const drawcanvas = (uqident: string, img: HTMLImageElement, part: number) => {
        const cnv = document.getElementById(`${ident}-uqcanvas-${uqident}`) as HTMLCanvasElement;
        const ctx = cnv.getContext('2d');
        ctx.clearRect(0, 0, 110, 110);
        if (img) { ctx.drawImage(img, 0, 0, 110, 110); }
        ctx.fillStyle = 'rgba(0,0,0,0.2)';
        ctx.fillRect(0, 0, 110, 110);
        ctx.strokeStyle = 'rgba(255,255,255,0.5)';
        ctx.lineWidth = 5;
        ctx.beginPath();
        ctx.arc(55, 55, 45, 0, 2 * Math.PI, false);
        ctx.stroke();
        ctx.fillStyle = 'rgba(255,255,255,0.5)';
        let end = 1.5 + 2 * part;
        if (2 < end) { end -= 2; }
        ctx.beginPath();
        ctx.moveTo(55, 55);
        ctx.arc(55, 55, 45, 1.5 * Math.PI, end * Math.PI, false);
        ctx.fill();
    };
    for (i = 0; i < uq.length; i++) {
        drawcanvas(uq[i][0], uq[i][1], 0);
    }
    const dropzone = document.getElementById(`${ident}-dropzone`);
    const drophover = () => {
        dropzone.style.backgroundColor = 'rgb(220,220,220)';
        dropzone.style.borderColor = 'rgb(150,150,150)';
    };
    dropzone.ondragover = drophover;
    dropzone.onmouseover = () => {
        if (olz_images_edit[ident].draggalery !== -1) { drophover(); }
    };
    const dropleave = () => {
        dropzone.style.backgroundColor = 'rgb(240,240,240)';
        dropzone.style.borderColor = 'rgb(180,180,180)';
    };
    dropzone.ondragleave = dropleave;
    dropzone.onmouseout = () => {
        if (olz_images_edit[ident].draggalery !== -1) { dropleave(); }
    };
    function uploadnextfile() {
        const uq_ = olz_images_edit[ident].uploadqueue;
        if (uq_.length <= 0) { return; } // Nix zu tun
        const arr = uq_[0];
        if (arr[2] === true) { return; } // Ein Bild wird gerade hochgeladen
        olz_images_edit[ident].uploadqueue[0][2] = true;
        const uqident = arr[0];
        const img = arr[1];
        const owid = img.width;
        const ohei = img.height;
        let wid = owid;
        let hei = ohei;
        const max = 800;
        if (ohei < owid) {
            if (max < owid) {
                wid = max;
                hei = wid * ohei / owid;
            }
        } else if (max < ohei) {
            hei = max;
            wid = hei * owid / ohei;
        }

        let base64 = '';
        if (owid <= max && ohei <= max) {
            base64 = img.src;
        } else {
            const canvas = document.createElement('canvas');
            canvas.width = wid;
            canvas.height = hei;

            // ### Browser-native scaling ###
            const max2scale = (srcImg: HTMLImageElement, dstImg: HTMLCanvasElement) => {
                let max2img: HTMLImageElement|HTMLCanvasElement = srcImg;
                if (dstImg.width * 2 < owid && dstImg.height * 2 < ohei) {
                    const bigcanvas = document.createElement('canvas');
                    bigcanvas.width = dstImg.width * 2;
                    bigcanvas.height = dstImg.height * 2;
                    const bigctx = bigcanvas.getContext('2d');
                    bigctx.drawImage(img, 0, 0, dstImg.width * 2, dstImg.height * 2);
                    max2img = bigcanvas;
                }
                const ctx = canvas.getContext('2d');
                ctx.drawImage(max2img, 0, 0, dstImg.width, dstImg.height);
            };
            max2scale(img, canvas);
            // #########

            try {
                base64 = canvas.toDataURL('image/jpeg');
            } catch (err: unknown) {
                base64 = canvas.toDataURL();
            }
        }

        const tryUpload = () => {
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.open('POST', `image_tools.php?request=uploadresized&db_table=${dbtable}&id=${id}`, true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xmlhttp.upload.onprogress = (m) => {
                drawcanvas(uqident, img, m.loaded * 0.9999 / m.total);
            };
            xmlhttp.onreadystatechange = () => {
                if (xmlhttp.readyState === 4) {
                    if (xmlhttp.status === 200) {
                        const resp = JSON.parse(xmlhttp.responseText);
                        const uq__ = olz_images_edit[ident].uploadqueue;
                        let pos = -1;
                        for (i = 0; i < uq__.length; i++) {
                            if (uq__[i][0] === uqident) {
                                pos = i;
                            }
                        }
                        if (-1 < pos) {
                            uq__.splice(pos, 1);
                        }
                        olz_images_edit[ident].uploadqueue = uq__;
                        if (resp[0] === 1) {
                            olz_images_edit[ident].count += 1;
                        }
                        if (uq__.length === 0) {
                            olz_images_edit_redraw(ident, dbtable, id, 0);
                        }
                        window.setTimeout(uploadnextfile, 0);
                    } else if (xmlhttp.status === 510) {
                        tryUpload();
                    }
                }
            };
            xmlhttp.send(`content=${obfuscateForUpload(base64)}`);
        };
        tryUpload();
    }
    const loadnextfile = (files: FileList, indArg: number) => {
        let ind = indArg;
        if (!ind) {
            const dropzone_ = document.getElementById(`${ident}-dropzone`);
            dropzone_.innerHTML = '<b>Bitte warten</b>, <br>Bilder werden gelesen und verkleinert...';
            dropzone_.ondrop = () => {};
            const fileselect = document.getElementById(`${ident}-fileselect`) as HTMLInputElement;
            fileselect.disabled = true;
            ind = 0;
        }
        const file = files[ind];
        const uqident = `id${new Date().getTime()}-${Math.random()}-${ind}`;
        const reader = new FileReader();
        reader.onload = (_m) => {
            const img = document.createElement('img');
            img.onerror = () => {
                if (
                    typeof reader.result === 'string'
                    && reader.result.match(/^data:image\/(jpg|jpeg|png)/i)
                ) {
                    // eslint-disable-next-line no-alert
                    alert(`"${files[ind].name}" ist ein beschädigtes Bild, bitte wähle ein korrektes Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
                } else {
                    // eslint-disable-next-line no-alert
                    alert(`"${files[ind].name}" ist kein Bild, bitte wähle ein Bild aus. \nEin Bild hat meist die Endung ".jpg", ".jpeg" oder ".png".`);
                }
                ind++;
                if (ind < files.length) {
                    window.setTimeout(() => {
                        loadnextfile(files, ind);
                    }, 0);
                } else {
                    olz_images_edit_redraw(ident, dbtable, id, 0);
                    window.setTimeout(uploadnextfile, 0);
                }
            };
            img.onload = () => {
                const uq_ = olz_images_edit[ident].uploadqueue;
                uq_.push([uqident, img, false]);
                olz_images_edit[ident].uploadqueue = uq_;
                ind++;
                if (ind < files.length) {
                    window.setTimeout(() => {
                        loadnextfile(files, ind);
                    }, 0);
                } else {
                    olz_images_edit_redraw(ident, dbtable, id, 0);
                    window.setTimeout(uploadnextfile, 0);
                }
            };
            if (typeof reader.result === 'string') {
                img.src = reader.result;
            }
        };
        reader.readAsDataURL(file);
    };
    dropzone.ondrop = (e) => {
        dropzone.style.backgroundColor = 'rgb(240,240,240)';
        dropzone.style.borderColor = 'rgb(180,180,180)';
        if (!e.dataTransfer) { return; }
        const files = e.dataTransfer.files;
        if (!files) { return; }
        window.setTimeout(() => {
            loadnextfile(files, 0);
        }, 0);
    };
    dropzone.onmouseup = (_e) => {
        dropzone.style.backgroundColor = 'rgb(240,240,240)';
        dropzone.style.borderColor = 'rgb(180,180,180)';
        if (olz_images_edit[ident].draggalery !== -1) {
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.open('POST', `image_tools.php?request=merge&db_table=${dbtable}`, true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xmlhttp.onreadystatechange = () => {
                if (xmlhttp.readyState === 4 && xmlhttp.status === 200) {
                    const resp = JSON.parse(xmlhttp.responseText);
                    if (resp[0] === 1) {
                        olz_images_edit[ident].count = resp[1];
                        olz_images_edit_redraw(ident, dbtable, id, 0);
                    }
                }
            };
            xmlhttp.send(`id=${id}&fromid=${olz_images_edit[ident].draggalery}`);
        }
    };
    const fileselect = document.getElementById(`${ident}-fileselect`) as HTMLInputElement;
    fileselect.onchange = (_e) => {
        const files = fileselect.files;
        if (!files) {
            return;
        }
        loadnextfile(files, 0);
    };
    for (i = 0; i < cnt + 1; i++) {
        const droptableelem = document.getElementById(`${ident}-droptable-${i + 1}`);
        droptableelem.onmouseover = (_e) => {
            if (olz_images_edit[ident].dragelem) { document.getElementById(`${ident}-borderdiv-${i + 1}`).style.backgroundColor = 'black'; }
        };
        droptableelem.onmouseout = (_e) => {
            document.getElementById(`${ident}-borderdiv-${i + 1}`).style.backgroundColor = '';
        };
        droptableelem.onmouseup = (_e) => {
            const from = olz_images_edit[ident].dragindex;
            if (from === -1) { return; }
            const to = i + 1;
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.open('POST', `image_tools.php?request=reorder&db_table=${dbtable}`, false);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xmlhttp.send(`id=${id}&from=${from}&to=${to}`);
            let resp: any[] = [0];
            try {
                // eslint-disable-next-line no-unused-vars
                resp = JSON.parse(xmlhttp.responseText) as any[];
            } catch (err: unknown) {
                // ignore
            }
            console.log(resp);
            for (let j = Math.min(from, to); j <= Math.max(from, to); j++) {
                const elem_ = document.getElementById(`${ident}-img-${j}`) as HTMLImageElement;
                if (elem_) {
                    elem_.style.visibility = 'visible';
                    elem_.style.transform = 'rotate(0deg)';
                    elem_.style.webkitTransform = 'rotate(0deg)';
                    elem_.src = '';
                    elem_.style.width = 'auto';
                    elem_.style.height = 'auto';
                    elem_.style.paddingTop = '0px';
                    elem_.style.paddingLeft = '0px';
                    elem_.src = `image_tools.php?request=thumb&db_table=${dbtable}&id=${id}&index=${j}&dim=110&reload=${new Date().getTime()}`;
                }
            }
        };
        if (cnt <= i) { break; }
        const imgelem = document.getElementById(`${ident}-img-${i + 1}`) as HTMLImageElement;
        imgelem.onmousedown = (e) => {
            e.preventDefault();
            olz_images_edit[ident].dragindex = i + 1;
            const delem = document.createElement('img');
            delem.style.pointerEvents = 'none';
            delem.style.position = 'absolute';
            delem.style.zIndex = '1003';
            delem.style.left = `${e.clientX - 32}px`;
            delem.style.top = `${e.clientY + window.pageYOffset + 5}px`;
            delem.style.width = '64px';
            delem.style.height = '64px';
            delem.src = imgelem.src;
            document.getElementsByTagName('body')[0].appendChild(delem);
            olz_images_edit[ident].dragelem = delem;
        };
        const fn0 = () => {
            const wid = imgelem.offsetWidth;
            const hei = imgelem.offsetHeight;
            if (hei < wid) {
                imgelem.style.width = '110px';
                imgelem.style.paddingTop = `${Math.round((1 - hei / wid) * 110 / 2)}px`;
            } else {
                imgelem.style.height = '110px';
                imgelem.style.paddingLeft = `${Math.round((1 - wid / hei) * 110 / 2)}px`;
            }
        };
        imgelem.onload = fn0;
        const actiondone = (i_: number) => {
            for (let j = 0; j < cnt; j++) {
                const celem = document.getElementById(`${ident}-confirm-${j + 1}`);
                const aelem = document.getElementById(`${ident}-actions-${j + 1}`);
                if (celem && aelem) {
                    if (j === i_) { celem.style.visibility = 'visible'; } else { aelem.style.visibility = 'hidden'; }
                }
            }
        };
        const confirmdone = (_i: number) => {
            for (let j = 0; j < cnt; j++) {
                const celem = document.getElementById(`${ident}-confirm-${j + 1}`);
                const aelem = document.getElementById(`${ident}-actions-${j + 1}`);
                if (celem && aelem) {
                    aelem.style.visibility = 'visible';
                    celem.style.visibility = 'hidden';
                }
            }
        };
        const rotelem = document.getElementById(`${ident}-rotate-${i + 1}`);
        const fn1 = ((i_: number) => {
            const elem_ = document.getElementById(`${ident}-img-${i_ + 1}`);
            if (elem_.style.transform === undefined && elem_.style.webkitTransform === undefined) {
                // eslint-disable-next-line no-alert
                alert('Diese Funktion wird von Ihrem Browser leider nicht unterstützt. Installieren Sie einen modernen Browser!');
            }
            const rt = olz_images_edit[ident].rotations[i_ + 1] ? olz_images_edit[ident].rotations[i_ + 1] : 0;
            elem_.style.transform = `rotate(${rt + 90}deg)`;
            elem_.style.webkitTransform = `rotate(${rt + 90}deg)`;
            olz_images_edit[ident].rotations[i_ + 1] = (rt + 90) % 360;
            actiondone(i_);
        }).bind(null, i);
        rotelem.onclick = fn1;
        const delelem = document.getElementById(`${ident}-delete-${i + 1}`);
        const fn2 = ((i_: number) => {
            document.getElementById(`${ident}-img-${i_ + 1}`).style.visibility = 'hidden';
            actiondone(i_);
        }).bind(null, i);
        delelem.onclick = fn2;
        const subelem = document.getElementById(`${ident}-submit-${i + 1}`);
        const fn3 = ((i_: number, dbtable_: string, id_: number) => {
            const delflag = (document.getElementById(`${ident}-img-${i_ + 1}`).style.visibility === 'hidden' ? 1 : 0);
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.open('POST', `image_tools.php?request=change&db_table=${dbtable_}`, false);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xmlhttp.send(`id=${id_}&index=${i_ + 1}&delete=${delflag}&rotate=${olz_images_edit[ident].rotations[i_ + 1]}`);
            let resp = [0];
            try {
                resp = JSON.parse(xmlhttp.responseText);
            } catch (err: unknown) {
                // ignore
            }
            if (resp[0] === 1) {
                olz_images_edit[ident].rotations[i_ + 1] = 0;
                if (resp[1] === 1) {
                    olz_images_edit[ident].count -= 1;
                    olz_images_edit_redraw(ident, dbtable_, id_, 0);
                } else {
                    const elem_ = document.getElementById(`${ident}-img-${i_ + 1}`) as HTMLImageElement;
                    elem_.style.visibility = 'visible';
                    elem_.style.transform = 'rotate(0deg)';
                    elem_.style.webkitTransform = 'rotate(0deg)';
                    elem_.src = '';
                    elem_.style.width = 'auto';
                    elem_.style.height = 'auto';
                    elem_.style.paddingTop = '0px';
                    elem_.style.paddingLeft = '0px';
                    elem_.src = `image_tools.php?request=thumb&db_table=${dbtable_}&id=${id_}&index=${i_ + 1}&dim=110&reload=${new Date().getTime()}`;
                }
            } else {
                // eslint-disable-next-line no-alert
                alert('Bild-Änderung fehlgeschlagen. Bitte informiere den Sysadmin.');
            }
            confirmdone(i_);
        }).bind(null, i, dbtable, id);
        subelem.onclick = fn3;
        const reselem = document.getElementById(`${ident}-reset-${i + 1}`);
        const fn = ((i_: number) => {
            const elem_ = document.getElementById(`${ident}-img-${i_ + 1}`);
            elem_.style.visibility = 'visible';
            elem_.style.transform = 'rotate(0deg)';
            elem_.style.webkitTransform = 'rotate(0deg)';
            olz_images_edit[ident].rotations[i_ + 1] = 0;
            confirmdone(i_);
        }).bind(null, i);
        reselem.onclick = fn;
    }
}
