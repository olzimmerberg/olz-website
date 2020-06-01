/* BILD FUNKTIONEN */
var olz_images_edit = {};

function olz_images_edit_rotatepreview(index) {
}
function olz_images_edit_redraw(ident, dbtable, id, count) {
    if (!olz_images_edit[ident]) olz_images_edit[ident] = {"uploadqueue":[], "rotations":{}, "dragindex":-1, "draggalery":-1, "dragelem":false};
    if (count) olz_images_edit[ident]["count"] = count;
    if (!("count" in olz_images_edit[ident])) {
        var xmlhttp;
        if (window.XMLHttpRequest) {
            xmlhttp = new XMLHttpRequest();
        } else {
            xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xmlhttp.open("GET", "image_tools.php?request=info&db_table="+dbtable+"&id="+id, false);
        xmlhttp.send();
        var info = JSON.parse(xmlhttp.responseText);
        olz_images_edit[ident]["count"] = info["count"];
    }
    window.addEventListener("dragover", function(e) {
        e = e || event;
        e.preventDefault();
    },false);
    window.addEventListener("drop", function(e) {
        e = e || event;
        e.preventDefault();
    },true);
    window.addEventListener("mousemove", function(e) {
        e = e || event;
        var delem = olz_images_edit[ident]["dragelem"];
        if (delem) {
            delem.style.left = (e.clientX-32)+"px";
            delem.style.top = (e.clientY+window.pageYOffset+5)+"px";
        }
    },true);
    window.addEventListener("mouseup", function(e) {
        e = e || event;
        var delem = olz_images_edit[ident]["dragelem"];
        if (delem) {
            try {
                document.getElementsByTagName("body")[0].removeChild(delem);
            } catch (err) {}
        }
        window.setTimeout(function () {
            olz_images_edit[ident]["dragelem"] = false;
            olz_images_edit[ident]["dragindex"] = -1;
            olz_images_edit[ident]["draggalery"] = -1;
        }, 100);
    },true);

    var cnt = olz_images_edit[ident]["count"];
    var elem = document.getElementById(ident);
    var htmlout = "";
    for (var i=0; i<cnt; i++) {
        htmlout += "<table style='display:inline-table; width:auto; margin:0px;' cellspacing='0' id='"+ident+"-droptable-"+(i+1)+"'><tr><td style='padding:5px; border:0px;'><div style='width:1px; height:134px;' id='"+ident+"-borderdiv-"+(i+1)+"'></div></td></tr></table>";
        htmlout += "<table style='display:inline-table; width:auto; margin:0px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><img src='image_tools.php?request=thumb&db_table="+dbtable+"&id="+id+"&index="+(i+1)+"&dim=110&reload="+(new Date().getTime())+"' style='margin:0px; border:0px;' id='"+ident+"-img-"+(i+1)+"'></td></tr><tr><td style='height:24px; padding:0px; border:0px; text-align:center;'><span id='"+ident+"-actions-"+(i+1)+"'><img src='icns/rot90_16.svg' alt='' title='90° im Uhrzeigersinn rotieren' style='border:0px;' id='"+ident+"-rotate-"+(i+1)+"'> <img src='icns/delete_16.svg' alt='' title='löschen' style='border:0px;' id='"+ident+"-delete-"+(i+1)+"'></span> &nbsp; <span style='visibility:hidden;' id='"+ident+"-confirm-"+(i+1)+"'><img src='icns/save_16.svg' alt='' title='sichern' style='border:0px;' id='"+ident+"-submit-"+(i+1)+"'> <img src='icns/cancel_16.svg' alt='' title='abbrechen' style='border:0px;' id='"+ident+"-reset-"+(i+1)+"'></span></td></tr></table>";
    }
    htmlout += "<table style='display:inline-table; width:auto; margin:0px;' cellspacing='0' id='"+ident+"-droptable-"+(i+1)+"'><tr><td style='padding:5px; border:0px;'><div style='width:1px; height:134px;' id='"+ident+"-borderdiv-"+(i+1)+"'></div></td></tr></table>";
    var uq = olz_images_edit[ident]["uploadqueue"];
    for (var i=0; i<uq.length; i++) {
        htmlout += "<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><canvas width='110' height='110' style='margin:0px;' id='"+ident+"-uqcanvas-"+uq[i][0]+"'></td></tr></table>";
    }
    htmlout += "<table style='display:inline-table; width:auto; margin:3px;' cellspacing='0'><tr><td style='width:110px; height:110px; padding:0px; border:0px;'><div style='width:94px; height:94px; background-color:rgb(240,240,240); border:3px dashed rgb(180,180,180); border-radius:10px; padding:5px;' id='"+ident+"-dropzone'>Zus&auml;tzliche Bilder per Drag&Drop hierhin ziehen</div></td></tr><tr><td style='height:24px; border:0px;'><input type='file' multiple='multiple' style='width:110px; border:0px;' id='"+ident+"-fileselect'></td></tr></table>";
    elem.innerHTML = htmlout;
    var drawcanvas = function (uqident, img, part) {
        var cnv = document.getElementById(ident+"-uqcanvas-"+uqident);
        var ctx = cnv.getContext("2d");
        ctx.clearRect(0, 0, 110, 110);
        if (img) ctx.drawImage(img, 0, 0, 110, 110);
        ctx.fillStyle = "rgba(0,0,0,0.2)";
        ctx.fillRect(0, 0, 110, 110);
        ctx.strokeStyle = "rgba(255,255,255,0.5)";
        ctx.lineWidth = 5;
        ctx.beginPath();
        ctx.arc(55, 55, 45, 0, 2*Math.PI, false);
        ctx.stroke();
        ctx.fillStyle = "rgba(255,255,255,0.5)";
        var end = 1.5+2*part;
        if (2<end) end -=2;
        ctx.beginPath();
        ctx.moveTo(55, 55);
        ctx.arc(55, 55, 45, 1.5*Math.PI, end*Math.PI, false);
        ctx.fill();
    };
    for (var i=0; i<uq.length; i++) {
        drawcanvas(uq[i][0], uq[i][1], 0);
    }
    var dropzone = document.getElementById(ident+"-dropzone");
    var drophover = (function (dropzone) { return function (e) {
        dropzone.style.backgroundColor = "rgb(220,220,220)";
        dropzone.style.borderColor = "rgb(150,150,150)";
    }; })(dropzone);
    dropzone.ondragover = drophover;
    dropzone.onmouseover = (function (drophover) { return function (e) {
        if (olz_images_edit[ident]["draggalery"]!=-1) drophover(e);
    }; })(drophover);
    var dropleave = (function (dropzone) { return function (e) {
        dropzone.style.backgroundColor = "rgb(240,240,240)";
        dropzone.style.borderColor = "rgb(180,180,180)";
    }; })(dropzone);
    dropzone.ondragleave = dropleave;
    dropzone.onmouseout = (function (dropleave) { return function (e) {
        if (olz_images_edit[ident]["draggalery"]!=-1) dropleave(e);
    }; })(dropleave);
    var uploadnextfile = function () {
        var uq = olz_images_edit[ident]["uploadqueue"];
        if (uq.length<=0) return; // Nix zu tun
        var arr = uq[0];
        if (arr[2]==true) return; // Ein Bild wird gerade hochgeladen
        olz_images_edit[ident]["uploadqueue"][0][2] = true;
        var uqident = arr[0];
        var img = arr[1];
        var owid = img.width;
        var ohei = img.height;
        var wid = owid;
        var hei = ohei;
        var max = 800;
        if (ohei<owid) {
            if (max<owid) {
                wid = max;
                hei = wid*ohei/owid;
            }
        } else {
            if (max<ohei) {
                hei = max;
                wid = hei*owid/ohei;
            }
        }

        var base64 = false;
        if (owid <= max && ohei <= max) {
          base64 = img.src;
        } else {
          var canvas = document.createElement("canvas");
          canvas.width = wid;
          canvas.height = hei;

          // ### Browser-native scaling ###
          var max2scale = function (srcImg, dstImg) {
              var max2img = srcImg;
              if (dstImg.width*2<owid && dstImg.height*2<ohei) {
                  var bigcanvas = document.createElement("canvas");
                  bigcanvas.width = dstImg.width*2;
                  bigcanvas.height = dstImg.height*2;
                  var bigctx = bigcanvas.getContext("2d");
                  bigctx.drawImage(img, 0, 0, dstImg.width*2, dstImg.height*2);
                  max2img = bigcanvas;
              }
              var ctx = canvas.getContext("2d");
              ctx.drawImage(max2img, 0, 0, dstImg.width, dstImg.height);
          };
          max2scale(img, canvas);
          // #########

          try {
              base64 = canvas.toDataURL("image/jpeg");
          } catch(err) {
              base64 = canvas.toDataURL();
          }
        }

        function tryUpload() {
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.open("POST", "image_tools.php?request=uploadresized&db_table="+dbtable+"&id="+id, true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.upload.onprogress = (function (xmlhttp, uqident, img) { return function (m) {
                drawcanvas(uqident, img, m.loaded*0.9999/m.total);
            }; })(xmlhttp, uqident, img);
            xmlhttp.onreadystatechange = (function (xmlhttp, uqident) { return function () {
                if (xmlhttp.readyState==4) {
                    if (xmlhttp.status==200) {
                        var resp = JSON.parse(xmlhttp.responseText);
                        var uq = olz_images_edit[ident]["uploadqueue"];
                        var pos = -1;
                        for (var i=0; i<uq.length; i++) {
                            if (uq[i][0]==uqident) pos = i;
                        }
                        if (-1<pos) uq.splice(pos, 1);
                        olz_images_edit[ident]["uploadqueue"] = uq;
                        if (resp[0]==1) olz_images_edit[ident]["count"] += 1;
                        if (uq.length==0) olz_images_edit_redraw(ident, dbtable, id);
                        window.setTimeout(uploadnextfile, 0);
                    } else if (xmlhttp.status==510) {
                        tryUpload();
                    }
                }
            }; })(xmlhttp, uqident);
            xmlhttp.send("content="+obfuscaseForUpload(base64));
        }
        tryUpload();
    }
    var loadnextfile = function (files, ind) {
        if (!ind) {
            var dropzone = document.getElementById(ident+"-dropzone");
            dropzone.innerHTML = "<b>Bitte warten</b>, <br>Bilder werden gelesen und verkleinert...";
            dropzone.ondrop = function () {};
            var fileselect = document.getElementById(ident+"-fileselect");
            fileselect.disabled = "disabled";
            ind = 0;
        }
        var file = files[ind];
        var uqident = "id"+(new Date().getTime())+"-"+Math.random()+"-"+ind;
        var reader = new FileReader();
        reader.onload = (function (reader, uqident, ind) { return function (m) {
            var img = document.createElement("img");
            img.onerror = (function (img, res, ind) { return function () {
                if (res.match(/^data\:image\/(jpg|jpeg|png)/i)) {
                    alert("\""+files[ind].name+"\" ist ein beschädigtes Bild, bitte wähle ein korrektes Bild aus. \nEin Bild hat meist die Endung \".jpg\", \".jpeg\" oder \".png\".");
                } else {
                    alert("\""+files[ind].name+"\" ist kein Bild, bitte wähle ein Bild aus. \nEin Bild hat meist die Endung \".jpg\", \".jpeg\" oder \".png\".");
                }
                ind++;
                if (ind<files.length) {
                    window.setTimeout((function (files, ind) { return function () {loadnextfile(files, ind);}; })(files, ind), 0);
                } else {
                    olz_images_edit_redraw(ident, dbtable, id);
                    window.setTimeout(uploadnextfile, 0);
                }
            }; })(img, reader.result, ind);
            img.onload = (function (img, uqident, ind) { return function () {
                var uq = olz_images_edit[ident]["uploadqueue"];
                uq.push([uqident, img, false]);
                olz_images_edit[ident]["uploadqueue"] = uq;
                ind++;
                if (ind<files.length) {
                    window.setTimeout((function (files, ind) { return function () {loadnextfile(files, ind);}; })(files, ind), 0);
                } else {
                    olz_images_edit_redraw(ident, dbtable, id);
                    window.setTimeout(uploadnextfile, 0);
                }
            }; })(img, uqident, ind);
            img.src = reader.result;
        }; })(reader, uqident, ind);
        reader.readAsDataURL(file);
    };
    dropzone.ondrop = (function (dropzone) {return function (e) {
        dropzone.style.backgroundColor = "rgb(240,240,240)";
        dropzone.style.borderColor = "rgb(180,180,180)";
        if (!e.dataTransfer) return;
        var files = e.dataTransfer.files;
        if (!files) return;
        window.setTimeout((function (files) {return function () { loadnextfile(files); }; })(files), 0);
    }; })(dropzone);
    dropzone.onmouseup = (function (dropzone) {return function (e) {
        dropzone.style.backgroundColor = "rgb(240,240,240)";
        dropzone.style.borderColor = "rgb(180,180,180)";
        if (olz_images_edit[ident]["draggalery"]!=-1) {
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.open("POST", "image_tools.php?request=merge&db_table="+dbtable, true);
            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
            xmlhttp.onreadystatechange = (function (xmlhttp) { return function () {
                if (xmlhttp.readyState==4 && xmlhttp.status==200) {
                    var resp = JSON.parse(xmlhttp.responseText);
                    if (resp[0]==1) {
                        olz_images_edit[ident]["count"] = resp[1];
                        olz_images_edit_redraw(ident, dbtable, id);
                    }
                }
            }; })(xmlhttp);
            xmlhttp.send("id="+id+"&fromid="+olz_images_edit[ident]["draggalery"]);
        }
    }; })(dropzone);
    var fileselect = document.getElementById(ident+"-fileselect");
    fileselect.onchange = (function (fileselect) {return function (e) {
        var files = fileselect.files;
        if (!files) return;
        loadnextfile(files);
    }; })(fileselect);
    for (var i=0; i<cnt+1; i++) {
        var droptableelem = document.getElementById(ident+"-droptable-"+(i+1));
        droptableelem.onmouseover = (function (i) { return function (e) {
            if (olz_images_edit[ident]["dragelem"]) document.getElementById(ident+"-borderdiv-"+(i+1)).style.backgroundColor = "black";
        }; })(i);
        droptableelem.onmouseout = (function (i) { return function (e) {
            document.getElementById(ident+"-borderdiv-"+(i+1)).style.backgroundColor = "";
        }; })(i);
        droptableelem.onmouseup = (function (i) { return function (e) {
            var from = olz_images_edit[ident]["dragindex"];
            if (from==-1) return;
            var to = i+1
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.open("POST", "image_tools.php?request=reorder&db_table="+dbtable, false);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("id="+id+"&from="+from+"&to="+to);
            var resp = [0];
            try {
                resp = JSON.parse(xmlhttp.responseText);
            } catch (err) {}
            for (var j=Math.min(from, to); j<=Math.max(from, to); j++) {
                var elem = document.getElementById(ident+"-img-"+j);
                if (elem) {
                    elem.style.visibility = "visible";
                    elem.style.transform = "rotate(0deg)";
                    elem.style.webkitTransform = "rotate(0deg)";
                    elem.style.msTransform = "rotate(0deg)";
                    elem.src = "";
                    elem.style.width = "auto";
                    elem.style.height = "auto";
                    elem.style.paddingTop = "0px";
                    elem.style.paddingLeft = "0px";
                    elem.src = "image_tools.php?request=thumb&db_table="+dbtable+"&id="+id+"&index="+j+"&dim=110&reload="+(new Date().getTime());
                }
            }
        }; })(i);
        if (cnt<=i) break;
        var imgelem = document.getElementById(ident+"-img-"+(i+1));
        imgelem.onmousedown = (function (i, imgelem) { return function (e) {
            e = e || event;
            e.preventDefault();
            olz_images_edit[ident]["dragindex"] = i+1;
            var delem = document.createElement("img");
            delem.style.pointerEvents = "none";
            delem.style.position = "absolute";
            delem.style.zIndex = 1003;
            delem.style.left = (e.clientX-32)+"px";
            delem.style.top = (e.clientY+window.pageYOffset+5)+"px";
            delem.style.width = "64px";
            delem.style.height = "64px";
            delem.src = imgelem.src;
            document.getElementsByTagName("body")[0].appendChild(delem);
            olz_images_edit[ident]["dragelem"] = delem;
        }; })(i, imgelem);
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
        var rotelem = document.getElementById(ident+"-rotate-"+(i+1));
        var fn = (function (i) { return function () {
            var elem = document.getElementById(ident+"-img-"+(i+1));
            if (elem.style.transform==undefined && elem.style.webkitTransform==undefined && elem.style.msTransform==undefined) alert("Diese Funktion wird von Ihrem Browser leider nicht unterstützt. Installieren Sie einen modernen Browser!");
            var rt = olz_images_edit[ident]["rotations"][i+1]?olz_images_edit[ident]["rotations"][i+1]:0;
            elem.style.transform = "rotate("+(rt+90)+"deg)";
            elem.style.webkitTransform = "rotate("+(rt+90)+"deg)";
            elem.style.msTransform = "rotate("+(rt+90)+"deg)";
            olz_images_edit[ident]["rotations"][i+1] = (rt+90)%360;
            actiondone(i);
        }; })(i);
        rotelem.onclick = fn;
        var delelem = document.getElementById(ident+"-delete-"+(i+1));
        var fn = (function (i) { return function () {
            document.getElementById(ident+"-img-"+(i+1)).style.visibility = "hidden";
            actiondone(i);
        }; })(i);
        delelem.onclick = fn;
        var subelem = document.getElementById(ident+"-submit-"+(i+1));
        var fn = (function (i, dbtable, id) { return function () {
            var delflag = (document.getElementById(ident+"-img-"+(i+1)).style.visibility=="hidden"?1:0);
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.open("POST", "image_tools.php?request=change&db_table="+dbtable, false);
            xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xmlhttp.send("id="+id+"&index="+(i+1)+"&delete="+delflag+"&rotate="+olz_images_edit[ident]["rotations"][i+1]);
            var resp = [0];
            try {
                resp = JSON.parse(xmlhttp.responseText);
            } catch (err) {}
            if (resp[0]==1) {
                olz_images_edit[ident]["rotations"][i+1] = 0;
                if (resp[1]==1) {
                    olz_images_edit[ident]["count"] -= 1;
                    olz_images_edit_redraw(ident, dbtable, id);
                } else {
                    var elem = document.getElementById(ident+"-img-"+(i+1));
                    elem.style.visibility = "visible";
                    elem.style.transform = "rotate(0deg)";
                    elem.style.webkitTransform = "rotate(0deg)";
                    elem.style.msTransform = "rotate(0deg)";
                    elem.src = "";
                    elem.style.width = "auto";
                    elem.style.height = "auto";
                    elem.style.paddingTop = "0px";
                    elem.style.paddingLeft = "0px";
                    elem.src = "image_tools.php?request=thumb&db_table="+dbtable+"&id="+id+"&index="+(i+1)+"&dim=110&reload="+(new Date().getTime());
                }
            } else {
                alert("Bild-Änderung fehlgeschlagen. Bitte informiere den Webmaster.");
            }
            confirmdone(i);
        }; })(i, dbtable, id);
        subelem.onclick = fn;
        var reselem = document.getElementById(ident+"-reset-"+(i+1));
        var fn = (function (i) { return function () {
            var elem = document.getElementById(ident+"-img-"+(i+1));
            elem.style.visibility = "visible";
            elem.style.transform = "rotate(0deg)";
            elem.style.webkitTransform = "rotate(0deg)";
            elem.style.msTransform = "rotate(0deg)";
            olz_images_edit[ident]["rotations"][i+1] = 0;
            confirmdone(i);
        }; })(i);
        reselem.onclick = fn;
    }
}
