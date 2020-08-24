/* BILD FUNKTIONEN */
var olz_files_edit = {};

function olz_files_edit_rotatepreview(index) {
}
function olz_files_edit_redraw(ident, dbtable, id, count) {
    if (!olz_files_edit[ident]) olz_files_edit[ident] = {"uploadqueue":[]};
    if (count) olz_files_edit[ident]["count"] = count;
    if (!("count" in olz_files_edit[ident])) {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.open("GET", "file_tools.php?request=info&db_table="+dbtable+"&id="+id, false);
        xmlhttp.send();
        var info = JSON.parse(xmlhttp.responseText);
        olz_files_edit[ident]["count"] = info["count"];
    }
    window.addEventListener("dragover", function(e) {
        e = e || event;
        e.preventDefault();
    },false);
    window.addEventListener("drop", function(e) {
        e = e || event;
        e.preventDefault();
    },true);

    var cnt = olz_files_edit[ident]["count"];
    var elem = document.getElementById(ident);
    var htmlout = "";
    for (var i=0; i<cnt; i++) {
        htmlout += "<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><img src='file_tools.php?request=thumb&db_table="+dbtable+"&id="+id+"&index="+(i+1)+"&dim=110' style='margin:0px; border:0px;' id='"+ident+"-img-"+(i+1)+"'></td></tr><tr><td style='height:24px; border:0px; text-align:center;'><span id='"+ident+"-actions-"+(i+1)+"'><img src='icns/delete_16.svg' alt='' title='löschen' style='border:0px;' id='"+ident+"-delete-"+(i+1)+"'></span> &nbsp; <span style='visibility:hidden;' id='"+ident+"-confirm-"+(i+1)+"'><img src='icns/save_16.svg' alt='' title='sichern' style='border:0px;' id='"+ident+"-submit-"+(i+1)+"'> <img src='icns/cancel_16.svg' alt='' title='abbrechen' style='border:0px;' id='"+ident+"-reset-"+(i+1)+"'></span></td></tr></table>";
    }
    var uq = olz_files_edit[ident]["uploadqueue"];
    for (var i=0; i<uq.length; i++) {
        htmlout += "<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><canvas width='110' height='110' style='margin:0px;' id='"+ident+"-uqcanvas-"+uq[i]+"'></td></tr></table>";
    }
    htmlout += "<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><div style='width:94px; height:94px; background-color:rgb(240,240,240); border:3px dashed rgb(180,180,180); border-radius:10px; padding:5px;' id='"+ident+"-dropzone'>Zus&auml;tzliche Dateien per Drag&Drop hierhin ziehen</div></td></tr><tr><td style='height:24px; border:0px;'><input type='file' multiple='multiple' style='width:110px; border:0px;' id='"+ident+"-fileselect'></td></tr></table>";
    elem.innerHTML = htmlout;
    var dropzone = document.getElementById(ident+"-dropzone");
    dropzone.ondragover = (function (dropzone) {
        return function (e) {
            dropzone.style.backgroundColor = "rgb(220,220,220)";
            dropzone.style.borderColor = "rgb(150,150,150)";
        };
    })(dropzone);
    dropzone.ondragleave = (function (dropzone) {
        return function (e) {
            dropzone.style.backgroundColor = "rgb(240,240,240)";
            dropzone.style.borderColor = "rgb(180,180,180)";
        };
    })(dropzone);
    var uploadfiles = function (files) {
        var uq = olz_files_edit[ident]["uploadqueue"];
        var drawcanvas = function (uqident, img, part) {
            var cnv = document.getElementById(ident+"-uqcanvas-"+uqident);
            var ctx = cnv.getContext("2d");
            ctx.clearRect(0, 0, 110, 110);
            if (img) ctx.drawImage(img, 0, 0, 110, 110);
            ctx.fillStyle = "rgba(0,0,0,0.2)";
            ctx.fillRect(0, 0, 110, 110);
            ctx.strokeStyle = "rgb(255,255,255)";
            ctx.lineWidth = 5;
            ctx.beginPath();
            ctx.arc(55, 55, 45, 0, 2*Math.PI, false);
            ctx.stroke();
            ctx.fillStyle = "rgb(255,255,255)";
            var end = 1.5+2*part;
            if (2<end) end -=2;
            ctx.beginPath();
            ctx.moveTo(55, 55);
            ctx.arc(55, 55, 45, 1.5*Math.PI, end*Math.PI, false);
            ctx.fill();
        }
		max_size = 256;
        var uploadpart = function (file, base64, part) {
            var last = (base64.length<=(part+1)*max_size*max_size?"1":"0");
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.open("POST", "file_tools.php?request=uploadpart&db_table="+dbtable+"&id="+id, true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.upload.onprogress = (function (base64, uqident) { return function (m) {
                drawcanvas(uqident, false, (part*max_size*max_size+m.loaded)*1/base64.length);
            }; })(base64, uqident);
            xmlhttp.onreadystatechange = (function (file, xmlhttp, base64, part, last) { return function () {
                if (xmlhttp.readyState==4) {
                    if (xmlhttp.status==200) {
                        var resp = JSON.parse(xmlhttp.responseText);
                        if (resp[0]!=1) alert("Fehler beim hochladen von Teil "+part+(last=="1"?" (letzter)":" (nicht letzter)")+"\nresp[0]!=1\n"+JSON.stringify(resp));
                        if (last=="1") {
                            var uq = olz_files_edit[ident]["uploadqueue"];
                            var pos = uq.indexOf(uqident);
                            if (-1<pos) uq.splice(pos, 1);
                            olz_files_edit[ident]["uploadqueue"] = uq;
                            if (resp[0]==1) olz_files_edit[ident]["count"] += 1;
                            olz_files_edit_redraw(ident, dbtable, id);
                        } else {
                            if (resp[1]!="continue") alert("Fehler beim hochladen von Teil "+part+(last=="1"?" (letzter)":" (nicht letzter)")+"\nresp[1]!=\"continue\"\n"+JSON.stringify(resp));
                            uploadpart(file, base64, part+1);
                        }
                    } else if (xmlhttp.status==510) { // Considered spam => just try again with different obfuscation
                        uploadpart(file, base64, part);
                    }
                }
            }; })(file, xmlhttp, base64, part, last);
            var partStr = base64.substr(part*max_size*max_size, max_size*max_size);
            xmlhttp.send("content="+obfuscaseForUpload(partStr)+"&part="+part+"&last="+last+"&filename="+file.name);
        };
        for (var i=0; i<files.length; i++) {
            var uqident = "id"+(new Date().getTime())+" - "+Math.random()+" - "+i;
            uq.push(uqident);
            var reader = new FileReader();
            reader.onprogress = (function (reader, uqident) { return function (m) {
                drawcanvas(uqident, false, m.loaded*0.25/m.total);
            }; })(reader, uqident);
            reader.onload = (function (file, reader, uqident) { return function (m) {
                var base64 = reader.result;
                //drawcanvas(uqident, false, 0.5);
                uploadpart(file, base64, 0);
            }; })(files[i], reader, uqident);
            reader.readAsDataURL(files[i]);
        }
        olz_files_edit[ident]["uploadqueue"] = uq;
        olz_files_edit_redraw(ident, dbtable, id);
    };
    dropzone.ondrop = (function (dropzone) {return function (e) {
        dropzone.style.backgroundColor = "rgb(240,240,240)";
        dropzone.style.borderColor = "rgb(180,180,180)";
        if (!e.dataTransfer) return;
        var files = e.dataTransfer.files;
        if (!files) return;
        uploadfiles(files);
    }; })(dropzone);
    var fileselect = document.getElementById(ident+"-fileselect");
    fileselect.onchange = (function (fileselect) {return function (e) {
        var files = fileselect.files;
        if (!files) return;
        uploadfiles(files);
    }; })(fileselect);
    for (var i=0; i<cnt; i++) {
        var imgelem = document.getElementById(ident+"-img-"+(i+1));
        var fn = (function (imgelem) { return function () {
            var wid = imgelem.offsetWidth;
            var hei = imgelem.offsetHeight;
            if (hei<wid) {
                imgelem.style.width = "110px";
                imgelem.style.paddingTop = Math.round((1-hei/wid)*110/2)+"px";
            } else {
                imgelem.style.height = "110px";
                imgelem.style.paddingLeft = Math.round((1-wid/hei)*110/2)+"px";
            }
        }; })(imgelem);
        imgelem.onload = fn;
        var actiondone = function (i) {
            for (var j=0; j<cnt; j++) {
                var celem = document.getElementById(ident+"-confirm-"+(j+1));
                var aelem = document.getElementById(ident+"-actions-"+(j+1));
                if (celem && aelem) {
                    if (j==i) celem.style.visibility = "visible";
                    else aelem.style.visibility = "hidden";
                }
            }
        }
        var confirmdone = function (i) {
            for (var j=0; j<cnt; j++) {
                var celem = document.getElementById(ident+"-confirm-"+(j+1));
                var aelem = document.getElementById(ident+"-actions-"+(j+1));
                if (celem && aelem) {
                    aelem.style.visibility = "visible";
                    celem.style.visibility = "hidden";
                }
            }
        }
        var delelem = document.getElementById(ident+"-delete-"+(i+1));
        var fn = (function (i) { return function () {
            document.getElementById(ident+"-img-"+(i+1)).style.visibility = "hidden";
            actiondone(i);
        }; })(i);
        delelem.onclick = fn;
        var subelem = document.getElementById(ident+"-submit-"+(i+1));
        var fn = (function (i) { return function () {
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.open("POST", "file_tools.php?request=change&db_table="+dbtable, false);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("id="+id+"&index="+(i+1)+"&delete="+(document.getElementById(ident+"-img-"+(i+1)).style.visibility=="hidden"?1:0));
            var resp = [0];
            try {
                resp = JSON.parse(xmlhttp.responseText);
            } catch (err) {}
            if (resp[0]==1) {
                if (resp[1]==1) olz_files_edit[ident]["count"] -= 1;
                olz_files_edit_redraw(ident, dbtable, id);
                for (var j=i+1; (resp[1]==1?j<1000:j==i+1); j++) {
                    var elem = document.getElementById(ident+"-img-"+j);
                    if (!elem) break;
                    elem.style.visibility = "visible";
                    elem.src = "file_tools.php?request=thumb&db_table="+dbtable+"&id="+id+"&index="+j+"&dim=110?reload="+Math.random();
                }
            } else {
                alert("Datei-Änderung fehlgeschlagen. Bitte informiere den Webmaster.");
            }
            confirmdone(i);
        }; })(i);
        subelem.onclick = fn;
        var reselem = document.getElementById(ident+"-reset-"+(i+1));
        var fn = (function (i) { return function () {
            var elem = document.getElementById(ident+"-img-"+(i+1));
            elem.style.visibility = "visible";
            confirmdone(i);
        }; })(i);
        reselem.onclick = fn;
    }
}
