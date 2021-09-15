/* BILD FUNKTIONEN */
import {obfuscateForUpload} from '../utils/generalUtils';

interface OlzFileEdit {
    count?: number;
    uploadqueue: string[];
}

const olz_files_edit: {[ident: string]: OlzFileEdit} = {};

export function olz_files_edit_rotatepreview(_index: number): void {
}

export function olz_files_edit_redraw(
    ident: string,
    dbtable: string,
    id: number,
    count: number,
): void {
    if (!olz_files_edit[ident]) {
        olz_files_edit[ident] = {'uploadqueue': []};
    }
    if (count) {
        olz_files_edit[ident].count = count;
    }
    if (!('count' in olz_files_edit[ident])) {
        const xmlhttp = new XMLHttpRequest();
        xmlhttp.open('GET', `file_tools.php?request=info&db_table=${dbtable}&id=${id}`, false);
        xmlhttp.send();
        const info = JSON.parse(xmlhttp.responseText);
        olz_files_edit[ident].count = info.count;
    }
    window.addEventListener('dragover', (e) => {
        e.preventDefault();
    }, false);
    window.addEventListener('drop', (e) => {
        e.preventDefault();
    }, true);

    const cnt = olz_files_edit[ident].count;
    const elem = document.getElementById(ident);
    let htmlout = '';
    for (let i = 0; i < cnt; i++) {
        htmlout += `<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><img src='file_tools.php?request=thumb&db_table=${dbtable}&id=${id}&index=${i + 1}&dim=110' style='margin:0px; border:0px;' id='${ident}-img-${i + 1}'></td></tr><tr><td style='height:24px; border:0px; text-align:center;'><span id='${ident}-actions-${i + 1}'><img src='icns/delete_16.svg' alt='' title='löschen' style='border:0px;' id='${ident}-delete-${i + 1}'></span> &nbsp; <span style='visibility:hidden;' id='${ident}-confirm-${i + 1}'><img src='icns/save_16.svg' alt='' title='sichern' style='border:0px;' id='${ident}-submit-${i + 1}'> <img src='icns/cancel_16.svg' alt='' title='abbrechen' style='border:0px;' id='${ident}-reset-${i + 1}'></span></td></tr></table>`;
    }
    const uq = olz_files_edit[ident].uploadqueue;
    for (let i = 0; i < uq.length; i++) {
        htmlout += `<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><canvas width='110' height='110' style='margin:0px;' id='${ident}-uqcanvas-${uq[i]}'></td></tr></table>`;
    }
    htmlout += `<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><div style='width:94px; height:94px; background-color:rgb(240,240,240); border:3px dashed rgb(180,180,180); border-radius:10px; padding:5px;' id='${ident}-dropzone'>Zus&auml;tzliche Dateien per Drag&Drop hierhin ziehen</div></td></tr><tr><td style='height:24px; border:0px;'><input type='file' multiple='multiple' style='width:110px; border:0px;' id='${ident}-fileselect'></td></tr></table>`;
    elem.innerHTML = htmlout;
    const dropzone = document.getElementById(`${ident}-dropzone`);
    dropzone.ondragover = (_e) => {
        dropzone.style.backgroundColor = 'rgb(220,220,220)';
        dropzone.style.borderColor = 'rgb(150,150,150)';
    };
    dropzone.ondragleave = (_e) => {
        dropzone.style.backgroundColor = 'rgb(240,240,240)';
        dropzone.style.borderColor = 'rgb(180,180,180)';
    };
    const uploadfiles = (files: FileList) => {
        const uq_ = olz_files_edit[ident].uploadqueue;
        const drawcanvas = (uqident: string, img: HTMLImageElement|undefined, part: number) => {
            const cnv = document.getElementById(`${ident}-uqcanvas-${uqident}`) as HTMLCanvasElement;
            const ctx = cnv.getContext('2d');
            ctx.clearRect(0, 0, 110, 110);
            if (img) {
                ctx.drawImage(img, 0, 0, 110, 110);
            }
            ctx.fillStyle = 'rgba(0,0,0,0.2)';
            ctx.fillRect(0, 0, 110, 110);
            ctx.strokeStyle = 'rgb(255,255,255)';
            ctx.lineWidth = 5;
            ctx.beginPath();
            ctx.arc(55, 55, 45, 0, 2 * Math.PI, false);
            ctx.stroke();
            ctx.fillStyle = 'rgb(255,255,255)';
            let end = 1.5 + 2 * part;
            if (2 < end) { end -= 2; }
            ctx.beginPath();
            ctx.moveTo(55, 55);
            ctx.arc(55, 55, 45, 1.5 * Math.PI, end * Math.PI, false);
            ctx.fill();
        };
        const max_size = 256;
        const uploadpart = (uqident: string, file: File, base64: string, part: number) => {
            const last = (base64.length <= (part + 1) * max_size * max_size ? '1' : '0');
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.open('POST', `file_tools.php?request=uploadpart&db_table=${dbtable}&id=${id}`, true);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xmlhttp.upload.onprogress = (m) => {
                drawcanvas(uqident, undefined, (part * max_size * max_size + m.loaded) * 1 / base64.length);
            };
            xmlhttp.onreadystatechange = () => {
                if (xmlhttp.readyState === 4) {
                    if (xmlhttp.status === 200) {
                        const resp = JSON.parse(xmlhttp.responseText);
                        if (resp[0] !== 1) {
                            // eslint-disable-next-line no-alert
                            alert(`Fehler beim hochladen von Teil ${part}${last === '1' ? ' (letzter)' : ' (nicht letzter)'}\nresp[0]!=1\n${JSON.stringify(resp)}`);
                        }
                        if (last === '1') {
                            const uq__ = olz_files_edit[ident].uploadqueue;
                            const pos = uq__.indexOf(uqident);
                            if (-1 < pos) { uq__.splice(pos, 1); }
                            olz_files_edit[ident].uploadqueue = uq__;
                            if (resp[0] === 1) { olz_files_edit[ident].count += 1; }
                            olz_files_edit_redraw(ident, dbtable, id, 0);
                        } else {
                            if (resp[1] !== 'continue') {
                                // eslint-disable-next-line no-alert
                                alert(`Fehler beim hochladen von Teil ${part} (nicht letzter)\nresp[1]!="continue"\n${JSON.stringify(resp)}`);
                            }
                            uploadpart(uqident, file, base64, part + 1);
                        }
                    } else if (xmlhttp.status === 510) { // Considered spam => just try again with different obfuscation
                        uploadpart(uqident, file, base64, part);
                    }
                }
            };
            const partStr = base64.substr(part * max_size * max_size, max_size * max_size);
            xmlhttp.send(`content=${obfuscateForUpload(partStr)}&part=${part}&last=${last}&filename=${file.name}`);
        };
        for (let i = 0; i < files.length; i++) {
            const uqident = `id${new Date().getTime()} - ${Math.random()} - ${i}`;
            uq_.push(uqident);
            const reader = new FileReader();
            reader.onprogress = ((uqident_: string, m: ProgressEvent) => {
                drawcanvas(uqident_, undefined, m.loaded * 0.25 / m.total);
            }).bind(null, uqident);
            reader.onload = ((uqident_: string, file: File, reader_: FileReader) => {
                const base64 = reader_.result as string;
                // drawcanvas(uqident, false, 0.5);
                uploadpart(uqident_, file, base64, 0);
            }).bind(null, uqident, files[i], reader);
            reader.readAsDataURL(files[i]);
        }
        olz_files_edit[ident].uploadqueue = uq_;
        olz_files_edit_redraw(ident, dbtable, id, 0);
    };
    dropzone.ondrop = (e) => {
        dropzone.style.backgroundColor = 'rgb(240,240,240)';
        dropzone.style.borderColor = 'rgb(180,180,180)';
        if (!e.dataTransfer) { return; }
        const files = e.dataTransfer.files;
        if (!files) { return; }
        uploadfiles(files);
    };
    const fileselect = document.getElementById(`${ident}-fileselect`) as HTMLInputElement;
    fileselect.onchange = (_e) => {
        const files = fileselect.files;
        if (!files) { return; }
        uploadfiles(files);
    };
    for (let i = 0; i < cnt; i++) {
        const imgelem = document.getElementById(`${ident}-img-${i + 1}`);
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
        const delelem = document.getElementById(`${ident}-delete-${i + 1}`);
        const fn1 = () => {
            document.getElementById(`${ident}-img-${i + 1}`).style.visibility = 'hidden';
            actiondone(i);
        };
        delelem.onclick = fn1;
        const subelem = document.getElementById(`${ident}-submit-${i + 1}`);
        const fn2 = () => {
            const xmlhttp = new XMLHttpRequest();
            xmlhttp.open('POST', `file_tools.php?request=change&db_table=${dbtable}`, false);
            xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xmlhttp.send(`id=${id}&index=${i + 1}&delete=${document.getElementById(`${ident}-img-${i + 1}`).style.visibility === 'hidden' ? 1 : 0}`);
            let resp = [0];
            try {
                resp = JSON.parse(xmlhttp.responseText);
            } catch (err: unknown) {
                // ignore
            }
            if (resp[0] === 1) {
                if (resp[1] === 1) { olz_files_edit[ident].count -= 1; }
                olz_files_edit_redraw(ident, dbtable, id, 0);
                for (let j = i + 1; (resp[1] === 1 ? j < 1000 : j === i + 1); j++) {
                    const elem_ = document.getElementById(`${ident}-img-${j}`) as HTMLImageElement;
                    if (!elem_) { break; }
                    elem_.style.visibility = 'visible';
                    elem_.src = `file_tools.php?request=thumb&db_table=${dbtable}&id=${id}&index=${j}&dim=110?reload=${Math.random()}`;
                }
            } else {
                // eslint-disable-next-line no-alert
                alert('Datei-Änderung fehlgeschlagen. Bitte informiere den Webmaster.');
            }
            confirmdone(i);
        };
        subelem.onclick = fn2;
        const reselem = document.getElementById(`${ident}-reset-${i + 1}`);
        const fn = () => {
            const elem_ = document.getElementById(`${ident}-img-${i + 1}`);
            elem_.style.visibility = 'visible';
            confirmdone(i);
        };
        reselem.onclick = fn;
    }
}
