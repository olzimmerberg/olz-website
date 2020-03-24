
var filePath = undefined;
if (window.location.search) {
    const match = /\?file\=([^&#]+)/.exec(window.location.search);
    if (match) {
        const filename = match[1];
        filePath = `/results/${filename}`;
    }
}

function allInRightOrder(sample, correct) {
    var distList = [];
    for (var j=0; j<correct.length+1; j++) {
        distList.push(j);
    }
    var matrix = [];
    matrix.push(distList.map(function (_, ind) {
        return ind==0 ? 0 : 1;
    }));
    for (var i=1; i<sample.length+1; i++) {
        var lastDistList = distList;
        var matrixRow = [];
        for (var j=0; j<correct.length+1; j++) {
            var opt0 = (j==0 ? i : lastDistList[j-1] + (sample[i-1]==correct[j-1] ? 0 : 2));
            var opt1 = lastDistList[j];
            var opt2 = (j==0 ? i : distList[j-1] + 1);
            var min = opt0;
            var argmin = 0;
            if (opt1<min) {
                min = opt1;
                argmin = 1;
            }
            if (opt2<min) {
                min = opt2;
                argmin = 2;
            }
            distList[j] = min;
            matrixRow.push(argmin);
        }
        matrix.push(matrixRow);
    }
    var sampleMapping = [];
    for (var i=0; i<sample.length; i++) {
        sampleMapping.push(NaN);
    }
    var correctMapping = [];
    for (var j=0; j<correct.length; j++) {
        correctMapping.push(NaN);
    }
    var i = matrix.length-1;
    var j = matrix[0].length-1;
    while (i>0 && j>0) {
        sampleMapping[i-1] = j-1;
        correctMapping[j-1] = i-1;
        if (matrix[i][j]==0) {
            i--;
            j--;
        } else if (matrix[i][j]==1) {
            sampleMapping[i-1] = NaN;
            i--;
        } else if (matrix[i][j]==2) {
            correctMapping[j-1] = NaN;
            j--;
        }
    }
    return {
        distance:distList[distList.length-1],
        sampleMapping:sampleMapping,
        correctMapping:correctMapping,
    };
}

function formatTime(numSeconds) {
    var hours = Math.floor(numSeconds/3600);
    var mins = Math.floor((numSeconds/60)%60);
    var secs = Math.floor(numSeconds%60);
    var msecs = Math.floor((numSeconds*1000)%1000);
    return (0<hours ? hours+":" : "")+("00"+mins).slice(-2)+":"+("00"+secs).slice(-2)+(0<msecs ? "."+("000"+msecs).slice(-3) : "");
}
function hashPath() {
    return location.hash.substr(1).split("/");
}
function popHash() {
    location.hash = location.hash.substr(0, location.hash.lastIndexOf('/'));
}
function pushHash(newComponent) {
    location.hash += "/" + newComponent;
}
export function setHash(newComponent, ind) {
    location.hash = hashPath().slice(0, ind).join('/') + "/" + newComponent
}

var selectedIndexesByClass = {};
function showChart(classInd) {
    if (classInd<0) return;
    if (!(classInd in selectedIndexesByClass)) selectedIndexesByClass[classInd] = {};
    var classResults = xmlDoc.querySelectorAll('ResultList > ClassResult');
    var ranking = classResults[classInd].querySelectorAll('PersonResult');
    var correctControls = [];

    var cont = true;
    for (var i=0; i<ranking.length && cont; i++) {
        var splitTimes = ranking[i].querySelectorAll('Result > SplitTime');
        for (var j=0; j<splitTimes.length; j++) {
            var controlCode = splitTimes[j].querySelector('ControlCode');
            var splitStatus = splitTimes[j].getAttribute('status');
            if ((splitStatus==null || splitStatus=='OK') && controlCode!==null) {
                correctControls.push({'controlCode':controlCode.textContent, 'globalFirstThree':[], 'localFirstThree':[]});
            }
            cont = false;
        }
        if (!cont) correctControls.push({'controlCode':'F', 'globalFirstThree':[], 'localFirstThree':[]});
    }
    // Get times of all runners
    var times = [];
    for (var i=0; i<ranking.length; i++) {
        var splitTimes = ranking[i].querySelectorAll('Result > SplitTime');
        var ind = 0;
        var lastTime = 0;
        var sampleCodes = [];
        for (var j=0; j<splitTimes.length; j++) {
            var controlCode = splitTimes[j].querySelector('ControlCode');
            sampleCodes.push(controlCode.textContent);
        }
        sampleCodes.push('F');
        var correctCodes = correctControls.map(function (e) {
            return e['controlCode'];
        });
        var res = allInRightOrder(sampleCodes, correctCodes);
        var timesTmp = res.correctMapping.map(function (e) {
            if (isNaN(e)) return NaN;
            if (sampleCodes[e]=='F') {
                var resultTime = ranking[i].querySelector('Result > Time');
                if (resultTime) {
                    return parseFloat(resultTime.textContent);
                }
                return NaN;
            } else {
                var splitStatus = splitTimes[e].getAttribute('status');
                if (splitStatus==null || splitStatus=='OK') {
                    return parseFloat(splitTimes[e].querySelector('Time').textContent);
                }
                return NaN;
            }
        });
        times.push(timesTmp);
    }
    console.log("Times:", times);

    // Get splits of global first three runners
    for (var i=0; i<times.length && i<3; i++) {
        for (var j=0; j<times[i].length; j++) {
            var splitTime = (j==0 ? times[i][j] : times[i][j]-times[i][j-1]);
            correctControls[j]['globalFirstThree'].push(splitTime);
        }
    }
    // Get splits of local first three runners
    for (var i=0; i<times.length; i++) {
        for (var j=0; j<times[i].length; j++) {
            if (isNaN(times[i][j])) continue;
            if (j!=0 && isNaN(times[i][j-1])) continue;
            var splitTime = (j==0 ? times[i][j] : times[i][j]-times[i][j-1]);
            for (var k=0; k<correctControls[j]['localFirstThree'].length && k<3; k++) {
                if (splitTime<correctControls[j]['localFirstThree'][k]) break;
            }
            if (k<3) correctControls[j]['localFirstThree'].splice(k, 0, splitTime);
            correctControls[j]['localFirstThree'].splice(3);
        }
    }

    var svg = document.querySelector('#grafik-svg');
    var wid = svg.width.animVal.value-100;
    var hei = svg.height.animVal.value;

    // Colors
    var colors = ['rgb(133,202,93)', 'rgb(145,210,144)', 'rgb(72,181,163)', 'rgb(111,183,214)', 'rgb(117,137,191)', 'rgb(165,137,193)', 'rgb(249,140,182)', 'rgb(252,169,133)'];

    // Control Spacing: First Local
    var referenceTimes = [];
    var sumReferenceTime = 0;
    for (var j=0; j<correctControls.length; j++) {
        sumReferenceTime += correctControls[j]['localFirstThree'][0];
        referenceTimes.push(sumReferenceTime);
    }
    var minVsReference = 0;
    var maxVsReference = 0;
    for (var i in selectedIndexesByClass[classInd]) {
        for (var j=0; j<times[i].length; j++) {
            if (isNaN(times[i][j])) continue;
            var vsReference = times[i][j]-referenceTimes[j];
            if (vsReference<minVsReference) minVsReference = vsReference;
            if (maxVsReference<vsReference) maxVsReference = vsReference;
        }
    }

    // Draw
    var svgout = "";
    svgout += "<rect x='0' y='0' width='"+wid+"' height='"+hei+"' fill='rgba(250,250,250,0.5)' />";
    for (var j=0; j<referenceTimes.length; j++) {
        svgout += "<rect x='"+Math.floor(referenceTimes[j]*wid/sumReferenceTime)+"' y='0' width='1' height='"+hei+"' fill='rgb(200,200,200)' />";
        svgout += "<text x='"+Math.floor(referenceTimes[j]*wid/sumReferenceTime-1)+"' y='"+(hei-2)+"' text-anchor='end' font-size='10px' fill='rgb(200,200,200)'>"+(j+1==referenceTimes.length ? "Z" : j+1)+"</text>";
    }
    var checkedInd = 0;
    for (var i in selectedIndexesByClass[classInd]) {
        var color = colors[checkedInd % colors.length];
        var lastTime = 0;
        var secsBehind = 0;
        var lastSecsBehind = 0;
        var xPart = 0;
        var lastXPart = 0;
        var everSkipped = false;
        var resultStatus = ranking[i].querySelector('Result > Status');
        var isValid = (resultStatus==null || resultStatus.textContent=='OK');
        var skippedLast = false;
        for (var j=0; j<times[i].length; j++) {
            if (isNaN(times[i][j])) {
                skippedLast = true;
                everSkipped = true;
                continue;
            }
            lastXPart = xPart;
            xPart = referenceTimes[j]/sumReferenceTime;
            lastSecsBehind = secsBehind;
            secsBehind = times[i][j]-referenceTimes[j];
            svgout += "<line x1='"+(lastXPart*wid)+"' y1='"+((lastSecsBehind-minVsReference)*(hei-20)/(maxVsReference-minVsReference)+10)+"' x2='"+(xPart*wid)+"' y2='"+((secsBehind-minVsReference)*(hei-20)/(maxVsReference-minVsReference)+10)+"' stroke-width='1' stroke='"+color+"'"+(skippedLast ? " stroke-dasharray='"+(isValid ? "5, 5" : "1, 5")+"'" : "")+(everSkipped && !isValid ? " stroke-opacity='0.3'" : "")+" />";
            svgout += "<circle cx='"+(xPart*wid)+"' cy='"+((secsBehind-minVsReference)*(hei-20)/(maxVsReference-minVsReference)+10)+"' r='2' fill='"+color+"'"+(everSkipped && !isValid ? " fill-opacity='0.3'" : "")+" />";
            lastTime = times[i][j];
            skippedLast = false;
        }
        var firstName = ranking[i].querySelector('Person > Name > Given');
        var lastName = ranking[i].querySelector('Person > Name > Family');
        svgout += "<text x='"+(xPart*wid+2)+"' y='"+((secsBehind-minVsReference)*(hei-20)/(maxVsReference-minVsReference)+14)+"' font-size='14px' fill='"+color+"'"+(everSkipped && !isValid ? " fill-opacity='0.3'" : "")+">"+(firstName ? firstName.textContent : "")+" "+(lastName ? lastName.textContent : "")+"</text>";
        checkedInd++;
    }
    svg.innerHTML = svgout;
}
function showRanking(classInd) {
    if (classInd<0) return;
    if (!(classInd in selectedIndexesByClass)) selectedIndexesByClass[classInd] = {};
    var classResults = xmlDoc.querySelectorAll('ResultList > ClassResult');
    var ranking = classResults[classInd].querySelectorAll('PersonResult');
    var htmlout = "";
    var shortName = classResults[classInd].querySelector('Class > ShortName');
    var name = classResults[classInd].querySelector('Class > Name');
    var length = classResults[classInd].querySelector('Course > Length');
    var climb = classResults[classInd].querySelector('Course > Climb');
    var numberOfControls = classResults[classInd].querySelector('Course > NumberOfControls');
    htmlout += "<h2 class='mobileonly'>" + (shortName ? shortName.textContent : name.textContent) + "</h2>";
    htmlout += "<div>(";
    htmlout += (length ? (length.textContent/1000).toFixed(1) : "?");
    htmlout += " km, ";
    htmlout += (climb ? climb.textContent : "?");
    htmlout += " m, ";
    htmlout += (numberOfControls ? numberOfControls.textContent : "?");
    htmlout += " Posten)</div><div class='mobileonly'><br /><a href='javascript:setHash(&quot;grafik&quot;, 2)' id='grafiklink'>Grafik</a></div><br /><table>";
    for (var i=0; i<ranking.length; i++) {
        var position = ranking[i].querySelector('Result > Position');
        var firstName = ranking[i].querySelector('Person > Name > Given');
        var lastName = ranking[i].querySelector('Person > Name > Family');
        var birthDate = ranking[i].querySelector('Person > BirthDate');
        var addressCity = ranking[i].querySelector('Person > Address > City');
        var addressZipCode = ranking[i].querySelector('Person > Address > ZipCode');
        var clubName = ranking[i].querySelector('Organisation > ShortName');
        var runTime = ranking[i].querySelector('Result > Time');
        htmlout += "<tr><td style='text-align:right;'><input type='checkbox' class='chart-chk' id='chk-"+i+"' /></td><td style='text-align:right;'>"+(position ? position.textContent + "." : "")+"</td><td>"+(firstName ? firstName.textContent : "")+" "+(lastName ? lastName.textContent : "")+"</td><td style='text-align:right;'>"+(birthDate ? birthDate.textContent.substring(0, 4) : "")+"</td><td>"+(addressCity ? addressCity.textContent : (addressZipCode ? addressZipCode.textContent : ""))+"</td><td>"+(clubName ? clubName.textContent : "")+"</td><td style='text-align:right;'>"+(runTime ? formatTime(runTime.textContent) : "--:--")+"</td></tr>";
    }
    htmlout += "</table>";
    document.getElementById('content-box').innerHTML = htmlout;
    for (var i in selectedIndexesByClass[classInd]) {
        document.getElementById('chk-'+i).checked = true;
    }
    for (var i=0; i<ranking.length; i++) {
        var chkBox = document.getElementById('chk-'+i);
        chkBox.onclick = function (classInd, i) {
            if (this.checked) {
                selectedIndexesByClass[classInd][i] = true;
            } else {
                delete selectedIndexesByClass[classInd][i];
            }
            showChart(classInd);
        }.bind(chkBox, classInd, i);
    }
    showChart(classInd);
}
function showClasses(res) {
    var classes = xmlDoc.querySelectorAll('ResultList > ClassResult > Class');
    console.log("Classes:", classes);
    var htmlout = "";
    for (var i=0; i<classes.length; i++) {
        var shortName = classes[i].querySelector('ShortName');
        var name = classes[i].querySelector('Name');
        htmlout += "<a class='classlink" + (i==res ? " selected" : "") + "' href='javascript:setHash(&quot;class" + i + "&quot;, 1)'>" + (shortName ? shortName.textContent : name.textContent) + "</a>";
    }
    document.getElementById('classes-box').innerHTML = htmlout;
}
function updateContent() {
    var path = hashPath();
    var classInd = -1;
    var grafik = false;
    for (var i=0; i<path.length; i++) {
        var resClass = /^class(\d+)$/.exec(path[i]);
        if (resClass) classInd = resClass[1];
        if (path[i]=='grafik' && classInd>=0) grafik = true;
    }
    showClasses(classInd);
    showRanking(classInd);
    showChart(classInd);
    document.getElementById('classes-box').className = (classInd==-1 ? "active" : "inactive");
    document.getElementById('content-box').className = (classInd>=0 && !grafik ? "active" : "inactive");
    document.getElementById('grafik-box').className = (grafik ? "active" : "inactive");
}
if (filePath !== undefined) {
    var lastUpdate = {};
    function checkUpdate() {
        var xhr = new XMLHttpRequest();
        xhr.open('HEAD', filePath, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState!=4) return;
            var etag = xhr.getResponseHeader('ETag');
            var lastModified = xhr.getResponseHeader('Last-modified');
            if (etag != lastUpdate.etag || lastModified != lastUpdate.lastModified) {
                loadUpdate();
            }
            lastUpdate.etag = etag
            lastUpdate.lastModified = lastModified;
        };
        xhr.send();
    }
    function loadUpdate() {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', filePath, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState!=4) return;
            var parser = new DOMParser();
            var etag = xhr.getResponseHeader('ETag');
            var lastModified = xhr.getResponseHeader('Last-modified');
            lastUpdate.etag = etag;
            lastUpdate.lastModified = lastModified;
            window.xmlDoc = parser.parseFromString(xhr.responseText, 'text/xml');
            console.log("XML", xmlDoc);
            var eventName = xmlDoc.querySelector('ResultList > Event > Name').textContent;
            document.getElementById('title').innerHTML = eventName;
            updateContent();
        };
        xhr.send();
    }
    window.addEventListener('hashchange', updateContent);
    window.addEventListener('load', function () {
        window.setInterval(checkUpdate, 15000);
        loadUpdate();
    });
}
