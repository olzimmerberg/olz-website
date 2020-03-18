var flakyElements = document.querySelectorAll('.test-flaky');
for (var i=0; i<flakyElements.length; i++) {
    var rect = flakyElements[i].getBoundingClientRect();
    var cover = document.getElementById('flaky-' + i);
    if (!cover) {
        var cover = document.createElement('div');
        document.documentElement.appendChild(cover);
        cover.id = 'flaky-' + i;
        cover.style.position = 'absolute';
        cover.style.backgroundColor = 'black';
        cover.style.zIndex = 999999;
    }
    cover.style.width = Math.ceil(rect.width+1) + 'px';
    cover.style.height = Math.ceil(rect.height+1) + 'px';
    cover.style.top = Math.floor(rect.top) + 'px';
    cover.style.left = Math.floor(rect.left) + 'px';
}
