import email.utils
import hashlib
from http.client import HTTPConnection
from http.server import BaseHTTPRequestHandler, HTTPServer
import mimetypes
import os
import random
import threading
import time
import webbrowser


def start_server(
    server_port=8080,
    public_html_path=os.path.realpath(os.path.join(
        os.path.dirname(__file__),
        '..',
        'public_html',
    )),
):
    """launch simple http server"""

    instance_hash = hashlib.new('SHA256', (
        str(time.time()) +
        str(random.random()) +
        str(random.random())
    ).encode('utf-8')).hexdigest()

    class OLZResultsRequestHandler(BaseHTTPRequestHandler):
        def do_GET(self):  # noqa: N802
            if self.path == '/echo':
                self.send_response(200)
                self.end_headers()
                self.wfile.write(b'echo')
            file_path = os.path.join(
                public_html_path,
                self.path[2 + len(instance_hash):],
            )
            try:
                file_path = file_path[:file_path.rindex('?')]
            except:
                pass
            if os.path.isfile(file_path):
                self.send_response(200)
                file_type, encoding = mimetypes.guess_type(file_path)
                file_date = email.utils.formatdate(os.path.getmtime(file_path))
                self.send_header('Content-type', file_type)
                self.send_header('Last-modified', file_date)
                h = hashlib.sha256()
                with open(file_path, 'rb') as fp:
                    h.update(fp.read())
                self.send_header('ETag', h.hexdigest())
                self.end_headers()
                with open(file_path, 'rb') as fp:
                    self.wfile.write(fp.read())
            else:
                self.send_response(404)
                self.end_headers()
                self.wfile.write(b"HTTP Error 404: Not Found")

        def do_HEAD(self):  # noqa: N802
            if self.path == '/echo':
                self.send_response(200)
                self.end_headers()
            file_path = os.path.join(
                public_html_path,
                self.path[2 + len(instance_hash):],
            )
            try:
                file_path = file_path[:file_path.rindex('?')]
            except:
                pass
            if os.path.isfile(file_path):
                self.send_response(200)
                file_type, encoding = mimetypes.guess_type(file_path)
                file_date = email.utils.formatdate(os.path.getmtime(file_path))
                self.send_header('Content-type', file_type)
                self.send_header('Last-modified', file_date)
                h = hashlib.sha256()
                with open(file_path, 'rb') as fp:
                    h.update(fp.read())
                self.send_header('ETag', h.hexdigest())
                self.end_headers()
            else:
                self.send_response(404)
                self.end_headers()

    httpd = HTTPServer(("", server_port), OLZResultsRequestHandler)
    print("serving at port {0}".format(server_port))

    def serve_forever(httpd):
        httpd.serve_forever()

    httpd.server_thread = threading.Thread(target=serve_forever, args=(httpd,))
    httpd.instance_hash = instance_hash
    httpd.server_thread.start()
    return httpd


def start_webbrowser(
    server_port,
    instance_hash,
    server_path,
):
    try:
        httpc = HTTPConnection("localhost", server_port, timeout=10)
        httpc.request("GET", "/echo")
        resp = httpc.getresponse()
        resp.read()
        httpc.close()
    except:
        print("Server did not start, aborting...")
        return
    webbrowser.open(
        'http://localhost:' + str(server_port) + '/' +
        instance_hash + '/' + server_path,
    )
