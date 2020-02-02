function obfuscaseForUpload(str) {
  var iv = Math.floor(Math.random() * 0xFFFF);
  var uploadStr = '';
  var current = iv;
  for (var i = 0; i < str.length; i++) {
    var chr = str.charCodeAt(i);
    uploadStr += String.fromCharCode(chr ^ ((current >> 8) & 0xFF));
    current = ((current << 5) - current) & 0xFFFF;
  }
  return iv + ';' + window.btoa(uploadStr);
}
